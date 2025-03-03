<?php
require '../../connection/connect.php';
require '../../models/admin.php'; // Include Admin model
require '../../utils/utility.php';

// Allow CORS for all domains
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

// Read the JSON body from the request
$data = json_decode(file_get_contents("php://input"), true);
// echo $data;

// Check if required fields exist
if (!isset($data['email']) || !isset($data['password'])) {
    http_response_code(400); // Bad request
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit;
}

$email = trim($data['email']);
$password = trim($data['password']);

// Initialize database connection
$admin = new Admin($conn); // Use $conn instead of $db (from connect.php)

$response = $admin->login($email, $password);

// Set HTTP status code based on response
http_response_code($response["status"] === "success" ? 200 : 401);
echo json_encode($response);
