<?php
$host = "sql206.infinityfree.com";
$username = "if0_38353184"; // Default XAMPP MySQL username
$password = "mfkj3NEPxUxtP4"; // Default XAMPP MySQL password (empty)
$database = "if0_38353184_vml"; // Your database name

$conn = new mysqli($host, $username, $password, $database);

// Check Connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
