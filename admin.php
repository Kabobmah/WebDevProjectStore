<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Доступ запрещен. Вы не админ.");
}

$categories = $conn->query("SELECT * FROM categories");

$tab = $_GET['tab'] ?? 'products';

$orders_query = null;
if ($tab === 'orders') {
    $name_field = ($current_lang === 'en') ? 'IFNULL(p.name_en, p.name)' : 'p.name';

    $orders_query = $conn->query("
        SELECT 
            o.*, 
            u.full_name as user_name,
            GROUP_CONCAT(CONCAT($name_field, ' (', oi.quantity, ' ', '" . __('lbl_pieces_short') . "', ') — ', p.price, ' ₸') SEPARATOR '||') as items_list
        FROM orders o
        JOIN users u ON o.user_id = u.id
        LEFT JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN product_variants pv ON oi.product_variant_id = pv.id
        LEFT JOIN products p ON pv.product_id = p.id
        GROUP BY o.id
        ORDER BY o.order_date DESC
    ");
}
?>
<!DOCTYPE html>
<html lang="<?= $current_lang ?>">
<head>
    <meta charset="utf-8">
    <title><?= __('admin_panel_title') ?> | AURA</title>
    <link rel="stylesheet" href="css/style.css">
        <script>
        const userIsLogged = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
        const userRole = '<?php echo $_SESSION['role'] ?? 'user'; ?>';
    </script>
    <style>
        .admin-container { padding: 120px 40px; max-width: 900px; margin: 0 auto; color: #000; background: #fff; }
        .admin-section { margin-bottom: 50px; padding-bottom: 40px; }
        h1, h2 { font-weight: 300; margin-bottom: 20px; text-transform: uppercase; letter-spacing: 1px; }
        h2 { font-size: 18px; }
        .admin-form { display: flex; flex-direction: column; gap: 20px; }
        .admin-form input, .admin-form textarea, .admin-form select {
            padding: 15px; border: 1px solid #eee; width: 100%; font-family: inherit;
        }
        .admin-btn { background: #000; color: #fff; padding: 20px; border: none; cursor: pointer; text-transform: uppercase; letter-spacing: 2px; transition: opacity 0.3s; }
        .admin-btn:hover { opacity: 0.8; }
        .lang-group { border-left: 3px solid #eee; padding-left: 20px; margin-bottom: 10px; }
        .lang-label { font-size: 10px; color: #999; text-transform: uppercase; margin-bottom: 5px; display: block; }
        
        .admin-nav { display: flex; gap: 30px; margin-bottom: 40px; border-bottom: 1px solid #000; padding-bottom: 15px; }
        .admin-nav-link { color: #999; text-decoration: none; font-size: 13px; text-transform: uppercase; letter-spacing: 2px; font-weight: 300; transition: color 0.3s; }
        .admin-nav-link:hover { color: #000; }
        .admin-nav-link.active { color: #000; font-weight: bold; border-bottom: 2px solid #000; padding-bottom: 14px; }

        .orders-table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 13px; }
        .orders-table th, .orders-table td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        .orders-table th { font-weight: bold; text-transform: uppercase; letter-spacing: 1px; font-size: 11px; background: #fafafa; }
        .order-status { display: inline-block; padding: 4px 8px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; border-radius: 4px; }
        .status-pending { background: #fef7e0; color: #b06000; }
        .status-completed { background: #e6f4ea; color: #137333; }
        .status-canceled { background: #fce8e6; color: #c5221f; }
        .no-orders { text-align: center; padding: 40px; color: #888; font-style: italic; }
    </style>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="admin-container">
    
    <nav class="admin-nav">
        <a href="admin.php?tab=products" class="admin-nav-link <?= $tab === 'products' ? 'active' : '' ?>"><?= __('admin_tab_products') ?></a>
        <a href="admin.php?tab=orders" class="admin-nav-link <?= $tab === 'orders' ? 'active' : '' ?>"><?= __('admin_tab_orders') ?></a>
    </nav>

    <?php if ($tab === 'products'): ?>
        <div class="admin-section" style="border-bottom: none; padding-bottom: 0;">
            <h1><?= __('admin_add_product') ?></h1>
            <form action="auth/add_product.php" method="POST" enctype="multipart/form-data" class="admin-form">
                
                <select name="category_id">
                    <?php while($cat = $categories->fetch_assoc()): ?>
                        <option value="<?= $cat['id'] ?>"><?= translate_db($cat, 'name') ?></option>
                    <?php endwhile; ?>
                </select>

                <div class="lang-group">
                    <span class="lang-label">RU Version</span>
                    <input type="text" name="name" placeholder="<?= __('lbl_name') ?> (RU)" required>
                    <textarea name="description" placeholder="<?= __('lbl_description') ?> (RU)" rows="3"></textarea>
                </div>

                <div class="lang-group">
                    <span class="lang-label">EN Version</span>
                    <input type="text" name="name_en" placeholder="<?= __('lbl_name') ?> (EN)">
                    <textarea name="description_en" placeholder="<?= __('lbl_description') ?> (EN)" rows="3"></textarea>
                </div>

                <input type="number" name="price" placeholder="<?= __('lbl_price') ?> (₸)" required>
                
                <label><?= __('lbl_main_image') ?>:</label>
                <input type="file" name="main_image" accept="image/*" required>
                
                <h3 style="font-size: 14px; margin-top: 20px;"><?= __('admin_variant_settings') ?></h3>
                <input type="text" name="color" placeholder="<?= __('lbl_color') ?>" required>
                <input type="text" name="size" placeholder="<?= __('lbl_size') ?>" required>
                <input type="number" name="stock" placeholder="<?= __('lbl_stock') ?>" required>

                <button type="submit" class="admin-btn"><?= __('btn_publish') ?></button>
            </form>
        </div>
    <?php endif; ?>

    <?php if ($tab === 'orders'): ?>
        <div class="admin-section" style="border-bottom: none; padding-bottom: 0;">
            <h2><?= __('admin_orders_title') ?></h2>
            
            <table class="orders-table">
                <thead>
                    <tr>
                        <th><?= __('th_order_id') ?></th>
                        <th><?= __('th_customer') ?></th>
                        <th><?= __('th_date') ?></th>
                        <th><?= __('th_total') ?></th>
                        <th><?= __('th_status') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($orders_query && $orders_query->num_rows > 0): ?>
                        <?php while($order = $orders_query->fetch_assoc()): ?>
                            <tr onclick="toggleOrderDetails(<?= $order['id'] ?>)" style="cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#f9f9f9'" onmouseout="this.style.background='transparent'">
                                <td><strong>#<?= $order['id'] ?></strong> <span style="font-size: 10px; color: #007aff; margin-left: 5px;">▼ <?= __('lbl_more_details') ?></span></td>
                                <td><?= htmlspecialchars($order['user_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= date('d.m.Y H:i', strtotime($order['order_date'])) ?></td>
                                <td><?= number_format($order['total_amount'], 0, '', ' ') ?> ₸</td>
                                <td>
                                    <?php 
                                    $status = $order['status'] ?? 'pending';
                                    $status_class = 'status-pending';
                                    $status_text = __('status_pending');
                                    
                                    if ($status === 'completed' || $status === 'выполнен') {
                                        $status_class = 'status-completed';
                                        $status_text = __('status_completed');
                                    } elseif ($status === 'canceled' || $status === 'отменен') {
                                        $status_class = 'status-canceled';
                                        $status_text = __('status_canceled');
                                    }
                                    ?>
                                    <span class="order-status <?= $status_class ?>">
                                        <?= $status_text ?>
                                    </span>
                                </td>
                            </tr>
                            
                            <tr id="details-<?= $order['id'] ?>" style="display: none; background: #fafafa;">
                                <td colspan="5" style="padding: 15px 25px; border-bottom: 1px solid #ddd;">
                                    <div style="font-family: sans-serif; line-height: 1.6; display: flex; flex-direction: column; gap: 15px;">
                                        <div>
                                            <h4 style="margin: 0 0 5px 0; font-size: 11px; letter-spacing: 1px; text-transform: uppercase; color: #333;">Информация о доставке:</h4>
                                            <p style="margin: 0; font-size: 13px; color: #555;"><strong>Адрес:</strong> <?php echo htmlspecialchars($order['address'] ?? 'Не указан', ENT_QUOTES, 'UTF-8'); ?></p>
                                            <p style="margin: 0; font-size: 13px; color: #555;"><strong>Телефон:</strong> <?php echo htmlspecialchars($order['phone'] ?? 'Не указан', ENT_QUOTES, 'UTF-8'); ?></p>
                                            <p style="margin: 0; font-size: 13px; color: #555;"><strong>Способ оплаты:</strong> 
                                                <?php 
                                                    $method = $order['payment_method'] ?? '';
                                                    if ($method === 'cash') {
                                                        echo 'Наличные';
                                                    } elseif ($method === 'card') {
                                                        echo 'Карта';
                                                    } else {
                                                        echo htmlspecialchars($method ? $method : 'Не указан', ENT_QUOTES, 'UTF-8');
                                                    }
                                                ?>
                                            </p>
                                        </div>
                                        <div>
                                            <h4 style="margin: 0 0 5px 0; font-size: 11px; letter-spacing: 1px; text-transform: uppercase; color: #333;"><?= __('lbl_order_content') ?>:</h4>
                                            <ul style="margin: 0; padding-left: 20px; font-size: 13px; color: #555;">
                                                <?php 
                                                if (!empty($order['items_list'])) {
                                                    $items = explode('||', $order['items_list']);
                                                    foreach ($items as $item) {
                                                        echo "<li style='margin-bottom: 5px;'>" . htmlspecialchars($item, ENT_QUOTES, 'UTF-8') . "</li>";
                                                    }
                                                } else {
                                                    echo "<li style='color: #999; font-style: italic;'>" . __('lbl_no_details') . "</li>";
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="no-orders"><?= __('lbl_no_orders_yet') ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    
</div>

<?php include 'includes/footer.php'; ?>

<script src="js/main.js"></script>

<script>
function toggleOrderDetails(orderId) {
    const detailsRow = document.getElementById('details-' + orderId);
    if (detailsRow) {
        if (detailsRow.style.display === 'none' || detailsRow.style.display === '') {
            detailsRow.style.display = 'table-row';
        } else {
            detailsRow.style.display = 'none';
        }
    }
}
</script>
</body>
</html>