<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die('Доступ запрещен');
}

if (isset($_POST['id'])) {
    $product_id = (int)$_POST['id'];

    $sql = "UPDATE products SET is_deleted = 1 WHERE id = $product_id";

    if ($conn->query($sql)) {
        echo 'success';
        exit();
    } else {
        echo "Ошибка базы данных: " . $conn->error;
    }
} else {
    echo "ID не получен";
}
?>