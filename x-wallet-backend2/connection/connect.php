<?php
$host = "localhost"; // Change if needed
$user = "root"; // Change if needed
$pass = ""; // Change if needed
$dbname = "x-wallet"; // Replace with your actual database name

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

?>
