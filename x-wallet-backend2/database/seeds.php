<?php
// Include the database connection from db.php
include('../connection/connect.php'); // Adjust path as needed

// Disable foreign key checks to avoid issues during seeding
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// ========= INSERT USERS (5 records) =========
// Insert users with wallet_id as NULL (to be updated later)
$userSeeds = [
    // [name, username, email, phone, password, verification_type, tier]
    ["John Doe", "johnd", "john@example.com", "1234567890", password_hash("password1", PASSWORD_DEFAULT), "un_verified", "basic"],
    ["Jane Smith", "janes", "jane@example.com", "2345678901", password_hash("password2", PASSWORD_DEFAULT), "un_verified", "premium"],
    ["Bob Johnson", "bobj", "bob@example.com", "3456789012", password_hash("password3", PASSWORD_DEFAULT), "un_verified", "standard"],
    ["Alice Brown", "aliceb", "alice@example.com", "4567890123", password_hash("password4", PASSWORD_DEFAULT), "un_verified", "basic"],
    ["Charlie Davis", "charlied", "charlie@example.com", "5678901234", password_hash("password5", PASSWORD_DEFAULT), "un_verified", "premium"],
];

$userInsert = $conn->prepare("INSERT INTO users (name, username, email, phone, password, isAdmin, createdAt, verification_type, wallet_id, tier) VALUES (?, ?, ?, ?, ?, 0, NOW(), ?, NULL, ?)");
foreach ($userSeeds as $user) {
    // $user: [name, username, email, phone, password, verification_type, tier]
    $userInsert->bind_param("sssssss", $user[0], $user[1], $user[2], $user[3], $user[4], $user[5], $user[6]);
    $userInsert->execute();
}
$userInsert->close();
// At this point, the inserted users will have auto-increment IDs 1 to 5.

// ========= INSERT WALLETS (5 records) =========
// Each wallet record references a user via userId.
$walletSeeds = [
    // [userId, balance, limits, currency]
    [1, 100.00, 500.00, "USD"],
    [2, 200.00, 1000.00, "USD"],
    [3, 300.00, 1500.00, "USD"],
    [4, 400.00, 2000.00, "USD"],
    [5, 500.00, 2500.00, "USD"],
];

$walletInsert = $conn->prepare("INSERT INTO wallets (userId, balance, limits, currency) VALUES (?, ?, ?, ?)");
$walletIds = []; // To store the auto-incremented wallet IDs
foreach ($walletSeeds as $wallet) {
    // $wallet: [userId, balance, limits, currency]
    $walletInsert->bind_param("idds", $wallet[0], $wallet[1], $wallet[2], $wallet[3]);
    $walletInsert->execute();
    $walletIds[] = $conn->insert_id;
}
$walletInsert->close();

// ========= UPDATE USERS with wallet_id =========
// Now update each user record to set the wallet_id field.
$updateStmt = $conn->prepare("UPDATE users SET wallet_id = ? WHERE id = ?");
for ($i = 0; $i < count($walletIds); $i++) {
    // Assuming the wallet was created for user with id = $walletSeeds[$i][0] (which is 1,2,3,4,5)
    $userId = $walletSeeds[$i][0];
    $walletId = $walletIds[$i];
    $updateStmt->bind_param("ii", $walletId, $userId);
    $updateStmt->execute();
}
$updateStmt->close();

// ========= INSERT FEES (5 records) =========
// fees table requires a valid userId.
$feesSeeds = [
    // [userId, p2p_fees, withdrawls, QR_pay]
    [1, 1.50, 2.50, 0.75],
    [2, 2.50, 3.50, 1.25],
    [3, 3.50, 4.50, 1.75],
    [4, 4.50, 5.50, 2.25],
    [5, 5.50, 6.50, 2.75],
];

$feesInsert = $conn->prepare("INSERT INTO fees (userId, p2p_fees, withdrawls, QR_pay) VALUES (?, ?, ?, ?)");
foreach ($feesSeeds as $fee) {
    $feesInsert->bind_param("iddd", $fee[0], $fee[1], $fee[2], $fee[3]);
    $feesInsert->execute();
}
$feesInsert->close();

// ========= INSERT NOTIFICATIONS (5 records) =========
// Each notification requires a valid userId.
$notificationsSeeds = [
    // [userId, message, is_deleted]
    [1, "Notification message 1", 0],
    [2, "Notification message 2", 0],
    [3, "Notification message 3", 1],
    [4, "Notification message 4", 0],
    [5, "Notification message 5", 1],
];

$notifInsert = $conn->prepare("INSERT INTO notifications (userId, message, is_deleted, createdAt) VALUES (?, ?, ?, NOW())");
foreach ($notificationsSeeds as $notif) {
    $notifInsert->bind_param("isi", $notif[0], $notif[1], $notif[2]);
    $notifInsert->execute();
}
$notifInsert->close();

// ========= INSERT TRANSACTIONS (5 records) =========
// Each transaction requires valid senderId, receiverId, and wallet_id.
// For simplicity, we use the wallet id associated with the sender.
$transactionsSeeds = [
    // [senderId, receiverId, wallet_id, amount, currency, type, fees]
    [1, 2, $walletIds[0], 50.00, "USD", "p2p", 0.50],
    [2, 3, $walletIds[1], 75.00, "USD", "withdraw", 1.00],
    [3, 4, $walletIds[2], 100.00, "USD", "QR_pay", 1.50],
    [4, 5, $walletIds[3], 125.00, "USD", "p2p", 2.00],
    [5, 1, $walletIds[4], 150.00, "USD", "withdraw", 2.50],
];

$txInsert = $conn->prepare("INSERT INTO transactions (senderId, receiverId, wallet_id, amount, currency, type, fees, createdAt) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
foreach ($transactionsSeeds as $tx) {
    $txInsert->bind_param("iiidssd", $tx[0], $tx[1], $tx[2], $tx[3], $tx[4], $tx[5], $tx[6]);
    $txInsert->execute();
}
$txInsert->close();

// Re-enable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

echo "Seed data inserted successfully.";
$conn->close();
?>
