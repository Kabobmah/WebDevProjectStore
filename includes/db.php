<?php
// C:\xampp\htdocs\store\includes\db.php
$conn = new mysqli("localhost", "root", "", "store");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>