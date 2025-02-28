<?php
require '../../../connection/connect.php';
require '../../../models/user.php';
require '../../../utils/utility.php';

// Allow CORS for all domains
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

// Check for missing fields
if (!isset($data['email'], $data['password'])) {
    sendJsonResponse(400, "Please provide both email/username and password.");
}

$userModel = new User($conn);
$response = $userModel->login($data['email'], $data['password']);

if ($response["success"]) {
    sendJsonResponse(200, "Login successful", ["user" => $response["user"]]);
} else {
    $statusCode = ($response["error"] === "User not found") ? 404 : 401;
    sendJsonResponse($statusCode, $response["error"]);
}

$conn->close();
?>
