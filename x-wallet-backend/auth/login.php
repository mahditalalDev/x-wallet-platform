<?php
require '../db.php'; 



// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

// Check for missing fields
if (!isset($data['email'], $data['password'])) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "Please provide both email/username and password."]);
    exit;
}

$emailOrUsername = $data['email']; 
$password = $data['password']; 

// Prepare the SQL query to check if the user exists
$stmt = $conn->prepare("SELECT id, name, username, email, phone, password, isAdmin FROM users WHERE email = ? OR username = ?");
$stmt->bind_param("ss", $emailOrUsername, $emailOrUsername);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // Verify the password
    if (password_verify($password, hash: $user['password'])) {
        unset($user['password']); // Remove the password before sending the response
        http_response_code(200); // OK
        echo json_encode(["message" => "Login successful", "user" => $user]);
    } else {
        http_response_code(401); // Unauthorized
        echo json_encode(["error" => "Invalid password"]);
    }
} else {
    http_response_code(404); // Not Found
    echo json_encode(["error" => "User not found"]);
}

$stmt->close();
$conn->close();
?>
