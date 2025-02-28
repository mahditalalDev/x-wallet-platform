<?php
require '../../../connection/connect.php';
require '../../../models/wallet.php';
require '../../../utils/utility.php';

// Allow CORS for all domains
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Retrieve JSON payload
$data = json_decode(file_get_contents("php://input"), true);

// Check for missing 'id' in the request
if (!isset($data['id'])) {
    sendJsonResponse(400, "Please provide the wallet id.");
}

// Initialize Wallet model
$walletModel = new Wallet($conn);

// Call the update method from the Wallet model with the provided fields
$isUpdated = $walletModel->update(
    $data['id'], 
    isset($data['limits']) ? $data['limits'] : null, 
    isset($data['currency']) ? $data['currency'] : null,
    isset($data['balance']) ? $data['balance'] : null
);

// Check if the update was successful
if ($isUpdated) {
    sendJsonResponse(200, "Wallet updated successfully.");
} else {
    sendJsonResponse(500, "Failed to update wallet. Please try again.");
}

$conn->close();
?>
