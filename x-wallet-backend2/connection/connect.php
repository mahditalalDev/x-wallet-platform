<?php
$host = "http://15.236.225.13/";
$user = "root"; 
$pass = "mahditalaldev"; 
$dbname = "x-wallet"; 

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

?>
