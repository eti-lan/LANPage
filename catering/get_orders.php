<?php
require_once 'config.php';
header('Content-Type: application/json');

$db = Database::getInstance();
$today = date('Y-m-d');

// Hole alle Bestellungen des aktuellen Tages
$stmt = $db->prepare('
    SELECT * FROM orders 
    WHERE date(order_time) = :today 
    ORDER BY order_time DESC
');
$stmt->bindValue(':today', $today, SQLITE3_TEXT);
$results = $stmt->execute();

$orders = [];
$totalOrders = 0;
$totalRevenue = 0;

while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
    $orders[] = $row;
    $totalOrders++;
    $totalRevenue += ($row['price'] * $row['quantity']);
}

echo json_encode([
    'orders' => $orders,
    'totalOrders' => $totalOrders,
    'totalRevenue' => $totalRevenue
]);
