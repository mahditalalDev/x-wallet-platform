<?php
require '../db.php';

// Allow CORS for all domains
header("Access-Control-Allow-Origin: *");

// Allow specific methods (POST, OPTIONS)
header("Access-Control-Allow-Methods: POST, OPTIONS");

// Allow certain headers to be sent (Content-Type, Authorization)
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Respond with status 200 for OPTIONS request
    header("HTTP/1.1 200 OK");
    exit();
}

// Read the JSON data sent from the frontend
$data = json_decode(file_get_contents("php://input"), true);

// Check for missing fields
if (!isset($data['name'], $data['email'], $data['phone'], $data['password'])) {
    // Missing required fields, return 400 Bad Request
    http_response_code(400);
    echo json_encode(["error" => "Please provide all required fields: name, email, phone, and password."]);
    exit;
}

// Get form data
$name = $data['name'];
$email = $data['email'];
$phone = $data['phone'];
$password = password_hash($data['password'], PASSWORD_BCRYPT); // Hash the password
$isAdmin = 0; // Default is not an admin

// Use email prefix for username if it's not set
$username = isset($data['username']) ? $data['username'] : explode('@', $email)[0];

// Prepare the SQL query to insert new user
$stmt = $conn->prepare("INSERT INTO users (name, username, email, phone, password, isAdmin) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssi", $name, $username, $email, $phone, $password, $isAdmin);

// Execute the query
if ($stmt->execute()) {
    // Registration successful, return 201 Created
    http_response_code(201);
    echo json_encode(["message" => "Registration successful. You can now log in."]);
} else {
    // Capture the MySQL error and return 500 Internal Server Error
    $error = $stmt->error;
    http_response_code(500);
    echo json_encode(["error" => "Something went wrong. Please try again later. If the issue persists, contact support."]);
}

// Close database connection
$stmt->close();
$conn->close();
?>
