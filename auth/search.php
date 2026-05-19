<?php
require_once '../includes/db.php';

header('Content-Type: application/json');

$query = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';

if (empty($query)) {
    echo json_encode(['status' => 'success', 'items' => []]);
    exit;
}

// Ищем товары и по русскому, и по английскому названию
// Добавили name_en в SELECT, чтобы JS мог его отобразить
$sql = "SELECT id, name, name_en, price, main_image 
        FROM products 
        WHERE name LIKE '%$query%' OR name_en LIKE '%$query%' 
        LIMIT 10";

$result = $conn->query($sql);
$items = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $row['price'] = (float)$row['price'];
        $items[] = $row;
    }
}

echo json_encode(['status' => 'success', 'items' => $items]);