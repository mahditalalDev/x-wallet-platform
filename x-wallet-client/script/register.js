const signup = document.getElementById("signup-form");
const reg_name = document.getElementById("reg-name");
const reg_username = document.getElementById("reg-userName");
const reg_email = document.getElementById("reg-email");
const reg_phone = document.getElementById("reg-phone");
const reg_password = document.getElementById("reg-password");

signup.addEventListener("submit", (event) => {
    event.preventDefault(); // Prevent auto refresh 

    // Log values correctly
    console.log({
        name: reg_name.value,
        username: reg_username.value,
        email: reg_email.value,
        phone: reg_phone.value,
        password: reg_password.value
    });
});
