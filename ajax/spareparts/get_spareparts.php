<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$limit = 50; // Limit results

// Base SQL query
$sql = "SELECT * FROM spareparts WHERE active = 1";

// Apply search filter if a query is provided
$params = [];
if (!empty($search)) {
    $sql .= " AND (partNumber LIKE ?s OR name LIKE ?s OR serialNumber LIKE ?s OR description LIKE ?s OR internalReference LIKE ?s)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

$sql .= " LIMIT ?i";
$params[] = $limit;

// Execute query using SafeMySQL
$rows = $db->getAll($sql, ...$params);

$data = [];
foreach ($rows as $row) {
    // Validate or set leadTime
    $leadTime = (!empty($row['leadTime']) && DateTime::createFromFormat('Y-m-d', $row['leadTime']))
        ? $row['leadTime']
        : date('Y-m-d', strtotime('+14 days'));

    $displayText = $row['partNumber']; // Default to partNumber
    if ($row['itemType'] === 'SPAREPART') {
        $displayText = trim($row['partNumber']); // Use partNumber for SPAREPART
    } else if ($row['itemType'] === 'MACHINE') {
        $displayText = $row['serialNumber']; // Use serialNumber for MACHINE
    } else if ($row['itemType'] === 'SERVICE') {
        $displayText = $row['name']; // Use name for SERVICE
    }

    $data[] = [
        'id' => $row['sparepartId'],
        // 'text' => $row['partNumber'],
        'text' => $displayText,
        'desc' => $row['description'],
        'internalReference' => $row['internalReference'],
        'leadtime' => $leadTime,
        'salesprice' => $row['salesPrice'],
        'hscode' => $row['hsCode'],
        'hspercentage' => $row['hsPercentage'],
        'uomid' => $row['uomId'],
        'uom' => getUOMNameFromID($row['uomId']),
    ];
}

// Return JSON response
echo json_encode(['results' => $data]);