<?php 
session_start(); 
require_once 'includes/db.php'; 

// Проверка авторизации для JS
$userIsLogged = isset($_SESSION['user_id']) ? 'true' : 'false';
$userRole = $_SESSION['role'] ?? 'user';

// Проверяем, сменил ли пользователь язык
if (isset($_GET['lang'])) {
    $lang = $_GET['lang'] == 'en' ? 'en' : 'ru';
    $_SESSION['lang'] = $lang;
}

// По умолчанию ставим русский
$current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ru';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 1;
//-----------------------------------------------------------------

$user_favs = [];
if (isset($_SESSION['user_id'])) {
    $uid = (int)$_SESSION['user_id'];
    $fav_query = $conn->query("SELECT product_id FROM favorites WHERE user_id = $uid");
    
    // Проверяем, что запрос прошел успешно и вернул данные
    if ($fav_query && $fav_query->num_rows > 0) {
        while ($f = $fav_query->fetch_assoc()) {
            $user_favs[] = $f['product_id'];
        }
    }
}

//-----------------------------------------------------------------
$sql = "SELECT p.*, c.name as cat_name FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.is_deleted = 0 
        ORDER BY p.id DESC";
$result = $conn->query($sql);

?>








<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Каталог | AURA</title>
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body style="background: #fff;">
<!-- --------------------------------------HEADER----------------------------------->
<?php include 'includes/header.php'; ?>

<!-- --------------------------------------MAIN----------------------------------->
<main class="catalog-container" style="padding-top: 140px;">
    <div style="text-align:center; margin-bottom: 40px;">
        <h1 style="font-size: 20px; font-weight: normal; letter-spacing: 2px;">КАТАЛОГ</h1>
    </div>

    <div class="product-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; padding: 0 40px;">
        <?php while($row = $result->fetch_assoc()): ?>
            <?php 
                $is_fav = in_array($row['id'], $user_favs); 
                $img_src = $is_fav ? 'heart-filled.png' : 'heart.png';
            ?>
            <div class="product-card-wrapper" style="position: relative;">
                <a href="item.php?id=<?php echo $row['id']; ?>" class="product-card" style="text-decoration:none; color:#000; display: block;">
                    <div class="product-image-wrapper">
                        <img src="src/<?php echo $row['main_image']; ?>">
                    </div>
                    
                    <div style="text-align:center; padding:15px;">
                        <div style="font-size:11px; text-transform:uppercase; margin-bottom:5px;"><?php echo $row['name']; ?></div>
                        <div style="font-weight:500;"><?php echo number_format($row['price'], 0, '', ' '); ?> ₸</div>
                    </div>
                </a>

                <div class="product-actions" style="position: absolute; top: 10px; right: 10px; display: flex; flex-direction: column; gap: 10px;">
                    <button class="icon-btn action-trigger" onclick="toggleAction(<?php echo $row['id']; ?>, 'favorite', this)" style=" color:'black'; background: rgba(100,100,100 ,0.8); border-radius: 50%; padding: 5px; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer;">
                        <img src="src/<?php echo $img_src; ?>" alt="heart" class="heart-icon" style="width: 20px; height: 20px; <?php echo $is_fav ? '' : 'filter: brightness(0);'; ?>">
                    </button>
                    <?php if ($userRole === 'admin'): ?>
                        <button onclick="deleteProduct(<?php echo $row['id']; ?>)" style="background: rgba(255, 0, 0, 0.7); border-radius: 50%; padding: 5px; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer; color: #fff; font-size: 18px; font-weight: bold;">
                                &times;
                        </button>
                    <?php endif; ?>
                    <button class="icon-btn action-trigger" onclick="toggleAction(<?php echo $row['id']; ?>, 'cart',this)" style=" color:'black'; background: rgba(100,100,100 ,0.8); border-radius: 50%; padding: 5px; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer;">
                        <img src="src/basket.png" alt="basket" style="width: 20px; height: 20px;">
                    </button>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</main>
<!-- --------------------------------------FOOTER----------------------------------->

<?php include 'includes/footer.php'; ?>

    <script src="js/main.js"></script>
</body> 
</html>