<?php
require '../../../connection/connect.php';
require '../../../models/user.php';
require '../../../utils/utility.php';


// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    sendJsonResponse(200, "Preflight request successful.");
}

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (!isset($data['name'], $data['email'], $data['phone'], $data['password'])) {
    sendJsonResponse(400, "Please provide all required fields: name, email, phone, and password.");
}

// Initialize User class
$userModel = new User($conn);
$response = $userModel->register($data);

if ($response["success"]) {
    sendJsonResponse(201, $response["message"], ["userId" => $response["userId"]]);
} else {
    sendJsonResponse(500, $response["error"]);
}

$conn->close();
?>
