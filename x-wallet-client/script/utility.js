function getUserLocalStorage() {
    // Retrieve the user data from localStorage
    const userData = localStorage.getItem('user');

    // Parse the user data to an object
    if (userData) {
        const user = JSON.parse(userData);
        
        // Extract the required fields
        const { id, name, username, email, phone, isAdmin } = user;

        // Return the extracted fields as an object
        return { id, name, username, email, phone, isAdmin };
    } else {
        console.error('No user data found in localStorage.');
        return null;
    }
}

// // Example usage
// const user = getUserData();
// if (user) {
//     console.log(user);
// }