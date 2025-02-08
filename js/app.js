document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('billForm');
  const billList = document.getElementById('billList');

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
      loadBills();
      updateChart();
    }
  });

  // Load bills via AJAX
  async function loadBills() {
    const response = await fetch('/includes/api.php?action=getAll');
    const bills = await response.json();

    billList.innerHTML = bills.map(bill => `
      <tr class="border-t">
        <td class="px-4 py-2">${new Date(bill.billDate).toISOString().split('T')[0]}</td>
        <td class="px-4 py-2">${bill.billName}</td>
        <td class="px-4 py-2">$${parseFloat(bill.amount).toFixed(2)}</td>
        <td class="px-4 py-2">${bill.paymentId}</td>
      </tr>
    `).join('');
  }

  // Initialize chart
  let chart;
  async function updateChart() {
    const response = await fetch('/includes/api.php?action=getTotals');
    const data = await response.json();

    if (chart) chart.destroy();

    chart = new Chart(document.getElementById('chart'), {
      type: 'bar',
      data: {
        labels: [...new Set(data.map(item => item.year))],
        datasets: Object.groupBy(data, ({ billName }) => billName).map(([name, values]) => ({
          label: name,
          data: values.map(v => v.total)
        }))
      }
    });
  }

  updateChart();
});
