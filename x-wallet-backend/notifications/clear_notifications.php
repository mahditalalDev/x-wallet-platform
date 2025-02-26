<?php
require '../db.php';

// Allow CORS for all domains
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Validate input
if (!isset($data['userId']) || empty($data['userId'])) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "Missing required field: userId"]);
    exit();
}

$userId = $data['userId'];

// Update notifications and set is_deleted = TRUE for the given user
$stmt = $conn->prepare("UPDATE notifications SET is_deleted = TRUE WHERE userId = ?");
$stmt->bind_param("i", $userId);

if ($stmt->execute()) {
    // Get affected rows count
    $affectedRows = $stmt->affected_rows;
    http_response_code(200); // OK
    echo json_encode([
        "message" => "All notifications marked as read successfully.",
        "affected_notifications" => $affectedRows
    ]);
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(["error" => "Failed to update notifications."]);
}

$stmt->close();
$conn->close();
?>
