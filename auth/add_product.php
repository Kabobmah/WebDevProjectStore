<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $name_en = trim($_POST['name_en']); 
    $price = (float)$_POST['price'];
    $cat_id = (int)$_POST['category_id'];
    $desc = trim($_POST['description']);
    $desc_en = trim($_POST['description_en']); 

    $color = trim($_POST['color']);
    $size = trim($_POST['size']);
    $stock = (int)$_POST['stock'];

    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
        $extension = pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION);
        $image_name = time() . '_' . uniqid() . '.' . $extension;
        $target = "../src/" . $image_name;

        if (move_uploaded_file($_FILES['main_image']['tmp_name'], $target)) {
            

            $stmt = $conn->prepare("INSERT INTO products (name, name_en, price, category_id, description, description_en, main_image) VALUES (?, ?, ?, ?, ?, ?, ?)");
            
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

                $stmt_variant = $conn->prepare("INSERT INTO product_variants (product_id, color, size, stock) VALUES (?, ?, ?, ?)");
                $stmt_variant->bind_param("issi", $new_product_id, $color, $size, $stock);
                                
                if ($stmt_variant->execute()) {
                    header("Location: ../admin.php?success=1");
                    exit();
                } else {
                    echo "Error creating product variant: " . $conn->error;
                }
            } else {
                echo "Database execution error: " . $stmt->error;
            }
        } else {
            echo "Error: Failed to move uploaded file to src directory. Check folder permissions.";
        }
    } else {
        echo "Error: File upload failed or file is corrupted. Error code: " . $_FILES['main_image']['error'];
    }
}
?>