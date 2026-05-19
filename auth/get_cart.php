<?php
session_start();
require_once '../includes/db.php';

// Устанавливаем заголовок JSON
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'auth_required']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];

// ВАЖНО: Добавил p.name_en в SELECT
$sql = "SELECT c.quantity, p.id, p.name, p.name_en, p.price, p.main_image 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = $user_id";

$result = $conn->query($sql);
$items = [];
$totalPrice = 0;

if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Приводим к числам для точности расчетов
        $price = (float)$row['price'];
        $qty = (int)$row['quantity'];
        
        $row['price'] = $price;
        $row['subtotal'] = $price * $qty;
        $totalPrice += $row['subtotal'];
        
        $items[] = $row;
    }
}

echo json_encode([
    'status' => 'success',
    'items' => $items,
    'totalPrice' => $totalPrice
]);