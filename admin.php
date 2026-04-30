<?php
session_start();
require_once 'includes/db.php';

// Проверка: пускаем только админа
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Доступ запрещен. Вы не админ.");
}
$user_favs = [];
if (isset($_SESSION['user_id'])) {
    $uid = (int)$_SESSION['user_id'];
    $fav_query = $conn->query("SELECT product_id FROM favorites WHERE user_id = $uid");
    while ($f = $fav_query->fetch_assoc()) {
        $user_favs[] = $f['product_id'];
    }
}
// Получаем категории для выпадающего списка
$categories = $conn->query("SELECT * FROM categories");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Админ-панель | AURA</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .admin-container { padding: 120px 40px; max-width: 800px; margin: 0 auto; color: #000; background: #fff; }
        .admin-form { display: flex; flex-direction: column; gap: 20px; }
        .admin-form input, .admin-form textarea, .admin-form select {
            padding: 15px; border: 1px solid #eee; width: 100%;
        }
        .admin-btn { background: #000; color: #fff; padding: 20px; border: none; cursor: pointer; text-transform: uppercase; }

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

    <div class="admin-container">
        <h1>Добавить новый товар</h1>
        <form action="auth/add_product.php" method="POST" enctype="multipart/form-data" class="admin-form">
            <input type="text" name="name" placeholder="Название товара" required>
            
            <select name="category_id">
                <?php while($cat = $categories->fetch_assoc()): ?>
                    <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                <?php endwhile; ?>
            </select>

            <input type="number" name="price" placeholder="Цена (₸)" required>
            <textarea name="description" placeholder="Описание" rows="5"></textarea>
            
            <label>Главное изображение:</label>
            <input type="text" name="main_image" placeholder="Имя файла картинки (например, item1.png)">

            <input type="file" name="main_image" accept="image/*" required>
            
            <h3 style="font-size: 14px; margin-top: 20px;">Настройки варианта</h3>
            <input type="text" name="color" placeholder="Цвет (например, Black)" required>
            <input type="text" name="size" placeholder="Размер (например, M)" required>
            <input type="number" name="stock" placeholder="Количество на складе" required>
            <button type="submit" class="admin-btn">Опубликовать товар</button>

            

        </form>
    </div>
<!-- --------------------------------------FOOTER----------------------------------->
<?php include 'includes/footer.php'; ?>

    <script src="js/main.js"></script>
</body>
</html>