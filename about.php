<?php
session_start();
require_once 'includes/db.php';

// Проверка авторизации для корректной работы JS
$userIsLogged = isset($_SESSION['user_id']) ? 'true' : 'false';
$userRole = $_SESSION['role'] ?? 'user';

// Переменная $current_lang уже определена в db.php, 
// поэтому здесь мы её просто используем для тега <html>
?>
<!DOCTYPE html>
<html lang="<?= $current_lang ?>">
<head>
    <meta charset="utf-8">
    <title><?= __('about_title') ?> | AURA</title>
    <link rel="stylesheet" href="css/style.css">
    <script>
        const userIsLogged = <?php echo $userIsLogged; ?>;
        const userRole = '<?php echo $userRole; ?>';
    </script>
    <style>
        /* Твои стили без изменений */
        .about-page { padding-top: 100px; background: #fff; }
        .about-hero { height: 60vh; display: flex; align-items: center; justify-content: center; text-align: center; flex-direction: column; border-bottom: 1px solid #eee; }
        .about-hero h1 { font-size: 40px; font-weight: 300; letter-spacing: 15px; text-transform: uppercase; margin-bottom: 20px; }
        .about-section { max-width: 1200px; margin: 0 auto; padding: 100px 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center; }
        .about-text h2 { font-size: 22px; font-weight: 400; margin-bottom: 25px; letter-spacing: 2px; text-transform: uppercase; }
        .about-text p { font-size: 14px; line-height: 1.8; color: #444; margin-bottom: 15px; text-align: justify; }
        .about-image img { width: 100%; height: auto; display: block; filter: grayscale(100%); transition: 0.5s; }
        .about-image img:hover { filter: grayscale(0%); }
        .philosophy { text-align: center; padding: 120px 20px; background: #fdfdfd; }
        .philosophy blockquote { font-size: 26px; font-style: italic; max-width: 800px; margin: 0 auto; line-height: 1.5; font-weight: 300; }
    </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="about-page">
    <section class="about-hero">
        <h1>AURA</h1>
        <p style="letter-spacing: 4px; color: #999; text-transform: uppercase; font-size: 10px;">Est. 2026</p>
    </section>

    <section class="about-section">
        <div class="about-image">
            <img src="src/about1.jpg" alt="Aura Concept">
        </div>
        <div class="about-text">
            <h2><?= __('about_section1_h2') ?></h2>
            <p><?= __('about_section1_p1') ?></p>
            <p><?= __('about_section1_p2') ?></p>
        </div>
    </section>

    <section class="about-section" style="direction: rtl;">
        <div class="about-image" style="direction: ltr;">
            <img src="src/about2.jpg" alt="Aura Quality">
        </div>
        <div class="about-text" style="direction: ltr;">
            <h2><?= __('about_section2_h2') ?></h2>
            <p><?= __('about_section2_p1') ?></p>
            <p><?= __('about_section2_p2') ?></p>
        </div>
    </section>

    <section class="philosophy">
        <blockquote><?= __('about_quote') ?></blockquote>
    </section>
</main>

<?php include 'includes/footer.php'; ?>

<script src="js/main.js"></script>
</body>
</html>