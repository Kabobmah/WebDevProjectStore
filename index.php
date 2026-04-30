<?php 
session_start(); 
require_once 'includes/db.php';

// Проверка авторизации для JS
$userIsLogged = isset($_SESSION['user_id']) ? 'true' : 'false';
$userRole = $_SESSION['role'] ?? 'user';


//-----------------------------------------------------------------

$user_favs = [];
if (isset($_SESSION['user_id'])) {
    $uid = (int)$_SESSION['user_id'];
    $fav_query = $conn->query("SELECT product_id FROM favorites WHERE user_id = $uid");
    while ($f = $fav_query->fetch_assoc()) {
        $user_favs[] = $f['product_id'];
    }
}





//-----------------------------------------------------------------





?>








<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Aura Store</title>
    <link rel="stylesheet" href="css/style.css">
    <script>
        // Передаем статус из PHP в JS
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
<body>

   
<!-- --------------------------------------HEADER----------------------------------->
<?php include 'includes/header.php'; ?>

<!-- --------------------------------------MAIN----------------------------------->

    <main class="hero-section" style="background-image: url('src/f.jpg');">
        <div class="hero-caption">
            <h1>Призрачная красота</h1>
        </div>
    </main>
    <section class="hero-section second-hero" style="background-image: url('src/7foto.jpg');">
            <div class="hero-caption">
                <h1>Новая коллекция</h1> 
            </div>
        </section>
<!-- --------------------------------------FOOTER----------------------------------->
<?php include 'includes/footer.php'; ?>

    <script src="js/main.js"></script>
</body>
</html>