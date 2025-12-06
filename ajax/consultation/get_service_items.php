<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$limit = 50; // Limit results

// Base SQL query
$sql = "SELECT * FROM consultation_services_items WHERE active = 1";

// Apply search filter if a query is provided
$params = [];
if (!empty($search)) {
    $sql .= " AND (itemName LIKE ?s OR description LIKE ?s)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

$sql .= " LIMIT ?i";
$params[] = $limit;

// Execute query using SafeMySQL
$rows = $db->getAll($sql, ...$params);

$data = [];
foreach ($rows as $row) {

    $data[] = [
        'id' => $row['itemId'],
        'text' => $row['itemName'],
        'desc' => $row['description'],
        'price' => $row['price'],
        'uom' => $row['uom'],
    ];
}

// Return JSON response
echo json_encode(['results' => $data]);