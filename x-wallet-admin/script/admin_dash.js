document.addEventListener("DOMContentLoaded", function () {
  const ctx1 = document.getElementById("myChart").getContext("2d");
  const ctx2 = document.getElementById("typeChart").getContext("2d");
  const transactionCountElement = document.getElementById("transaction-count");
  


  // Fetch transactions using Axios
  axios
    .get(
      "http://localhost/SEfactory/x-wallet-platform/x-wallet-backend2/admin/v1/transactions/read-transactions.php"
    )
    .then((response) => {
      const data = response.data;

      if (data.status === "success") {
        const transactions = data.data;

        // Update transaction count
        transactionCountElement.textContent = transactions.length;

        // Process transactions: Group by date
        const transactionsPerDay = {};
        const typeTotals = { p2p: 0, QR_pay: 0, withdraw: 0 };

        transactions.forEach((transaction) => {
          const date = transaction.createdAt.split(" ")[0]; // Extract YYYY-MM-DD
          transactionsPerDay[date] = (transactionsPerDay[date] || 0) + 1;

          // Aggregate amounts based on transaction type
          if (typeTotals.hasOwnProperty(transaction.type)) {
            typeTotals[transaction.type] += parseFloat(transaction.amount);
          }
        });

        // Prepare data for transaction count chart
        const labels1 = Object.keys(transactionsPerDay).sort();
        const values1 = Object.values(transactionsPerDay);
        const colors1 = labels1.map(
          () => `hsl(${Math.random() * 360}, 40%, 50%)`
        );

        // Prepare data for transaction type amount chart
        const labels2 = Object.keys(typeTotals);
        const values2 = Object.values(typeTotals);
        const colors2 = ["#FF5733", "#33FF57", "#3357FF"];

        // Create transactions per day chart
        new Chart(ctx1, {
          type: "bar",
          data: {
            labels: labels1,
            datasets: [
              {
                label: "Transactions Per Day",
                data: values1,
                backgroundColor: colors1,
                borderWidth: 1,
              },
            ],
          },
          options: {
            responsive: true,
            scales: {
              y: { beginAtZero: true },
            },
          },
        });

        // Create transaction type amount chart
        new Chart(ctx2, {
          type: "pie",
          data: {
            labels: labels2,
            datasets: [
              {
                label: "Total Amount by Transaction Type",
                data: values2,
                backgroundColor: colors2,
                borderWidth: 1,
              },
            ],
          },
          options: {
            responsive: true,
          },
        });
      } else {
        console.error("No transactions found.");
      }
    })
    .catch((error) => console.error("Error fetching transactions:", error));
});
