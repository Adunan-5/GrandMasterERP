<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

try {

    $companyId = intval($_SESSION['SES_SELECTED_COMPANY']);

    $baseQuery = "SELECT * FROM accounts_receivable WHERE companyId = ?s;";

    $res = $db->query($baseQuery, $companyId);

    // Fetch data
    $data = [];
    while ($row = mysqli_fetch_assoc($res)) {

        $row['customerName'] = getCustomerNameFromID($row['customerId']);
        $row['invoiceNumber'] = getInvoiceNumberFromInvoiceID($row['invoiceId']);
        $row['pendingAmount'] = number_format($row['totalAmount'] - $row['receivedAmount'], 2, '.', '');
        $data[] = $row;
    }

    // Response format for DataTables
    $response = [
        "data" => $data
    ];

    echo json_encode($response);
} catch (Exception $e) {
    echo json_encode([
        "error" => "An error occurred: " . $e->getMessage()
    ]);
}