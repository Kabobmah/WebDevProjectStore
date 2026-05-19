<?php
session_start();
require_once 'includes/db.php';

// Проверка: пускаем только админа
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Доступ запрещен. Вы не админ.");
}

// Получаем категории
$categories = $conn->query("SELECT * FROM categories");

// Язык здесь берем из db.php через $current_lang
?>
<!DOCTYPE html>
<html lang="<?= $current_lang ?>">
<head>
    <meta charset="utf-8">
    <title><?= __('admin_panel_title') ?> | AURA</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .admin-container { padding: 120px 40px; max-width: 800px; margin: 0 auto; color: #000; background: #fff; }
        .admin-form { display: flex; flex-direction: column; gap: 20px; }
        .admin-form input, .admin-form textarea, .admin-form select {
            padding: 15px; border: 1px solid #eee; width: 100%; font-family: inherit;
        }
        .admin-btn { background: #000; color: #fff; padding: 20px; border: none; cursor: pointer; text-transform: uppercase; letter-spacing: 2px; }
        .lang-group { border-left: 3px solid #eee; padding-left: 20px; margin-bottom: 10px; }
        .lang-label { font-size: 10px; color: #999; text-transform: uppercase; margin-bottom: 5px; display: block; }
    </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="admin-container">
    <h1><?= __('admin_add_product') ?></h1>
    <form action="auth/add_product.php" method="POST" enctype="multipart/form-data" class="admin-form">
        
        <!-- Выбор категории -->
        <select name="category_id">
            <?php while($cat = $categories->fetch_assoc()): ?>
                <option value="<?= $cat['id'] ?>"><?= translate_db($cat, 'name') ?></option>
            <?php endwhile; ?>
        </select>

        <!-- Русская версия -->
        <div class="lang-group">
            <span class="lang-label">RU Version</span>
            <input type="text" name="name" placeholder="<?= __('lbl_name') ?> (RU)" required>
            <textarea name="description" placeholder="<?= __('lbl_description') ?> (RU)" rows="3"></textarea>
        </div>

        <!-- Английская версия -->
        <div class="lang-group">
            <span class="lang-label">EN Version</span>
            <input type="text" name="name_en" placeholder="<?= __('lbl_name') ?> (EN)">
            <textarea name="description_en" placeholder="<?= __('lbl_description') ?> (EN)" rows="3"></textarea>
        </div>

        <input type="number" name="price" placeholder="<?= __('lbl_price') ?> (₸)" required>
        
        <label><?= __('lbl_main_image') ?>:</label>
        <input type="file" name="main_image" accept="image/*" required>
        
        <h3 style="font-size: 14px; margin-top: 20px;"><?= __('admin_variant_settings') ?></h3>
        <input type="text" name="color" placeholder="<?= __('lbl_color') ?>" required>
        <input type="text" name="size" placeholder="<?= __('lbl_size') ?>" required>
        <input type="number" name="stock" placeholder="<?= __('lbl_stock') ?>" required>

        <button type="submit" class="admin-btn"><?= __('btn_publish') ?></button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>

<script src="js/main.js"></script>
</body>
</html>