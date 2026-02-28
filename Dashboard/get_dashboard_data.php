<?php
include '../db.php';
header('Content-Type: application/json');

$selected_year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

// 1. Monthly Earnings
$months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sept", "Oct", "Nov", "Dec"];
$monthly_earnings = [];
foreach($months as $index => $name) {
    $m = $index + 1;
    $res = $conn->query("SELECT SUM(total_amount) as earned FROM transactions WHERE MONTH(date_created) = $m AND YEAR(date_created) = $selected_year");
    $monthly_earnings[] = (float)($res->fetch_assoc()['earned'] ?? 0);
}

// 2. Payment Methods
$pay_cash = $conn->query("SELECT COUNT(*) FROM transactions WHERE mode_of_payment='Cash' AND YEAR(date_created) = $selected_year")->fetch_row()[0];
$pay_pos = $conn->query("SELECT COUNT(*) FROM transactions WHERE mode_of_payment='POS' AND YEAR(date_created) = $selected_year")->fetch_row()[0];
$pay_transfer = $conn->query("SELECT COUNT(*) FROM transactions WHERE mode_of_payment='Transfer' AND YEAR(date_created) = $selected_year")->fetch_row()[0];

echo json_encode([
    "months" => $months,
    "earnings" => $monthly_earnings,
    "payments" => [(int)$pay_cash, (int)$pay_pos, (int)$pay_transfer]
]);