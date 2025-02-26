const form = document.getElementById("login-form");
const emailInput = document.getElementById("email");
const passwordInput = document.getElementById("password");

function showError(input, message) {
  const errorDiv = input.nextElementSibling;
  errorDiv.textContent = message;
  errorDiv.style.color = "red";
}

function clearError(input) {
  const errorDiv = input.nextElementSibling;
  errorDiv.textContent = "";
}

form.addEventListener("submit", (event) => {
  event.preventDefault(); // Prevent auto refresh

  let isValid = true;

  // Email/Username validation
  if (emailInput.value.trim() === "") {
    showError(emailInput, "Email or username is required.");
    isValid = false;
  } else {
    clearError(emailInput);
  }

  // Password validation
  if (passwordInput.value.trim() === "") {
    showError(passwordInput, "Password is required.");
    isValid = false;
  } else {
    clearError(passwordInput);
  }

  if (isValid) {
    const loginData = {
      email: emailInput.value,  // email or username 
      password: passwordInput.value,
    };

    console.log("Sending login data:", loginData);

    axios.post("http://localhost/SEfactory/x-wallet-platform/x-wallet-backend/auth/login.php", loginData)
      .then(response => {
        console.log("Login successful", response.data);
        if (response.data.message === "Login successful") {
          // Save user data in localStorage
          localStorage.setItem('user', JSON.stringify(response.data.user));
          window.location.href = "./../home.html";  // Update with your home page URL
        }
      })
      .catch(error => {
        console.error("Login failed", error);
        alert("Login failed: " + error.response.data.error);
      });
  }
});
