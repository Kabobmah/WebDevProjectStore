<?php

$conn = new mysqli("sql209.infinityfree.com", "if0_42139428", "0WORKYA29k", "if0_42139428_webprojectstore");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['lang'])) {
    $allowed_langs = ['ru', 'en']; 
    if (in_array($_GET['lang'], $allowed_langs)) {
        $_SESSION['lang'] = $_GET['lang'];
    }
}

$current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ru';

$lang_file = dirname(__DIR__) . '/lang.json'; 

if (file_exists($lang_file)) {
    $json_data = file_get_contents($lang_file);
    $translations = json_decode($json_data, true);
} else {
    $translations = []; 
}

if (empty($translations)) {
    echo "<!-- No json: " . htmlspecialchars($lang_file) . " -->";
}

function __($key) {
    global $translations, $current_lang;
    return $translations[$current_lang][$key] ?? $key;
}

function translate_db($row, $field) {
    global $current_lang;
    $en_field = $field . '_en';
    
    if ($current_lang === 'en' && !empty($row[$en_field])) {
        return $row[$en_field];
    }
    return $row[$field] ?? '';
}
?>