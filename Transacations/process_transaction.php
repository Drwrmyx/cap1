<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Generate a unique Receipt Number
    $receipt = "REC-" . strtoupper(substr(uniqid(), 7));
    
    // 2. Collect Form Data
    $total    = $_POST['total_amount'];
    $tendered = $_POST['tendered'];
    $change   = $_POST['change'];
    $pay_mode = $_POST['payment_mode'];
    $name     = !empty($_POST['cust_name']) ? $_POST['cust_name'] : 'Walk-in Customer';
    $phone    = $_POST['cust_phone'];
    $email    = $_POST['cust_email'];

    // --- START TRANSACTION ---
    $conn->begin_transaction();

    try {
        // 3. Insert into Transactions Table (Prepared Statement)
        $stmt = $conn->prepare("INSERT INTO transactions (receipt_no, total_amount, amount_tendered, change_due, mode_of_payment, customer_name, customer_phone, customer_email) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sdddssss", $receipt, $total, $tendered, $change, $pay_mode, $name, $phone, $email);
        $stmt->execute();
        $last_id = $conn->insert_id;

        // 4. Update Product Stock and Record Items
        foreach ($_POST['product_id'] as $key => $prod_id) {
            $qty = (int)$_POST['qty'][$key];
            
            if ($qty > 0) {
                // Fetch current price to save in history (protects against future price changes)
                $price_query = $conn->prepare("SELECT price FROM products WHERE id = ?");
                $price_query->bind_param("i", $prod_id);
                $price_query->execute();
                $res = $price_query->get_result();
                $product = $res->fetch_assoc();
                $current_price = $product['price'];

                // Record the specific item sold
                $item_stmt = $conn->prepare("INSERT INTO transaction_items (transaction_id, product_id, quantity, price_at_sale) VALUES (?, ?, ?, ?)");
                $item_stmt->bind_param("iiid", $last_id, $prod_id, $qty, $current_price);
                $item_stmt->execute();

                // Deduct stock and increment earnings
                $update_stmt = $conn->prepare("UPDATE products SET 
                                               stock = stock - ?, 
                                               total_sold = total_sold + ?,
                                               total_earned_on_item = total_earned_on_item + (? * ?)
                                               WHERE id = ?");
                $update_stmt->bind_param("iiddi", $qty, $qty, $current_price, $qty, $prod_id);
                $update_stmt->execute();
            }
        }

        // 5. COMMIT ALL CHANGES
        $conn->commit();
        header("Location: print_receipt.php?id=" . $last_id);
        exit(); 

    } catch (Exception $e) {
        // If anything fails, undo all database changes
        $conn->rollback();
        die("Transaction failed: " . $e->getMessage());
    }
} else {
    echo $last_id; 
    exit();
}
?>