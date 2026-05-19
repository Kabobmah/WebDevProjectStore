<?php 
// Подключаем базу и логику перевода
require_once "includes/db.php"; 
?>
<!DOCTYPE html>
<html lang="<?= $current_lang ?>">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/WebDevProject/css/style.css">
    <style>
        /* Стили для переключателя, чтобы не развалилась верстка */
        .lang-switcher { display: flex; gap: 10px; margin-right: 15px; align-items: center; }
        .lang-switcher a { text-decoration: none; color: #000; font-size: 14px; opacity: 0.6; }
        .lang-switcher a.active { opacity: 1; font-weight: bold; border-bottom: 1px solid #000; }
    </style>
    <script>
        // Передаем текущий язык и все переводы в JS
        const currentLang = '<?= $_SESSION['lang'] ?? 'ru' ?>';
        const translations = <?= file_get_contents("auth/lang.json") ?>;

        // Удобная функция для получения перевода в JS
        function _t(key) {
            return translations[currentLang][key] || key;
        }
    </script>
</head>
<body>
    <header class="navbar">
        <div class="nav-left">
            <a href="index.php" class="logo-link">
                <img src="src/aura.png" alt="Aura Logo" class="logo-img">
            </a>
            <nav class="main-menu">
                <!-- Используем функцию __() для статики из JSON -->
                <a href="new.php" class="menu-item" data-type="new"><?= __('nav_catalog') ?></a>
                <a href="#" class="menu-item" data-type="clothes"><?= __('nav_clothes') ?></a>
                <a href="#" class="menu-item" data-type="accs"><?= __('nav_accs') ?></a>
                <a href="#" class="menu-item sale" data-type="sale"><?= __('nav_sale') ?></a>
            </nav>
        </div>

        <div class="nav-right">
            <!-- Переключатель языков с сохранением GET-параметров -->
            <div class="lang-switcher">
                <?php
                // Сохраняем текущие параметры URL (например, id=2)
                $params = $_GET;
                
                // Формируем ссылку для RU
                $params['lang'] = 'ru';
                $ru_url = '?' . http_build_query($params);
                
                // Формируем ссылку для EN
                $params['lang'] = 'en';
                $en_url = '?' . http_build_query($params);
                ?>
                <a href="<?= $ru_url ?>" class="<?= $current_lang == 'ru' ? 'active' : '' ?>">RU</a>
                <a href="<?= $en_url ?>" class="<?= $current_lang == 'en' ? 'active' : '' ?>">EN</a>
            </div>

            <button class="icon-btn" onclick="openSidebar('search')"><img src="src/search.png" alt="search"></button>
            <button class="icon-btn" onclick="openSidebar('profile')"><img src="src/profile.png" alt="profile"></button>
            <button class="icon-btn" onclick="openSidebar('favorites')"><img src="src/heart.png" alt="favorites"></button>
            <button class="icon-btn" onclick="openSidebar('basket')"><img src="src/basket.png" alt="basket"></button>
        </div>

        <div id="mega-menu" class="dropdown-content">
            <div class="dropdown-inner">
                <div class="menu-column" id="menu-links"></div>
                <div class="menu-images" id="menu-previews"></div>
            </div>
        </div>
    </header>

    <div id="overlay" class="overlay"></div>
    <div id="sidebar-container" class="side-panel">
        <button id="close-sidebar" class="icon-btn" style="position:absolute; top:20px; right:20px; font-size:30px;">&times;</button>
        <div id="sidebar-content" class="sidebar-content">
            <!-- В сайдбаре тоже можно будет использовать __() в JS или PHP -->
        </div>
    </div>