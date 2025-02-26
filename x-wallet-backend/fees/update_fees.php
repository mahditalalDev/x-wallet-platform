<?php
require '../db.php';

// Allow CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

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
if (!isset($data['userId']) || (!isset($data['p2p_fees']) && !isset($data['withdrawls']) && !isset($data['QR_pay']))) {
    http_response_code(400);
    echo json_encode(["error" => "Please provide userId and at least one field (p2p_fees, withdrawls, or QR_pay) to update."]);
    exit();
}

$userId = intval($data['userId']);
$p2p_fees = isset($data['p2p_fees']) ? floatval($data['p2p_fees']) : null;
$withdrawls = isset($data['withdrawls']) ? floatval($data['withdrawls']) : null;
$QR_pay = isset($data['QR_pay']) ? floatval($data['QR_pay']) : null;

// Build dynamic SQL query
$updateFields = [];
$params = [];
$paramTypes = "";

if ($p2p_fees !== null) {
    $updateFields[] = "p2p_fees = ?";
    $params[] = $p2p_fees;
    $paramTypes .= "d"; // Decimal type
}
if ($withdrawls !== null) {
    $updateFields[] = "withdrawls = ?";
    $params[] = $withdrawls;
    $paramTypes .= "d";
}
if ($QR_pay !== null) {
    $updateFields[] = "QR_pay = ?";
    $params[] = $QR_pay;
    $paramTypes .= "d";
}

$params[] = $userId;
$paramTypes .= "i"; // Integer type for userId

// Prepare and execute the query dynamically
$sql = "UPDATE fees SET " . implode(", ", $updateFields) . " WHERE userId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param($paramTypes, ...$params);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    http_response_code(200);
    echo json_encode(["message" => "Fees updated successfully."]);
} else {
    http_response_code(400);
    echo json_encode(["error" => "Failed to update fees. User ID may not exist."]);
}

// Close connection
$stmt->close();
$conn->close();
?>
