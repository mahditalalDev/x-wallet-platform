<?php
require '../db.php';


if (!isset($_GET['userId'])) {
    http_response_code(400);
    echo json_encode(["error" => "User ID is required"]);
    exit();
}

$userId = $_GET['userId'];

$stmt = $conn->prepare("
    SELECT * FROM transactions 
    WHERE senderId = ? OR receiverId = ? 
    ORDER BY createdAt DESC
");
$stmt->bind_param("ii", $userId, $userId);
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
