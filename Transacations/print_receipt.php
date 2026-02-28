<?php
include '../db.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$res = $conn->query("SELECT * FROM transactions WHERE id = $id");

if ($res->num_rows == 0) {
    die("Receipt not found.");
}

$data = $res->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Receipt - <?= $data['receipt_no'] ?></title>
<link rel="stylesheet" href="style.css">
</head>
<body onload="window.print();">
    <div class="text-center">
        <h2 style="margin:0;">YOUR STORE</h2>
        <p style="margin:5px 0;">Official Receipt</p>
    </div>
    
    <div class="divider"></div>
    
    <table>
        <tr><td>Receipt No:</td><td align="right"><?= $data['receipt_no'] ?></td></tr>
        <tr><td>Date:</td><td align="right"><?= date('M d, Y H:i', strtotime($data['date_created'])) ?></td></tr>
        <tr><td>Staff:</td><td align="right">Admin Demo</td></tr>
    </table>
    
    <div class="divider"></div>
    
    <table>
        <tr>
            <td><strong>Grand Total:</strong></td>
            <td align="right" class="total">₦<?= number_format($data['total_amount'], 2) ?></td>
        </tr>
        <tr>
            <td>Amount Tendered:</td>
            <td align="right">₦<?= number_format($data['amount_tendered'], 2) ?></td>
        </tr>
        <tr>
            <td><strong>Change Due:</strong></td>
            <td align="right">₦<?= number_format($data['change_due'], 2) ?></td>
        </tr>
    </table>
    
    <div class="divider"></div>
    
    <div class="text-center">
        <p>Customer: <?= htmlspecialchars($data['customer_name']) ?></p>
        <p>Thank you for your patronage!</p>
        <button class="no-print" onclick="window.location.href='transaction.php'">Back to Sales</button>
    </div>
</body>
</html>