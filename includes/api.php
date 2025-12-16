<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once './bill.php';
require_once './db.php';
require_once __DIR__.'/../data/import.php';

header('Content-Type: application/json');

class InvalidActionException extends Exception {}
class MissingRequiredException extends Exception {}
class UpdateException extends Exception {}

try {
  $action = $_GET['action'] ?? '';

  switch($action) {
    case 'add':
      $data = [
        'date'      => (new DateTime($_POST['date']))->format('Y-m-d'),
        'payeeId'   => $_POST['payeeId'], // Payee ID instead of name
        'amount'    => (float)$_POST['amount'],
        'paymentId' => $_POST['paymentId'],
        'comment'   => $_POST['comment'] ?? '',
        'year'      => (int)explode('-', $_POST['date'])[0]
      ];

       // Validate required fields
      if (empty($data['date']) || empty($data['payeeId']) || $data['amount'] <= 0) {
        echo json_encode(['error' => 'Missing required fields.']);
        exit;
      }

      $stmt = DB::connect()->prepare("
        INSERT INTO bills (billDate, payeeId, amount, paymentId, comment, year)
        VALUES (?, ?, ?, ?, ?, ?)
      ");

      $result = $stmt->execute([
        $data['date'],
        $data['payeeId'],
        $data['amount'],
        $data['paymentId'],
        $data['comment'],
        $data['year']
      ]);

      echo json_encode(['success' => $result]);
      break;

    case 'edit':
      file_put_contents('debug.log', print_r($_POST, true), FILE_APPEND);

      try {
        $data = [
          'id'        => $_POST['id'],
          'date'      => (new DateTime($_POST['date']))->format('Y-m-d'),
          'payeeId'   => $_POST['payeeId'],
          'amount'    => (float)$_POST['amount'],
          'paymentId' => $_POST['paymentId'],
          'comment'   => $_POST['comment'] ?? ''
        ];

        // Validate required fields
        if (empty($data['id']) || empty($data['date']) || empty($data['payeeId']) || $data['amount'] <= 0) {
          throw new MissingRequiredException('Missing required fields.');
        }

        // Prepare and execute the update statement
        $stmt = DB::connect()->prepare("
          UPDATE bills
          SET billDate = ?, payeeId = ?, amount = ?, paymentId = ?, comment = ?
          WHERE id = ?
        ");

        $result = $stmt->execute([
          $data['date'],
          $data['payeeId'],
          $data['amount'],
          $data['paymentId'],
          $data['comment'],
          $data['id']
        ]);

        if (!$result) {
          throw new UpdateException('Failed to update the database.');
        }

        echo json_encode(['success' => true]);
      } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
      }
      break;

    case 'getAll':
      echo json_encode(Bill::getAll()->fetchAll(PDO::FETCH_ASSOC));
      break;

    case 'getById':
      $id = $_GET['id'];

      $stmt = DB::connect()->prepare("
        SELECT bills.*, payees.name AS payeeName, payees.id AS payeeId
        FROM bills
        JOIN payees ON bills.payeeId = payees.id
        WHERE bills.id = ?
      ");
      $stmt->execute([$id]);

      $bill = $stmt->fetch(PDO::FETCH_ASSOC);

      echo json_encode($bill);
      break;

    case 'getTotals':
      echo json_encode(Bill::getYearlyTotals()->fetchAll(PDO::FETCH_ASSOC));
      break;

    case 'getByYear':
      $year = $_GET['year'] ?? date('Y');
      $sort = $_GET['sort'] ?? 'date_desc';

      $sortOptions = [
        'date_asc' => 'bills.billDate ASC',
        'date_desc' => 'bills.billDate DESC',
        'payee' => 'payees.name ASC, bills.billDate ASC'
      ];
      $orderBy = $sortOptions[$sort] ?? $sortOptions['date_asc'];

      $stmt = DB::connect()->prepare("
        SELECT bills.*, payees.name AS payeeName
        FROM bills
        JOIN payees ON bills.payeeId = payees.id
        WHERE bills.year = ?
        ORDER BY $orderBy
      ");
      $stmt->execute([$year]);
      echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
      break;

    case 'addPayee':
      $stmt = DB::connect()->prepare("INSERT INTO payees (name) VALUES (?)");
      $result = $stmt->execute([$_POST['payeeName']]);
      echo json_encode(['success' => $result]);
      break;

    case 'getPayees':
      try {
        $stmt = DB::connect()->query("SELECT id, name FROM payees ORDER BY name ASC");
        $payees = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($payees);
      } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
      }
      break;

    case 'getYears':
      try {
        $stmt = DB::connect()->query("SELECT DISTINCT year FROM bills ORDER BY year DESC");
        $years = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo json_encode($years);
      } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
      }
      break;

    case 'getYtdAmounts':
      $year = $_GET['year'] ?? date('Y');

      // Fetch YTD amounts for each payee
      $stmt = DB::connect()->prepare("
        SELECT payees.name AS payeeName, SUM(bills.amount) AS totalAmount
        FROM bills
        JOIN payees ON bills.payeeId = payees.id
        WHERE bills.year = ?
        GROUP BY payees.name
        ORDER BY payees.name ASC
      ");
      $stmt->execute([$year]);

      $payeeAmounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

      // Fetch overall YTD amount
      $stmt = DB::connect()->prepare("
        SELECT SUM(amount) AS overallAmount
        FROM bills
        WHERE year = ?
      ");
      $stmt->execute([$year]);

      $overallAmount = $stmt->fetch(PDO::FETCH_ASSOC)['overallAmount'] ?? 0;

      echo json_encode([
        'payeeAmounts' => $payeeAmounts,
        'overallAmount' => $overallAmount
      ]);
      break;

    case 'importCsv':
      try {
        if (!isset($_FILES['file'])) {
          throw new MissingRequiredException('No file uploaded.');
        }

        if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
          throw new RuntimeException('Upload failed with error code '.$_FILES['file']['error']);
        }

        $tmpPath = tempnam(sys_get_temp_dir(), 'billtrak_csv_');
        if (!$tmpPath || !move_uploaded_file($_FILES['file']['tmp_name'], $tmpPath)) {
          throw new RuntimeException('Could not process uploaded file.');
        }

        try {
          $result = importCsvToDatabase($tmpPath);
        } finally {
          if (is_file($tmpPath)) {
            unlink($tmpPath);
          }
        }

        echo json_encode(['success' => true, 'result' => $result]);
      } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
      }
      break;

    default:
      throw new InvalidActionException('Invalid action');
  }
} catch(Exception $e) {
  http_response_code(500);

  echo json_encode(['error' => $e->getMessage()]);
}
