<?php
class Admin
{
    private $conn;
    private $table = "users";

    // Constructor to initialize the database connection
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Function to log in an admin user
    public function login($email, $password)
    {
        // Query to check if the user exists and is an admin
        $query = "SELECT * FROM users WHERE (email = ? OR username = ?) AND isAdmin = 1 LIMIT 1";
        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            die("SQL Error: " . $this->conn->error);
        }

        $stmt->bind_param("ss", $email, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if a matching user was found
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $user['password'])) {
                return [
                    "status" => "success",
                    "message" => "Login successful",
                    "data" => [
                        "id" => $user['id'],
                        "userName" => $user['username'],
                        "email" => $user['email'],
                        "role" => "admin"
                    ]
                ];
            } else {
                return ["status" => "error", "message" => "Invalid password"];
            }
        } else {
            return ["status" => "error", "message" => "Admin user not found"];
        }
    }
}
