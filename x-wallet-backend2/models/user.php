<?php
class User {
    private $conn;
    private $table = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Register new user
    public function register($data) {
        $this->conn->begin_transaction();
        
        try {
            $userId = $this->insertUser($data);
            $walletId = $this->insertWallet($userId);
            $this->updateUserWallet($userId, $walletId);
            $this->insertOrUpdateFees($userId);
            
            $this->conn->commit();
            return ["success" => true, "message" => "Registration successful.", "userId" => $userId];
        } catch (Exception $e) {
            $this->conn->rollback();
            return ["success" => false, "error" => "Something went wrong: " . $e->getMessage()];
        }
    }

    // Insert a new user
    public function insertUser($data) {
        $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);
        $isAdmin = 0;
        $username = isset($data['username']) ? $data['username'] : explode('@', $data['email'])[0];
        
        $stmt = $this->conn->prepare("INSERT INTO users (name, username, email, phone, password, isAdmin) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $data['name'], $username, $data['email'], $data['phone'], $passwordHash, $isAdmin);
        $stmt->execute();
        $userId = $stmt->insert_id;
        $stmt->close();
        return $userId;
    }

    // Insert a wallet for the user
    public function insertWallet($userId) {
        $stmt = $this->conn->prepare("INSERT INTO wallets (userId, balance, limits) VALUES (?, 0.00, 0.00)");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $walletId = $stmt->insert_id;
        $stmt->close();
        return $walletId;
    }

    // Update user with wallet ID
    public function updateUserWallet($userId, $walletId) {
        $stmt = $this->conn->prepare("UPDATE users SET wallet_id = ? WHERE id = ?");
        $stmt->bind_param("ii", $walletId, $userId);
        $stmt->execute();
        $stmt->close();
    }

    // Insert or update fees for the user
    public function insertOrUpdateFees($userId, $p2p_fees = 0.00, $withdrawals = 0.00, $QR_pay = 0.00) {
        $stmt = $this->conn->prepare("INSERT INTO fees (userId, p2p_fees, withdrawals, QR_pay) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE p2p_fees = VALUES(p2p_fees), withdrawals = VALUES(withdrawals), QR_pay = VALUES(QR_pay)");
        $stmt->bind_param("iddd", $userId, $p2p_fees, $withdrawals, $QR_pay);
        $stmt->execute();
        $stmt->close();
    }

    // Read user data
    public function read($id = null) {
        if ($id) {
            $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $id);
        } else {
            $query = "SELECT * FROM " . $this->table;
            $stmt = $this->conn->prepare($query);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // User login
    public function login($emailOrUsername, $password) {
        $query = "SELECT * from " . $this->table . " WHERE email = ? OR username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $emailOrUsername, $emailOrUsername);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                unset($user['password']);
                return ["success" => true, "user" => $user];
            } else {
                return ["success" => false, "error" => "Invalid password"];
            }
        } else {
            return ["success" => false, "error" => "User not found"];
        }
    }
    
    // Update user profile
    public function update_profile($id, $data) {
        $query = "UPDATE " . $this->table . " SET name = ?, username = ?, email = ?, phone = ?, password = ?, isAdmin = ?, verification_type = ?, wallet_id = ?, tier = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssssisiii", $data['name'], $data['username'], $data['email'], $data['phone'], $data['password'], $data['isAdmin'], $data['verification_type'], $data['wallet_id'], $data['tier'], $id);
        return $stmt->execute();
    }

    // Delete user account
    public function delete_user($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
