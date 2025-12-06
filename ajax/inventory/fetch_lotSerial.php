<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

$sparepartId = filter_input(INPUT_POST, 'sparepartId', FILTER_SANITIZE_NUMBER_INT);
$warehouseId = filter_input(INPUT_POST, 'warehouseId', FILTER_SANITIZE_NUMBER_INT);

if (!$sparepartId || !$warehouseId) {
    echo json_encode(['success' => false, 'message' => 'Invalid sparepartId or warehouseId']);
    exit;
}

$stock = $db->query(
    "SELECT lotSerial FROM inventory_stock WHERE sparepartId = ?s AND warehouseId = ?s",
    $sparepartId,
    $warehouseId
)->fetch_assoc();

if ($stock && isset($stock['lotSerial'])) {
    echo json_encode(['success' => true, 'lotSerial' => $stock['lotSerial']]);
} else {
    echo json_encode(['success' => false, 'lotSerial' => '', 'message' => 'No lot/serial found']);
}

exit;
?>