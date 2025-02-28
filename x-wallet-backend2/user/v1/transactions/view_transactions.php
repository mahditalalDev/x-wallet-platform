<?php
require '../../../connection/connect.php';
require '../../../models/transaction.php';
require '../../../utils/utility.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Validate input
if (!isset($_GET['userId'])) {
    sendJsonResponse(400, "User ID is required.");
}

$userId = $_GET['userId'];

// Initialize Transaction class
$transactionModel = new Transaction($conn);
$transactions = $transactionModel->getUserTransactions($userId);

sendJsonResponse(200, "Transactions fetched successfully.", $transactions);

$conn->close();
?>
