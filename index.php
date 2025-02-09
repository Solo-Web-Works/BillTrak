<?php require_once 'includes/init.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bill Tracker</title>
  <meta charset="UTF-8">

  <link href="css/style.css" rel="stylesheet">

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="js/app.js" defer></script>
</head>

<body class="bg-gray-100">
  <div class="container mx-auto p-4">
    <h1 class="text-4xl font-bold mb-4">Bill Tracker</h1>

    <!-- Add Bill Form -->
    <form id="billForm" class="bg-white p-4 mb-6 rounded shadow">
      <h2 class="text-2xl font-bold mb-4">Add New Payment</h2>

      <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <input type="date" name="date" required class="p-2 border rounded">

        <select required id="payeeId" name="payeeId" class="border rounded p-2">
          <!-- Payees will be populated here -->
        </select>

        <input type="number" step="0.01" name="amount" placeholder="Amount" required class="p-2 border rounded">
        <input type="text" name="paymentId" placeholder="Payment ID" class="p-2 border rounded">

        <input type="text" name="comment" class="p-2 border rounded" placeholder="Add a comment (optional)"></input>
      </div>

      <button type="submit" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded">Add Bill</button>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white p-4 mb-6 rounded shadow">
      <!-- Add Payee Form -->
      <form id="addPayeeForm" class="border p-4 rounded shadow bg-gray-100">
        <h2 class="text-2xl font-bold mb-4">Add New Payee</h2>

        <input type="text" id="newPayeeName" name="payeeName" class="bg-white border rounded p-2 w-5/6" placeholder="New Payee Name">

        <button type="submit" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded">Add Payee</button>
      </form>

      <!-- Chart & Totals -->
      <div class="border p-4 rounded shadow bg-gray-100 grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div id="ytdPieChartSection" class="bg-white p-4 rounded shadow">
          <h3 class="text-xl font-bold mb-4">YTD Chart</h3>
          <canvas id="ytdPieChart"></canvas>
        </div>

        <div id="ytdSection" class="bg-white p-4 rounded shadow">
          <h3 class="text-xl font-bold mb-4">Year-to-Date Summary</h3>

          <div id="ytdOverall" class="mb-4">
            <h3 class="text-lg font-semibold mb-0 inline-block">Overall YTD Amount:</h3> <span id="ytdOverallAmount" class="text-lg font-semibold">$0.00</span>
          </div>

          <div id="ytdPayees">
            <h3 class="text-lg font-semibold mb-0 inline-block">YTD by Payee:</h3>

            <ul id="ytdPayeeList" class="list-none pl-0">
              <!-- Payee amounts will be populated here -->
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- Bill List -->
    <div class="bg-white p-4 shodow rounded">
      <div class="flex justify-between">
        <h2 class="text-2xl font-bold mb-4">Payments</h2>

        <div class="ml-auto flex items-center gap-4 mb-4 w-fit">
          <div class="">
            <label for="sortSelect" class="block mb-2 font-bold">Sort By:</label>
            <select id="sortSelect" class="border rounded px-3 py-2 bg-gray-50">
              <option value="date_desc">Date (Descending)</option>
              <option value="date_asc">Date (Ascending)</option>
              <option value="payee">Payee (Sorted by Date)</option>
            </select>
          </div>

          <div class="">
            <label for="yearSelect" class="block mb-2 font-bold">Select Year:</label>
            <select id="yearSelect" class="border rounded px-3 py-2 bg-gray-50">
              <option value="2025">2025</option>
              <option value="2024">2024</option>
              <option value="2023">2023</option>
              <option value="2022">2022</option>
            </select>
          </div>
        </div>
      </div>

      <div id="billList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4"></div>
    </div>
  </div>

  <!-- Edit Modal -->
  <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-96">
      <h2 class="text-xl font-bold mb-4">Edit Payment</h2>
      <form id="editForm" class="space-y-4">
        <!-- Hidden input for the payment ID -->
        <input type="hidden" id="editFormId" name="id">

        <!-- Date -->
        <div>
          <label for="editFormDate" class="block text-sm font-medium">Date</label>
          <input type="date" id="editFormDate" name="date" class="border rounded px-3 py-2 w-full">
        </div>

        <!-- Bill Name -->
        <div>
          <label for="editFormPayeeId" class="block text-sm font-medium">Payee</label>
          <select id="editFormPayeeId" name="payeeId" class="border rounded px-3 py-2 w-full">
            <option value="" disabled>Select Payee</option>
            <!-- Options will be populated dynamically -->
          </select>
        </div>

        <!-- Amount -->
        <div>
          <label for="editFormAmount" class="block text-sm font-medium">Amount</label>
          <input type="number" id="editFormAmount" name="amount" step="0.01" class="border rounded px-3 py-2 w-full">
        </div>

        <!-- Payment ID -->
        <div>
          <label for="editFormPaymentId" class="block text-sm font-medium">Payment ID</label>
          <input type="text" id="editFormPaymentId" name="paymentId" class="border rounded px-3 py-2 w-full">
        </div>

        <!-- Comment -->
        <div>
          <label for="editFormComment" class="block text-sm font-medium">Comment</label>
          <textarea id="editFormComment" name="comment" class="border rounded px-3 py-2 w-full" rows="3"></textarea>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end space-x-2">
          <button type="button" id="editFormCancel" class="bg-red-300 px-4 py-2 rounded hover:bg-red-400">Cancel</button>
          <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Save</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
