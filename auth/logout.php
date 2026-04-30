<?php
// Запускаем сессию, чтобы иметь доступ к текущим данным
session_start();

// Полностью очищаем массив $_SESSION
$_SESSION = array();

// Если используются куки сессии, удаляем их (для полной безопасности)
if (ini_get("session_use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Уничтожаем саму сессию на сервере
session_destroy();

// Перенаправляем пользователя на главную страницу (выходим из папки auth на уровень вверх)
header("Location: ../index.php");
exit();
?>