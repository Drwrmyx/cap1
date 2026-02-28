<?php 
include '../db.php'; 

// --- DATE & FILTER LOGIC ---
$available_years_res = $conn->query("SELECT DISTINCT YEAR(date_created) as year FROM transactions ORDER BY year DESC");
$years = [];
while($row = $available_years_res->fetch_assoc()) { $years[] = $row['year']; }

$selected_year = isset($_GET['year']) ? (int)$_GET['year'] : (count($years) > 0 ? $years[0] : date('Y'));

// --- KPI DATA ---
$today = date('Y-m-d');
$total_sales_today = $conn->query("SELECT COUNT(*) as total FROM transactions WHERE DATE(date_created) = '$today'")->fetch_assoc()['total'] ?? 0;
$all_time_total = $conn->query("SELECT COUNT(*) as total FROM transactions")->fetch_assoc()['total'] ?? 0;
$items_in_stock = $conn->query("SELECT SUM(stock) as total FROM products")->fetch_assoc()['total'] ?? 0;
$low_stock_count = $conn->query("SELECT COUNT(*) as total FROM products WHERE stock <= 15")->fetch_assoc()['total'] ?? 0;

// Payment Methods Data
$pay_cash = $conn->query("SELECT COUNT(*) FROM transactions WHERE mode_of_payment='Cash' AND YEAR(date_created) = $selected_year")->fetch_row()[0];
$pay_pos = $conn->query("SELECT COUNT(*) FROM transactions WHERE mode_of_payment='POS' AND YEAR(date_created) = $selected_year")->fetch_row()[0];
$pay_transfer = $conn->query("SELECT COUNT(*) FROM transactions WHERE mode_of_payment='Transfer' AND YEAR(date_created) = $selected_year")->fetch_row()[0];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Management Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <nav class="navbar">
        <a href="#" class="navbar-brand"></a>
        <ul class="navbar-links">
            <li><a href="../Dashboard/dashboard.php" class="active"><i class="fa fa-tachometer-alt"></i> Dashboard</a></li>
            
            <li><a href="../Products/products.php"><i class="fa fa-boxes"></i> Inventory</a></li>
            
            <li><a href="../Transactions/transaction.php"><i class="fa fa-exchange-alt"></i> Transactions</a></li>
        </ul>
    </nav>

    <div class="top-bar">
        <div>
            <label style="font-weight:bold;">Select Account Year: </label>
            <select onchange="changeYear(this.value)">
                <?php foreach($years as $yr): ?>
                    <option value="<?= $yr ?>" <?= ($yr == $selected_year) ? 'selected' : '' ?>><?= $yr ?></option>
                <?php endforeach; ?>
                <?php if(count($years) == 0): ?><option><?= date('Y') ?></option><?php endif; ?>
            </select>
        </div>
    </div>

    <div class="kpi-row">
        <div class="kpi-card bg-success">
            <i class="fa fa-exchange-alt"></i>
            <h1><?= $total_sales_today ?></h1>
            <p>Total Sales Today</p>
            <div class="kpi-footer">Number of Items Sold Today</div>
        </div>
        <div class="kpi-card bg-warning">
            <i class="fa fa-server"></i>
            <h1><?= $all_time_total ?></h1>
            <p>Total Transactions</p>
            <div class="kpi-footer">All-time Total Transactions</div>
        </div>
        <div class="kpi-card bg-primary">
            <i class="fa fa-shopping-cart"></i>
            <h1><?= $items_in_stock ?></h1>
            <p>Items in Stock</p>
            <div class="kpi-footer">Total Units in Inventory</div>
        </div>
        <div class="kpi-card bg-danger">
            <i class="fa fa-exclamation-triangle"></i>
            <h1><?= $low_stock_count ?></h1>
            <p>Low Stock Items</p>
            <div class="kpi-footer">Items with 15 or less remaining</div>
        </div>
    </div>

    <div class="main-grid">
        <div class="chart-container">
            <h4 style="margin: 0 0 10px 0;">Earnings (<?= $selected_year ?>)</h4>
            <canvas id="earningsChart" height="120"></canvas>
        </div>
        <div class="pie-container">
            <h4 style="margin: 0 0 10px 0; color: #333; text-align: center;">Payment Methods (%)</h4>
            <canvas id="paymentPie"></canvas>
        </div>
    </div>

    <div class="table-area" style="margin-bottom: 20px;">
        <div class="table-header" style="background: #d9534f;">
            <i class="fa fa-list"></i> Critical Stock Alert (Items to Reorder)
        </div>
        <table>
            <thead>
                <tr>
                    <th>Item Code</th>
                    <th>Product Name</th>
                    <th>Current Stock</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // UPDATED: Threshold set to 15
                $threshold = 15;
                $critical_sql = "SELECT item_code, name, stock FROM products WHERE stock <= $threshold ORDER BY stock ASC";
                $critical_res = $conn->query($critical_sql);

                if ($critical_res && $critical_res->num_rows > 0) {
                    while($row = $critical_res->fetch_assoc()) {
                        // Logic for labels
                        if ($row['stock'] == 0) {
                            $status_label = 'OUT OF STOCK';
                            $label_bg = '#000';
                        } elseif ($row['stock'] <= 5) {
                            $status_label = 'CRITICAL';
                            $label_bg = '#d9534f';
                        } else {
                            $status_label = 'LOW STOCK';
                            $label_bg = '#f0ad4e'; // Orange for 6-15 items
                        }
                        ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($row['item_code']) ?></strong></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td style="color: <?= ($row['stock'] <= 5) ? '#d9534f' : '#333' ?>; font-weight: bold;">
                                <?= $row['stock'] ?>
                            </td>
                            <td>
                                <span style="background: <?= $label_bg ?>; color: white; padding: 2px 8px; border-radius: 3px; font-size: 10px; font-weight: bold;">
                                    <?= $status_label ?>
                                </span>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo '<tr><td colspan="4" style="text-align:center; padding: 20px; color: #777;">Stock levels are healthy (All items above 15).</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="report-grid">
        <div class="table-area">
            <div class="table-header"><i class="fa fa-arrow-trend-up"></i> High in Demand</div>
            <table>
                <thead><tr><th>Item</th><th>Qty Sold</th></tr></thead>
                <tbody>
                    <?php $high = $conn->query("SELECT name, total_sold FROM products ORDER BY total_sold DESC LIMIT 5");
                    while($r = $high->fetch_assoc()) echo "<tr><td>{$r['name']}</td><td>{$r['total_sold']}</td></tr>"; ?>
                </tbody>
            </table>
        </div>
        <div class="table-area">
            <div class="table-header"><i class="fa fa-money-bill-trend-up"></i> Highest Earning</div>
            <table>
                <thead><tr><th>Item</th><th>Total Earned</th></tr></thead>
                <tbody>
                    <?php $earn = $conn->query("SELECT name, total_earned_on_item FROM products ORDER BY total_earned_on_item DESC LIMIT 5");
                    while($r = $earn->fetch_assoc()) echo "<tr><td>{$r['name']}</td><td>₱".number_format($r['total_earned_on_item'], 2)."</td></tr>"; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="report-grid">
        <div class="table-area">
            <div class="table-header"><i class="fa fa-arrow-trend-down"></i> Low in Demand</div>
            <table>
                <thead><tr><th>Item</th><th>Qty Sold</th></tr></thead>
                <tbody>
                    <?php $low_demand = $conn->query("SELECT name, total_sold FROM products ORDER BY total_sold ASC LIMIT 5");
                    while($r = $low_demand->fetch_assoc()) echo "<tr><td>{$r['name']}</td><td>{$r['total_sold']}</td></tr>"; ?>
                </tbody>
            </table>
        </div>
        <div class="table-area">
            <div class="table-header"><i class="fa fa-money-bill-transfer"></i> Lowest Earning</div>
            <table>
                <thead><tr><th>Item</th><th>Total Earned</th></tr></thead>
                <tbody>
                    <?php $low_earn = $conn->query("SELECT name, total_earned_on_item FROM products ORDER BY total_earned_on_item ASC LIMIT 5");
                    while($r = $low_earn->fetch_assoc()) echo "<tr><td>{$r['name']}</td><td>₱".number_format($r['total_earned_on_item'], 2)."</td></tr>"; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="report-grid">
        <div class="table-area">
            <div class="table-header">Daily Transactions</div>
            <table>
                <thead><tr><th>Date</th><th>Tot. Trans</th><th>Tot. Earned</th></tr></thead>
                <tbody>
                    <?php $daily = $conn->query("SELECT DATE(date_created) as d, COUNT(*) as c, SUM(total_amount) as s FROM transactions GROUP BY d ORDER BY d DESC LIMIT 5");
                    while($r = $daily->fetch_assoc()) echo "<tr><td>".date('D, d M Y', strtotime($r['d']))."</td><td>{$r['c']}</td><td>₱".number_format($r['s'], 2)."</td></tr>"; ?>
                </tbody>
            </table>
        </div>
        <div class="table-area">
            <div class="table-header">Transactions by Days</div>
            <table>
                <thead><tr><th>Day</th><th>Tot. Trans</th><th>Tot. Earned</th></tr></thead>
                <tbody>
                    <?php $days_rep = $conn->query("SELECT DAYNAME(date_created) as day, COUNT(*) as c, SUM(total_amount) as s FROM transactions GROUP BY day ORDER BY FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')");
                    while($r = $days_rep->fetch_assoc()) echo "<tr><td>{$r['day']}s</td><td>{$r['c']}</td><td>₱".number_format($r['s'], 2)."</td></tr>"; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="report-grid">
        <div class="table-area">
            <div class="table-header">Transactions by Months</div>
            <table>
                <thead><tr><th>Month</th><th>Year</th><th>Tot. Trans</th><th>Tot. Earned</th></tr></thead>
                <tbody>
                    <?php 
                    $months_rep = $conn->query("SELECT MONTHNAME(date_created) as m, YEAR(date_created) as y, COUNT(*) as c, SUM(total_amount) as s FROM transactions WHERE YEAR(date_created) = $selected_year GROUP BY m ORDER BY MONTH(date_created) DESC");
                    while($r = $months_rep->fetch_assoc()) echo "<tr><td>{$r['m']}</td><td>{$r['y']}</td><td>{$r['c']}</td><td>₱".number_format($r['s'], 2)."</td></tr>"; ?>
                </tbody>
            </table>
        </div>
        <div class="table-area">
            <div class="table-header">Transactions by Years</div>
            <table>
                <thead><tr><th>Year</th><th>Tot. Trans</th><th>Tot. Earned</th></tr></thead>
                <tbody>
                    <?php 
                    $years_rep = $conn->query("SELECT YEAR(date_created) as y, COUNT(*) as c, SUM(total_amount) as s FROM transactions GROUP BY y ORDER BY y DESC");
                    while($r = $years_rep->fetch_assoc()) echo "<tr><td><strong>{$r['y']}</strong></td><td>{$r['c']}</td><td>₱".number_format($r['s'], 2)."</td></tr>"; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        const dashboardData = {
            months: <?= json_encode($months) ?>,
            earnings: <?= json_encode($monthly_earnings) ?>,
            paymentData: [<?= "$pay_cash, $pay_pos, $pay_transfer" ?>]
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="script.js"></script>
</body>
</html>