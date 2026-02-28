<?php
include '../db.php';

if (isset($_POST['add_item'])) {
    $code = $_POST['item_code'];
    $name = $_POST['name'];
    $stock = $_POST['stock'];
    $price = $_POST['price'];
    $desc = $_POST['description'];

    $sql = "INSERT INTO products (item_code, name, stock, price, description) 
            VALUES ('$code', '$name', $stock, $price, '$desc')";

    if ($conn->query($sql)) {
        header("Location: products.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>