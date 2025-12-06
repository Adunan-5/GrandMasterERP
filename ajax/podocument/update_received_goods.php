<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";
// Read the raw POST data
$rawData = file_get_contents("php://input");



// Decode the JSON data into a PHP array
$data = json_decode($rawData, true);



if ($data) {
    // Extract the repeater data and additional data
    $lineItems = $data['lineItems']['line-item'];
    $poID        = $data['poID'];

    foreach ($lineItems as $key => $values) {

        $sparepartID = $values['sparepartID'];
        $acceptedQty = isset($values['accepted']) && $values['accepted'] !== '' ? intval($values['accepted']) : 0;
        $rejectedQty = isset($values['rejected']) && $values['rejected'] !== '' ? intval($values['rejected']) : 0;
        $pendingQty = isset($values['pending']) && $values['pending'] !== '' ? intval($values['pending']) : 0;
        $rejectReason = $values['rejectReason'];
        $aisle = $values['aisle'] ?? null;
        $bin = $values['bin'] ?? null;
        $lot = $values['lot'] ?? null;
        $subTotal = isset($values['subTotal']) && $values['subTotal'] !== '' ? floatval($values['subTotal']) : null;

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

    echo json_encode(['status' => 'success', 'message' => 'Received Goods Updated Successfully.', 'documentId' => $poID]);

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing data']);
}


