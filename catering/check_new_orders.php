<?php
require_once 'config.php';
header('Content-Type: application/json');

$db = Database::getInstance();
$lastId = (int)$_GET['last_id'];

$stmt = $db->prepare('
    SELECT * FROM orders 
    WHERE id > :last_id 
    ORDER BY order_time DESC
');
$stmt->bindValue(':last_id', $lastId, SQLITE3_INTEGER);
$results = $stmt->execute();

$newOrders = [];
while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
    $newOrders[] = $row;
}

echo json_encode(['orders' => $newOrders]);
