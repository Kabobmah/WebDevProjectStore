<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)$_POST['product_id'];
    $user_id = (int)$_SESSION['user_id'];
    
    $rating = (int)$_POST['rating'];
    if ($rating < 1) $rating = 1;
    if ($rating > 5) $rating = 5;

    $comment = trim($_POST['comment']);
    $comment = htmlspecialchars($comment, ENT_QUOTES, 'UTF-8');

    if (!empty($comment)) {
        $stmt = $conn->prepare("INSERT INTO reviews (product_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $product_id, $user_id, $rating, $comment);
        
        if ($stmt->execute()) {
            header("Location: ../item.php?id=" . $product_id . "&review=success");
            exit();
        } else {
            header("Location: ../item.php?id=" . $product_id . "&review=error");
            exit();
        }
    } else {
        header("Location: ../item.php?id=" . $product_id . "&review=empty");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}