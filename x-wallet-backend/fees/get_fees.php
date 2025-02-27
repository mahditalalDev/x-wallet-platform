<?php
header("Content-Type: application/json");
require_once "../db.php"; // Ensure you have a database connection file

if (isset($_GET["userId"])) {
    $userId = intval($_GET["userId"]);
    $stmt = $conn->prepare("SELECT p2p_fees, withdrawls, QR_pay FROM fees WHERE userId = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode(["status" => "success", "fees" => $row]);
    } else {
        echo json_encode(["status" => "error", "message" => "No fees found for this userId"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Missing userId"]);
}
?>
