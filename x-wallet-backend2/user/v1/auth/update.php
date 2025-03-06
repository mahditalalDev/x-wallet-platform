<?php
require '../../../connection/connect.php';
require '../../../models/user.php';
require '../../../utils/utility.php';


// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit;
}

// Ensure userId is provided
$userId = isset($_POST["userId"]) ? intval($_POST["userId"]) : null;
if (!$userId) {
    echo json_encode(["status" => "error", "message" => "User ID is required"]);
    exit;
}

// Initialize the User model
$userModel = new User($conn);

// Get request data
$data = $_POST;
$files = $_FILES;

// Call updateUser function
$response = $userModel->updateUser($userId, $data, $files);

// Return response as JSON
echo json_encode($response);
?>
