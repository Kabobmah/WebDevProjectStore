<?php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'auth_required']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];

$sql = "SELECT p.id, p.name, p.name_en, p.price, p.main_image 
        FROM favorites f 
        JOIN products p ON f.product_id = p.id 
        WHERE f.user_id = $user_id" AND p.is_deleted = "0" ;

$result = $conn->query($sql);

if (!$result) {
    echo json_encode(['status' => 'error', 'message' => $conn->error]);
    exit;
}

$items = [];
while ($row = $result->fetch_assoc()) {
    $row['price'] = (float)$row['price'];
    $items[] = $row;
}

echo json_encode([
    'status' => 'success',
    'items' => $items
]);