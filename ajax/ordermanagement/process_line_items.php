<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

$orderManagementId = filter_input(INPUT_POST, 'orderManagementId', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$itemsJson = filter_input(INPUT_POST, 'items', FILTER_DEFAULT);
$items = json_decode($itemsJson, true);
$status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

if (!$orderManagementId || !is_array($items) || empty($items)) {
    echo json_encode(['status' => "ERROR", 'message' => 'Invalid orderManagementId or items']);
    exit;
}

if ($status === ORDER_WH_STATUS_RESERVED) {
    try {
        $db->query("START TRANSACTION");

        $shipmentNumberPrefix = PREFIX_SHIPMENT;
        $shipmentNumber = getNextNewShipmentNumber();


        foreach ($items as $item) {
            $itemId = filter_var($item['itemId'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $releasedQty = (int)$item['releasedQty'];

            if (!$itemId || $releasedQty <= 0) {
                throw new Exception("Invalid itemId or releasedQty");
            }

            $line = $db->getRow(
                "SELECT quantity, releasedQty FROM order_management_line_items WHERE orderManagementId = ?s AND itemId = ?s",
                $orderManagementId, $itemId
            );

            if (!$line) {
                throw new Exception("Line item not found for itemId $itemId");
            }

            $quantity = (int)$line['quantity'];
            $currentReleasedQty = (int)$line['releasedQty'];
            $newReleasedQty = $currentReleasedQty + $releasedQty;

            if ($newReleasedQty > $quantity) {
                throw new Exception("Cannot release more than ordered quantity for itemId $itemId");
            }

            $newStatus = ($newReleasedQty < $quantity) ? ORDER_WH_STATUS_PARTIALLY_RESERVED : ORDER_WH_STATUS_RESERVED;


            $originWHId = $db->getOne(
                "SELECT originWHId FROM order_management_line_items WHERE orderManagementId = ?s AND itemId = ?s",
                $orderManagementId, $itemId
            );

            if (!$originWHId) {
                throw new Exception("originWHId not found for itemId $itemId in orderManagementId $orderManagementId");
            }

            $res = $db->query(
                "INSERT INTO `pick_and_pack_line_items` (`orderManagementId`, `itemId`, `quantity`, `orderWHStatus`, `originWHId`, `shipmentNumberPrefix`, `shipmentNumber`) 
                    VALUES (?s, ?s, ?s, ?s, ?s, ?s, ?s)",
                $orderManagementId, $itemId, $releasedQty, $status, $originWHId, $shipmentNumberPrefix, $shipmentNumber
            );

            $res = $db->query("UPDATE order_management_line_items 
                 SET releasedQty = ?s, orderWHStatus = ?s 
                 WHERE orderManagementId = ?s AND itemId = ?s",
                $newReleasedQty, $newStatus, $orderManagementId, $itemId);

//            if (!$res || $db->affectedRows() <= 0) {
//                throw new Exception("No matching line item found for orderManagementId $orderManagementId and itemId $itemId");
//            }
        }

        $statuses = $db->getCol(
            "SELECT orderWHStatus FROM order_management_line_items WHERE orderManagementId = ?s",
            $orderManagementId
        );

        $statuses = $db->getCol(
            "SELECT orderWHStatus FROM order_management_line_items WHERE orderManagementId = ?s",
            $orderManagementId
        );
        $uniqueStatuses = array_unique($statuses);

        if (count($uniqueStatuses) === 1) {
            $documentStatus = $uniqueStatuses[0];
        } else {
            // Mixed statuses like RESERVED + STAGED → PARTIALLY_RESERVED
            $documentStatus = ORDER_WH_STATUS_PARTIALLY_RESERVED;
        }

// Update order_management_documents
        $db->query(
            "UPDATE order_management_documents SET orderWHStatus = ?s WHERE orderManagementId = ?s",
            $documentStatus,
            $orderManagementId
        );

        $documentData = $db->getRow(
            "SELECT documentId, transactionType FROM order_management_documents WHERE orderManagementId = ?s",
            $orderManagementId
        );

        if ($documentData && $documentData['transactionType'] === 'SALESORDER') {
            $db->query(
                "UPDATE key_documents SET orderWHStatus = ?s WHERE documentId = ?s",
                $documentStatus,
                $documentData['documentId']
            );
        }

        $db->query("COMMIT");
        echo json_encode(['status' => 'SUCCESS', 'message' => 'Items released successfully']);
    } catch (Exception $e) {
        $db->query("ROLLBACK");
        error_log("Error bulk releasing line items: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Error releasing line items: ' . $e->getMessage()]);
    }
}

if ($status === ORDER_WH_STATUS_RETURNED) {
    try {
        $db->query("START TRANSACTION");

        foreach ($items as $item) {
            $itemId = filter_var($item['itemId'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            if (!$itemId) {
                throw new Exception("Invalid itemId: $itemId");
            }

            $res = $db->query(
                "UPDATE order_management_line_items 
                 SET orderWHStatus = ?s 
                 WHERE orderManagementId = ?s AND itemId = ?s",
                $status, $orderManagementId, $itemId
            );

//            if (!$res || $db->affectedRows() <= 0) {
//                throw new Exception("No matching line item found for orderManagementId $orderManagementId and itemId $itemId");
//            }
        }

        $db->query("COMMIT");
        echo json_encode(['status' => 'success', 'message' => 'Items returned successfully']);
    } catch (Exception $e) {
        $db->query("ROLLBACK");
        error_log("Error returning line items: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Error returning line items: ' . $e->getMessage()]);
    }
}

exit;
