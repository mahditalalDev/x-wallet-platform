<?php
require '../../../connection/connect.php';
require '../../../models/user.php';
require '../../../utils/utility.php';

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

// Read input data
$data = json_decode(file_get_contents("php://input"), true);

// Check if userId is provided
if (!isset($_GET['userId'])) {
    sendJsonResponse(400, "User ID is required.");
    exit();
}

$userModel = new User($conn);

$response = $userModel->read($_GET['userId']);

// Check if user exists
if (!empty($response)) {
    sendJsonResponse(200, "User fetched successfully.", ["user" => $response]);
} else {
    sendJsonResponse(404, "User not found.");
}

$conn->close();
?>
