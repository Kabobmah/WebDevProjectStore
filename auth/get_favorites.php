<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error']); 
    exit;
}

$user_id = $_SESSION['user_id'];

// ДОБАВЛЕНО p.name_en В ВЫБОРКУ ИЗ БАЗЫ
$sql = "SELECT p.id, p.name, p.name_en, p.price, p.main_image 
        FROM favorites f 
        JOIN products p ON f.product_id = p.id 
        WHERE f.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
while ($row = $result->fetch_assoc()) { 
    $items[] = $row; 
}

echo json_encode(['status' => 'success', 'items' => $items]);