<?php
require '../db.php';

// Allow CORS for all domains
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

// Read the JSON data sent from the frontend
$data = json_decode(file_get_contents("php://input"), true);

// Check for required fields
if (!isset($data['userId'], $data['tier'])) {
    http_response_code(400);
    echo json_encode(["error" => "Please provide userId and tier."]);
    exit;
}

$userId = intval($data['userId']);
$tier = strtolower(trim($data['tier'])); // Convert tier to lowercase for consistency

// Define allowed tiers
$allowedTiers = ['basic', 'standard', 'premium'];

if (!in_array($tier, $allowedTiers)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid tier. Allowed values: basic, standard, premium."]);
    exit;
}

// Prepare the SQL statement
$stmt = $conn->prepare("UPDATE users SET tier = ? WHERE id = ?");
$stmt->bind_param("si", $tier, $userId);

// Execute and check result
if ($stmt->execute() && $stmt->affected_rows > 0) {
    http_response_code(200);
    echo json_encode(["message" => "Tier updated successfully."]);
} else {
    http_response_code(400);
    echo json_encode(["error" => "Failed to update tier. User may not exist."]);
}

// Close connection
$stmt->close();
$conn->close();
?>
