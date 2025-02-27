<?php
require '../db.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

$stmt = $conn->prepare("
    SELECT * FROM transactions 
    ORDER BY createdAt DESC
");
$stmt->execute();

$result = $stmt->get_result();
$transactions = [];

while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
}

$stmt->close();
$conn->close();

http_response_code(200);
echo json_encode($transactions);
?>
