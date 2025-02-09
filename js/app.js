document.addEventListener('DOMContentLoaded', () => {
  const yearSelect = document.getElementById('yearSelect');
  const sortSelect = document.getElementById('sortSelect');
  const billList = document.getElementById('billList');
  const form = document.getElementById('billForm');

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
  async function loadPayees() {
    const response = await fetch('/includes/api.php?action=getPayees');
    const payees = await response.json();

    const payeeSelect = document.getElementById('editFormBillName');
    payeeSelect.innerHTML = ''; // Clear existing options

    // Add default "Select Payee" option
    const defaultOption = document.createElement('option');
    defaultOption.value = '';
    defaultOption.textContent = 'Select Payee';
    defaultOption.disabled = true;
    payeeSelect.appendChild(defaultOption);

    // Populate payees
    payees.forEach(payee => {
      const option = document.createElement('option');
      option.value = payee;
      option.textContent = payee;
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
      <p><strong>Bill Name:</strong> ${bill.billName}</p>
      <p><strong>Amount:</strong> $${bill.amount.toFixed(2)}</p>
      <p><strong>Payment ID:</strong> ${bill.paymentId || 'N/A'}</p>
      <p><strong>Comment:</strong> ${bill.comment || 'N/A'}</p>
      <button class="block absolute top-2 right-2 border text-gray-500 hover:text-gray-700 px-2 py-0 rounded" data-id="${bill.id}">Edit</button>
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
    await loadPayees(); // Ensure the payee dropdown is populated
    document.getElementById('editFormBillName').value  = bill.billName;

    // Open the modal
    document.getElementById('editModal').classList.add('flex');
    document.getElementById('editModal').classList.remove('hidden');
  }

  // Add event listener for year selection
  yearSelect.addEventListener('change', (e) => {
    const selectedYear = e.target.value;
    loadBills(selectedYear, sortSelect.value); // Use the selected sort order
  });

  // Event listener for sort selection
  sortSelect.addEventListener('change', (e) => {
    const selectedSort = e.target.value;
    loadBills(yearSelect.value, selectedSort); // Use the selected year
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
      loadPayees(); // Reload payee dropdown
      e.target.reset(); // Clear the form
    } else {
      alert('Failed to add payee. Please try again.');
    }
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
    } else {
      alert('Failed to add bill. Please try again.');
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

  // On page load
  loadYears();
  loadPayees();
});
