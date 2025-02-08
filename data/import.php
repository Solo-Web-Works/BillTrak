<?php
require_once __DIR__.'/../includes/db.php';

function importCsvToDatabase($csvFile, $year) {
  $db = DB::connect();

  if (!file_exists($csvFile)) {
    die("Error: File not found - $csvFile");
  }

  $handle = fopen($csvFile, 'r');
  if ($handle === false) {
    die("Error: Unable to open file - $csvFile");
  }

  // Skip the header row
  fgetcsv($handle);

  // Prepare SQL statement for inserting data
  $stmt = $db->prepare("INSERT INTO bills (billDate, billName, amount, paymentId, year) VALUES (?, ?, ?, ?, ?)");

  // Process each row
  while (($row = fgetcsv($handle)) !== false) {
    $date = $row[0];
    $billName = $row[1];
    $amount = floatval($row[2]);
    $paymentId = $row[3] ?? null;

    $stmt->execute([$date, $billName, $amount, $paymentId, $year]);
  }

  fclose($handle);

  echo "Data imported successfully from $csvFile.\r\n";
}

// Example usage
importCsvToDatabase('2025.csv', 2025);
importCsvToDatabase('2024.csv', 2024);
importCsvToDatabase('2023.csv', 2023);
importCsvToDatabase('2022.csv', 2022);
