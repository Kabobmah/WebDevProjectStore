<?php
session_start();
require_once '../includes/db.php';

// Проверка на админа
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Доступ запрещен");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Чистим данные
    $name = trim($_POST['name']);
    $name_en = trim($_POST['name_en']); // Забираем EN название
    $price = (float)$_POST['price'];
    $cat_id = (int)$_POST['category_id'];
    $desc = trim($_POST['description']);
    $desc_en = trim($_POST['description_en']); // Забираем EN описание

    $color = trim($_POST['color']);
    $size = trim($_POST['size']);
    $stock = (int)$_POST['stock'];

    // Работа с изображением
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
        $extension = pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION);
        $image_name = time() . '_' . uniqid() . '.' . $extension;
        $target = "../src/" . $image_name;

        if (move_uploaded_file($_FILES['main_image']['tmp_name'], $target)) {
            
            // 1. ОБНОВЛЕННЫЙ ЗАПРОС: Добавляем колонки name_en и description_en
            // В строке типов теперь 7 символов: "ssdssis" (s-string, d-double, i-int)
            $stmt = $conn->prepare("INSERT INTO products (name, name_en, price, category_id, description, description_en, main_image) VALUES (?, ?, ?, ?, ?, ?, ?)");
            
            // 2. ОБНОВЛЕННЫЙ BIND_PARAM: Передаем 7 переменных
            $stmt->bind_param("ssdssis", 
                $name,          // name
                $name_en,       // name_en
                $price,         // price
                $cat_id,        // category_id
                $desc,          // description
                $desc_en,       // description_en
                $image_name     // main_image
            );
            
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
                echo "Ошибка при выполнении запроса к БД: " . $stmt->error;
            }
        } else {
            echo "Ошибка: Не удалось переместить файл в папку src. Проверьте права папки.";
        }
    } else {
        echo "Ошибка: Файл не загружен или поврежден. Код ошибки: " . $_FILES['main_image']['error'];
    }
}
?>