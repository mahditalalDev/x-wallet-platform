<?php
class Notification
{
    private $conn;
    private $table = "notifications";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Create a new notification and return its full data
    public function createNotification($userId, $message)
    {
        $query = "INSERT INTO " . $this->table . " (userId, message, is_deleted, createdAt) VALUES (?, ?, 0, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("is", $userId, $message);
        $stmt->execute();

        // Fetch the newly created notification
        $lastId = $this->conn->insert_id;
        return $this->getNotificationById($lastId);
    }

    // Fetch a notification by ID
    public function getNotificationById($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc(); // Return single notification as associative array
    }

    // Get notifications (all or by userId)
    public function getNotifications($userId = null)
    {
        if ($userId) {
            $query = "SELECT * FROM " . $this->table . " WHERE userId = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $userId);
        } else {
            $query = "SELECT * FROM " . $this->table;
            $stmt = $this->conn->prepare($query);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC); // Return array of notifications
    }

    // Update a notification and return the updated row
    public function updateNotification($id, $message, $is_deleted)
    {
        $query = "UPDATE " . $this->table . " SET message = ?, is_deleted = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sii", $message, $is_deleted, $id);
        $stmt->execute();

        return $this->getNotificationById($id); // Fetch and return updated notification
    }


    // Delete a notification (soft delete) and return the updated row
    public function deleteNotification($id)
    {
        $query = "UPDATE " . $this->table . " SET is_deleted = 1 WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $this->getNotificationById($id); // Fetch and return soft-deleted notification
    }
}
