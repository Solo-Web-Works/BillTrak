<?php
class DB {
  public static function connect() {
    $db = new PDO('sqlite:'.__DIR__.'/../data/bills.db');

    $db->exec("CREATE TABLE IF NOT EXISTS payees (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      name TEXT NOT NULL UNIQUE,
      createdAt TEXT DEFAULT CURRENT_TIMESTAMP
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS bills (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      billDate TEXT NOT NULL,
      payeeId INTEGER NOT NULL,
      amount REAL NOT NULL,
      paymentId TEXT,
      comment TEXT,
      year INTEGER NOT NULL,
      FOREIGN KEY (payeeId) REFERENCES payees(id) ON DELETE CASCADE
    )");

    return $db;
  }
}
