<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";


$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$warehouseID = isset($_GET['warehouseID']) ? intval($_GET['warehouseID']) : null; // Get warehouseID from GET
$limit = 50; // Limit results

// Base SQL query with JOIN to get warehouse-specific stock
$sql = "SELECT 
            s.*, 
            COALESCE(i.quantity, 0) AS totalAvailable
        FROM spareparts s
        LEFT JOIN inventory_stock i ON s.sparepartId = i.sparepartId";

// Apply warehouse filter if provided
$params = [];
if (!empty($warehouseID)) {
    $sql .= " AND i.warehouseId = ?i";
    $params[] = $warehouseID;
}

// Apply search filter if a query is provided
$sql .= " WHERE s.active = 1";
if (!empty($search)) {
    $sql .= " AND (s.partNumber LIKE ?s OR s.description LIKE ?s OR s.internalReference LIKE ?s)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

$sql .= " GROUP BY s.sparepartId LIMIT ?i";
$params[] = $limit;

// Execute query using SafeMySQL
$rows = $db->getAll($sql, ...$params);

$data = [];
foreach ($rows as $row) {
    // Validate or set leadTime
    $leadTime = (!empty($row['leadTime']) && DateTime::createFromFormat('Y-m-d', $row['leadTime']))
        ? $row['leadTime']
        : date('Y-m-d', strtotime('+14 days'));

    $data[] = [
        'id' => $row['sparepartId'],
        'text' => $row['partNumber'],
        'desc' => $row['description'],
        'internalReference' => $row['internalReference'],
        'leadtime' => $leadTime,
        'salesprice' => $row['salesPrice'],
        'hscode' => $row['hsCode'],
        'hspercentage' => $row['hsPercentage'],
        'uomid' => $row['uomId'],
        'uom' => getUOMNameFromID($row['uomId']),
        'totalAvailable' => (int) $row['totalAvailable'] // Convert to integer for consistency
    ];
}

// Return JSON response
echo json_encode(['results' => $data]);
