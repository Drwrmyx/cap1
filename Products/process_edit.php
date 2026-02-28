<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = (int)$_POST['id'];
    $name = $conn->real_escape_string($_POST['name']);
    $code = $conn->real_escape_string($_POST['item_code']);
    $price = (float)$_POST['price'];
    $desc = $conn->real_escape_string($_POST['description']);

    // GUARD: Check if price is negative
    if ($price < 0) {
        die("Error: Price cannot be a negative value.");
    }

    // Secure Update Query
    $sql = "UPDATE products SET 
            name='$name', 
            item_code='$code', 
            price='$price', 
            description='$desc' 
            WHERE id=$id";

    if ($conn->query($sql)) {
        header("Location: products.php?status=edited");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>