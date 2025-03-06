<?php
require '../../../connection/connect.php';
require '../../../models/user.php';
require '../../../utils/utility.php';


// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

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
