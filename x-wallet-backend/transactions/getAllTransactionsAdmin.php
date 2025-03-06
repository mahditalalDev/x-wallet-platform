<?php
require '../db.php';


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
