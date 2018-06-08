<?php
$servername = "localhost";
$username = getenv('DB_USER_NAME');
$password = getenv('DB_USER_PASS');

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
echo "Connected successfully\n";

$conn->close();
?>
