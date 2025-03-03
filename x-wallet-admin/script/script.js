const baseURL =
  "http://localhost/SEfactory/x-wallet-platform/x-wallet-backend2/admin/v1/login.php";

document
  .getElementById("login-form")
  .addEventListener("submit", async (event) => {
    event.preventDefault(); // Prevent page reload

    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;
    console.log("Login attempt:", email, password);
    await login(email, password);
  });
async function login(email, password) {
  const response = await axios.post(`${baseURL}`, {
    email,
    password,
  });
  console.log("email:", email);
  console.log("Password:", password);

  if (response.data.status === "success") {
    localStorage.setItem("admin", response.data);
    window.location.href = "./dashboard.html";
  } else {
    alert("Invalid credentials");
  }
}
