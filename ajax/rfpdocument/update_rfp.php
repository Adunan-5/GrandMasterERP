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

    $rfpID        = $data['rfpID'];
    $supplierID   = $data['supplierID'];
    $warehouseID   = $data['warehouseID'];
    $rfpDate      = $data['rfpDate'];
    $paymentTerms = $data['paymentTerms'];
    $refDocID     = $data['refDocID'];
    $reasonForRFP = $data['reasonForRFP'];

    $refDocID = (int)trim($refDocID);


    try {
        if (empty($rfpID)) {
            //Create a new document
            $res                = $db->query("INSERT INTO `rfp_documents` (`supplierId`, `warehouseId`, `rfpDateCreated`, `salesPersonId`, `paymentTermId`, `rfpReason`,`saleOrderId` ) VALUES (?s, ?s, ?s, ?s, ?s, ?s, ?i)", $supplierID, $warehouseID, $rfpDate, getUserIDOfCurrentUser(), $paymentTerms, $reasonForRFP, $refDocID);
            $modifiedDocumentId = $db->insertId();
//            recordKeyDocumentHistory($modifiedDocumentId, OT_NEW_QUOTATION,logActivity(OT_NEW_QUOTATION) );

        } else {

            $res = $db->query("UPDATE `rfp_documents` SET `rfpDateCreated`= ?s, `paymentTermId`=?s, `supplierId`=?s, `warehouseId`=?s, `rfpReason`=?s, `saleOrderId` = ?s WHERE `rfpId`=?s", $rfpDate, $paymentTerms, $supplierID, $warehouseID, $reasonForRFP,$refDocID, $rfpID );
//            recordKeyDocumentHistory($documentId, OT_SAVE_QUOTATION_WITH_ATTACHMENT, logActivity(OT_SAVE_QUOTATION_WITH_ATTACHMENT), $poAttachment);
            $modifiedDocumentId = $rfpID;
        }

    } catch (Exception $e) {
        error_log("Error inserting document info: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => "Inserting Document Info: " . $e->getMessage()]);
        exit();
    }

    // Process the lineitem data
    //Delete all the lineItems before inserting.
    $res = $db->query("DELETE FROM rfp_line_items WHERE rfpId = ?s", $modifiedDocumentId);
    foreach ($lineItems as $key => $values) {

        $sparepartID = $values['sparepartID'];
        $qty = $values['qty'];
        $uom = $values['uom'];
        $cost = isset($values['cost']) && $values['cost'] !== '' ? floatval($values['cost']) : null;
        $hsCode = $values['hsCode'] ?? null;
        $aisle = $values['aisle'] ?? null;
        $bin = $values['bin'] ?? null;
        $lot = $values['lot'] ?? null;
        $subTotal = isset($values['subTotal']) && $values['subTotal'] !== '' ? floatval($values['subTotal']) : null;

        if (!empty($sparepartID)) {
            try {
                $res = $db->query("INSERT INTO `rfp_line_items` 
                                                    (`rfpId`, `itemId`, `quantity`, `UOM`, `costPrice`, `hsCode`, `aisle`, `bin`, `lotSerial`, `subTotal`, `isDeleted`, `active`)
                                                    VALUES ( ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, 0, 1)",
                    $modifiedDocumentId,
                    $sparepartID,
                    $qty,
                    $uom,
                    $cost,
                    $hsCode,
                    $aisle,
                    $bin,
                    $lot,
                    $subTotal);
            } catch (Exception $e) {
                error_log("Error inserting line items: " . $e->getMessage());
                echo json_encode(['status' => 'error', 'message' => "Line Items Error: " . $e->getMessage()]);
            }
        }
    }

    echo json_encode(['status' => 'success', 'message' => 'RFP saved successfully.', 'documentId' => $modifiedDocumentId]);

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing data']);
}

