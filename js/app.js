document.addEventListener('DOMContentLoaded', () => {
  const yearSelect = document.getElementById('yearSelect');
  const sortSelect = document.getElementById('sortSelect');
  const billList = document.getElementById('billList');
  const form = document.getElementById('billForm');
  const currentYear = new Date().getFullYear();
  let ytdPieChart; // To hold the chart instance

  // Populate the year dropdown
  async function loadYears() {
    const response = await fetch('/includes/api.php?action=getYears');
    const years = await response.json();

    yearSelect.innerHTML = ''; // Clear existing options
    years.forEach(year => {
      const option = document.createElement('option');
      option.value = year;
      option.textContent = year;
      yearSelect.appendChild(option);
    });

    // Set default year to the most recent one
    if (years.length > 0) {
      yearSelect.value = years[0];
      loadBills(years[0], sortSelect.value); // Load bills for the most recent year with the default sort order
    } else {
      billList.innerHTML = '<p class="text-gray-500">No data available.</p>';
    }
  }

  // Fetch and display payees
  async function loadPayees(selectId) {
    const response = await fetch('/includes/api.php?action=getPayees');
    const payees = await response.json();

    const payeeSelect = document.getElementById(selectId || 'payeeId');

    payeeSelect.innerHTML = ''; // Clear existing options

    const defaultOption = document.createElement('option');
    defaultOption.value = '';
    defaultOption.textContent = 'Select Payee';
    defaultOption.disabled = true;
    defaultOption.selected = true;
    payeeSelect.appendChild(defaultOption);

    payees.forEach(payee => {
      const option = document.createElement('option');
      option.value = payee.id; // Use payee ID as the value
      option.textContent = payee.name; // Display payee name
      payeeSelect.appendChild(option);
    });
  }

  // Fetch and display bills
  async function loadBills(year, sort) {
    const response = await fetch(`/includes/api.php?action=getByYear&year=${year}&sort=${sort}`);
    const data = await response.json();

    billList.innerHTML = ''; // Clear current bills
    if (data.length === 0) {
      billList.innerHTML = '<p class="text-gray-500">No bills found for this year.</p>';
      return;
    }

    data.forEach(bill => {
      const billItem = document.createElement('div');
      billItem.className = 'mb-2 p-4 border rounded bg-gray-100 shadow-sm relative';

      // Parse the date string as a local date
      const dateParts = bill.billDate.split('-'); // Split "YYYY-MM-DD"
      const localDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]); // Month is 0-indexed

      const formattedDate = new Intl.DateTimeFormat('en-US', {
        year:  'numeric',
        month: 'short',
        day:   'numeric',
      }).format(localDate);

      billItem.innerHTML = `
      <p><strong>Date:</strong> ${formattedDate}</p>
      <p><strong>Bill Name:</strong> ${bill.payeeName}</p>
      <p><strong>Amount:</strong> $${bill.amount.toFixed(2)}</p>
      <p><strong>Payment ID:</strong> ${bill.paymentId || 'N/A'}</p>
      <p><strong>Comment:</strong> ${bill.comment || 'N/A'}</p>
      <button class="block absolute top-2 right-2 px-2 py-0 pb-1 rounded bg-green-500 text-white hover:bg-green-600" data-id="${bill.id}">Edit</button>
      `;

      billList.appendChild(billItem);
    });
  }

  // Edit a bill
  async function editBill(id) {
    // Find the specific bill data by ID
    const response = await fetch(`/includes/api.php?action=getById&id=${id}`);
    const bill = await response.json();

    if (!bill) {
      alert("Error: Could not retrieve the bill data.");
      return;
    }

    // Populate modal fields with the bill data
    document.getElementById('editFormId').value        = bill.id;
    document.getElementById('editFormDate').value      = bill.billDate;
    document.getElementById('editFormAmount').value    = bill.amount;
    document.getElementById('editFormPaymentId').value = bill.paymentId;
    document.getElementById('editFormComment').value   = bill.comment || '';

    // Populate the payee dropdown and select the correct option
    await loadPayees('editFormPayeeId'); // Ensure the payee dropdown is populated

    const payeeDropdown = document.getElementById('editFormPayeeId');
    payeeDropdown.value = bill.payeeId; // Use payeeId instead of billName

    // Open the modal
    document.getElementById('editModal').classList.add('flex');
    document.getElementById('editModal').classList.remove('hidden');
  }

  async function loadYtdAmounts(year) {
    const response = await fetch(`/includes/api.php?action=getYtdAmounts&year=${year}`);
    const data = await response.json();

    // Display overall YTD amount
    const ytdOverallAmount = document.getElementById('ytdOverallAmount');
    ytdOverallAmount.textContent = `$${(data.overallAmount || 0).toFixed(2)}`;

    // Display YTD amounts by payee
    const ytdPayeeList = document.getElementById('ytdPayeeList');
    ytdPayeeList.innerHTML = ''; // Clear existing items

    data.payeeAmounts.forEach(payee => {
      const listItem = document.createElement('li');
      const strongElement = document.createElement('strong');
      strongElement.textContent = `${payee.payeeName}:`;
      listItem.appendChild(strongElement);
      listItem.appendChild(document.createTextNode(` $${payee.totalAmount.toFixed(2)}`));
      ytdPayeeList.appendChild(listItem);
    });
  }

  async function renderYtdPieChart(year) {
    const response = await fetch(`/includes/api.php?action=getYtdAmounts&year=${year}`);
    const data = await response.json();

    // Extract payee amounts from the response
    const payeeAmounts = data.payeeAmounts || [];
    const overallTotal = data.overallAmount || 0;

    if (overallTotal === 0) {
      console.warn('Overall total is zero. No data available for the chart.');
      return;
    }

    // Extract labels (payee names) and data (YTD totals)
    const labels = payeeAmounts.map(item => item.payeeName);
    const totals = payeeAmounts.map(item => item.totalAmount);

    // Get the chart canvas
    const ctx = document.getElementById('ytdPieChart').getContext('2d');

    // Destroy the existing chart if it exists
    if (ytdPieChart) {
      ytdPieChart.destroy();
    }

    // Create a new pie chart
    ytdPieChart = new Chart(ctx, {
      type: 'pie',
      data: {
      labels: labels,
      datasets: [{
        data: totals,
        backgroundColor: [
        '#003962', '#00607F', '#008B9C', '#00B8B9', '#00E6D6', '#00FFF4', '#007ACC' // New color added
        ], // Add more colors if needed
        hoverBackgroundColor: [
        '#042c4c', '#004968', '#007384', '#009FA1', '#00CCBD', '#00FBDA', '#005BB5' // New hover color added
        ]
      }]
      },
      options: {
      responsive: true,
      plugins: {
        legend: {
        position: 'bottom'
        },
        tooltip: {
        callbacks: {
          label: function (tooltipItem) {
          const value = tooltipItem.raw; // Raw data for this item
          const percentage = ((value / overallTotal) * 100).toFixed(2); // Calculate percentage
          return ` $${value.toFixed(2)} (${percentage}%)`;
          }
        }
        }
      }
      }
    });
  }


  // Add event listener for year selection
  yearSelect.addEventListener('change', (e) => {
    const selectedYear = e.target.value;
    loadYtdAmounts(selectedYear);              // Update YTD amounts when the year changes
    renderYtdPieChart(selectedYear);           // Update the pie chart when the year changes
    loadBills(selectedYear, sortSelect.value); // Use the selected sort order
  });

  // Event listener for sort selection
  sortSelect.addEventListener('change', (e) => {
    const selectedSort = e.target.value;
    loadBills(yearSelect.value, selectedSort); // Use the selected year
  });

  // Add new bill
  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(form);

    const response = await fetch('/includes/api.php?action=add', {
      method: 'POST',
      body: formData
    });

    if (response.ok) {
      form.reset();
      loadYears(); // Reload years in case a new year was added
      loadYtdAmounts(yearSelect.value); // Reload YTD amounts
    } else {
      alert('Failed to add bill. Please try again.');
    }
  });

  // Add new payee
  document.getElementById('addPayeeForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(e.target);
    const response = await fetch('/includes/api.php?action=addPayee', {
      method: 'POST',
      body: formData
    });

    if (response.ok) {
      loadPayees('payeeId'); // Reload payee dropdown
      e.target.reset(); // Clear the form
    } else {
      alert('Failed to add payee. Please try again.');
    }
  });

  // Event listener for edit buttons within bill items
  billList.addEventListener('click', (e) => {
    if (e.target.tagName === 'BUTTON' && e.target.textContent === 'Edit') {
      const billId = e.target.getAttribute('data-id');
      editBill(billId);
    }
  });

  // Save edited bill
  document.getElementById('editForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(e.target);

    const response = await fetch('/includes/api.php?action=edit', {
      method: 'POST',
      body: formData
    });

    if (response.ok) {
      loadBills(yearSelect.value, sortSelect.value); // Reload bills after editing
      document.getElementById('editModal').classList.add('hidden'); // Close the modal
    } else {
      alert('Failed to edit bill. Please try again.');
    }
  });

  // Event listener for close button in the modal
  document.getElementById('editFormCancel').addEventListener('click', () => {
    // Clear modal fields
    document.getElementById('editForm').reset();

    // Close the modal
    document.getElementById('editModal').classList.add('hidden');
    document.getElementById('editModal').classList.remove('flex');
  });

  // Dark mode toggle
  const darkModeToggle = document.getElementById('darkModeToggle');
  const htmlElement    = document.documentElement;
  const sunIcon        = document.getElementById('sunIcon');
  const moonIcon       = document.getElementById('moonIcon');

  // Function to update icons based on the current theme
  function updateIcons() {
    const isDarkMode = htmlElement.classList.contains('dark');
    if (isDarkMode) {
      sunIcon.classList.remove('hidden');
      moonIcon.classList.add('hidden');
    } else {
      sunIcon.classList.add('hidden');
      moonIcon.classList.remove('hidden');
    }
  }

  // Check and apply the saved preference
  const isDarkMode = localStorage.getItem('theme') === 'dark';
  if (isDarkMode) {
    htmlElement.classList.add('dark');
  }

  updateIcons();

  // Add toggle functionality
  darkModeToggle.addEventListener('click', () => {
    if (htmlElement.classList.contains('dark')) {
      htmlElement.classList.remove('dark');
      localStorage.setItem('theme', 'light');
    } else {
      htmlElement.classList.add('dark');
      localStorage.setItem('theme', 'dark');
    }

    updateIcons();
  });

  // On page load
  loadPayees('payeeId');          // Load payees
  loadYears();                    // Load years
  loadYtdAmounts(currentYear);    // Load YTD amounts for the current year
  renderYtdPieChart(currentYear); // Render the pie chart for the current year
});
