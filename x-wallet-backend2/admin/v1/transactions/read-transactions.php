<?php
require_once '../../../connection/connect.php';
require_once '../../../models/Transaction.php';


// Initialize Transaction model
$transaction = new Transaction($conn);

// Get all transactions
$transactions = $transaction->getTransactions();

// Check if transactions exist
if (!empty($transactions)) {
    $response = [
        "status" => "success",
        "message" => "Transactions retrieved successfully.",
        "data" => $transactions
    ];
    http_response_code(200);
} else {
    $response = [
        "status" => "error",
        "message" => "No transactions found."
    ];
    http_response_code(404);
}

// Return JSON response
echo json_encode($response);
?>
