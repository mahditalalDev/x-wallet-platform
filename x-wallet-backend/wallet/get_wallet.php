<?php
header("Content-Type: application/json");
require_once "../db.php"; // Ensure you have a database connection file

if (isset($_GET["userId"])) {
    $userId = intval($_GET["userId"]);
    $stmt = $conn->prepare("SELECT * FROM wallets WHERE userId = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $wallets = [];
    while ($row = $result->fetch_assoc()) {
        $wallets[] = $row;
    }
    
    echo json_encode(["status" => "success", "wallets" => $wallets]);
} else {
    echo json_encode(["status" => "error", "message" => "Missing userId"]);
}
?>
