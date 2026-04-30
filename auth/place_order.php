<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $user_id = (int)$_SESSION['user_id'];
    $address = $conn->real_escape_string($_POST['address']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $order_date = date('Y-m-d H:i:s');

    // 1. Сначала считаем общую сумму корзины
    $cart_query = "SELECT c.product_id, c.quantity, p.price 
                   FROM cart c 
                   JOIN products p ON c.product_id = p.id 
                   WHERE c.user_id = $user_id";
    $cart_res = $conn->query($cart_query);

    $total_amount = 0;
    $items = [];
    while ($row = $cart_res->fetch_assoc()) {
        $items[] = $row;
        $total_amount += $row['price'] * $row['quantity'];
    }

    if (count($items) === 0) {
        die("Корзина пуста");
    }

    // 2. Создаем запись в таблице orders
    // (Поля из твоего скриншота: user_id, order_date, total_amount, status)
    $sql_order = "INSERT INTO orders (user_id, order_date, total_amount, status) 
                  VALUES ($user_id, '$order_date', $total_amount, 'pending')";
    
    if ($conn->query($sql_order)) {
        $order_id = $conn->insert_id; // Получаем ID только что созданного заказа

        // 3. Переносим товары в order_items
        // ВНИМАНИЕ: На твоем скриншоте в order_items поле 'product_variant_id'. 
        // Если у тебя пока нет вариантов, используем product_id из корзины.
        // Находим любой существующий вариант для этого товара, чтобы БД не ругалась
        foreach ($items as $item) {
            $p_id = $item['product_id'];
            $qty = $item['quantity'];

            // Ищем ID варианта для этого товара
            $variant_query = $conn->query("SELECT id FROM product_variants WHERE product_id = $p_id LIMIT 1");
            $variant = $variant_query->fetch_assoc();

            if ($variant) {
                $v_id = $variant['id'];
                $sql_item = "INSERT INTO order_items (order_id, product_variant_id, quantity) 
                             VALUES ($order_id, $v_id, $qty)";
                $conn->query($sql_item);
            } else {
                // Если вариантов вообще нет в базе — будет ошибка. 
                // В этом случае лучше использовать Способ 1 (удалить связь).
                die("Ошибка: У товара ID $p_id нет ни одного варианта в таблице product_variants!");
            }
        }

        // 4. Очищаем корзину
        $conn->query("DELETE FROM cart WHERE user_id = $user_id");

        echo "<script>
                alert('Заказ #$order_id успешно оформлен!');
                window.location.href = '../index.php';
              </script>";
    } else {
        echo "Ошибка при создании заказа: " . $conn->error;
    }
}