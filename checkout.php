<?php
session_start();
require_once 'includes/db.php';

$userIsLogged = isset($_SESSION['user_id']) ? 'true' : 'false';
$userRole = $_SESSION['role'] ?? 'user';

$user_id = $_SESSION['user_id'];

$user_favs = [];
if (isset($_SESSION['user_id'])) {
    $uid = (int)$_SESSION['user_id'];
    $fav_query = $conn->query("SELECT product_id FROM favorites WHERE user_id = $uid");
    while ($f = $fav_query->fetch_assoc()) {
        $user_favs[] = $f['product_id'];
    }
}

$sql = "SELECT p.name, p.name_en, p.price, c.quantity 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = $user_id";
$result = $conn->query($sql);
$total = 0;
$items = [];

while ($row = $result->fetch_assoc()) {
    $items[] = $row;
    $total += $row['price'] * $row['quantity'];
}

if ($total == 0) { 
    echo __('cart_empty_msg'); 
    exit; 
}

if (isset($_GET['lang'])) {
    $lang = $_GET['lang'] == 'en' ? 'en' : 'ru';
    $_SESSION['lang'] = $lang;
}

$current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ru';
?>

<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="utf-8">
    <title><?php echo __('checkout_title'); ?> | AURA</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .checkout-container { max-width: 1000px; margin: 150px auto; display: grid; grid-template-columns: 1fr 350px; gap: 50px; padding: 0 20px; }
        .checkout-form h2 { font-size: 18px; letter-spacing: 2px; margin-bottom: 30px; text-transform: uppercase; }
        .input-box { margin-bottom: 20px; }
        .input-box label { display: block; font-size: 11px; text-transform: uppercase; margin-bottom: 5px; color: #666; }
        .input-box input { width: 100%; padding: 12px; border: 1px solid #eee; outline: none; transition: 0.3s; }
        .input-box input:focus { border-color: #000; }
        .order-summary { background: #f9f9f9; padding: 30px; height: fit-content; }
        .summary-item { display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 13px; }
        .pay-btn { width: 100%; background: #000; color: #fff; border: none; padding: 15px; cursor: pointer; letter-spacing: 2px; margin-top: 20px; transition: 0.4s; }
        .add-btn { width: 100%; background: #000; color: #fff; border: none; padding: 15px; cursor: pointer; letter-spacing: 2px; margin-top: 20px; transition: 0.4s; }
        .pay-btn:hover { opacity: 0.8; }
    </style>
    <script>
        const userIsLogged = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
        const userRole = '<?php echo $_SESSION['role'] ?? 'user'; ?>';
        const currentLang = '<?php echo $_SESSION['lang'] ?? 'ru'; ?>';
    </script>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="checkout-container">
        <div class="checkout-form">
            <h2><?php echo __('shipping_details'); ?></h2>
            <form action="auth/place_order.php" method="POST">
                <div class="input-box">
                    <label><?php echo __('shipping_address'); ?></label>
                    <input type="text" name="address" placeholder="<?php echo __('address_placeholder'); ?>" required>
                </div>
                <div class="input-box">
                    <label><?php echo __('phone_number'); ?></label>
                    <input type="text" name="phone" placeholder="+7 (___) ___-__-__" required>
                </div>
                
                <h2 style="margin-top: 40px;"><?php echo __('payment_method'); ?></h2>
                <div style="display: flex; gap: 20px; margin-bottom: 30px;">
                    <label><input type="radio" name="payment" value="card" checked> <?php echo __('payment_card'); ?></label>
                    <label><input type="radio" name="payment" value="cash"> <?php echo __('payment_cash'); ?></label>
                </div>

                <button type="submit" class="pay-btn"><?php echo __('btn_place_order'); ?></button>
            </form>
        </div>

        <div class="order-summary">
            <h2 style="font-size: 14px; margin-bottom: 20px;"><?php echo __('your_order'); ?></h2>
            <?php foreach ($items as $item): ?>
                <div class="summary-item">
                    <!-- Перевод названия товара в списке заказа -->
                    <span><?php echo translate_db($item, 'name'); ?> (x<?php echo $item['quantity']; ?>)</span>
                    <span><?php echo number_format($item['price'] * $item['quantity'], 0, '', ' '); ?> ₸</span>
                </div>
            <?php endforeach; ?>
            <hr style="border: none; border-top: 1px solid #ddd; margin: 20px 0;">
            <div class="summary-item" style="font-weight: bold; font-size: 16px;">
                <span><?php echo __('total_label'); ?>:</span>
                <span><?php echo number_format($total, 0, '', ' '); ?> ₸</span>
            </div>
        </div>
    </div>
    <script src="js/main.js"></script>
</body>
</html>