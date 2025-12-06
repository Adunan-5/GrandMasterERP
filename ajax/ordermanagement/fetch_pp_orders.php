<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

try {

    $baseQuery = "
                SELECT *
                FROM
                    `pick_and_pack_documents`
                WHERE active = 1
            ";

    $res = $db->query($baseQuery);


    // Fetch data and apply modifiers
    $data = [];
    while ($row = mysqli_fetch_assoc($res)) {

        $row['gdnNumber'] = getGDNNumberFromDocumentID($row['gdnId']);
        $row['originWHName'] = getWarehouseNameFromID($row['originWHID']) ?: '-';

        if ($row['transactionType'] == 'SALESORDER') {
            $row['internalRef'] = getSalesOrderNumberFromDocumentID($row['documentId']);
            $row['destinationWHName'] = getFormattedCustomerAddressByID($row['customerId']);
            $row['customerAccount'] = getCustomerCodeFromID($row['customerId']);

        } else if($row['transactionType'] == 'TRANSFER') {
            $row['internalRef'] = getTransferNumberFromDocumentID($row['documentId']);
            $row['destinationWHName'] = getWarehouseNameFromID($row['destinationWHID']);
            $row['customerAccount'] = '-';
        }
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