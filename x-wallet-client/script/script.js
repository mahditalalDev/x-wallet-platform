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
  event.preventDefault(); //preveent auto refresh 
  let isValid = true;

//   email validation todo:update the validation 
  if (emailInput.value.trim() === "") {
    showError(emailInput, "Email or username is required.");
    isValid = false;
  } else {
    clearError(emailInput);
  }
// password validation todo:update validation procces for password or maybe hashing
  if (passwordInput.value.trim() === "") {
    showError(passwordInput, "Password is required.");
    isValid = false;
  } else {
    clearError(passwordInput);
  }

  if (isValid) {
    console.log("Email/Username:", emailInput.value);
    console.log("Password:", passwordInput.value);
    // todo: login api ,if success redirect to home page with logs some caches
    // if not show the error message
    // form.submit();
  }
});
