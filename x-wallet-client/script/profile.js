document.addEventListener("DOMContentLoaded", fetchUserData);

document.querySelector("form").addEventListener("submit", async function (event) {
  event.preventDefault();
  await updateUserData();
});

async function fetchUserData() {
  const apiUrl = "http://localhost/SEfactory/x-wallet-platform/x-wallet-backend2/user/v1/auth/get_user.php?userId=7";
  try {
    const response = await axios.get(apiUrl);
    const user = response.data.data.user.user;
    console.log(user)

    document.getElementById("name").value = user.name || "";
    document.getElementById("username").value = user.username || "";
    document.getElementById("email").value = user.email || "";
    document.getElementById("phone").value = user.phone || "";

    populateWallets(user.wallets);
    displayUploadedDocument(user.id_document);
  } catch (error) {
    console.error("Error fetching user data:", error);
    alert("Failed to load user data.");
  }
}

function populateWallets(wallets) {
  const walletSelect = document.getElementById("wallet-currency");
  const walletInput = document.getElementById("wallet");
  walletSelect.innerHTML = "";

  if (wallets && wallets.length > 0) {
    wallets.forEach(wallet => {
      const option = document.createElement("option");
      option.value = wallet.currency;
      option.textContent = wallet.currency;
      walletSelect.appendChild(option);
    });
    walletSelect.value = wallets[0].currency;
    walletInput.value = `Wallet ID: ${wallets[0].id}`;
  }

  walletSelect.addEventListener("change", function () {
    const selectedWallet = wallets.find(w => w.currency === this.value);
    if (selectedWallet) {
      walletInput.value = `Wallet ID: ${selectedWallet.id}`;
    }
  });
}
function extractFileName(url) {
    return url.split('/').pop();
  }

function displayUploadedDocument(filePath) {
    const newFilePath = "http://localhost/SEfactory/x-wallet-platform/x-wallet-backend2/uploads/"+extractFileName(filePath)
    console.log(newFilePath)
//   const filePreview = document.getElementById("file-preview");
//   if (filePath) {
//     filePreview.innerHTML = `<a href="${filePath}" target="_blank">View Uploaded Document</a>`;
//   } else {
//     filePreview.textContent = "No file uploaded";
//   }
}

async function updateUserData() {
  const formData = new FormData();
  formData.append("userId", 7);
  formData.append("name", document.getElementById("name").value);
  formData.append("username", document.getElementById("username").value);
  formData.append("email", document.getElementById("email").value);
  formData.append("phone", document.getElementById("phone").value);

  const password = document.getElementById("password").value;
  const confirmPassword = document.getElementById("confirm-password").value;

  if (password) {
    if (password !== confirmPassword) {
      alert("Passwords do not match!");
      return;
    }
    formData.append("password", password);
  }

  const fileInput = document.getElementById("id-document");
  if (fileInput.files.length > 0) {
    formData.append("id_document", fileInput.files[0]);
  }

  try {
    const response = await axios.post(
      "http://localhost/SEfactory/x-wallet-platform/x-wallet-backend2/user/v1/auth/update.php",
      formData,
      { headers: { "Content-Type": "multipart/form-data" } }
    );
    alert(response.data.message);
    fetchUserData(); // Refresh user data
  } catch (error) {
    console.error("Error updating profile:", error);
    alert("Failed to update profile.");
  }
}