<?php
session_start();
require_once '../includes/db.php';

// Устанавливаем заголовок, что возвращаем JSON
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'auth_required']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];

// ВАЖНО: Добавил p.name_en в SELECT, чтобы JS мог его прочитать
$sql = "SELECT p.id, p.name, p.name_en, p.price, p.main_image 
        FROM favorites f 
        JOIN products p ON f.product_id = p.id 
        WHERE f.user_id = $user_id";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode(['status' => 'error', 'message' => $conn->error]);
    exit;
}

$items = [];
while ($row = $result->fetch_assoc()) {
    // Приводим цену к числу для корректной работы Number().toLocaleString() в JS
    $row['price'] = (float)$row['price'];
    $items[] = $row;
}

echo json_encode([
    'status' => 'success',
    'items' => $items
]);