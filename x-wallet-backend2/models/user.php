<?php
class User
{
    private $conn;
    private $table = "users";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // register new user
    public function register($data)
    {
        $this->conn->begin_transaction(); // Start transaction

        try {
            // Check if the email or username already exists
            $query = "SELECT id FROM " . $this->table . " WHERE email = ? OR username = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ss", $data['email'], $data['username']);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Return an error if user already exists
                return ["success" => false, "error" => "User with this email or username already exists."];
            }

            // Proceed with registration if no existing user
            $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);
            $isAdmin = 0; // Default to non-admin
            $username = isset($data['username']) ? $data['username'] : explode('@', $data['email'])[0];

            // Insert new user
            $stmt = $this->conn->prepare("INSERT INTO users (name, username, email, phone, password, isAdmin) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssi", $data['name'], $username, $data['email'], $data['phone'], $passwordHash, $isAdmin);
            $stmt->execute();
            $userId = $stmt->insert_id;
            $stmt->close();

            // Insert a wallet for the new user
            $stmt = $this->conn->prepare("INSERT INTO wallets (userId, balance, limits) VALUES (?, 0.00, 0.00)");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $walletId = $stmt->insert_id;
            $stmt->close();

            // Update the user with the wallet_id
            $stmt = $this->conn->prepare("UPDATE users SET wallet_id = ? WHERE id = ?");
            $stmt->bind_param("ii", $walletId, $userId);
            $stmt->execute();
            $stmt->close();

            // Insert default fees for the user
            $stmt = $this->conn->prepare("INSERT INTO fees (userId, p2p_fees, withdrawls, QR_pay) VALUES (?, 0.00, 0.00, 0.00)");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $stmt->close();

            $this->conn->commit(); // Commit the transaction

            return ["success" => true, "message" => "Registration successful.", "userId" => $userId];
        } catch (Exception $e) {
            $this->conn->rollback(); // Rollback in case of error
            return ["success" => false, "error" => "Something went wrong: " . $e->getMessage()];
        }
    }

    // Read Users data 
    public function read($id = null)
    {
        if ($id) {
            // Fetch user details including the document
            $query = "SELECT id, name, username, email, phone, id_document FROM " . $this->table . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $userResult = $stmt->get_result();
            $userData = $userResult->fetch_assoc();

            if (!$userData) {
                return ["success" => false, "error" => "User not found"];
            }

            // Fetch fees
            $queryFees = "SELECT * FROM fees WHERE userId = ?";
            $stmt = $this->conn->prepare($queryFees);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $feesResult = $stmt->get_result();
            $feesData = $feesResult->fetch_assoc();

            // Fetch wallets
            $queryWallets = "SELECT * FROM wallets WHERE userId = ?";
            $stmt = $this->conn->prepare($queryWallets);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $walletsResult = $stmt->get_result();
            $walletsData = $walletsResult->fetch_all(MYSQLI_ASSOC);

            // Merge data
            $userData['fees'] = $feesData ?: [];
            $userData['wallets'] = $walletsData;
            $userData['id_document'] = !empty($userData['id_document']) ? "http://localhost/uploads/" . $userData['id_document'] : null;

            return ["success" => true, "user" => $userData];
        } else {
            return ["success" => false, "error" => "User ID is required"];
        }
    }
    public function updateUser($userId, $data, $file)
    {
        if (!$userId) {
            return ["status" => "error", "message" => "User ID is required"];
        }

        $updates = [];
        $values = [];
        $types = ""; // To store parameter types for bind_param

        if (!empty($data["name"])) {
            $updates[] = "name = ?";
            $values[] = $data["name"];
            $types .= "s"; // 's' for string
        }

        if (!empty($data["username"])) {
            $updates[] = "username = ?";
            $values[] = $data["username"];
            $types .= "s";
        }

        if (!empty($data["email"])) {
            $updates[] = "email = ?";
            $values[] = $data["email"];
            $types .= "s";
        }

        if (!empty($data["phone"])) {
            $updates[] = "phone = ?";
            $values[] = $data["phone"];
            $types .= "s";
        }

        if (!empty($data["password"])) {
            $updates[] = "password = ?";
            $values[] = password_hash($data["password"], PASSWORD_BCRYPT);
            $types .= "s";
        }

        // Ensure the "uploads" directory exists
        $uploadDir = __DIR__ . "/../uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Handle ID document upload
        if (!empty($file["id_document"])) {
            $fileName = basename($file["id_document"]["name"]);
            $filePath = $uploadDir . $fileName;

            if (move_uploaded_file($file["id_document"]["tmp_name"], $filePath)) {
                $updates[] = "id_document = ?";
                $values[] = $fileName;
                $types .= "s";
            } else {
                return ["status" => "error", "message" => "Failed to upload ID document"];
            }
        }

        if (!empty($updates)) {
            $sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?";
            $values[] = $userId;
            $types .= "i"; // 'i' for integer (user ID)

            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                return ["status" => "error", "message" => "SQL Error: " . $this->conn->error];
            }

            // Dynamically bind parameters
            $stmt->bind_param($types, ...$values);

            if ($stmt->execute()) {
                return ["status" => "success", "message" => "User updated successfully"];
            } else {
                return ["status" => "error", "message" => "Failed to update user: " . $stmt->error];
            }
        } else {
            return ["status" => "error", "message" => "No fields provided for update"];
        }
    }


    public function login($emailOrUsername, $password)
    {
        $query = "SELECT * from " . $this->table . " WHERE email = ? OR username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $emailOrUsername, $emailOrUsername);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Verify the password
            if (password_verify($password, $user['password'])) {
                unset($user['password']); // Remove password from the response
                return ["success" => true, "user" => $user];
            } else {
                return ["success" => false, "error" => "Invalid password"];
            }
        } else {
            return ["success" => false, "error" => "User not found"];
        }
    }


    // Update User profilerof
    public function update_profile($id, $data)
    {
        $query = "UPDATE " . $this->table . " SET name = ?, username = ?, email = ?, phone = ?, password = ?, 
                  isAdmin = ?, verification_type = ?, wallet_id = ?, tier = ? WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param(
            "sssssisiii",
            $data['name'],
            $data['username'],
            $data['email'],
            $data['phone'],
            $data['password'],
            $data['isAdmin'],
            $data['verification_type'],
            $data['wallet_id'],
            $data['tier'],
            $id
        );

        return $stmt->execute();
    }

    // Delete User account
    public function delete_user($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
