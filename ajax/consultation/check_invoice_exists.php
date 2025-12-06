<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

// Read the POST data
$gdnID = filter_input(INPUT_POST, 'refDocID', FILTER_VALIDATE_INT);

if ($gdnID === false || $gdnID === null) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid GDN ID']);
    exit();
}

try {
    // Query to check if a Invoice ID exists for the given gdnID
    $res = $db->query("SELECT invoiceId FROM invoice_documents WHERE gdnId = ?s AND active = 1", $gdnID);
    $row = mysqli_fetch_assoc($res);

    if ($row && isset($row['invoiceId'])) {
        // Invoice exists, return its ID
        echo json_encode(['status' => 'success', 'invoiceExists' => true, 'invoiceId' => $row['invoiceId']]);
    } else {
        // No Invoice exists for this gdnID
        echo json_encode(['status' => 'success', 'invoiceExists' => false, 'invoiceId' => null]);
    }
} catch (Exception $e) {
    error_log("Error checking Invoice existence: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => "Database error: " . $e->getMessage()]);
    exit();
}