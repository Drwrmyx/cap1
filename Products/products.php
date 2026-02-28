<?php 
include '../db.php'; 

// --- 1. Logic for Search, Sort, and Pagination ---
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name_asc';
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;

// Map dropdown values to SQL ORDER BY
$sort_options = [
    'name_asc'      => "name ASC",
    'item_code_asc' => "item_code ASC",
    'price_high'    => "price DESC",
    'qty_high'      => "stock DESC",
    'name_desc'     => "name DESC",
    'item_code_desc'=> "item_code DESC",
    'price_low'     => "price ASC",
    'qty_low'       => "stock ASC"
];
$sort_sql = $sort_options[$sort] ?? "name ASC";

// Main Data Query
$query = "SELECT * FROM products 
          WHERE name LIKE '%$search%' OR item_code LIKE '%$search%' 
          ORDER BY $sort_sql LIMIT $limit";
$res = $conn->query($query);

// Calculate Total Worth
$total_worth_res = $conn->query("SELECT SUM(price * stock) as total FROM products");
$total_worth = $total_worth_res->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar">
    <a href="#" class="navbar-brand"></a>
    <ul class="navbar-links">
        <li><a href="../Dashboard/dashboard.php"><i class="fa fa-tachometer-alt"></i> Dashboard</a></li>
        
        <li><a href="../Products/products.php" class="active"><i class="fa fa-boxes"></i> Inventory</a></li>
        
        <li><a href="../Transactions/transaction.php"><i class="fa fa-exchange-alt"></i> Transactions</a></li>
    </ul>
</nav>

<div class="controls-row">
    <div>
        Show <select onchange="location.href='?limit='+this.value">
            <option value="10" <?= $limit==10?'selected':'' ?>>10</option>
            <option value="25" <?= $limit==25?'selected':'' ?>>25</option>
            <option value="50" <?= $limit==50?'selected':'' ?>>50</option>
        </select> per page
    </div>

    <div>
        Sort by: 
        <select onchange="location.href='?sort='+this.value">
            <?php foreach($sort_options as $key => $val): ?>
                <option value="<?= $key ?>" <?= $sort==$key?'selected':'' ?>><?= ucwords(str_replace(['_', 'asc', 'desc'], [' ', '(Asc)', '(Desc)'], $key)) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <form method="GET" class="search-box">
        <i class="fa fa-search"></i>
        <input type="text" name="search" placeholder="Search Items" value="<?= htmlspecialchars($search) ?>">
    </form>
</div>

<div class="total-worth">Items Total Worth/Price: ₱<?= number_format($total_worth, 2) ?></div>

<div class="main-layout">
    <div class="sidebar">
        <form action="process.php" method="POST">
            <label>Item Code</label><input type="text" name="item_code">
            <label>Item Name</label><input type="text" name="name" required>
            <label>Quantity</label><input type="number" name="stock" min="0" required>
            <label>(₱)Unit Price</label><input type="number" step="0.01" name="price" min="0" required>
            <label>Description (Optional)</label><textarea name="description" rows="4"></textarea>
            <div class="flex-btn-group">
                <button type="submit" name="add_item" class="btn-blue flex-fill">Add Item</button>
                <button type="reset" class="btn-red flex-fill">Cancel</button>
            </div>
        </form>
    </div>

    <div class="table-container">
        <div class="table-header">Items</div>
        <table>
            <thead>
                <tr>
                    <th>SN</th>
                    <th>ITEM NAME</th>
                    <th>ITEM CODE</th>
                    <th>DESCRIPTION</th>
                    <th>QTY IN STOCK</th>
                    <th>UNIT PRICE</th>
                    <th>TOTAL SOLD</th>
                    <th>TOTAL EARNED</th>
                    <th>UPDATE QUANTITY</th>
                    <th>EDIT</th>
                    <th>DELETE</th>
                </tr>
            </thead>
            <tbody>
                <?php $sn = 1; while($row = $res->fetch_assoc()): ?>
                <tr class="<?= $row['stock'] <= 0 ? 'low-stock' : '' ?>">
                    <td><?= $sn++ ?>.</td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['item_code']) ?></td>
                    <td><?= htmlspecialchars($row['description']) ?></td>
                    <td><?= $row['stock'] ?></td>
                    <td>₱<?= number_format($row['price'], 2) ?></td>
                    <td><?= $row['total_sold'] ?></td>
                    <td>₱<?= number_format($row['total_earned_on_item'] ?? 0, 2) ?></td>
                    <td><a href="#" class="update-link" onclick='openUpdateModal(<?= json_encode($row) ?>)'>Update Quantity</a></td>
                    <td><i class="fa fa-pencil edit-icon" onclick='openEditModal(<?= json_encode($row) ?>)'></i></td>
                    <td><a href="process_delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this item?')" class="delete-link"><i class="fa fa-trash"></i></a></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="updateModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">Update Stock</div>
        <form action="process_update.php" method="POST">
            <input type="hidden" name="product_id" id="up_id">
            <div class="modal-body">
                <div><label>Item Name</label><input type="text" id="up_name" readonly class="readonly-input"></div>
                <div><label>Item Code</label><input type="text" id="up_code" readonly class="readonly-input"></div>
                <div><label>Qty in Stock</label><input type="text" id="up_stock" readonly class="readonly-input"></div>
                <div style="grid-column: span 2;">
                    <label>Update Type</label>
                    <select name="type" required style="width:100%; padding:8px;">
                        <option value="">---</option>
                        <option value="add">Add to Stock</option>
                        <option value="remove">Remove from Stock</option>
                    </select>
                </div>
                <div><label>Quantity</label><input type="number" name="new_qty" min="1" required></div>
                <div class="full-width"><label>Description</label><textarea name="desc" rows="3"></textarea></div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-blue">Update</button>
                <button type="button" class="btn-red" onclick="closeModals()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">Edit Item</div>
        <form action="process_edit.php" method="POST">
            <input type="hidden" name="id" id="edit_id">
            <div class="modal-body">
                <div><label>Item Name</label><input type="text" name="name" id="edit_name" required></div>
                <div><label>Item Code</label><input type="text" name="item_code" id="edit_code"></div>
                <div><label>Unit Price</label><input type="number" step="0.01" name="price" id="edit_price" min="1" required></div>
                <div class="full-width"><label>Description</label><textarea name="description" id="edit_desc" rows="4"></textarea></div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-blue">Save Changes</button>
                <button type="button" class="btn-red" onclick="closeModals()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script src="script.js"></script>

</body>
</html>
