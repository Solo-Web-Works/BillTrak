<?php require_once 'includes/init.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bill Tracker</title>
  <meta charset="UTF-8">

  <link href="css/style.css" rel="stylesheet">

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="app.js" defer></script>
</head>

<body class="bg-gray-100">
  <div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Bill Payments</h1>

    <!-- Add Bill Form -->
    <form id="billForm" class="bg-white p-4 mb-6 rounded shadow">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <input type="date" name="date" required class="p-2 border rounded">
        <select name="billName" required class="p-2 border rounded">
          <option value="MTS">MTS</option>
          <option value="Hydro">Hydro</option>
          <option value="Shaw">Shaw</option>
          <option value="Telus">Telus</option>
          <option value="Water">Water</option>
          <option value="PC Mastercard">PC Mastercard</option>
        </select>

        <input type="number" step="0.01" name="amount" placeholder="Amount" required class="p-2 border rounded">
        <input type="text" name="paymentId" placeholder="Payment ID" class="p-2 border rounded">
      </div>

      <button type="submit" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded">Add Bill</button>
    </form>

    <!-- Chart -->
    <div class="bg-white p-4 mb-6 rounded shadow">
      <canvas id="chart"></canvas>
    </div>

    <!-- Bills Table -->
    <div class="bg-white rounded shadow overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-2">Date</th>
            <th class="px-4 py-2">Bill</th>
            <th class="px-4 py-2">Amount</th>
            <th class="px-4 py-2">Payment ID</th>
          </tr>
        </thead>

        <tbody id="billList">
          <?php foreach (Bill::getAll() as $bill): ?>
            <tr class="border-t">
              <td class="px-4 py-2"><?php echo date('Y-m-d', strtotime($bill['billDate'])) ?></td>
              <td class="px-4 py-2"><?php echo htmlspecialchars($bill['billName']) ?></td>
              <td class="px-4 py-2">$<?php echo number_format($bill['amount'], 2) ?></td>
              <td class="px-4 py-2"><?php echo $bill['paymentId'] ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
