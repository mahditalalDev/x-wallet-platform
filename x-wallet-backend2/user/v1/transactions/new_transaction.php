<?php
require '../../../connection/connect.php';
require '../../../models/transaction.php';
require '../../../utils/utility.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (!isset($data['senderId'], $data['receiverId'], $data['wallet_id'], $data['amount'], $data['currency'], $data['type'], $data['fees'])) {
    sendJsonResponse(400, "Missing required fields.");
}

// Extract variables
$senderId = $data['senderId'];
$receiverId = $data['receiverId'];
$walletId = $data['wallet_id'];
$amount = floatval($data['amount']);
$currency = $data['currency'];
$type = $data['type'];
$fees = floatval($data['fees']);

// Process transaction using Transaction class
$transactionModel = new Transaction($conn);
$response = $transactionModel->processTransaction($senderId, $receiverId, $walletId, $amount, $currency, $type, $fees);

if ($response["success"]) {
    sendJsonResponse(201, $response["message"]);
} else {
    sendJsonResponse(400, $response["error"]);
}

$conn->close();
?>
