<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

// Read the POST data
$rfpID = filter_input(INPUT_POST, 'rfpID', FILTER_VALIDATE_INT);

if ($rfpID === false || $rfpID === null) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid RFP ID']);
    exit();
}

try {
    // Query to check if a PO exists for the given rfpID
    $res = $db->query("SELECT poId FROM purchase_orders WHERE rfpId = ?s AND active = 1", $rfpID);
    $row = mysqli_fetch_assoc($res);

    if ($row && isset($row['poId'])) {
        // PO exists, return its ID
        echo json_encode(['status' => 'success', 'poExists' => true, 'poId' => $row['poId']]);
    } else {
        // No PO exists for this rfpID
        echo json_encode(['status' => 'success', 'poExists' => false, 'poId' => null]);
    }
} catch (Exception $e) {
    error_log("Error checking PO existence: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => "Database error: " . $e->getMessage()]);
    exit();
}