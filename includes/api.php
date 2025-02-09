<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once './bill.php';
require_once './db.php';

header('Content-Type: application/json');

class InvalidActionException extends Exception {}
class MissingRequiredException extends Exception {}
class UpdateException extends Exception {}

try {
  $action = $_GET['action'] ?? '';

  switch($action) {
    case 'add':
      $data = [
        'date'      => (new DateTime($_POST['date']))->format('Y-m-d'), // Normalize to YYYY-MM-DD
        'billName'  => $_POST['billName'],
        'amount'    => (float)$_POST['amount'],
        'paymentId' => $_POST['paymentId'],
        'year'      => (int)explode('-', $_POST['date'])[0],
        'comment'   => $_POST['comment']
      ];

      $result = Bill::add($data);
      echo json_encode(['success' => $result]);
      break;

    case 'edit':
      file_put_contents('debug.log', print_r($_POST, true), FILE_APPEND);

      try {
        $data = [
          'id'        => $_POST['id'],
          'date'      => (new DateTime($_POST['date']))->format('Y-m-d'),
          'billName'  => $_POST['billName'],
          'amount'    => (float)$_POST['amount'],
          'paymentId' => $_POST['paymentId'],
          'comment'   => $_POST['comment'] ?? ''
        ];

        // Validate required fields
        if (empty($data['id']) || empty($data['date']) || empty($data['billName']) || $data['amount'] <= 0) {
          throw new MissingRequiredException('Missing required fields.');
        }

        // Prepare and execute the update statement
        $stmt = DB::connect()->prepare("
          UPDATE bills
          SET billDate = ?, billName = ?, amount = ?, paymentId = ?, comment = ?
          WHERE id = ?
        ");

        $result = $stmt->execute([
          $data['date'],
          $data['billName'],
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

    case 'getPayees':
      $stmt = DB::connect()->query("SELECT DISTINCT billName FROM bills ORDER BY billName ASC");
      echo json_encode($stmt->fetchAll(PDO::FETCH_COLUMN));
      break;

    case 'addPayee':
      $stmt = DB::connect()->prepare("INSERT INTO bills (billName) VALUES (?)");
      $result = $stmt->execute([$_POST['billName']]);
      echo json_encode(['success' => $result]);
      break;

    case 'getAll':
      echo json_encode(Bill::getAll()->fetchAll(PDO::FETCH_ASSOC));
      break;

    case 'getById':
      $id = $_GET['id'];
      $stmt = DB::connect()->prepare("SELECT * FROM bills WHERE id = ?");
      $stmt->execute([$id]);
      echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
      break;

      case 'getTotals':
      echo json_encode(Bill::getYearlyTotals()->fetchAll(PDO::FETCH_ASSOC));
      break;

    case 'getByYear':
      $year = $_GET['year'] ?? date('Y');
      $sort = $_GET['sort'] ?? 'date_asc'; // Default sorting

      $sortOptions = [
        'date_asc'  => 'billDate ASC',
        'date_desc' => 'billDate DESC',
        'payee'     => 'billName ASC, billDate ASC',
      ];

      $orderBy = $sortOptions[$sort] ?? $sortOptions['date_asc'];

      $stmt = DB::connect()->prepare("SELECT * FROM bills WHERE year = ? ORDER BY $orderBy");
      $stmt->execute([$year]);
      echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
      break;

    case 'getYears':
      $stmt = DB::connect()->query("SELECT DISTINCT year FROM bills ORDER BY year DESC");
      echo json_encode($stmt->fetchAll(PDO::FETCH_COLUMN));
      break;

    default:
      throw new InvalidActionException('Invalid action');
  }
} catch(Exception $e) {
  http_response_code(500);

  echo json_encode(['error' => $e->getMessage()]);
}
