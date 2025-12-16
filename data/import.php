<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/db.php';

/**
 * Normalize incoming dates to YYYY-MM-DD.
 *
 * @throws InvalidArgumentException
 */
function normalizeDate(string $rawDate): string {
  $rawDate = trim($rawDate);
  $formats = ['M d, Y', 'M j, Y', 'Y-m-d', 'Y/m/d'];

  foreach ($formats as $format) {
    $date = DateTime::createFromFormat($format, $rawDate);
    if ($date instanceof DateTime) {
      return $date->format('Y-m-d');
    }
  }

  $timestamp = strtotime($rawDate);
  if ($timestamp !== false) {
    return date('Y-m-d', $timestamp);
  }

  throw new InvalidArgumentException("Unable to parse date: {$rawDate}");
}

/**
 * Import a CSV file with the columns: Date, Payee, Reference Number, Amount.
 *
 * - Creates payees on the fly if they don't exist.
 * - Skips duplicate bills that match date + payee + amount + paymentId.
 *
 * @return array Summary of the import run.
 */
function importCsvToDatabase(string $csvFile, ?PDO $db = null): array {
  $db = $db ?: DB::connect();

  // Allow relative paths from the project root or current working directory
  $resolvedPath = $csvFile;
  if (!is_readable($resolvedPath)) {
    $altPath = __DIR__.'/../'.$csvFile;
    if (is_readable($altPath)) {
      $resolvedPath = $altPath;
    }
  }

  if (!is_readable($resolvedPath)) {
    throw new InvalidArgumentException("File not found or unreadable: {$csvFile}");
  }

  $handle = fopen($resolvedPath, 'r');
  if ($handle === false) {
    throw new RuntimeException("Unable to open file: {$resolvedPath}");
  }

  // Explicitly set escape char to avoid deprecation warnings on newer PHP versions
  $headers = fgetcsv($handle, 0, ',', '"', '\\');
  if ($headers === false) {
    fclose($handle);
    throw new RuntimeException("CSV file appears to be empty: {$csvFile}");
  }

  $headers = array_map('trim', $headers);
  $requiredHeaders = ['Date', 'Payee', 'Reference Number', 'Amount'];
  $headerMap = [];

  foreach ($requiredHeaders as $column) {
    $index = array_search($column, $headers);
    if ($index === false) {
      fclose($handle);
      throw new RuntimeException("Missing required column \"{$column}\" in {$csvFile}");
    }
    $headerMap[$column] = $index;
  }

  $inserted = 0;
  $skipped = 0;
  $payeesCreated = 0;
  $errors = [];
  $lineNumber = 1; // Start after header

  $findPayeeStmt = $db->prepare("SELECT id FROM payees WHERE name = ?");
  $insertPayeeStmt = $db->prepare("INSERT OR IGNORE INTO payees (name) VALUES (?)");
  $findBillStmt = $db->prepare("
    SELECT id FROM bills
    WHERE billDate = ? AND payeeId = ? AND amount = ? AND IFNULL(paymentId, '') = IFNULL(?, '')
  ");
  $insertBillStmt = $db->prepare("
    INSERT INTO bills (billDate, payeeId, amount, paymentId, comment, year)
    VALUES (?, ?, ?, ?, ?, ?)
  ");

  $db->beginTransaction();

  try {
    while (($row = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
      $lineNumber++;

      // Skip rows that are completely empty
      if (count(array_filter($row, fn($value) => trim((string)$value) !== '')) === 0) {
        continue;
      }

      try {
        $date = normalizeDate($row[$headerMap['Date']] ?? '');
        $payeeName = trim((string)($row[$headerMap['Payee']] ?? ''));
        $paymentId = trim((string)($row[$headerMap['Reference Number']] ?? '')) ?: null;
        $amountRaw = (string)($row[$headerMap['Amount']] ?? '0');
        $amount = (float)str_replace([',', '$'], '', $amountRaw);
        $year = (int)substr($date, 0, 4);

        if ($payeeName === '' || $amount === 0.0) {
          throw new RuntimeException('Missing payee or zero amount.');
        }

        // Fetch or create payee
        $findPayeeStmt->execute([$payeeName]);
        $payeeId = $findPayeeStmt->fetchColumn();

        if (!$payeeId) {
          $insertPayeeStmt->execute([$payeeName]);
          if ($insertPayeeStmt->rowCount() > 0) {
            $payeesCreated++;
          }

          $findPayeeStmt->execute([$payeeName]);
          $payeeId = $findPayeeStmt->fetchColumn();
        }

        if (!$payeeId) {
          throw new RuntimeException("Could not resolve payee ID for \"{$payeeName}\".");
        }

        // Skip duplicates
        $findBillStmt->execute([$date, $payeeId, $amount, $paymentId]);
        if ($findBillStmt->fetchColumn()) {
          $skipped++;
          continue;
        }

        $insertBillStmt->execute([$date, $payeeId, $amount, $paymentId, '', $year]);
        $inserted++;
      } catch (Throwable $e) {
        $errors[] = "Line {$lineNumber}: {$e->getMessage()}";
      }
    }

    $db->commit();
  } catch (Throwable $e) {
    $db->rollBack();
    fclose($handle);
    throw $e;
  }

  fclose($handle);

  return [
    'file' => $csvFile,
    'inserted' => $inserted,
    'skipped' => $skipped,
    'payeesCreated' => $payeesCreated,
    'errors' => $errors
  ];
}

if (PHP_SAPI === 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
  $files = array_slice($argv, 1);

  if (empty($files)) {
    echo "Usage: php data/import.php <csv-file> [<csv-file> ...]\n";
    echo "The file must contain the columns: Date, Payee, Reference Number, Amount.\n";
    exit(1);
  }

  foreach ($files as $file) {
    try {
      $result = importCsvToDatabase($file);

      echo "Imported {$result['inserted']} bills from {$file} ";
      echo "(skipped {$result['skipped']} duplicates, {$result['payeesCreated']} new payees).\n";

      if (!empty($result['errors'])) {
        echo "Warnings:\n  - ".implode("\n  - ", $result['errors'])."\n";
      }
    } catch (Throwable $e) {
      echo "Failed to import {$file}: {$e->getMessage()}\n";
    }
  }
}
