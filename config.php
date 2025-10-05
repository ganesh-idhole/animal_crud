<?php
$servername = "localhost:3307"; // server name
$username = "root";      // default XAMPP username
$password = "";          // default XAMPP password
$dbname = "pixel6";      // db name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . $conn->connect_error);
}
?>