<?php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'auth_required']);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'] ?? null;
$action = $_POST['action'] ?? null;

if (!$product_id || !$action) {
    echo json_encode(['status' => 'error', 'message' => 'invalid_data']);
    exit;
}

// 1. УДАЛЕНИЕ ИЗ КОРЗИНЫ (проверяем первым)
if ($action === 'remove_cart') {
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ? LIMIT 1");
    $stmt->bind_param("ii", $user_id, $product_id);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'action' => 'removed']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
    exit;
}

// 2. ИЗБРАННОЕ
if ($action === 'favorite') {
    $check = $conn->prepare("SELECT id FROM favorites WHERE user_id = ? AND product_id = ?");
    $check->bind_param("ii", $user_id, $product_id);
    $check->execute();
    
    if ($check->get_result()->num_rows > 0) {
        $del = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND product_id = ?");
        $del->bind_param("ii", $user_id, $product_id);
        $del->execute();
        echo json_encode(['status' => 'success', 'action' => 'removed']); 
    } else {
        $ins = $conn->prepare("INSERT INTO favorites (user_id, product_id) VALUES (?, ?)");
        $ins->bind_param("ii", $user_id, $product_id);
        $ins->execute();
        echo json_encode(['status' => 'success', 'action' => 'added']);
    }
    exit;
}

// 3. ДОБАВЛЕНИЕ В КОРЗИНУ
if ($action === 'cart') {
    $ins = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
    $ins->bind_param("ii", $user_id, $product_id);
    if ($ins->execute()) {
        echo json_encode(['status' => 'success', 'action' => 'added']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
    exit;
}