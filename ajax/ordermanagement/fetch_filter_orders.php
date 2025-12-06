<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

try {
    $filter = filter_input(INPUT_GET, 'filter', FILTER_SANITIZE_SPECIAL_CHARS);

    if ($filter == 'SALES_ORDER') {
        try {
            $baseQuery = "SELECT
                      omd.*
                    FROM
                      `order_management_documents` omd
                    WHERE
                      omd.transactionType = 'SALESORDER'
                      AND omd.active = 1";

            $res = $db->query($baseQuery);

            // Fetch data
            $data = [];
            while ($row = mysqli_fetch_assoc($res)) {
                // Modifiers
                $row['orderNumber'] = $row['orderNumber'];
                $row['orderWHStatus'] = $row['orderWHStatus'];
                $row['originWHName'] = getWarehouseNameFromID($row['originWHId']) ?: '-';
                $row['destinationWHName'] = getFormattedCustomerAddressByID($row['customerId']);
                $row['customerAccount'] = getCustomerCodeFromID($row['customerId']);
                $row['transactionType'] = 'Sales Order';
                $row['shipmentNumber'] = getShipmentNumberFromDocumentID($row['documentId'], 'SALESORDER')?: '-';
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
    }

    if ($filter == 'TRANSFER_WH') {
        try {
            $baseQuery = "SELECT
                      omd.*
                    FROM
                      `order_management_documents` omd
                    WHERE
                      omd.transactionType = 'TRANSFER'
                      AND omd.active = 1";

            $res = $db->query($baseQuery);

            // Fetch data
            $data = [];
            while ($row = mysqli_fetch_assoc($res)) {
                // Modifiers
                $row['documentId'] = $row['documentId'];
                $row['orderNumber'] = $row['orderNumber'];
                $row['orderWHStatus'] = $row['orderWHStatus'];
                $row['originWHName'] = getWarehouseNameFromID($row['originWHId']);
                $row['destinationWHName'] = getWarehouseNameFromID($row['destinationWHId']);
                $row['customerAccount'] = '-';
                $row['transactionType'] = 'TRANSFER';
                $row['shipmentNumber'] = getShipmentNumberFromDocumentID($row['documentId'], 'TRANSFER')?: '-';
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
    }

    if ($filter == 'ALL') {
        try {

            $baseQuery = "
                SELECT *
                FROM
                    `pick_and_pack_line_items`
                WHERE active = 1
            ";

            $res = $db->query($baseQuery);


            // Fetch data and apply modifiers
            $data = [];
            while ($row = mysqli_fetch_assoc($res)) {

                $orderRes = $db->query("SELECT * FROM `order_management_documents` WHERE ordermanagementId = ?s AND active = 1", $row['orderManagementId'] );
                $orderRow = mysqli_fetch_assoc($orderRes);

                $row['internalRef'] = getInternalReferenceNumberForSparepartID($row['itemId']);
                $row['orderNumber'] = $orderRow['orderNumber'];
                $row['originWHName'] = getWarehouseNameFromID($orderRow['originWHId']) ?: '-';
                $row['originWHCode'] = getWarehouseCodeFromID($orderRow['originWHId']) ?: '-';
                $row['transactionType'] = $orderRow['transactionType'];
                $row['documentId'] = $orderRow['documentId'];
                $row['shipmentNumber'] = getShipmentNumberFromOrderAndIPickAndPackID($row['orderManagementId'], $row['pickAndPackLineItemId']);

                if ($orderRow['transactionType'] == 'SALESORDER') {
                    $row['destinationWHName'] = getFormattedCustomerAddressByID($orderRow['customerId']);
                    $row['customerAccount'] = getCustomerCodeFromID($orderRow['customerId']);

                } else if($orderRow['transactionType'] == 'TRANSFER') {
                    $row['destinationWHName'] = getWarehouseNameFromID($orderRow['destinationWHId']);
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
    }
} catch (Exception $e) {
    echo json_encode([
        "error" => "An error occurred: " . $e->getMessage()
    ]);
}