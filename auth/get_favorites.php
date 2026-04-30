<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error']); exit;
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT p.id, p.name, p.price, p.main_image 
        FROM favorites f 
        JOIN products p ON f.product_id = p.id 
        WHERE f.user_id = $user_id";

$result = $conn->query($sql);
$items = [];
while ($row = $result->fetch_assoc()) { $items[] = $row; }

echo json_encode(['status' => 'success', 'items' => $items]);