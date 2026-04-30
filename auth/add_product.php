<?php
session_start();
require_once '../includes/db.php';

// Проверка на админа
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Доступ запрещен");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = (float)$_POST['price'];
    $cat_id = (int)$_POST['category_id'];
    $desc = trim($_POST['description']);

    $color = trim($_POST['color']);
    $size = trim($_POST['size']);
    $stock = (int)$_POST['stock'];

    // Работа с изображением
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
        $extension = pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION);
        $image_name = time() . '_' . uniqid() . '.' . $extension;
        $target = "../src/" . $image_name;

        // ВАЖНО: здесь tmp_name, а не tmp_path
        if (move_uploaded_file($_FILES['main_image']['tmp_name'], $target)) {
            
            // Исправленная строка: 5 переменных = 5 символов типов "sdiss"
            $stmt = $conn->prepare("INSERT INTO products (name, price, category_id, description, main_image) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sdiss", $name, $price, $cat_id, $desc, $image_name);
            
            if ($stmt->execute()) {
            $new_product_id = $conn->insert_id;

            // 3. Вставляем данные в таблицу product_variants
            $stmt_variant = $conn->prepare("INSERT INTO product_variants (product_id, color, size, stock) VALUES (?, ?, ?, ?)");
            $stmt_variant->bind_param("issi", $new_product_id, $color, $size, $stock);
                            
                if ($stmt_variant->execute()) {
                    header("Location: ../admin.php?success=1");
                    exit();
                } else {
                    echo "Ошибка при создании варианта: " . $conn->error;
                }
        } else {
            echo "Ошибка: Не удалось переместить файл в папку src. Проверьте права папки.";
        }
    } else {
        echo "Ошибка: Файл не загружен или поврежден. Код ошибки: " . $_FILES['main_image']['error'];
    }
}

    
}