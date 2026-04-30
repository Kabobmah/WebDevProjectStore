<?php
session_start();
require_once '../includes/db.php';

// Проверяем, залогинен ли юзер
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'no_auth']);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

if ($product_id > 0) {
    // 1. Проверяем наличие
    $check = $conn->prepare("SELECT id FROM favourites WHERE user_id = ? AND product_id = ?");
    $check->bind_param("ii", $user_id, $product_id);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows > 0) {
        // 2. Если есть — удаляем
        $stmt = $conn->prepare("DELETE FROM favourites WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        echo json_encode(['status' => 'success', 'action' => 'removed']);
    } else {
        // 3. Если нет — добавляем
        $stmt = $conn->prepare("INSERT INTO favourites (user_id, product_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        echo json_encode(['status' => 'success', 'action' => 'added']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'invalid_id']);
}