<?php
session_start();
require_once 'includes/db.php';

// Простая функция перевода для PHP (аналог той, что в JS)
function get_lang_text($item, $field) {
    $current_lang = $_SESSION['lang'] ?? 'ru';
    if ($current_lang === 'en' && !empty($item[$field . '_en'])) {
        return $item[$field . '_en'];
    }
    return $item[$field];
}

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Получаем товары и ОБЯЗАТЕЛЬНО тянем name_en
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
    header("Location: index.php"); // Если пустая корзина, лучше просто кинуть на главную
    exit;
}

$current_lang = $_SESSION['lang'] ?? 'ru';
?>

<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="utf-8">
    <title>CHECKOUT | AURA</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Твои стили */
        .checkout-container { max-width: 1000px; margin: 150px auto; display: grid; grid-template-columns: 1fr 350px; gap: 50px; padding: 0 20px; }
        .checkout-form h2 { font-size: 18px; letter-spacing: 2px; margin-bottom: 30px; text-transform: uppercase; }
        .input-box { margin-bottom: 20px; }
        .input-box label { display: block; font-size: 11px; text-transform: uppercase; margin-bottom: 5px; color: #666; }
        .input-box input { width: 100%; padding: 12px; border: 1px solid #eee; outline: none; transition: 0.3s; }
        .input-box input:focus { border-color: #000; }
        .order-summary { background: #f9f9f9; padding: 30px; height: fit-content; }
        .summary-item { display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 13px; }
        .pay-btn { width: 100%; background: #000; color: #fff; border: none; padding: 15px; cursor: pointer; letter-spacing: 2px; margin-top: 20px; transition: 0.4s; }
        .pay-btn:hover { opacity: 0.8; }
        
        /* Фикс для того, чтобы сайдбар не ломал верстку */
        #sidebar-container { z-index: 10000; }
        #overlay { z-index: 9999; }
    </style>
</head>
<body>
    
    <?php include 'includes/header.php'; ?>

    <!-- КРИТИЧНО: Добавляем разметку сайдбара, иначе JS выдаст ошибку -->
    <div id="overlay"></div>
    <div id="sidebar-container">
        <div class="sidebar-header">
            <button id="close-sidebar">✕</button>
        </div>
        <div id="sidebar-content"></div>
    </div>

    <div class="checkout-container">
        <div class="checkout-form">
            <h2>Shipping Details</h2> <!-- Тут используй свои функции перевода -->
            <form action="auth/place_order.php" method="POST">
                <div class="input-box">
                    <label>Address</label>
                    <input type="text" name="address" required>
                </div>
                <div class="input-box">
                    <label>Phone</label>
                    <input type="text" name="phone" placeholder="+7 (___) ___-__-__" required>
                </div>
                
                <h2 style="margin-top: 40px;">Payment</h2>
                <div style="display: flex; gap: 20px; margin-bottom: 30px;">
                    <label><input type="radio" name="payment" value="card" checked> Card</label>
                    <label><input type="radio" name="payment" value="cash"> Cash</label>
                </div>

                <button type="submit" class="pay-btn">PLACE ORDER</button>
            </form>
        </div>

        <div class="order-summary">
            <h2 style="font-size: 14px; margin-bottom: 20px;">Your Order</h2>
            <?php foreach ($items as $item): ?>
                <div class="summary-item">
                    <!-- Используем нашу функцию для перевода названия -->
                    <span><?php echo get_lang_text($item, 'name'); ?> (x<?php echo $item['quantity']; ?>)</span>
                    <span><?php echo number_format($item['price'] * $item['quantity'], 0, '', ' '); ?> ₸</span>
                </div>
            <?php endforeach; ?>
            <hr style="border: none; border-top: 1px solid #ddd; margin: 20px 0;">
            <div class="summary-item" style="font-weight: bold; font-size: 16px;">
                <span>Total:</span>
                <span><?php echo number_format($total, 0, '', ' '); ?> ₸</span>
            </div>
        </div>
    </div>

    <!-- КРИТИЧНО: Передаем данные из PHP в JS и подключаем сам main.js -->
    <script>
        const userIsLogged = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
        const userRole = '<?php echo $_SESSION['role'] ?? 'user'; ?>';
    </script>
    <script src="js/main.js"></script>
</body>
</html>