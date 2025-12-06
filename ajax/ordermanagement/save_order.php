<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

if ($data) {
    $lineItems        = $data['lineItems']['line-item'];
    $originWHID       = $data['originWHID'];
    $orderId          = $data['orderID'];
    $orderNumber      = $data['orderNumber'];
    $isShipmentFilter = $data['isShipmentFilter'];

    $modifiedDocumentId = 0;
    $orderWHStatus      = '';

    // Get current status and document info
    $res = $db->query("SELECT * FROM `order_management_documents` WHERE `orderManagementId` = ?s", $orderId);
    $row = mysqli_fetch_assoc($res);

    if ($row) {
        $orderWHStatus    = $row['orderWHStatus'];
        $documentId       = $row['documentId'];
        $transactionType  = $row['transactionType'];
    }

    try {
        $updatedWHStatus = $orderWHStatus;

        if (!empty($orderId) && $orderWHStatus == ORDER_WH_STATUS_ALLOCATED) {
            $db->query(
                "UPDATE `order_management_documents` SET `originWHId` = ?s, `orderWHStatus` = ?s, `salesPersonId` = ?s WHERE `orderManagementId` = ?s",
                $originWHID,
                ORDER_WH_STATUS_STAGED,
                getUserIDOfCurrentUser(),
                $orderId
            );
            $updatedWHStatus = ORDER_WH_STATUS_STAGED;
            $modifiedDocumentId = $orderId;
        } elseif (!empty($orderId)) {
            $db->query(
                "UPDATE `order_management_documents` SET `originWHId` = ?s, `salesPersonId` = ?s WHERE `orderManagementId` = ?s",
                $originWHID,
                getUserIDOfCurrentUser(),
                $orderId
            );
            $modifiedDocumentId = $orderId;
        }

        // ✅ Update key_documents only if type is SALESORDER
        if (!empty($documentId) && $transactionType === 'SALESORDER') {
            $db->query(
                "UPDATE `key_documents` SET `orderWHStatus` = ?s WHERE `documentId` = ?s",
                $updatedWHStatus,
                $documentId
            );
        }

    } catch (Exception $e) {
        error_log("Error updating documents: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => "Document Update Error: " . $e->getMessage()]);
        exit();
    }

    // Process each line item
    foreach ($lineItems as $key => $values) {
        $sparepartID = $values['sparepartID'];
        $qty         = $values['qty'];
        $lot         = $values['lot'] ?? null;
        $status      = $values['status'] ?? null;

        if (!empty($sparepartID)) {
            try {
                $db->query(
                    "UPDATE `order_management_line_items` SET `lotSerial` = ?s, `orderWHStatus` = ?s, `originWHId` = ?s WHERE `orderManagementId` = ?s AND `itemId` = ?s",
                    $lot,
                    $status,
                    $originWHID,
                    $orderId,
                    $sparepartID
                );
            } catch (Exception $e) {
                error_log("Line Item Update Error: " . $e->getMessage());
                echo json_encode(['status' => 'error', 'message' => "Line Item Error: " . $e->getMessage()]);
                exit();
            }
        }
    }

    echo json_encode([
        'status'      => 'success',
        'message'     => 'Order saved successfully.',
        'documentId'  => $modifiedDocumentId
    ]);

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing data']);
}
