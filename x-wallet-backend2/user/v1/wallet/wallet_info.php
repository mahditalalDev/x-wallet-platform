<?php
require '../../../connection/connect.php';
require '../../../models/wallet.php';
require '../../../utils/utility.php';

// Allow CORS for all domains
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Retrieve query parameters
$userId = isset($_GET['userId']) ? $_GET['userId'] : null;
$currency = isset($_GET['currency']) ? $_GET['currency'] : null;

// Check for missing fields
// if (!$userId || !$currency) {
//     sendJsonResponse(400, "Please provide both userId and currency as query parameters.");
// }

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
?>
