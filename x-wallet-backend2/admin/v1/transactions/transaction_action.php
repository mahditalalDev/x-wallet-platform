<?php

require_once '../../../connection/connect.php';
require_once '../../../models/Transaction.php';
$transaction = new Transaction($conn);
// Allow CORS for all domains
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "error" => "Invalid request method"]);
    exit;
}

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['transactionId']) || !isset($data['status'])) {
    echo json_encode(["success" => false, "error" => "Missing required fields"]);
    exit;
}

$transactionId = intval($data['transactionId']);
$status = strtolower(trim($data['status']));

if (!in_array($status, ['accepted', 'rejected'])) {
    echo json_encode(["success" => false, "error" => "Invalid status value"]);
    exit;
}

// Call the updateStatus function
$response = $transaction->updateStatus($transactionId, $status);

// Return JSON response
echo json_encode($response);
?>
