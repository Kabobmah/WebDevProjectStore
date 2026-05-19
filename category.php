<?php 
session_start(); 
require_once 'includes/db.php'; 

// Проверка авторизации для JS
$userIsLogged = isset($_SESSION['user_id']) ? 'true' : 'false';
$userRole = $_SESSION['role'] ?? 'user';

// 1. Получаем ID категории из URL
$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// Избранное
$user_favs = [];
if (isset($_SESSION['user_id'])) {
    $uid = (int)$_SESSION['user_id'];
    $fav_query = $conn->query("SELECT product_id FROM favorites WHERE user_id = $uid");
    
    if ($fav_query && $fav_query->num_rows > 0) {
        while ($f = $fav_query->fetch_assoc()) {
            $user_favs[] = $f['product_id'];
        }
    }
}

// 2. Получаем категорию (используем translate_db для заголовка)
$cat_name_query = $conn->query("SELECT * FROM categories WHERE id = $category_id");
$current_category = $cat_name_query->fetch_assoc();
// Используем функцию из db.php для перевода названия категории
$page_title_raw = $current_category ? translate_db($current_category, 'name') : __('nav_catalog');
$page_title = mb_strtoupper($page_title_raw);

// 3. Выбираем товары ТОЛЬКО этой категории
$sql = "SELECT p.*, c.name as cat_name, c.name_en as cat_name_en FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.category_id = $category_id AND p.is_deleted = 0 
        ORDER BY p.id DESC";
$result = $conn->query($sql);

// $current_lang уже определен в db.php
?>

<!DOCTYPE html>
<html lang="<?= $current_lang ?>">
<head>
    <meta charset="utf-8">
    <title><?php echo $page_title; ?> | AURA</title>
    <link rel="stylesheet" href="css/style.css">
    <script>
        const userIsLogged = <?php echo $userIsLogged; ?>;
        const userRole = '<?php echo $userRole; ?>';
    </script>
    <style>
        .breadcrumb { font-size: 10px; letter-spacing: 1px; color: #999; text-transform: uppercase; margin-bottom: 10px; text-align: center; }
        .breadcrumb a { color: #999; text-decoration: none; }
    </style>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body style="background: #fff;">
<?php include 'includes/header.php'; ?>

<main class="catalog-container" style="padding-top: 140px;">
    <div class="breadcrumb">
        <a href="index.php"><?= __('nav_home') ?></a> / <span><?php echo $page_title; ?></span>
    </div>

    <div style="text-align:center; margin-bottom: 40px;">
        <h1 style="font-size: 20px; font-weight: normal; letter-spacing: 2px;"><?php echo $page_title; ?></h1>
    </div>

    <div class="product-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; padding: 0 40px;">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <?php 
                    $is_fav = in_array($row['id'], $user_favs); 
                    $img_src = $is_fav ? 'heart-filled.png' : 'heart.png';
                ?>
                <div class="product-card-wrapper" style="position: relative;">
                    <a href="item.php?id=<?php echo $row['id']; ?>" class="product-card" style="text-decoration:none; color:#000; display: block;">
                        <div class="product-image-wrapper">
                            <img src="src/<?php echo $row['main_image']; ?>" style="width: 100%;">
                        </div>
                        
                        <div style="text-align:center; padding:15px;">
                            <!-- Перевод названия товара из БД -->
                            <div style="font-size:11px; text-transform:uppercase; margin-bottom:5px;">
                                <?= translate_db($row, 'name') ?>
                            </div>
                            <div style="font-weight:500;"><?php echo number_format($row['price'], 0, '', ' '); ?> ₸</div>
                        </div>
                    </a>

                    <div class="product-actions" style="position: absolute; top: 10px; right: 10px; display: flex; flex-direction: column; gap: 10px;">
                        <button class="icon-btn action-trigger" onclick="toggleAction(<?php echo $row['id']; ?>, 'favorite', this)" style="background: rgba(100,100,100 ,0.8); border-radius: 50%; padding: 5px; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer;">
                            <img src="src/<?php echo $img_src; ?>" alt="heart" class="heart-icon" style="width: 20px; height: 20px; <?php echo $is_fav ? '' : 'filter: brightness(0);'; ?>">
                        </button>
                        
                        <?php if ($userRole === 'admin'): ?>
                            <button onclick="deleteProduct(<?php echo $row['id']; ?>)" style="background: rgba(255, 0, 0, 0.7); border-radius: 50%; padding: 5px; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer; color: #fff; font-size: 18px; font-weight: bold;">
                                    &times;
                            </button>
                        <?php endif; ?>
                        
                        <button class="icon-btn action-trigger" onclick="toggleAction(<?php echo $row['id']; ?>, 'cart', this)" style="background: rgba(100,100,100 ,0.8); border-radius: 50%; padding: 5px; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer;">
                            <img src="src/basket.png" alt="basket" style="width: 20px; height: 20px;">
                        </button>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 100px; color: #999; letter-spacing: 1px;">
                <?= __('msg_no_products') ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
<script src="js/main.js"></script>
</body> 
</html>