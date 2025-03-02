<?php
$host = "localhost"; // Change if needed
$user = "root"; // Change if needed
$pass = ""; // Change if needed
$dbname = "x-wallet"; // Replace with your actual database name

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

// // Check if the database exists
// $db_check_sql = "SHOW DATABASES LIKE '$dbname'";
// $result = $conn->query($db_check_sql);

// if ($result->num_rows == 0) {
//     // Database does not exist, create it
//     $create_db_sql = "CREATE DATABASE `$dbname`";
//     if ($conn->query($create_db_sql) === TRUE) {
//         echo json_encode(["success" => "Database '$dbname' created successfully."]);
//     } else {
//         die(json_encode(["error" => "Error creating database: " . $conn->error]));
//     }
// } else {
//     echo json_encode(["success" => "Database '$dbname' already exists."]);
// }

// // Now, select the database for further use
// $conn->select_db($dbname);
?>
