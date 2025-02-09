<?php
class Bill {
  public static function getAll() {
    $db = DB::connect();

    return $db->query("SELECT * FROM bills ORDER BY billDate DESC");
  }

  public static function add($data) {
    $db = DB::connect();

    $stmt = $db->prepare("INSERT INTO bills
      (billDate, billName, amount, paymentId, year, comment)
      VALUES (?, ?, ?, ?, ?, ?)");

    return $stmt->execute([
      $data['date'],
      $data['billName'],
      $data['amount'],
      $data['paymentId'],
      $data['year'],
      $data['comment']
    ]);
  }

  public static function getYearlyTotals() {
    $db = DB::connect();

    return $db->query("SELECT year, billName, SUM(amount) as total
      FROM bills GROUP BY year, billName");
  }
}
