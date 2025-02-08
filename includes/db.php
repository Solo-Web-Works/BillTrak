<?php
class DB {
  public static function connect() {
    $db = new PDO('sqlite:'.__DIR__.'/../data/bills.db');

    $db->exec("CREATE TABLE IF NOT EXISTS bills (
        id INTEGER PRIMARY KEY,
        billDate TEXT NOT NULL,
        billName TEXT NOT NULL,
        amount REAL NOT NULL,
        paymentId TEXT,
        year INTEGER NOT NULL
      )");

    return $db;
  }
}
