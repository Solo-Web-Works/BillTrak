<?php
require_once './bill.php';
require_once './db.php';

header('Content-Type: application/json');

class InvalidActionException extends Exception {}

try {
  $action = $_GET['action'] ?? '';

  switch($action) {
    case 'add':
      $data = [
        'date' => $_POST['date'],
        'billName' => $_POST['billName'],
        'amount' => (float)$_POST['amount'],
        'paymentId' => $_POST['paymentId'],
        'year' => (int)explode('-', $_POST['date'])[0]
      ];

      echo json_encode(['success' => Bill::add($data)]);
      break;

    case 'getAll':
      echo json_encode(Bill::getAll()->fetchAll(PDO::FETCH_ASSOC));
      break;

    case 'getTotals':
      echo json_encode(Bill::getYearlyTotals()->fetchAll(PDO::FETCH_ASSOC));
      break;

    default:
      throw new InvalidActionException('Invalid action');
  }
} catch(Exception $e) {
  http_response_code(500);

  echo json_encode(['error' => $e->getMessage()]);
}
