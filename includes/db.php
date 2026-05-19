<?php
// C:\xampp\htdocs\WebDevProject\includes\db.php

// 1. Твое стандартное подключение (оставляем как есть)
$conn = new mysqli("localhost", "root", "", "webprojectstore");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// --- ВОТ ЗДЕСЬ НАЧИНАЕТСЯ МАГИЯ ПЕРЕВОДА (БЕЗОПАСНО) ---

// 2. Запускаем сессию, если она еще не запущена
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 3. Определяем язык (приоритет: ссылка -> сессия -> русский по умолчанию)
if (isset($_GET['lang'])) {
    $allowed_langs = ['ru', 'en']; // На всякий случай проверяем, что нам не подсунули мусор
    if (in_array($_GET['lang'], $allowed_langs)) {
        $_SESSION['lang'] = $_GET['lang'];
    }
}

$current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ru';

// 4. Загружаем твой JSON словарь
// dirname(__DIR__) поднимает нас на один уровень вверх из папки includes в корень проекта
$lang_file = dirname(__DIR__) . '/lang.json'; 

if (file_exists($lang_file)) {
    $json_data = file_get_contents($lang_file);
    $translations = json_decode($json_data, true);
} else {
    $translations = []; 
}

// Отладка (если не работает, увидишь правильный путь в исходном коде страницы)
if (empty($translations)) {
    echo "<!-- Ошибка: JSON не загружен или пуст. Искал тут: " . htmlspecialchars($lang_file) . " -->";
}

// 5. Функция-помощник для вывода слов из JSON (статический перевод)
function __($key) {
    global $translations, $current_lang;
    return $translations[$current_lang][$key] ?? $key;
}

// 6. Мини-функция для перевода данных из БД (динамический перевод)
// Она будет сама выбирать name или name_en из таблиц categories/products
function translate_db($row, $field) {
    global $current_lang;
    $en_field = $field . '_en';
    
    if ($current_lang === 'en' && !empty($row[$en_field])) {
        return $row[$en_field];
    }
    return $row[$field] ?? '';
}
?>