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

// Read JSON payload
$data = json_decode(file_get_contents("php://input"), true);

// Validate input
if (!isset($data['userId'], $data['message']) || empty($data['userId']) || empty($data['message'])) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "Missing required fields: userId and message"]);
    exit();
}

$userId = $data['userId'];
$message = $data['message'];

// Insert into database
$stmt = $conn->prepare("INSERT INTO notifications (userId, message) VALUES (?, ?)");
$stmt->bind_param("is", $userId, $message);

if ($stmt->execute()) {
    // Get the newly inserted notification details
    $notificationId = $stmt->insert_id;

    $response = [
        "id" => $notificationId,
        "userId" => $userId,
        "message" => $message,
        "is_deleted" => false,
        "createdAt" => date("Y-m-d H:i:s") // Current timestamp
    ];
    
    http_response_code(201); // Created
    echo json_encode(["message" => "Notification created successfully", "notification" => $response]);
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(["error" => "Failed to create notification. Please try again later."]);
}

$stmt->close();
$conn->close();
?>
