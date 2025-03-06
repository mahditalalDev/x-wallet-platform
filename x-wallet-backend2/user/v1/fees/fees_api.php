<?php
require '../../../connection/connect.php';
require '../../../models/fees.php';
require '../../../utils/utility.php';


// Connect to the database
$fees = new Fees($conn);


$method = $_SERVER['REQUEST_METHOD'];

if ($method === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['userId'])) {
        echo json_encode(["error" => "userId is required"]);
        http_response_code(400);
        exit;
    }

    $userId = $data['userId'];
    $p2p_fees = $data['p2p_fees'] ?? 0.0;
    $withdrawls = $data['withdrawls'] ?? 0.0;
    $QR_pay = $data['QR_pay'] ?? 0.0;

    if ($fees->setFees($userId, $p2p_fees, $withdrawls, $QR_pay)) {
        echo json_encode(["message" => "Fees updated successfully"]);
    } else {
        echo json_encode(["error" => "Failed to update fees"]);
        http_response_code(500);
    }
}

elseif ($method === "GET") {
    if (!isset($_GET['userId'])) {
        echo json_encode(["error" => "userId is required"]);
        http_response_code(400);
        exit;
    }

    $userId = intval($_GET['userId']);
    $result = $fees->getFeesByUserId($userId);

    if ($result) {
        echo json_encode($result);
    } else {
        echo json_encode(["message" => "No fees found for userId: $userId"]);
    }
}

elseif ($method === "DELETE") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['userId'])) {
        echo json_encode(["error" => "userId is required"]);
        http_response_code(400);
        exit;
    }

    $userId = $data['userId'];
    
    if ($fees->deleteFees($userId)) {
        echo json_encode(["message" => "Fees deleted successfully"]);
    } else {
        echo json_encode(["error" => "Failed to delete fees"]);
        http_response_code(500);
    }
}

else {
    echo json_encode(["error" => "Invalid request method"]);
    http_response_code(405);
}
?>
