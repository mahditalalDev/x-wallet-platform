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
    public function updateStatus($transactionId, $status) {
        $this->conn->begin_transaction(); // Start transaction
    
        try {
            // Get transaction details
            $stmt = $this->conn->prepare("SELECT senderId, receiverId, wallet_id, amount, currency, status FROM transactions WHERE id = ?");
            $stmt->bind_param("i", $transactionId);
            $stmt->execute();
            $result = $stmt->get_result();
            $transaction = $result->fetch_assoc();
            $stmt->close();
    
            if (!$transaction) {
                throw new Exception("Transaction not found.");
            }
    
            if ($transaction['status'] !== 'pending') {
                throw new Exception("Transaction has already been processed.");
            }
    
            $senderId = $transaction['senderId'];
            $receiverId = $transaction['receiverId'];
            $walletId = $transaction['wallet_id'];
            $amount = $transaction['amount'];
            $currency = $transaction['currency'];
    
            if ($status === 'accepted') {
                // Update transaction status
                $stmt = $this->conn->prepare("UPDATE transactions SET status = ? WHERE id = ?");
                $stmt->bind_param("si", $status, $transactionId);
                $stmt->execute();
                $stmt->close();
                
                // Transfer money to receiver
                $stmt = $this->conn->prepare("UPDATE wallets SET balance = balance + ? WHERE userId = ? AND currency = ?");
                $stmt->bind_param("dis", $amount, $receiverId, $currency);
                $stmt->execute();
                $stmt->close();
    
            } elseif ($status === 'rejected') {
                // Update transaction status
                $stmt = $this->conn->prepare("UPDATE transactions SET status = ? WHERE id = ?");
                $stmt->bind_param("si", $status, $transactionId);
                $stmt->execute();
                $stmt->close();
    
                // Refund money to sender
                $stmt = $this->conn->prepare("UPDATE wallets SET balance = balance + ? WHERE id = ?");
                $stmt->bind_param("di", $amount, $walletId);
                $stmt->execute();
                $stmt->close();
            } else {
                throw new Exception("Invalid status.");
            }
    
            $this->conn->commit(); // Commit transaction
            return ["success" => true, "message" => "Transaction status updated successfully"];
        } catch (Exception $e) {
            $this->conn->rollback(); // Rollback in case of error
            return ["success" => false, "error" => $e->getMessage()];
        }
    }
    

}
?>
