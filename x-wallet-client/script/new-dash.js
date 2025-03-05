// Global Variables
const menuBtn = document.getElementById("menu-btn");
const navLinks = document.getElementById("nav-links");
const p2pButton = document.getElementById("p2p");
const p2pSendButton = document.getElementById("p2p_send_button123");
const p2pFee = document.getElementById("p2p-fee");
const withdrawFee = document.getElementById("Withdraw-fee");
const qrFee = document.getElementById("QR_fee");
const userData = getUserLocalStorage();
const walletSelect = document.getElementById("walletSelect");
const balanceElement = document.getElementById("balance-acc");
const transactionsContainer = document.querySelector(".transactions");
const balanceTypePopup = document.getElementById("wallet-currency-popup");
const feesPopup = document.getElementById("fees_popup");
const logout_Btn = document.getElementById("logout-btn");
logout_Btn.addEventListener("click", () => {
  window.localStorage.removeItem("userData");
  window.location.href = "../../authentication/login.html";
});

let currentCurrency = "USD"; // Default currency
let popUp_balance_money_USD = 0.0;
let popUp_balance_money_LBP = 0.0;

// Event Listeners
menuBtn.addEventListener("click", () => navLinks.classList.toggle("active"));
p2pSendButton.addEventListener("click", () => console.log("hello"));
balanceTypePopup.addEventListener("change", (e) => console.log(e.target.value));

// Initialize QR Code
const qr = new QRCode(document.getElementById("qrcode"), {
  text: "Hello World",
  width: 150,
  height: 128,
});

// Fetch wallet data
async function fetchWallet() {
  try {
    const response = await axios.get(
      `http://localhost/SEfactory/x-wallet-platform/x-wallet-backend/wallet/get_wallet.php?userId=${userData.id}`
    );
    const wallets = response.data.wallets;
    console.log("qqqqqqqqqqqqqqqqqqqqqqq", wallets);
    localStorage.setItem("WalletBalance", JSON.stringify(wallets));

    console.log("Fetched wallets:", wallets);
    populateWalletDropdown(wallets);
    if (wallets.length > 0) {
      currentCurrency = wallets[0].currency;
      updateBalance(wallets[0].balance);
    }
  } catch (error) {
    console.error("Error fetching wallet data:", error);
  }
}

// Populate wallet dropdown
function populateWalletDropdown(wallets) {
  walletSelect.innerHTML = '<option value="">Select a wallet...</option>';
  wallets.forEach((wallet) => {
    const option = document.createElement("option");
    option.value = wallet.currency;
    option.textContent = `${wallet.currency}: ${wallet.balance}`;
    option.dataset.balance = wallet.balance;
    walletSelect.appendChild(option);
  });
  walletSelect.addEventListener("change", updateSelectedWallet);
}

// Update selected wallet balance
function updateSelectedWallet(event) {
  currentCurrency = event.target.value;
  const selectedOption = event.target.options[event.target.selectedIndex];
  updateBalance(selectedOption.dataset.balance);
}

// Update balance display
function updateBalance(balance) {
  balanceElement.innerHTML = balance
    ? ` ${balance} <span>${currentCurrency}</span>`
    : "No wallet selected or invalid selection";
}

// Fetch user transactions
function getUserTransactions() {
  axios
    .get(
      `http://localhost/SEfactory/x-wallet-platform/x-wallet-backend2/user/v1/transactions/view_transactions.php?userId=${userData.id}`
    )
    .then((response) => {
      if (response.data && response.data.data) {
        populateTransactions(response.data.data);
      }
    })
    .catch((error) => console.error("Error fetching transactions:", error));
}

// Populate transactions
function populateTransactions(transactions) {
  transactionsContainer.innerHTML += `
    <div class="transactions-table">
        <p class="title-text flex-flex_grow">Type & Date</p>
        <p class="title-text flex-flex_grow">From</p>
        <p class="title-text flex-flex_grow">To</p>
        <p class="title-text flex-flex_grow">Amount</p>
        <p class="title-text flex-flex_grow">Fees</p>
    </div>
  `;

  transactions.forEach((transaction) => {
    const transactionElement = document.createElement("div");
    transactionElement.classList.add("transaction");
    transactionElement.innerHTML = `
        <p class="title-text flex-col flex-flex_grow">${transaction.type} <br> <span class="trans_time"> ${transaction.createdAt}</span></p>
        <p class="title-text  flex-flex_grow">${transaction.senderId}</p>
        <p class="title-text flex-flex_grow">${transaction.receiverId}</p>
        <p class="title-text flex-flex_grow">${transaction.amount} ${transaction.currency}</p>
        <p class="title-text flex-flex_grow">${transaction.fees}</p>
        
    `;
    transactionsContainer.appendChild(transactionElement);
  });
}

