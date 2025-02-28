// Function to fetch wallet data
async function fetchWalletData() {
    // Get user data from localStorage and parse it
    const userData = localStorage.getItem("user");
    if (!userData) {
        alert("User data not found in localStorage!");
        return;
    }

    const user = JSON.parse(userData); // Convert JSON string to object
    const userId = user.id; // Extract userId

    const currency = document.getElementById("currency-toggle").checked ? "USD" : "LBP"; // Detect currency

    try {
        const response = await axios.get(`http://localhost/SEfactory/x-wallet-platform/x-wallet-backend/wallet/get_wallet.php`, {
            params: { userId, currency }
        });

        if (response.data.status === "success" && response.data.wallets.length > 0) {
            const wallet = response.data.wallets[0]; // Get first wallet (modify if needed)
            document.getElementById("wallet-balance").innerHTML = `${wallet.balance} <span id="wallet-currency">${wallet.currency}</span>`;
            document.getElementById("wallet-id").innerText = `Wallet ID: ${wallet.id}`;
            console.log(wallet.id)
        } else {
            document.getElementById("wallet-balance").innerHTML = "No wallet found";
        }
    } catch (error) {
        console.error("Error fetching wallet:", error);
    }
}

// Event listener for toggle switch
document.getElementById("currency-toggle").addEventListener("change", fetchWalletData);

// Fetch data on page load
window.onload = fetchWalletData;

// Function to fetch transactions and update the table
async function fetchTransactions() {
    try {
        // Get user data from localStorage
        const userData = localStorage.getItem("user");
        if (!userData) {
            alert("User data not found in localStorage!");
            return;
        }

        const user = JSON.parse(userData); // Convert JSON string to object
        const userId = user.id; // Extract userId

        // Fetch transactions from API
        const response = await axios.get("http://localhost/SEfactory/x-wallet-platform/x-wallet-backend/transactions/getAllTransactionsUser.php", {
            params: { userId }
        });
        console.log(response)

        if (response.data.length === 0) {
            document.querySelector(".transactions-body").innerHTML =
                "<tr><td colspan='5' style='text-align:center;'>No transactions found</td></tr>";
            return;
        }

        // Get table body and clear previous data
        const transactionsBody = document.querySelector(".transactions-body");
        transactionsBody.innerHTML = "";

        // Loop through transactions and create table rows
        response.data.forEach((transaction) => {
            const transactionRow = `
            <tr class="buy">
                <td colspan="5">
                    <table class="item">
                        <tbody>
                            <tr>
                                <td class="type">${transaction.type.toUpperCase()}</td>
                                <td>${transaction.senderId}</td>
                                <td>${transaction.receiverId}</td>
                                <td>${transaction.amount} <span>${transaction.currency}</span></td>
                            </tr>
                            <tr>
                                <td colspan="5" class="time">${formatTimeAgo(transaction.createdAt)}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>`;

            transactionsBody.innerHTML += transactionRow;
        });
    } catch (error) {
        console.error("Error fetching transactions:", error);
    }
}

// Function to format time difference
function formatTimeAgo(datetime) {
    const timeAgo = new Date(datetime);
    const now = new Date();
    const diff = Math.floor((now - timeAgo) / 1000); // Difference in seconds

    if (diff < 60) return `${diff} seconds ago`;
    if (diff < 3600) return `${Math.floor(diff / 60)} minutes ago`;
    if (diff < 86400) return `${Math.floor(diff / 3600)} hours ago`;
    return `${Math.floor(diff / 86400)} days ago`;
}

// Fetch data on page load
// window.onload = fetchTransactions;
fetchTransactions() 

// 🔹 Event listener for toggle switch
document.getElementById("currency-toggle").addEventListener("change", fetchWalletData);

// 🔹 Fetch data on page load
window.onload = fetchWalletData;
const qr = new QRCode(document.getElementById("qrcode"), {
    text: "Hello World",
    width: 150,
    height: 128
});