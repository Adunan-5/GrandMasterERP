<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

try {

    $baseQuery = "SELECT `order_management_documents`.*
                    FROM
                      `order_management_documents`
                      LEFT JOIN `users` ON `order_management_documents`.`salesPersonId` = `users`.`userID`
                  WHERE `order_management_documents`.`active` = 1";


    $res = $db->query($baseQuery);

    // Fetch data
    $data = [];
    while ($row = mysqli_fetch_assoc($res)) {
        //modifiers
        $orderId = $row['orderManagementId'];
        $row['shipmentNumber'] = getShipmentNumberFromDocumentID($orderId);
        $row['originWHName'] = getWarehouseNameFromID($row['originWHId']);
        $row['destinationWHName'] = getWarehouseNameFromID($row['destinationWHId']);
        $row['customerAccount'] = !empty($row['customerAccount']) ? $row['customerAccount'] : '-';
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