// Update transaction status
function updateTransactionStatus(transactionId, status) {
  axios
    .post(
      "http://localhost/SEfactory/x-wallet-platform/x-wallet-backend2/user/v1/transactions/view_transactions.php?userId=7",
      { id: transactionId, status: status }
    )
    .then((response) => {
      console.log("Transaction updated:", response.data);
      location.reload();
    })
    .catch((error) => console.error("Error updating transaction:", error));
}

// Handle P2P send button click
function p2pSendButtonClicked() {
  console.log("clicked");
  document.getElementById("popup-window").style.display = "block";
  fillInitialPopupData();
}
const walletDropdown = document.getElementById("wallet-currency-popup");

walletDropdown.addEventListener("change", fillInitialPopupData);

// Fill initial popup data
async function fillInitialPopupData() {
  const storedFeesData = JSON.parse(localStorage.getItem("feesData"));
  feesPopup.value = storedFeesData.p2p_fees;
  const selectedCurrency = document.getElementById(
    "wallet-currency-popup"
  ).value;
  const userId = userData.id;
  getUserBalancePerCurrency(userId, selectedCurrency);
}
async function getUserBalancePerCurrency(userId, currency = "USD") {
  try {
    const response = await axios.get(
      `http://localhost/SEfactory/x-wallet-platform/x-wallet-backend/wallet/get_wallet.php?userId=${userId}&currency=${currency}`
    );
    const popUp_balance = document.getElementById("balance-popup");
    popUp_balance.value = `${response.data.wallets[0].balance}`;
    localStorage.setItem(
      "My-wallet",
      JSON.stringify({
        balance: response.data.wallets[0].balance,
        id: response.data.wallets[0].id,
      })
    );

    return response.data.wallets[0].balance;
  } catch (error) {
    console.log("Error in getUserBalancePerCurrency:", error);
    return 0.0; // Default balance in case of error
  }
}

async function fillInitialPopupData() {
  const storedFeesData = JSON.parse(localStorage.getItem("feesData"));
  feesPopup.value = storedFeesData.p2p_fees;

  const selectedCurrency = document.getElementById(
    "wallet-currency-popup"
  ).value;
  const userId = userData.id;

  const balance_money = await getUserBalancePerCurrency(
    userId,
    selectedCurrency
  );
  console.log("heeeeellloooo", balance_money);
}

function getUserFees() {
  // Fetch user fees
  axios
    .get(
      `http://localhost/SEfactory/x-wallet-platform/x-wallet-backend2/user/v1/fees/fees_api.php?userId=${userData.id}`
    )
    .then((response) => {
      console.log("response", response.data);
      p2pFee.innerText = response.data.p2p_fees + "%";
      withdrawFee.innerText = response.data.withdrawls + "%";
      qrFee.innerText = response.data.QR_pay + "%";
      localStorage.setItem("feesData", JSON.stringify(response.data));
    })
    .catch((error) => console.error("Login failed", error));
}
document
  .getElementById("popup-form")
  .addEventListener("submit", function (event) {
    event.preventDefault(); // Prevent default form submission

    const currency = document.getElementById("wallet-currency-popup").value;
    const balance =
      parseFloat(document.getElementById("balance-popup").value) || 0;
    const receiverId = document.getElementById("receiver").value.trim();
    const amount = parseFloat(document.getElementById("amount").value) || 0;
    const fees = parseFloat(document.getElementById("fees_popup").value) || 0;
    const type = "P2P";

    console.log(currency, balance, receiverId, amount, fees, type);

    if (!receiverId || amount <= 0) {
      alert("Please fill in all required fields with valid values.");
      return;
    }

    // Check if the total amount (amount + fees) is within the available balance
    const totalAmount = amount + fees;
    if (totalAmount > balance) {
      alert(
        "Insufficient balance. The total amount (including fees) cannot exceed your available balance."
      );
      return;
    }

    // If everything is valid, proceed with sending data
    sendData(currency, receiverId, amount, fees, type);
  });

function sendData(currency, receiverId, amount, fees, type) {
  const storedUserWalletData = JSON.parse(localStorage.getItem("My-wallet"));

  const userData = JSON.parse(localStorage.getItem("user"));
  console.log(storedUserWalletData);

  const data = {
    senderId: userData.id,
    receiverId: parseInt(receiverId),
    wallet_id: storedUserWalletData.id,
    amount: amount,
    currency: currency,
    type: type,
    fees: fees,
  };
  console.log(data);

  try {
    axios
      .post(
        "http://localhost/SEfactory/x-wallet-platform/x-wallet-backend2/user/v1/transactions/new_transaction.php",
        data,
        {
          headers: {
            "Content-Type": "application/json",
          },
        }
      )
      .then((response) => {
        document.getElementById("popup-window").style.display = "none";
        // fetchWallet()
        location.reload();
      });
  } catch (error) {}
}

fetchWallet();
getUserTransactions();
getUserFees();
