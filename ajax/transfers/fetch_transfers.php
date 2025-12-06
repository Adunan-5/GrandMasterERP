<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

try {

    $baseQuery = "SELECT `transfer_documents`.*
                    FROM
                      `transfer_documents`
                      LEFT JOIN `users` ON `transfer_documents`.`salesPersonId` = `users`.`userID`
                  WHERE `transfer_documents`.`active` = 1";


    $res = $db->query($baseQuery);

    // Fetch data
    $data = [];
    while ($row = mysqli_fetch_assoc($res)) {
        //modifiers
        $transferId = $row['transferId'];
        $row['transferNumber'] = getTransferNumberFromDocumentID($transferId);
        $row['originWHName'] = getWarehouseNameFromID($row['originWHId']);
        $row['destinationWHName'] = getWarehouseNameFromID($row['destinationWHId']);
        $data[] = $row;
    }


    // Response format for DataTables
    $response = [
        "data" => $data
    ];

    // Output JSON response
    echo json_encode($response);
} catch (Exception $e) {
    // Handle error
    echo json_encode([
        "error" => "An error occurred: " . $e->getMessage()
    ]);
}