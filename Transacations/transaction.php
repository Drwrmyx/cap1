<?php 
include '../db.php'; 

// --- 1. Logic for Search, Sort, and Pagination ---
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'date_desc';
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;

$sort_options = [
    'date_desc'   => "date_created DESC",
    'date_asc'    => "date_created ASC",
    'amount_high' => "total_amount DESC",
    'amount_low'  => "total_amount ASC",
    'name_asc'    => "customer_name ASC"
];
$sort_sql = $sort_options[$sort] ?? "date_created DESC";

$query = "SELECT * FROM transactions 
          WHERE receipt_no LIKE '%$search%' 
          OR customer_name LIKE '%$search%' 
          ORDER BY $sort_sql LIMIT $limit";
$history = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transaction System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
<body>

    <nav class="navbar">
        <a href="#" class="navbar-brand"></a>
        <ul class="navbar-links">
            <li><a href="../Dashboard/dashboard.php"><i class="fa fa-tachometer-alt"></i> Dashboard</a></li>
            
            <li><a href="../Products/products.php"><i class="fa fa-boxes"></i> Inventory</a></li>
            
            <li><a href="../Transactions/transaction.php" class="active"><i class="fa fa-exchange-alt"></i> Transactions</a></li>
        </ul>
    </nav>


<div class="card">
    <form action="process_transaction.php" method="POST">
        <div id="item-list">
            <div class="item-row">
                <div>
                    <label>Item</label>
                    <select name="product_id[]" onchange="updateRow(this)" required>
                        <option value="">Select Item</option>
                        <?php
                        $prods = $conn->query("SELECT * FROM products WHERE stock > 0");
                        while($p = $prods->fetch_assoc()) {
                            echo "<option value='{$p['id']}' data-price='{$p['price']}' data-stock='{$p['stock']}'>{$p['name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div><label>Available</label><input type="text" class="row-stock" readonly style="background:#fff;"></div>
                <div><label>Unit Price</label><input type="text" class="row-price" readonly style="background:#fff;"></div>
                <div><label>Quantity</label><input type="number" name="qty[]" class="row-qty" oninput="calculateRow(this)" value="0"></div>
                <div><label>Total Price</label><input type="text" class="row-total" value="0.00" readonly style="background:#fff;"></div>
                <div><button type="button" class="btn btn-red" onclick="removeRow(this)">×</button></div>
            </div>
        </div>

        <button type="button" class="btn btn-blue" onclick="addItemRow()" style="margin-top: 10px;">+ Add Item</button>

        <div class="summary-grid">
            <div><label>VAT(%)</label><input type="number" name="vat" value="0" oninput="calculateGrandTotal()"></div>
            <div><label>Discount(%)</label><input type="number" name="discount_percent" value="0" oninput="syncDiscount('percent')"></div>
            <div><label>Discount(Value)</label><input type="number" step="0.01" name="discount_val" value="0.00" oninput="syncDiscount('value')"></div>
            <div>
                <label>Payment Mode</label>
                <select name="payment_mode">
                    <option>Cash</option>
                    <option>POS</option>
                    <option>Transfer</option>
                </select>
            </div>
        </div>

        <div class="full-row">
            <div><label>Cumulative Amount</label><input type="text" id="grand-total" name="total_amount" value="0.00" readonly style="background:#fff; font-weight:bold; color: #337ab7;"></div>
            <div><label>Amount Tendered</label><input type="number" id="tendered" name="tendered" oninput="calculateChange()" required></div>
            <div><label>Change Due</label><input type="text" id="change-due" name="change" value="0.00" readonly style="background:#fff;"></div>
        </div>

        <div class="full-row">
            <div><label>Customer Name</label><input type="text" name="cust_name" placeholder="Name"></div>
            <div><label>Phone</label><input type="text" name="cust_phone" placeholder="Phone"></div>
            <div><label>Email</label><input type="email" name="cust_email" placeholder="Email"></div>
        </div>

        <div style="text-align: right; margin-top: 20px;">
            <button type="submit" class="btn btn-blue" style="padding: 10px 40px;">Confirm Order</button>
            <button type="reset" class="btn btn-red">Clear Order</button>
        </div>
    </form>
</div>

<div class="filter-row">
    <div>
        Show 
        <select onchange="location.href='?limit='+this.value+'&sort=<?= $sort ?>&search=<?= $search ?>'">
            <option value="10" <?= $limit==10?'selected':'' ?>>10</option>
            <option value="25" <?= $limit==25?'selected':'' ?>>25</option>
            <option value="50" <?= $limit==50?'selected':'' ?>>50</option>
        </select> per page
    </div>
    <div>
        Sort by 
        <select onchange="location.href='?sort='+this.value+'&limit=<?= $limit ?>&search=<?= $search ?>'">
            <option value="date_desc" <?= $sort=='date_desc'?'selected':'' ?>>date(Latest First)</option>
            <option value="amount_high" <?= $sort=='amount_high'?'selected':'' ?>>Total Amount Spent (Highest first)</option>
            <option value="name_asc" <?= $sort=='name_asc'?'selected':'' ?>>Customer Name (A-Z)</option>
        </select>
    </div>
    <form method="GET" class="search-container">
        <i class="fa fa-search"></i>
        <input type="text" name="search" placeholder="Search Transactions" value="<?= htmlspecialchars($search) ?>">
    </form>
</div>

<div class="table-area">
    <div class="table-header">RECENT TRANSACTIONS</div>
    <table>
        <thead>
            <tr>
                <th>SN</th>
                <th>Receipt No</th>
                <th>Amount</th>
                <th>Tendered</th>
                <th>Change</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Print</th> </tr>
        </thead>
        <tbody>
            <?php
            $history = $conn->query("SELECT * FROM transactions ORDER BY date_created DESC LIMIT 10");
            $sn = 1;
            while($row = $history->fetch_assoc()): ?>
            <tr>
                <td><?= $sn++ ?>.</td>
                <td style="color:#337ab7; font-weight:bold;"><?= $row['receipt_no'] ?></td>
                <td>₦<?= number_format($row['total_amount'], 2) ?></td>
                <td>₦<?= number_format($row['amount_tendered'], 2) ?></td>
                <td>₦<?= number_format($row['change_due'], 2) ?></td>
                <td><?= htmlspecialchars($row['customer_name']) ?></td>
                <td><?= date('Y-m-d H:i', strtotime($row['date_created'])) ?></td>
                <td>
                    <a href="print_receipt.php?id=<?= $row['id'] ?>" target="_blank" style="color:#337ab7;">
                        <i class="fa fa-print"></i> Receipt
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="script.js"></script>

</body>
</html>