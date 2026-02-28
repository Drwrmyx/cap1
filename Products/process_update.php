<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['product_id'];
    $type = $_POST['type']; 
    $qty_change = (int)$_POST['new_qty']; 
    
    if ($qty_change <= 0) {
        die("Error: Quantity must be greater than zero.");
    }

    if ($type === 'add') {
        $sql = "UPDATE products SET stock = stock + $qty_change WHERE id = $id";
    } elseif ($type === 'remove') {
        // New Check: Prevent stock from going below 0
        $sql = "UPDATE products SET stock = stock - $qty_change WHERE id = $id AND stock >= $qty_change";
    } else {
        die("Error: Invalid update type selected.");
    }

    if ($conn->query($sql)) {
        if ($conn->affected_rows > 0) {
            header("Location: products.php?success=stock_updated");
        } else {
            die("Error: Cannot remove more items than are currently in stock.");
        }
    } else {
        echo "Database Error: " . $conn->error;
    }
}
?>