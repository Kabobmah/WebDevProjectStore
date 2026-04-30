<?php
require_once '../includes/db.php';

$query = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';

if (empty($query)) {
    echo json_encode(['status' => 'success', 'items' => []]);
    exit;
}

// Ищем товары по названию
$sql = "SELECT id, name, price, main_image FROM products 
        WHERE name LIKE '%$query%' 
        LIMIT 10";

$result = $conn->query($sql);
$items = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
}

echo json_encode(['status' => 'success', 'items' => $items]);