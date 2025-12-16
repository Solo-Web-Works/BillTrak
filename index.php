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
    <div class="flex justify-between">
      <h1 class="text-4xl font-bold p-4">Bill Tracker</h1>

      <div class="flex justify-end p-4">
        <button id="darkModeToggle" class="flex items-center focus:outline-none p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">
          <svg id="sunIcon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500 hidden" fill="none" viewBox="0 0 50 50" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M 24.90625 3.96875 C 24.863281 3.976563 24.820313 3.988281 24.78125 4 C 24.316406 4.105469 23.988281 4.523438 24 5 L 24 11 C 23.996094 11.359375 24.183594 11.695313 24.496094 11.878906 C 24.808594 12.058594 25.191406 12.058594 25.503906 11.878906 C 25.816406 11.695313 26.003906 11.359375 26 11 L 26 5 C 26.011719 4.710938 25.894531 4.433594 25.6875 4.238281 C 25.476563 4.039063 25.191406 3.941406 24.90625 3.96875 Z M 10.65625 9.84375 C 10.28125 9.910156 9.980469 10.183594 9.875 10.546875 C 9.769531 10.914063 9.878906 11.304688 10.15625 11.5625 L 14.40625 15.8125 C 14.648438 16.109375 15.035156 16.246094 15.410156 16.160156 C 15.78125 16.074219 16.074219 15.78125 16.160156 15.410156 C 16.246094 15.035156 16.109375 14.648438 15.8125 14.40625 L 11.5625 10.15625 C 11.355469 9.933594 11.054688 9.820313 10.75 9.84375 C 10.71875 9.84375 10.6875 9.84375 10.65625 9.84375 Z M 39.03125 9.84375 C 38.804688 9.875 38.59375 9.988281 38.4375 10.15625 L 34.1875 14.40625 C 33.890625 14.648438 33.753906 15.035156 33.839844 15.410156 C 33.925781 15.78125 34.21875 16.074219 34.589844 16.160156 C 34.964844 16.246094 35.351563 16.109375 35.59375 15.8125 L 39.84375 11.5625 C 40.15625 11.265625 40.246094 10.800781 40.0625 10.410156 C 39.875 10.015625 39.460938 9.789063 39.03125 9.84375 Z M 24.90625 15 C 24.875 15.007813 24.84375 15.019531 24.8125 15.03125 C 24.75 15.035156 24.6875 15.046875 24.625 15.0625 C 24.613281 15.074219 24.605469 15.082031 24.59375 15.09375 C 19.289063 15.320313 15 19.640625 15 25 C 15 30.503906 19.496094 35 25 35 C 30.503906 35 35 30.503906 35 25 C 35 19.660156 30.746094 15.355469 25.46875 15.09375 C 25.433594 15.09375 25.410156 15.0625 25.375 15.0625 C 25.273438 15.023438 25.167969 15.003906 25.0625 15 C 25.042969 15 25.019531 15 25 15 C 24.96875 15 24.9375 15 24.90625 15 Z M 24.9375 17 C 24.957031 17 24.980469 17 25 17 C 25.03125 17 25.0625 17 25.09375 17 C 29.46875 17.050781 33 20.613281 33 25 C 33 29.421875 29.421875 33 25 33 C 20.582031 33 17 29.421875 17 25 C 17 20.601563 20.546875 17.035156 24.9375 17 Z M 4.71875 24 C 4.167969 24.078125 3.78125 24.589844 3.859375 25.140625 C 3.9375 25.691406 4.449219 26.078125 5 26 L 11 26 C 11.359375 26.003906 11.695313 25.816406 11.878906 25.503906 C 12.058594 25.191406 12.058594 24.808594 11.878906 24.496094 C 11.695313 24.183594 11.359375 23.996094 11 24 L 5 24 C 4.96875 24 4.9375 24 4.90625 24 C 4.875 24 4.84375 24 4.8125 24 C 4.78125 24 4.75 24 4.71875 24 Z M 38.71875 24 C 38.167969 24.078125 37.78125 24.589844 37.859375 25.140625 C 37.9375 25.691406 38.449219 26.078125 39 26 L 45 26 C 45.359375 26.003906 45.695313 25.816406 45.878906 25.503906 C 46.058594 25.191406 46.058594 24.808594 45.878906 24.496094 C 45.695313 24.183594 45.359375 23.996094 45 24 L 39 24 C 38.96875 24 38.9375 24 38.90625 24 C 38.875 24 38.84375 24 38.8125 24 C 38.78125 24 38.75 24 38.71875 24 Z M 15 33.875 C 14.773438 33.90625 14.5625 34.019531 14.40625 34.1875 L 10.15625 38.4375 C 9.859375 38.679688 9.722656 39.066406 9.808594 39.441406 C 9.894531 39.8125 10.1875 40.105469 10.558594 40.191406 C 10.933594 40.277344 11.320313 40.140625 11.5625 39.84375 L 15.8125 35.59375 C 16.109375 35.308594 16.199219 34.867188 16.039063 34.488281 C 15.882813 34.109375 15.503906 33.867188 15.09375 33.875 C 15.0625 33.875 15.03125 33.875 15 33.875 Z M 34.6875 33.875 C 34.3125 33.941406 34.011719 34.214844 33.90625 34.578125 C 33.800781 34.945313 33.910156 35.335938 34.1875 35.59375 L 38.4375 39.84375 C 38.679688 40.140625 39.066406 40.277344 39.441406 40.191406 C 39.8125 40.105469 40.105469 39.8125 40.191406 39.441406 C 40.277344 39.066406 40.140625 38.679688 39.84375 38.4375 L 35.59375 34.1875 C 35.40625 33.988281 35.148438 33.878906 34.875 33.875 C 34.84375 33.875 34.8125 33.875 34.78125 33.875 C 34.75 33.875 34.71875 33.875 34.6875 33.875 Z M 24.90625 37.96875 C 24.863281 37.976563 24.820313 37.988281 24.78125 38 C 24.316406 38.105469 23.988281 38.523438 24 39 L 24 45 C 23.996094 45.359375 24.183594 45.695313 24.496094 45.878906 C 24.808594 46.058594 25.191406 46.058594 25.503906 45.878906 C 25.816406 45.695313 26.003906 45.359375 26 45 L 26 39 C 26.011719 38.710938 25.894531 38.433594 25.6875 38.238281 C 25.476563 38.039063 25.191406 37.941406 24.90625 37.96875 Z"></path>
          </svg>
          <svg id="moonIcon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Add Bill Form -->
    <form id="billForm" class="bg-white p-4 mb-6 rounded shadow">
      <h2 class="text-2xl font-bold mb-4">Add New Payment</h2>

      <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <input type="date" name="date" required class="p-2 border rounded bg-gray-50">

        <select required id="payeeId" name="payeeId" class="border rounded p-2 bg-gray-50">
          <!-- Payees will be populated here -->
        </select>

        <input type="number" step="0.01" name="amount" placeholder="Amount" required class="p-2 border rounded bg-gray-50">
        <input type="text" name="paymentId" placeholder="Payment ID" class="p-2 border rounded bg-gray-50">

        <input type="text" name="comment" class="p-2 border rounded bg-gray-50" placeholder="Add a comment (optional)"></input>
      </div>

      <div class="flex items-center gap-4 mt-4">
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Add Bill</button>
        <button type="button" id="openImportModal" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Import CSV</button>
      </div>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white p-4 mb-6 rounded shadow">
      <!-- Add Payee Form -->
      <form id="addPayeeForm" class="border p-4 rounded shadow bg-gray-100">
        <h2 class="text-2xl font-bold mb-4">Add New Payee</h2>

        <input type="text" id="newPayeeName" name="payeeName" class="bg-white border rounded p-2 w-5/6" placeholder="New Payee Name">

        <button type="submit" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded">Add Payee</button>

        <div class="mt-4">
          <h3 class="text-lg font-semibold mb-2">Manage Payees</h3>
          <p id="payeeListEmpty" class="text-gray-500 text-sm">No payees yet.</p>
          <ul id="payeeList" class="list-none pl-0 space-y-2 text-sm"></ul>
        </div>
      </form>

      <!-- Chart & Totals -->
      <div class="border p-4 rounded shadow bg-gray-100 grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div id="ytdPieChartSection" class="bg-white p-4 rounded shadow">
          <h3 class="text-xl font-bold mb-4">YTD Chart</h3>
          <canvas id="ytdPieChart" height="320"></canvas>
          <div id="ytdLegend" class="mt-4 flex flex-wrap gap-3 text-sm text-gray-500"></div>
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
            <select id="yearSelect" class="border rounded px-3 py-2 bg-gray-50 w-full">
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

  <!-- Import Modal -->
  <div id="importModal" class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-96">
      <h2 class="text-xl font-bold mb-4">Import CSV</h2>
      <form id="importForm" class="space-y-4">
        <div>
          <label for="importFile" class="block text-sm font-medium">Select CSV File</label>
          <input type="file" id="importFile" name="file" accept=".csv,text/csv" class="border rounded px-3 py-2 w-full bg-gray-50">
        </div>

        <div class="flex justify-end space-x-2">
          <button type="button" id="importCancel" class="bg-red-300 px-4 py-2 rounded hover:bg-red-400">Cancel</button>
          <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Upload &amp; Import</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
