<?php
session_start();
require_once 'includes/db.php';

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// 1. Получаем данные пользователя
$user_query = $conn->query("SELECT full_name, email FROM users WHERE id = $user_id");
$user = $user_query->fetch_assoc();

// 2. Получаем заказы пользователя
$orders_query = $conn->query("SELECT * FROM orders WHERE user_id = $user_id ORDER BY order_date DESC");

// Проверяем, сменил ли пользователь язык
if (isset($_GET['lang'])) {
    $lang = $_GET['lang'] == 'en' ? 'en' : 'ru';
    $_SESSION['lang'] = $lang;
}

// По умолчанию ставим русский
$current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ru';

?>

<!DOCTYPE html>
<html lang="<?= $current_lang ?>">
<head>
    <meta charset="utf-8">
    <title><?= __('nav_profile') ?> | AURA</title>
    <script>
        const userIsLogged = true;
        const userRole = '<?php echo $_SESSION['role'] ?? 'user'; ?>';
    </script>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .profile-container { max-width: 1000px; margin: 120px auto; padding: 20px; display: grid; grid-template-columns: 300px 1fr; gap: 50px; }
        .user-info h2, .user-orders h2 { font-size: 14px; text-transform: uppercase; letter-spacing: 2px; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px; }
        .info-block { margin-bottom: 15px; font-size: 13px; }
        .info-block label { color: #888; display: block; margin-bottom: 5px; }
        
        .order-card { border: 1px solid #eee; padding: 20px; margin-bottom: 20px; }
        .order-header { display: flex; justify-content: space-between; margin-bottom: 15px; font-weight: bold; font-size: 13px; }
        .order-item { display: flex; justify-content: space-between; font-size: 12px; color: #555; margin-bottom: 5px; }
        .status-pending { color: orange; }
        .status-completed { color: green; }
    </style>
</head>
<body>
    <!-- --------------------------------------HEADER----------------------------------->
    <?php include 'includes/header.php'; ?>

    <!-- --------------------------------------MAIN----------------------------------->
    <div class="profile-container">
        <div class="user-info">
            <h2><?= __('profile_personal_data') ?></h2>
            <div class="info-block">
                <label><?= __('profile_label_name') ?></label>
                <span><?php echo htmlspecialchars($user['full_name']); ?></span>
            </div>
            <div class="info-block">
                <label>Email</label>
                <span><?php echo htmlspecialchars($user['email']); ?></span>
            </div>
            <a href="auth/logout.php" style="color: red; font-size: 12px; text-decoration: none;"><?= mb_strtoupper(__('btn_logout')) ?></a>
        </div>

        <div class="user-orders">
            <h2><?= __('profile_my_orders') ?></h2>
            <?php if ($orders_query->num_rows > 0): ?>
                <?php while ($order = $orders_query->fetch_assoc()): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <span><?= __('order_word') ?> #<?php echo $order['id']; ?></span>
                            <span><?php echo date('d.m.Y', strtotime($order['order_date'])); ?></span>
                        </div>
                        
                        <div style="margin-bottom: 15px;">
                            <?php
                            $order_id = $order['id'];
                            // Добавили p.name_en в запрос для корректного перевода товаров в истории
                            $items_query = $conn->query("
                                SELECT oi.quantity, p.name, p.name_en, p.price 
                                FROM order_items oi 
                                JOIN product_variants pv ON oi.product_variant_id = pv.id 
                                JOIN products p ON pv.product_id = p.id 
                                WHERE oi.order_id = $order_id
                            ");
                            while ($item = $items_query->fetch_assoc()):
                            ?>
                                <div class="order-item">
                                    <span><?= translate_db($item, 'name') ?> x<?php echo $item['quantity']; ?></span>
                                    <span><?php echo number_format($item['price'] * $item['quantity'], 0, '', ' '); ?> ₸</span>
                                </div>
                            <?php endwhile; ?>
                        </div>

                        <div class="order-header" style="border-top: 1px solid #eee; padding-top: 10px; margin-bottom: 0;">
                            <span><?= __('order_total') ?>:</span>
                            <span><?php echo number_format($order['total_amount'], 0, '', ' '); ?> ₸</span>
                        </div>
                        <div style="font-size: 11px; margin-top: 5px;" class="status-<?php echo $order['status']; ?>">
                            <?= __('order_status') ?>: <?php 
                                // Перевод статусов (если они фиксированные, можно через __())
                                echo strtoupper($order['status']); 
                            ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="color: #888;"><?= __('msg_no_orders') ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- --------------------------------------FOOTER----------------------------------->
    <?php include 'includes/footer.php'; ?>
    
</body>
</html>