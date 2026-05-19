<?php
require_once '../includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // 1. Подготавливаем запрос (выбираем только нужные поля)
    $stmt = $conn->prepare("SELECT id, full_name, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // 2. Проверяем существование пользователя и пароль
    if ($user && password_verify($password, $user['password'])) {
        // Регенерируем ID сессии (защита от фиксации сессии, хороший тон)
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role']; 

        // 3. Редирект на главную
        header("Location: ../index.php");
        exit; // Всегда пишем exit после header
    } else {
        // Лучше не умирать через die, а возвращать ошибку, 
        // но для простоты оставим понятный текст
        die("Неверный логин или пароль");
    }
} else {
    // Если кто-то зашел на файл напрямую — кидаем на главную
    header("Location: ../index.php");
    exit;
}