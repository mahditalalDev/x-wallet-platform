<?php
require '../db.php';

// Allow CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (!isset($data['name'], $data['email'], $data['phone'], $data['password'])) {
    http_response_code(400);
    echo json_encode(["error" => "Please provide all required fields: name, email, phone, and password."]);
    exit();
}

// Get user details
$name = $data['name'];
$email = $data['email'];
$phone = $data['phone'];
$password = password_hash($data['password'], PASSWORD_BCRYPT);
$isAdmin = 0; // Default to non-admin
$username = isset($data['username']) ? $data['username'] : explode('@', $email)[0];

$conn->begin_transaction(); // Start transaction

try {
    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (name, username, email, phone, password, isAdmin) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $name, $username, $email, $phone, $password, $isAdmin);
    $stmt->execute();
    
    // Get the newly inserted user's ID
    $userId = $stmt->insert_id;
    $stmt->close();

    // Insert a wallet for the new user
    $stmt = $conn->prepare("INSERT INTO wallets (userId, balance, limits) VALUES (?, 0.00, 0.00)");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    
    // Get the newly created wallet's ID
    $walletId = $stmt->insert_id;
    $stmt->close();

    // Update the user with the wallet_id
    $stmt = $conn->prepare("UPDATE users SET wallet_id = ? WHERE id = ?");
    $stmt->bind_param("ii", $walletId, $userId);
    $stmt->execute();
    $stmt->close();

    // Insert default fees for the user
    $stmt = $conn->prepare("INSERT INTO fees (userId, p2p_fees, withdrawls, QR_pay) VALUES (?, 0.00, 0.00, 0.00)");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->close();

    // Commit the transaction
    $conn->commit();

    // Success response
    http_response_code(201);
    echo json_encode(["message" => "Registration successful. A wallet and fees record have been created for the user."]);
} catch (Exception $e) {
    $conn->rollback(); // Roll back transaction in case of error

    http_response_code(500);
    echo json_encode(["error" => "Something went wrong: " . $e->getMessage()]);
}

// Close connection
$conn->close();
?>
