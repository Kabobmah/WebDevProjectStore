<?php
session_start();
require_once '../includes/db.php';

// Проверяем админа
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die('Доступ запрещен');
}

// МЕНЯЕМ $_GET['id'] на $_POST['id']
if (isset($_POST['id'])) {
    $product_id = (int)$_POST['id'];

    // Твой запрос на скрытие
    $sql = "UPDATE products SET is_deleted = 1 WHERE id = $product_id";

    if ($conn->query($sql)) {
        // ОБЯЗАТЕЛЬНО выводим success, чтобы JS его поймал
        echo 'success';
        exit();
    } else {
        echo "Ошибка базы данных: " . $conn->error;
    }
} else {
    echo "ID не получен";
}
?>