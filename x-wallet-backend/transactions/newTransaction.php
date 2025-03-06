<?php
require '../db.php';


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (!isset($data['senderId'], $data['receiverId'], $data['wallet_id'], $data['amount'], $data['currency'], $data['type'], $data['fees'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing required fields"]);
    exit();
}

// Extract variables
$senderId = $data['senderId'];
$receiverId = $data['receiverId'];
$walletId = $data['wallet_id'];
$amount = floatval($data['amount']);
$currency = $data['currency'];
$type = $data['type'];
$fees = floatval($data['fees']);

$conn->begin_transaction(); // Start transaction

try {
    // Check if sender has enough balance
    $stmt = $conn->prepare("SELECT balance, currency FROM wallets WHERE id = ? AND userId = ?");
    $stmt->bind_param("ii", $walletId, $senderId);
    $stmt->execute();
    $result = $stmt->get_result();
    $wallet = $result->fetch_assoc();
    $stmt->close();

    if (!$wallet) {
        throw new Exception("Sender's wallet not found.");
    }

    if ($wallet['currency'] !== $currency) {
        throw new Exception("Currency mismatch in transaction.");
    }

    if ($wallet['balance'] < ($amount + $fees)) {
        throw new Exception("Insufficient balance.");
    }

    // Deduct balance from sender
    $newSenderBalance = $wallet['balance'] - ($amount + $fees);
    $stmt = $conn->prepare("UPDATE wallets SET balance = ? WHERE id = ?");
    $stmt->bind_param("di", $newSenderBalance, $walletId);
    $stmt->execute();
    $stmt->close();

    // Add balance to receiver
    $stmt = $conn->prepare("SELECT id, balance FROM wallets WHERE userId = ? AND currency = ?");
    $stmt->bind_param("is", $receiverId, $currency);
    $stmt->execute();
    $result = $stmt->get_result();
    $receiverWallet = $result->fetch_assoc();
    $stmt->close();

    if ($receiverWallet) {
        $newReceiverBalance = $receiverWallet['balance'] + $amount;
        $stmt = $conn->prepare("UPDATE wallets SET balance = ? WHERE id = ?");
        $stmt->bind_param("di", $newReceiverBalance, $receiverWallet['id']);
        $stmt->execute();
        $stmt->close();
    } else {
        throw new Exception("Receiver's wallet not found in the same currency.");
    }

    // Insert transaction record
    $stmt = $conn->prepare("INSERT INTO transactions (senderId, receiverId, wallet_id, amount, currency, type, fees) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiisssd", $senderId, $receiverId, $walletId, $amount, $currency, $type, $fees);
    $stmt->execute();
    $stmt->close();

    $conn->commit(); // Commit transaction

    http_response_code(201);
    echo json_encode(["message" => "Transaction successful"]);

} catch (Exception $e) {
    $conn->rollback(); // Roll back transaction in case of error
    http_response_code(400);
    echo json_encode(["error" => $e->getMessage()]);
}

$conn->close();
?>
