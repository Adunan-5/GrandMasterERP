<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

try {

            $baseQuery = "SELECT * FROM `shipments`";

            $res = $db->query($baseQuery);

            // Fetch data
            $data = [];
            while ($row = mysqli_fetch_assoc($res)) {

                $orderManagementId = $row['documentId'];

                $orderRes = $db->query("SELECT * FROM order_management_documents WHERE orderManagementId = ?s", $orderManagementId);
                $orderRow = mysqli_fetch_assoc($orderRes);

                $documentId = $orderRow['documentId'];

                $docRes = $db->query("SELECT * FROM order_management_documents WHERE transactionType = ?s AND documentId = ?s", $row['documentType'], $documentId);
                $docRow = $db->fetch($docRes);

                if($docRow) {
                    $originWHId = $docRow['originWHId'];
                    $destinationWHId = $docRow['destinationWHId'];
                    $customerId = $docRow['customerId'];

                    // Modifiers
                    if ($row['documentType'] == 'SALESORDER') {
                        $row['originWHName'] = getWarehouseNameFromID($originWHId);
                        $row['destinationWHName'] = getFormattedCustomerAddressByID($customerId);
                        $row['carrier'] = !empty(getCarrierFromShipmentID($row['shipmentId'])) ? getCarrierFromShipmentID($row['shipmentId']) : '-';
                        $row['bolNumber'] = !empty(getBOLNumberFromShipmentID($row['shipmentId'])) ? getBOLNumberFromShipmentID($row['shipmentId']) : '-';
                        $row['internalRef'] = getSalesOrderNumberFromDocumentID($documentId);
                        $row['shipmentNumber'] = getShipmentNumberFromDocumentID($documentId, 'SALESORDER') ?: '-';
                    } else if ($row['documentType'] == 'TRANSFER') {
                        $row['originWHName'] = getWarehouseNameFromID($originWHId);
                        $row['destinationWHName'] = getWarehouseNameFromID($destinationWHId);
                        $row['carrier'] = !empty(getCarrierFromShipmentID($row['shipmentId'])) ? getCarrierFromShipmentID($row['shipmentId']) : '-';
                        $row['bolNumber'] = !empty(getBOLNumberFromShipmentID($row['shipmentId'])) ? getBOLNumberFromShipmentID($row['shipmentId']) : '-';
                        $row['internalRef'] = getTransferNumberFromDocumentID($documentId);
                        $row['shipmentNumber'] = getShipmentNumberFromDocumentID($documentId, 'TRANSFER') ?: '-';
                    }
                    $data[] = $row;
                }
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