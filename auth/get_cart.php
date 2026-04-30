<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'auth_required']);
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT c.quantity, p.id, p.name, p.price, p.main_image 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = $user_id";

$result = $conn->query($sql);
$items = [];
$totalPrice = 0;

while ($row = $result->fetch_assoc()) {
    $row['subtotal'] = $row['price'] * $row['quantity'];
    $totalPrice += $row['subtotal'];
    $items[] = $row;
}

echo json_encode([
    'status' => 'success',
    'items' => $items,
    'totalPrice' => $totalPrice
]);