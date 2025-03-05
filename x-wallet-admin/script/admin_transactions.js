document.addEventListener("DOMContentLoaded", function () {
  const transactionBody = document.getElementById("transaction-body");
  const modal = document.getElementById("modal");
  const overlay = document.getElementById("overlay");
  const closeModalBtn = document.getElementById("close-btn");
  const acceptBtn = document.getElementById("accept-btn");
  const rejectBtn = document.getElementById("reject-btn");

  let selectedTransaction = null;

  axios
    .get(
      "http://localhost/SEfactory/x-wallet-platform/x-wallet-backend2/admin/v1/transactions/read-transactions.php"
    )
    .then((response) => {
      const data = response.data;
      console.log(data);
      if (data.status === "success") {
        transactionBody.innerHTML = "";
        data.data.forEach((transaction) => {
          const row = document.createElement("tr");
          row.innerHTML = `
                          <td>${transaction.id}</td>
                          <td>${transaction.senderId}</td>
                          <td>${transaction.receiverId}</td>
                          <td>${transaction.type}</td>
                          <td>${parseFloat(transaction.amount).toFixed(
                            2
                          )} <span>${transaction.currency}</span></td>
                          <td><span class="status">${
                            transaction.status
                          }</span></td>
                          <td>${transaction.createdAt}</td>
                      `;

          row.addEventListener("click", function () {
            selectedTransaction = transaction;
            document.getElementById("modal-id").textContent = transaction.id;
            document.getElementById("modal-user").textContent =
              transaction.senderId;
            document.getElementById("modal-type").textContent =
              transaction.type;
            document.getElementById("modal-amount").textContent =
              transaction.amount + " " + transaction.currency;
            modal.style.display = "block";
            overlay.style.display = "block";
          });

          transactionBody.appendChild(row);
        });
      }
    });

  closeModalBtn.onclick = () => {
    modal.style.display = "none";
    overlay.style.display = "none";
  };
  acceptBtn.onclick = () => {
    console.log("Accepted:", selectedTransaction);
    updateStatus(selectedTransaction.id, "accepted");
    closeModalBtn.click();
  };
  rejectBtn.onclick = () => {
    console.log("Rejected:", selectedTransaction);
    closeModalBtn.click();
  };
});
function updateStatus(transactionId, status) {
    axios
     .post(
        `http://localhost/SEfactory/x-wallet-platform/x-wallet-backend2/admin/v1/transactions/transaction_action.php`
        ,{
            transactionId:transactionId,
            status:status
        },
        { headers: { "Content-Type": "application/json" } }
      )
     .then((response) => {
        console.log(response.data);
        location.reload();
      
      });
}
