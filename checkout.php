<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Получаем товары в корзине для итоговой суммы
$sql = "SELECT p.name, p.price, c.quantity 
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

if ($total == 0) { echo "Корзина пуста"; exit; }
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Оформление заказа | AURA</title>
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
        .pay-btn:hover { opacity: 0.8; }
    </style>
</head>
<body>
    <header class="navbar">
        <a href="index.php" class="logo-link"><img src="src/aura.png" alt="Logo" class="logo-img"></a>
    </header>

    <div class="checkout-container">
        <div class="checkout-form">
            <h2>Данные доставки</h2>
            <form action="auth/place_order.php" method="POST">
                <div class="input-box">
                    <label>Адрес доставки</label>
                    <input type="text" name="address" placeholder="Город, улица, дом, квартира" required>
                </div>
                <div class="input-box">
                    <label>Номер телефона</label>
                    <input type="text" name="phone" placeholder="+7 (___) ___-__-__" required>
                </div>
                
                <h2 style="margin-top: 40px;">Способ оплаты</h2>
                <div style="display: flex; gap: 20px; margin-bottom: 30px;">
                    <label><input type="radio" name="payment" value="card" checked> Карта</label>
                    <label><input type="radio" name="payment" value="cash"> При получении</label>
                </div>

                <button type="submit" class="pay-btn">ОПЛАТИТЬ И ОФОРМИТЬ</button>
            </form>
        </div>

        <div class="order-summary">
            <h2 style="font-size: 14px; margin-bottom: 20px;">Ваш заказ</h2>
            <?php foreach ($items as $item): ?>
                <div class="summary-item">
                    <span><?php echo $item['name']; ?> (x<?php echo $item['quantity']; ?>)</span>
                    <span><?php echo number_format($item['price'] * $item['quantity'], 0, '', ' '); ?> ₸</span>
                </div>
            <?php endforeach; ?>
            <hr style="border: none; border-top: 1px solid #ddd; margin: 20px 0;">
            <div class="summary-item" style="font-weight: bold; font-size: 16px;">
                <span>ИТОГО:</span>
                <span><?php echo number_format($total, 0, '', ' '); ?> ₸</span>
            </div>
        </div>
    </div>
</body>
</html>