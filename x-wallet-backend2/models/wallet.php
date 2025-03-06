<?php
class Wallet
{
    private $conn;
    private $table = "wallets";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Create a new wallet
    public function create($userId, $balance = 0.00, $limits = 0.00, $currency = 'USD')
    {
        try {
            $query = "INSERT INTO " . $this->table . " (userId, balance, limits, currency) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("idds", $userId, $balance, $limits, $currency);
            $stmt->execute();
            $walletId = $stmt->insert_id;
            $stmt->close();
            return ["success" => true, "walletId" => $walletId];
        } catch (Exception $e) {
            return ["success" => false, "error" => "Something went wrong: " . $e->getMessage()];
        }
    }

    // Read wallet data (fetch by wallet ID or user ID)
    public function read($userId = null, $currency = null)
    {
        // Check if currency is provided
        if ($currency !== null) {
            $stmt = $this->conn->prepare("SELECT * FROM wallets WHERE userId = ? AND currency = ?");
            $stmt->bind_param("is", $userId, $currency);
        } else {
            $stmt = $this->conn->prepare("SELECT * FROM wallets WHERE userId = ?");
            $stmt->bind_param("i", $userId);
        }
    
        // Execute the query
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
    
        $wallets = [];
        while ($row = $result->fetch_assoc()) {
            $wallets[] = $row;
        }
    
        // Return the result instead of echoing it
        return $wallets;
    }
    


   // Update wallet data
public function update($walletId, $limits = null, $currency = null, $balance = null)
{
    $query = "UPDATE wallets SET";
    $params = [];
    $types = "";

    if ($limits !== null) {
        $query .= " limits = ?,";
        $params[] = $limits;
        $types .= "d"; 
    }

    if ($currency !== null) {
        $query .= " currency = ?,";
        $params[] = $currency;
        $types .= "s";
    }

    if ($balance !== null) {
        $query .= " balance = ?,";
        $params[] = $balance;
        $types .= "d"; 
    }

    if (empty($params)) {
        return false; 
    }

    $query = rtrim($query, ",");

    $query .= " WHERE id = ?";
    $params[] = $walletId;
    $types .= "i"; 

    $stmt = $this->conn->prepare($query);
    
    if (!$stmt) {
        die("Prepare failed: " . $this->conn->error);
    }

    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        $stmt->close();
        return true; 
    } else {
        die("Execute failed: " . $stmt->error); 
        $stmt->close();
        return false;
    }
}

    

    // Delete a wallet
    public function delete($walletId)
    {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $walletId);
            $stmt->execute();
            $stmt->close();
            return ["success" => true, "message" => "Wallet deleted successfully"];
        } catch (Exception $e) {
            return ["success" => false, "error" => "Something went wrong: " . $e->getMessage()];
        }
    }
}
