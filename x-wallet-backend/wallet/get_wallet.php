<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");require_once "../db.php"; // Ensure you have a database connection file

// Check if userId is provided
if (!isset($_GET["userId"])) {
    echo json_encode(["status" => "error", "message" => "Missing userId"]);
    exit;
}

$userId = intval($_GET["userId"]); // Get and sanitize userId
$currency = isset($_GET["currency"]) ? trim($_GET["currency"]) : null; // Optional currency filter

// Prepare SQL query based on whether currency is provided
if ($currency) {
    $stmt = $conn->prepare("SELECT * FROM wallets WHERE userId = ? AND currency = ?");
    $stmt->bind_param("is", $userId, $currency);
} else {
    $stmt = $conn->prepare("SELECT * FROM wallets WHERE userId = ?");
    $stmt->bind_param("i", $userId);
}

$stmt->execute();
$result = $stmt->get_result();

$wallets = [];
while ($row = $result->fetch_assoc()) {
    $wallets[] = $row;
}

// Close connection
$stmt->close();
$conn->close();

// Return JSON response
if (!empty($wallets)) {
    echo json_encode(["status" => "success", "wallets" => $wallets]);
} else {
    echo json_encode(["status" => "error", "message" => "No wallets found"]);
}
?>
