const form = document.getElementById("login-form");
const signup = document.getElementById("signup-form");
const emailInput = document.getElementById("email");
const passwordInput = document.getElementById("password");
const reg_name = document.getElementById("reg-name");
const reg_username = document.getElementById("reg-userName");
const reg_email = document.getElementById("reg-email");
const reg_phone = document.getElementById("reg-phone");
const reg_password = document.getElementById("reg-password");


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
signup.addEventListener("submit",(event)=>{
    event.preventDefault(); //preveent auto refresh 
    console.log(reg_email,reg_password,reg_name,reg_phone,reg_username)
 
})
