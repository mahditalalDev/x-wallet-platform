<?php
class Transaction {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }
     public function processTransaction($senderId, $receiverId, $walletId, $amount, $currency, $type, $fees) {
        $this->conn->begin_transaction(); // Start transaction

        try {
            // Check sender balance
            $stmt = $this->conn->prepare("SELECT balance, currency FROM wallets WHERE id = ? AND userId = ?");
            $stmt->bind_param("ii", $walletId, $senderId);
            $stmt->execute();
            $result = $stmt->get_result();
            $wallet = $result->fetch_assoc();
            $stmt->close();

            if (!$wallet) {
                throw new Exception("Sender's wallet not found.");
            }

            if ($wallet['currency'] !== $currency) {
                throw new Exception("Currency mismatch in transaction.");
            }

            if ($wallet['balance'] < ($amount + $fees)) {
                throw new Exception("Insufficient balance.");
            }

            // Deduct balance from sender
            $newSenderBalance = $wallet['balance'] - ($amount + $fees);
            $stmt = $this->conn->prepare("UPDATE wallets SET balance = ? WHERE id = ?");
            $stmt->bind_param("di", $newSenderBalance, $walletId);
            $stmt->execute();
            $stmt->close();

            // Add balance to receiver
            $stmt = $this->conn->prepare("SELECT id, balance FROM wallets WHERE userId = ? AND currency = ?");
            $stmt->bind_param("is", $receiverId, $currency);
            $stmt->execute();
            $result = $stmt->get_result();
            $receiverWallet = $result->fetch_assoc();
            $stmt->close();

            if ($receiverWallet) {
                $newReceiverBalance = $receiverWallet['balance'] + $amount;
                $stmt = $this->conn->prepare("UPDATE wallets SET balance = ? WHERE id = ?");
                $stmt->bind_param("di", $newReceiverBalance, $receiverWallet['id']);
                $stmt->execute();
                $stmt->close();
            } else {
                throw new Exception("Receiver's wallet not found in the same currency.");
            }

            // Insert transaction record
            $stmt = $this->conn->prepare("INSERT INTO transactions (senderId, receiverId, wallet_id, amount, currency, type, fees) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iiisssd", $senderId, $receiverId, $walletId, $amount, $currency, $type, $fees);
            $stmt->execute();
            $stmt->close();

            $this->conn->commit(); // Commit transaction
            return ["success" => true, "message" => "Transaction successful"];
        } catch (Exception $e) {
            $this->conn->rollback(); // Roll back transaction in case of error
            return ["success" => false, "error" => $e->getMessage()];
        }
    }

    public function getUserTransactions($userId) {
        $stmt = $this->conn->prepare("
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
        return $transactions;
    }
    public function getTransactions() {
        $stmt = $this->conn->prepare("
            SELECT * FROM transactions  
            ORDER BY createdAt DESC
        ");
        // $stmt->bind_param();
        $stmt->execute();
        
        $result = $stmt->get_result();
        $transactions = [];

        while ($row = $result->fetch_assoc()) {
            $transactions[] = $row;
        }

        $stmt->close();
        return $transactions;
    }

}
?>
