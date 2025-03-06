<?php
require '../db.php';


// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

// Ensure the request method is PUT
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Only PUT requests are allowed."]);
    exit();
}

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (!isset($data['id']) || (!isset($data['balance']) && !isset($data['limits']))) {
    http_response_code(400);
    echo json_encode(["error" => "Please provide wallet ID and at least one field (balance or limits) to update."]);
    exit();
}

$walletId = intval($data['id']);
$balance = isset($data['balance']) ? floatval($data['balance']) : null;
$limits = isset($data['limits']) ? floatval($data['limits']) : null;

// Build dynamic SQL query
$updateFields = [];
$params = [];
$paramTypes = "";

if ($balance !== null) {
    $updateFields[] = "balance = ?";
    $params[] = $balance;
    $paramTypes .= "d"; // Decimal/Double type
}
if ($limits !== null) {
    $updateFields[] = "limits = ?";
    $params[] = $limits;
    $paramTypes .= "d";
}

$params[] = $walletId;
$paramTypes .= "i"; // Integer type for wallet ID

// Prepare the query dynamically
$sql = "UPDATE wallets SET " . implode(", ", $updateFields) . " WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param($paramTypes, ...$params);

// Execute and return response
if ($stmt->execute() && $stmt->affected_rows > 0) {
    http_response_code(200);
    echo json_encode(["message" => "Wallet updated successfully."]);
} else {
    http_response_code(400);
    echo json_encode(["error" => "Failed to update wallet. Wallet ID may not exist."]);
}

// Close connection
$stmt->close();
$conn->close();
?>
