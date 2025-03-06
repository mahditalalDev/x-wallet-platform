<?php
require '../../../connection/connect.php';
require '../../../models/user.php';
require '../../../utils/utility.php';


// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

// Check if userId is provided
if (!isset($_GET['userId'])) {
    sendJsonResponse(400, "User ID is required.");
    exit();
}

$userModel = new User($conn);
$response = $userModel->read($_GET['userId']);

if (!empty($response)) {
    sendJsonResponse(200, "User fetched successfully.", ["user" => $response]);
} else {
    sendJsonResponse(404, "User not found.");
}

$conn->close();
?>
