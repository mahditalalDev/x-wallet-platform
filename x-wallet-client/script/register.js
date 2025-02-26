// import axios from "axios"; // Import Axios

const signup = document.getElementById("signup-form");
const reg_name = document.getElementById("reg-name");
const reg_username = document.getElementById("reg-userName");
const reg_email = document.getElementById("reg-email");
const reg_phone = document.getElementById("reg-phone");
const reg_password = document.getElementById("reg-password");

signup.addEventListener("submit", async (event) => {
    event.preventDefault(); // Prevent page reload

    const userData = {
        name: reg_name.value,
        username: reg_username.value || null,
        email: reg_email.value,
        phone: reg_phone.value,
        password: reg_password.value
    };

    console.log("Sending user data:", userData);

    try {
        const response = await axios.post("http://localhost/SEfactory/x-wallet-platform/x-wallet-backend/auth/register.php", userData, {
            headers: { "Content-Type": "application/json" }
        });

        console.log("Response:", response.data);
        
        if (response.data.message) {
            alert("Registration successful!");
            signup.reset(); // Clear form after successful registration
        } else {
            alert("Error: " + response.data.error);
        }
    } catch (error) {
        console.error("Registration failed:", error);
        alert("An error occurred. Please try again.");
    }
});
