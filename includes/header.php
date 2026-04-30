
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/store/css/style.css">
</head>
<body>
    <header class="navbar">
        <div class="nav-left">
            <a href="index.php" class="logo-link">
                <img src="src/aura.png" alt="Aura Logo" class="logo-img">
            </a>
            <nav class="main-menu">
                <a href="new.php" class="menu-item" data-type="new">Каталог</a>
                <a href="#" class="menu-item" data-type="clothes">Одежда</a>
                <a href="#" class="menu-item" data-type="accs">Аксессуары</a>
                <a href="#" class="menu-item sale" data-type="sale">Скидки</a>
            </nav>
        </div>
        <div class="nav-right">
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
            </div>
    </div>