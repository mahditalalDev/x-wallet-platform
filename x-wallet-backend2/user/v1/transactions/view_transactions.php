<?php
require '../../../connection/connect.php';
require '../../../models/transaction.php';
require '../../../utils/utility.php';


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
