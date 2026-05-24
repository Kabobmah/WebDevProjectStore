<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'auth_required']);
    exit;
}

$p_id = (int)$_POST['product_id'];
$u_id = $_SESSION['user_id'];

$sql = "INSERT INTO cart (user_id, product_id, quantity) 
        VALUES ($u_id, $p_id, 1) 
        ON DUPLICATE KEY UPDATE quantity = quantity + 1";

if ($conn->query($sql)) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => $conn->error]);
}