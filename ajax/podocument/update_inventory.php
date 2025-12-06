<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

$data = json_decode(file_get_contents('php://input'), true);

$poID = null;
$lineItems = [];

if (is_array($data) && isset($data['poID']) && isset($data['lineItems'])) {
    $poID = filter_var($data['poID'], FILTER_VALIDATE_INT);
    $lineItems = $data['lineItems'];
}

if (is_array($data) && isset($data['poID'])) {
    $poID = filter_var($data['poID'], FILTER_VALIDATE_INT);
}

if ($poID === false || $poID === null || empty($lineItems)) {
    $response = [
        "status"  => "ERROR",
        "message" => "Invalid or missing PO ID or line items."
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

    foreach ($lineItems as $item) {
        $sparepartID = $item['sparepartID'];
        $acceptedQty = isset($item['accepted']) && $item['accepted'] !== '' ? intval($item['accepted']) : 0;
        $rejectedQty = isset($item['rejected']) && $item['rejected'] !== '' ? intval($item['rejected']) : 0;
        $pendingQty = isset($item['pending']) && $item['pending'] !== '' ? intval($item['pending']) : 0;
        $rejectReason = $item['rejectReason'] ?? null;
        $aisle = $item['aisle'] ?? null;
        $bin = $item['bin'] ?? null;
        $lot = $item['lot'] ?? null;

        if (!empty($sparepartID)) {
            try {
                $res = $db->query("UPDATE `po_line_items`
                                    SET 
                                        `acceptedQty` = ?s,
                                        `rejectedQty` = ?s,
                                        `pendingQty` = ?s,
                                        `rejectReason` = ?s,
                                        `aisle` = ?s,
                                        `bin` = ?s,
                                        `lotSerial` = ?s
                                    WHERE `poId` = ?s AND `itemId` = ?s",
                    $acceptedQty, $rejectedQty, $pendingQty, $rejectReason, $aisle, $bin, $lot, $poID, $sparepartID);

                if (!$res || $db->affectedRows() < 0) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => "No matching line item found for poId $poID and itemId $sparepartID"
                    ]);
                    exit();
                }
            } catch (Exception $e) {
                error_log("Error updating line items: " . $e->getMessage());
                echo json_encode(['status' => 'error', 'message' => "Line Items Error: " . $e->getMessage()]);
            }
        }
    }

    $lineItemsRes = $db->query(
        "SELECT * FROM po_line_items 
        WHERE poId = ?s 
        AND acceptedQty > 0",
        $poID
    );
    $receivableItems = $db->numRows($lineItemsRes);

    if ($receivableItems < 1) {
        $db->query("ROLLBACK");
        $response = [
            "status"  => "ERROR",
            "message" => "No receivable items found."
        ];
        echo json_encode($response);
        exit();
    }

    $processedItems = 0;
    while ($lineItem = mysqli_fetch_assoc($lineItemsRes)) {
        $sparepartId = $lineItem['itemId'];
        $acceptedQty = (int) $lineItem['acceptedQty'];
        $poLineItemId = $lineItem['poLineItemId'];
        $aisle = $lineItem['aisle'] ?? null;
        $bin = $lineItem['bin'] ?? null;
        $lotSerial = $lineItem['lotSerial'] ?? null;

        if ($acceptedQty <= 0) {
            continue;
        }

        $transactionRes = $db->query(
            "SELECT transactionId, quantity 
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

        $quantityDiff = $acceptedQty - $previousQty;

        if ($transactionId) {
            $db->query(
                "UPDATE inventory_transactions 
                SET quantity = ?s 
                WHERE transactionId = ?s",
                $acceptedQty,
                $transactionId
            );
        } else {
            $db->query(
                "INSERT INTO inventory_transactions 
                (sparepartId, warehouseId, transactionType, quantity, referenceId) 
                VALUES (?s, ?s, 'INBOUND', ?s, ?s)",
                $sparepartId,
                $warehouseID,
                $acceptedQty,
                $poID
            );
        }

        $currentQuantity = 0;
        $inventoryRes = $db->query(
            "SELECT quantity FROM inventory_stock 
            WHERE warehouseId = ?s AND sparepartId = ?s",
            $warehouseID, $sparepartId
        );
        if ($inventoryRow = mysqli_fetch_assoc($inventoryRes)) {
            $currentQuantity = (int) $inventoryRow['quantity'];
        }

        $newQuantity = $currentQuantity + $quantityDiff;

        $db->query(
            "INSERT INTO inventory_ledger 
            (sparepartId, updatedBy, updateReason, quantityBeforeUpdate, newQuantity) 
            VALUES (?s, ?s, ?s, ?s, ?s)",
            $sparepartId,
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
            $sparepartId,
            $warehouseID,
            $acceptedQty,
            $aisle,
            $bin,
            $lotSerial,
            $quantityDiff,
            $aisle,
            $bin,
            $lotSerial
        );

        $processedItems++;
    }

    $db->query("COMMIT");

    $response = [
        "status"  => "SUCCESS",
        "message" => "Inventory Updated Successfully.",
        'documentId' => $poID
    ];
    echo json_encode($response);

} catch (Exception $e) {
    $db->query("ROLLBACK");
    error_log("Error updating inventory: " . $e->getMessage());
    $response = [
        "status"  => "ERROR",
        "message" => "Failed to update inventory.",
        "console" => $e->getMessage()
    ];
    echo json_encode($response);
}

exit();