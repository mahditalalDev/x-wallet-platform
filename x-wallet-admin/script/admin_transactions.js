
        document.addEventListener("DOMContentLoaded", function () {
            const transactionBody = document.getElementById("transaction-body");

            axios.get("http://localhost/SEfactory/x-wallet-platform/x-wallet-backend2/admin/v1/transactions/read-transactions.php")
                .then(response => {
                    const data = response.data;

                    if (data.status === "success") {
                        const transactions = data.data;
                        console.log("Transactions fetched successfully:", transactions);
                        transactionBody.innerHTML = ""; // Clear loading text

                        transactions.forEach(transaction => {
                            const statusClass = transaction.status === "success" ? "success" :
                                                transaction.status === "failed" ? "failed" : "pending";

                            const row = `
                                <tr>
                                    <td>${transaction.id}</td>
                                    <td>${transaction.senderId}</td>
                                    <td>${transaction.receiverId}</td>
                                    <td>${transaction.type}</td>
                                    <td>${transaction.fees} %</td>
                                    <td>${parseFloat(transaction.amount).toFixed(2)}</td>
                                    <td><span class="status ${statusClass}">${transaction.status}</span></td>
                                    <td>${transaction.createdAt}</td>
                                </tr>
                            `;
                            transactionBody.innerHTML += row;
                        });

                    } else {
                        transactionBody.innerHTML = "<tr><td colspan='6'>No transactions found.</td></tr>";
                    }
                })
                .catch(error => {
                    console.error("Error fetching transactions:", error);
                    transactionBody.innerHTML = "<tr><td colspan='6'>Error loading data.</td></tr>";
                });
        });
    