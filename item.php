<?php
session_start();
require_once 'includes/db.php';

$userIsLogged = isset($_SESSION['user_id']) ? 'true' : 'false';
$userRole = $_SESSION['role'] ?? 'user';
$user_favs = [];

$id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

$sql = "SELECT p.*, c.name as cat_name, c.name_en as cat_name_en FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.id = $id";

$result = $conn->query($sql);
$product = $result->fetch_assoc();

if (!$product) { 
    die("Товар не найден."); 
}

$isDeleted = (int)$product['is_deleted'];
?>

<!DOCTYPE html>
<html lang="<?= $current_lang ?>">
<head>
    <meta charset="utf-8">
    <title><?= translate_db($product, 'name') ?> | AURA</title>
    <link rel="stylesheet" href="css/style.css">
    <script>
        const userIsLogged = <?php echo $userIsLogged; ?>;
        const userRole = '<?php echo $userRole; ?>';
    </script>

    <style>
        .product-page-layout { display: flex; min-height: 100vh; }
        .product-visual { flex: 1.2; }
        .product-visual img { width: 100%; height: auto; display: block; }
        .product-sidebar { 
            flex: 0.8; padding: 100px 60px; 
            position: sticky; top: 0; height: 100vh; box-sizing: border-box; 
        }
        .breadcrumb { font-size: 10px; letter-spacing: 1px; color: #999; text-transform: uppercase; margin-bottom: 20px; }
        .breadcrumb a { color: #999; text-decoration: none; }
        .item-name { font-size: 24px; font-weight: 300; margin-bottom: 15px; }
        .item-price { font-size: 18px; margin-bottom: 30px; }
        .item-description { font-size: 13px; line-height: 1.7; color: #444; border-top: 1px solid #eee; padding-top: 20px; }
        .add-btn { 
            width: 100%; padding: 20px; background: #000; color: #fff; border: none; 
            cursor: pointer; letter-spacing: 2px; font-size: 11px; margin-top: auto;
        }
    </style>
    
</head>
<body>





<?php



if ($isDeleted === 1 && $userRole !== 'admin') {
    include 'includes/header.php';
    ?>
    <div style="text-align: center; padding: 150px 20px; font-family: sans-serif; background: #fff; min-height: 50vh;">
        <!-- Используем ключи из JSON для сообщения об удалении -->
        <h1 style="font-weight: 300; letter-spacing: 2px; color: #000;"><?= __('msg_not_available') ?></h1>
        <p style="color: #888; margin-top: 20px; font-size: 14px;"><?= __('msg_sold_out') ?></p>
        <a href="new.php" style="display: inline-block; margin-top: 30px; color: #000; text-decoration: underline; font-size: 12px; letter-spacing: 1px;"><?= __('back_to_catalog') ?></a>
    </div>
    <?php
    include 'includes/footer.php';
    exit; 
}

if (isset($_GET['lang'])) {
    $lang = $_GET['lang'] == 'en' ? 'en' : 'ru';
    $_SESSION['lang'] = $lang;
}

$current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ru';
?>




<!-- --------------------------------------HEADER----------------------------------->
<?php include 'includes/header.php'; ?>

<!-- --------------------------------------MAIN----------------------------------->
<div class="product-page-layout">
    <div class="product-visual">
            <img src="src/<?php echo $product['main_image']; ?>">
    </div>

    <div class="product-sidebar">
            <div class="breadcrumb">
                <a href="index.php"><?= __('nav_home') ?></a> / 
                <?php 
                    $cat_data = ['name' => $product['cat_name'], 'name_en' => $product['cat_name_en']];
                    echo translate_db($cat_data, 'name'); 
                ?>
            </div>
            
            <h1 class="item-name"><?= translate_db($product, 'name') ?></h1>
            <div class="item-price"><?php echo number_format($product['price'], 0, '', ' '); ?> ₸</div>
            
            <div class="item-description">
                <?= nl2br(translate_db($product, 'description')) ?>
            </div>

            <div class="product-actions" style="display: flex; gap: 10px; align-items: center; margin-top: 30px;">
                <button class="add-btn" onclick="addToCart(<?php echo $product['id']; ?>)" style="flex: 1;">
                    <?= __('btn_add_to_cart') ?>
                </button>
    
                <?php
                    $isFav = false;
                if (isset($_SESSION['user_id'])) {
                    $u_id = (int)$_SESSION['user_id'];
                    $p_id = (int)$product['id'];
                    $check = $conn->query("SHOW TABLES LIKE 'favorites'");
                    if ($check && $check->num_rows > 0) {
                    $favRes = $conn->query("SELECT id FROM favorites WHERE user_id = $u_id AND         product_id = $p_id");
                    if ($favRes && $favRes->num_rows > 0) $isFav = true;
                    }
                }
                ?>
    
                <button class="favorite-btn" onclick="toggleAction(<?php echo $product['id']; ?>, 'favorite', this)">
                    <img src="src/<?php echo $isFav ? 'heart-filled.png' : 'heart.png'; ?>" alt="fav" style="width: 45px; height: px; <?php echo $isFav ? '' : 'filter: brightness(0);'; ?>">
                </button>
            </div>
    </div>
</div>
<!-- --------------------------------------FOOTER----------------------------------->
<?php include 'includes/footer.php'; ?>

    <script src="js/main.js"></script>

</body>
</html>