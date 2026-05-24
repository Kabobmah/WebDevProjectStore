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
    die(__('itemnotfound')); 
}
$reviews_query = $conn->query("
    SELECT r.*, u.full_name as user_name 
    FROM reviews r 
    JOIN users u ON r.user_id = u.id 
    WHERE r.product_id = $id 
    ORDER BY r.created_at DESC
");
$isDeleted = (int)$product['is_deleted'];

$reviews_query = $conn->query("
    SELECT r.*, u.full_name as user_name 
    FROM reviews r 
    JOIN users u ON r.user_id = u.id 
    WHERE r.product_id = $id 
    ORDER BY r.created_at DESC
");

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
            box-sizing: border-box; 
            display: flex; flex-direction: column;
            background: #fff;
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

        .sidebar-reviews-block {
            margin-top: 60px;
            border-top: 1px solid #000;
            padding-top: 30px;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }
        .sidebar-reviews-block h2 {
            font-size: 14px;
            font-weight: 300;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 30px;
        }
        .reviews-list {
            display: flex;
            flex-direction: column;
            gap: 25px;
            margin-bottom: 40px;
        }
        .review-card {
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }
        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
        }
        .review-author {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .review-date {
            font-size: 10px;
            color: #999;
        }
        .review-rating {
            color: #000;
            font-size: 11px;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }
        .review-comment {
            font-size: 12px;
            line-height: 1.6;
            color: #444;
        }
        .review-form-container {
            background: #fafafa;
            padding: 25px;
            border: 1px solid #eee;
        }
        .review-form-container h3 {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 20px;
        }
        .review-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .review-form select, .review-form textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #eee;
            background: #fff;
            font-family: inherit;
            font-size: 12px;
            outline: none;
        }
        .review-form select:focus, .review-form textarea:focus {
            border-color: #000;
        }
        .review-submit-btn {
            background: #000;
            color: #fff;
            padding: 12px;
            border: none;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 10px;
            transition: opacity 0.3s;
        }
        .review-submit-btn:hover { opacity: 0.8; }
        .review-status-msg {
            padding: 12px;
            margin-bottom: 15px;
            font-size: 11px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .status-success { background: #e6f4ea; color: #137333; }
        .status-error { background: #fce8e6; color: #c5221f; }
        .no-reviews { color: #888; font-size: 12px; font-style: italic; }

        @media (max-width: 768px) {
            .product-page-layout { flex-direction: column; }
            .product-visual { position: relative; }
            .product-sidebar { padding: 40px 20px; }
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
            <div class="sidebar-reviews-block">
            <h2><?=__('Reviews')?></h2>

            <?php if(isset($_GET['review'])): ?>
                <?php if($_GET['review'] === 'success'): ?>
                    <div class="review-status-msg status-success"><?=__('newReview')?></div>
                <?php elseif($_GET['review'] === 'empty'): ?>
                    <div class="review-status-msg status-error"><?=__('enterReview')?></div>
                <?php else: ?>
                    <div class="review-status-msg status-error"><?=__('errorReview')?></div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="reviews-list">
                <?php if ($reviews_query && $reviews_query->num_rows > 0): ?>
                    <?php while($review = $reviews_query->fetch_assoc()): ?>
                        <div class="review-card">
                            <div class="review-header">
                                <span class="review-author"><?php echo htmlspecialchars($review['user_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                                <span class="review-date"><?php echo date('d.m.Y', strtotime($review['created_at'])); ?></span>
                            </div>
                            <div class="review-rating">
                                <?php echo str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']); ?>
                            </div>
                            <div class="review-comment">
                                <?php echo nl2br(htmlspecialchars($review['comment'], ENT_QUOTES, 'UTF-8')); ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="no-reviews"><?=__('noReviews')?></p>
                <?php endif; ?>
            </div>

            <div class="review-form-container">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <h3><?=__('leaveaReview')?></h3>
                    <form action="auth/add_review.php" method="POST" class="review-form">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        
                        <div>
                            <select name="rating" required>
                                <option value="5">5 ★★★★★ </option>
                                <option value="4">4 ★★★★☆ </option>
                                <option value="3">3 ★★★☆☆ </option>
                                <option value="2">2 ★★☆☆☆ </option>
                                <option value="1">1 ★☆☆☆☆ </option>
                            </select>
                        </div>

                        <div>
                            <textarea name="comment" rows="4" placeholder="<?= __('yourReview')?>" required></textarea>
                        </div>

                        <button type="submit" class="review-submit-btn"><?= __('sent')?></button>
                    </form>
                <?php else: ?>
                    <h3><?=__('leaveaReview')?></h3>
                    <p style="font-size: 12px; color: #777; line-height: 1.5;"><?= __('authorize')?></p>
                <?php endif; ?>
    </div>
</div>
<!-- --------------------------------------FOOTER----------------------------------->
<?php include 'includes/footer.php'; ?>

    <script src="js/main.js"></script>

</body>
</html>