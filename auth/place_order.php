<?php
session_start();
require_once '../includes/db.php';

$current_lang = $_SESSION['lang'] ?? 'ru';

$lang_dict = [
    'ru' => [
        'msg_success' => 'Заказ успешно оформлен!',
        'err_empty'   => 'Корзина пуста',
        'err_variant' => 'Ошибка: У товара ID %d нет ни одного варианта в таблице product_variants!',
        'err_create'  => 'Ошибка при создании заказа: '
    ],
    'en' => [
        'msg_success' => 'Order successfully placed!',
        'err_empty'   => 'Cart is empty',
        'err_variant' => 'Error: Product ID %d has no variants in product_variants table!',
        'err_create'  => 'Error creating order: '
    ]
];

function get_inline_txt($key, $lang, $dict) {
    return $dict[$lang][$key] ?? $dict['ru'][$key] ?? $key;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $user_id = (int)$_SESSION['user_id'];
    $address = $conn->real_escape_string($_POST['address']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $order_date = date('Y-m-d H:i:s');

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
        die(get_inline_txt('err_empty', $current_lang, $lang_dict));
    }

    $sql_order = "INSERT INTO orders (user_id, order_date, total_amount, address, phone, status) 
                  VALUES ($user_id, '$order_date', $total_amount, '$address', '$phone', 'pending')";
    
    if ($conn->query($sql_order)) {
        $order_id = $conn->insert_id; 

        foreach ($items as $item) {
            $p_id = (int)$item['product_id'];
            $qty = (int)$item['quantity'];

            $variant_query = $conn->query("SELECT id FROM product_variants WHERE product_id = $p_id LIMIT 1");
            $variant = $variant_query->fetch_assoc();

            if ($variant) {
                $v_id = (int)$variant['id'];
                
                $sql_item = "INSERT INTO order_items (order_id, product_variant_id, quantity) 
                             VALUES ($order_id, $v_id, $qty)";
                $conn->query($sql_item);

                $update_variant_sql = "UPDATE product_variants 
                                       SET stock = CASE 
                                           WHEN stock - $qty <= 0 THEN 0 
                                           ELSE stock - $qty 
                                       END 
                                       WHERE id = $v_id";
                $conn->query($update_variant_sql);

                $stock_check = $conn->query("SELECT SUM(stock) as total_stock FROM product_variants WHERE product_id = $p_id");
                $stock_data = $stock_check->fetch_assoc();
                $total_stock = (int)($stock_data['total_stock'] ?? 0);

                if ($total_stock <= 0) {
                    $conn->query("UPDATE products SET is_deleted = 1 WHERE id = $p_id");
                }

            } else {
                $err_msg = sprintf(get_inline_txt('err_variant', $current_lang, $lang_dict), $p_id);
                die($err_msg);
            }
        }

        $conn->query("DELETE FROM cart WHERE user_id = $user_id");

        $alert_text = get_inline_txt('msg_success', $current_lang, $lang_dict) . " #" . $order_id;

        echo "<script>
                alert('" . addslashes($alert_text) . "');
                window.location.href = '../index.php';
              </script>";
    } else {
        echo get_inline_txt('err_create', $current_lang, $lang_dict) . $conn->error;
    }
}
?>