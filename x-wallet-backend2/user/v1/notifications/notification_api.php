<?php
require '../../../connection/connect.php';
require '../../../models/Notification.php';
require '../../../utils/utility.php';

// Connect to the database
$notification = new Notification($conn);

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Handle API Requests
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') { // Create a notification
    if (!isset($data['userId'], $data['message'])) {
        sendJsonResponse(400, "Missing required fields");
    }
    // $is_deleted = isset($data['is_deleted']) ? $data['is_deleted'] : 0;
    $result = $notification->createNotification($data['userId'], $data['message']);
    sendJsonResponse(201, "notifications send successfully", ($result));
} elseif ($method == 'GET') { // Get notifications
    $userId = isset($_GET['userId']) ? $_GET['userId'] : null;
    $result = $notification->getNotifications($userId);
    sendJsonResponse(200, "getting all notifications", $result);
} elseif ($method == 'PUT') {
    if (!isset($data['id'], $data['userId'])) {
        sendJsonResponse(400, "Missing required fields: id and userId are mandatory");
    }
    if (!isset($data['message']) && !isset($data['is_deleted'])) {
        sendJsonResponse(400, "At least one of message or is_deleted is required");
    }
    $existingNotification = $notification->getNotificationById($data['id']);
    if (!$existingNotification) {
        sendJsonResponse(404, "Notification not found");
    }
    $message = isset($data['message']) ? $data['message'] : $existingNotification['message'];
    $is_deleted = isset($data['is_deleted']) ? $data['is_deleted'] : $existingNotification['is_deleted'];

    $result = $notification->updateNotification($data['id'], $message, $is_deleted);
    sendJsonResponse(200, "Notification updated successfully", $result);
} elseif ($method == 'DELETE') { // Delete (soft delete) a notification
    if (!isset($data['id'])) {
        sendJsonResponse(400, "Missing notification ID");
    }
    $result = $notification->deleteNotification($data['id']);
    sendJsonResponse(200, "deleted success", data: $result);
} else {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
}
