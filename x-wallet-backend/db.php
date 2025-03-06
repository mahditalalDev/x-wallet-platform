<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$server = "localhost";
$user = "root";
$pass = "mahditalaldev";
$dbname = "x-wallet";

try {
    $pdo = new PDO("mysql:host=$server;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully"; // Debugging message
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

?>