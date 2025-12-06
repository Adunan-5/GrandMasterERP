<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

$data = json_decode(file_get_contents('php://input'), true);

$poID = null;
if (is_array($data)) {
    if (isset($data['poID'])) {
        $poID = filter_var($data['poID'], FILTER_VALIDATE_INT);
    }
}

if ($poID === false || $poID === null) {
    $response = [
        "status"  => "ERROR",
        "message" => "Invalid or missing PO ID."
    ];
    echo json_encode($response);
    exit();
}

try {
    $db->query("START TRANSACTION");

    $poRes = $db->query("SELECT * FROM purchase_orders WHERE poId = ?s", $poID);
    $poRow = mysqli_fetch_assoc($poRes);
    if (!$poRow) {
        $db->query("ROLLBACK");
        $response = [
            "status"  => "ERROR",
            "message" => "Purchase Order not found."
        ];
        echo json_encode($response);
        exit();
    }
    $warehouseID = $poRow['warehouseId'];
    $poReason = $poRow['rfpReason'];
    $poStatus = $poRow['poStatus'];

    if ($poStatus == PO_STATUS_GOODS_RECEIVED) {
        $db->query("ROLLBACK");
        $response = [
            "status"  => "ERROR",
            "message" => "Purchase Order already marked as goods received."
        ];
        echo json_encode($response);
        exit();
    }

    // Check if all quantities match
    $lineItemsRes = $db->query("SELECT * FROM po_line_items WHERE poId = ?s", $poID);
    $allQuantitiesMatch = true;
    while ($lineItem = mysqli_fetch_assoc($lineItemsRes)) {
        if (
            !isset($lineItem['quantity']) ||
            !isset($lineItem['acceptedQty']) ||
            $lineItem['acceptedQty'] !== $lineItem['quantity']
        ) {
            $allQuantitiesMatch = false;
            break;
        }
    }

    $res = $db->query("SELECT * FROM po_line_items WHERE poId = ?s", $poID);
    $receivableItems = $db->numRows($res);
    while ($row = mysqli_fetch_assoc($res)) {
        $sparepartId = $row['itemId'];
        $quantityToUse = (int) $row['acceptedQty'];
        $aisle = $row['aisle'] ?? null;
        $bin = $row['bin'] ?? null;
        $lotSerial = $row['lotSerial'] ?? null;
        if ($quantityToUse <= 0) {
            continue;
        }

        $currentQuantity = 0;
        $currentQuantityRes = $db->query(
            "SELECT quantity FROM inventory_stock 
            WHERE warehouseId = ?s AND sparepartId = ?s",
            $warehouseID,
            $row['itemId']
        );
        if ($currentQuantityRow = mysqli_fetch_assoc($currentQuantityRes)) {
            $currentQuantity = (int) $currentQuantityRow['quantity'];
        }

        $transactionRes = $db->query(
            "SELECT * 
            FROM inventory_transactions 
            WHERE referenceId = ?s AND sparepartId = ?s AND warehouseId = ?s 
            ORDER BY createdAt DESC LIMIT 1",
            $poID,
            $sparepartId,
            $warehouseID
        );
        $transactionRow = mysqli_fetch_assoc($transactionRes);
        $previousQty = $transactionRow ? (int) $transactionRow['quantity'] : 0;
        $transactionId = $transactionRow ? $transactionRow['transactionId'] : null;

        $quantityDiff = $quantityToUse - $previousQty;

        if ($transactionId) {
            $db->query(
                "UPDATE inventory_transactions
                 SET quantity = ?s
                 WHERE transactionId = ?s",
                $quantityToUse,
                $transactionId
            );
        } else {
            $db->query(
                "INSERT INTO inventory_transactions 
                (sparepartId, warehouseId, transactionType, quantity, referenceId) 
                VALUES (?s, ?s, 'INBOUND', ?s, ?s)",
                $sparepartId,
                $warehouseID,
                $quantityToUse,
                $poID
            );
        }

        $newQuantity = $currentQuantity + $quantityDiff;

        $db->query(
            "INSERT INTO inventory_ledger 
            (sparepartId, updatedBy, updateReason, quantityBeforeUpdate, newQuantity) 
            VALUES (?s, ?s, ?s, ?s, ?s)",
            $row['itemId'],
            getAuthenticatedUser()->userID,
            $poReason,
            $currentQuantity,
            $newQuantity
        );

        $db->query(
            "INSERT INTO inventory_stock 
            (sparepartId, warehouseId, quantity, aisle, bin, lotSerial) 
            VALUES (?s, ?s, ?s, ?s, ?s, ?s) 
            ON DUPLICATE KEY UPDATE 
            quantity = quantity + ?s, aisle = ?s, bin = ?s, lotSerial = ?s",
            $row['itemId'],
            $warehouseID,
            $quantityToUse,
            $aisle,
            $bin,
            $lotSerial,
            $quantityDiff,
            $aisle,
            $bin,
            $lotSerial
        );

        if ($allQuantitiesMatch) {
            $db->query(
                "UPDATE po_line_items 
                SET itemReceived = 1 
                WHERE poLineItemId = ?s",
                $row['poLineItemId']
            );
        }
    }

    if ($receivableItems < 1 || !$allQuantitiesMatch) {
        $db->query("ROLLBACK");
        $response = [
            "status"  => "ERROR",
            "message" => "Items must be fully received or quantities do not match."
        ];
        echo json_encode($response);
        exit();
    }

    $db->query("UPDATE purchase_orders SET poStatus = ?s WHERE poId = ?s", PO_STATUS_GOODS_RECEIVED, $poID);
    $db->query("COMMIT");

    echo json_encode(['status' => 'SUCCESS', 'message' => 'Inventory Updated Successfully.', 'documentId' => $poID]);
} catch (Exception $e) {
    $db->query("ROLLBACK");
    echo json_encode(['status' => 'ERROR', 'message' => 'Invalid or missing data', 'console' => json_encode($e->getMessage())]);
}