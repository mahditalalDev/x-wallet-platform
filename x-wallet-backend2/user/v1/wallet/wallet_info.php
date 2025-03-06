<?php
require '../../../connection/connect.php';
require '../../../models/wallet.php';
require '../../../utils/utility.php';

// Retrieve query parameters
$userId = isset($_GET['userId']) ? $_GET['userId'] : null;
$currency = isset($_GET['currency']) ? $_GET['currency'] : null;



// Initialize Wallet model
$walletModel = new Wallet($conn);

// Fetch wallet data based on userId and currency
$response = $walletModel->read($userId, $currency);

if ($response) {
    sendJsonResponse(200, "Wallet information fetched successfully", ["wallet" => $response]);
} else {
    sendJsonResponse(404, "Wallet not found or currency mismatch.");
}


$conn->close();
