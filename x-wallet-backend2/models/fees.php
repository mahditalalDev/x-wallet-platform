<?php
class Fees {
    private $conn;
    private $table = "fees";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create or update fees for a user
    public function setFees($userId, $p2p_fees, $withdrawls, $QR_pay) {
        // Check if the user already has a fees record
        $query = "SELECT id FROM " . $this->table . " WHERE userId = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->fetch_assoc();

        if ($exists) {
            // Update existing record
            $query = "UPDATE " . $this->table . " SET p2p_fees = ?, withdrawls = ?, QR_pay = ? WHERE userId = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("dddi", $p2p_fees, $withdrawls, $QR_pay, $userId);
        } else {
            // Insert new record
            $query = "INSERT INTO " . $this->table . " (userId, p2p_fees, withdrawls, QR_pay) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("iddd", $userId, $p2p_fees, $withdrawls, $QR_pay);
        }

        return $stmt->execute();
    }

    // Get fees by userId
    public function getFeesByUserId($userId) {
        $query = "SELECT * FROM " . $this->table . " WHERE userId = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Delete fees by userId
    public function deleteFees($userId) {
        $query = "DELETE FROM " . $this->table . " WHERE userId = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $userId);
        return $stmt->execute();
    }
}
?>
