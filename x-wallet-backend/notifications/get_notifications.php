<?php
require '../db.php'; 

// Allow CORS for all domains
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Validate if userId is provided as a GET parameter
if (!isset($_GET['userId']) || empty($_GET['userId'])) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "Missing required parameter: userId"]);
    exit();
}

$userId = $_GET['userId'];

// Fetch notifications for the user where is_deleted = FALSE
$stmt = $conn->prepare("SELECT id, userId, message, is_deleted, createdAt FROM notifications WHERE userId = ? AND is_deleted = FALSE ORDER BY createdAt DESC");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];

while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

$stmt->close();
$conn->close();

// Check if there are notifications
if (count($notifications) > 0) {
    http_response_code(200); // OK
    echo json_encode(["notifications" => $notifications]);
} else {
    http_response_code(404); // Not Found
    echo json_encode(["message" => "No notifications found for this user"]);
}
?>
