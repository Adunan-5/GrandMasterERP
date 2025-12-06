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
    $rfpDate      = $data['rfpDate'];
    $paymentTerms = $data['paymentTerms'];
    $refDocID     = $data['refDocID'];
    $reasonForRFP = $data['reasonForRFP'];
    $carrier = $data['carrier'];
    $etaDate = $data['etaDate'];
    $supplierQuotationAttachmentFilename = $data['supplierQuotationAttachmentFileName'];
    $supplierQuotationNo = $data['supplierQuotationNo'];
    $tracking = $data['tracking'];
    $shippingCost = isset($data['shippingCost']) && $data['shippingCost'] != '' ? floatval($data['shippingCost']) : null;
    $supplierCurrencyID = $data['supplierCurrency'] ?? null;

    $refDocID = (int)trim($refDocID);


    try {
        if (empty($rfpID)) {
            //Create a new document
            $res                = $db->query("INSERT INTO `consultation_rfp_documents` (`supplierId`, `rfpDateCreated`, `salesPersonId`, `paymentTermId`, `rfpReason`,`saleOrderId`, `shippingMethod`, `etaDate`, `supplierQuotationAttachment`, `supplierQuotationNumber`, `trackingNumber`, `shippingCost`, `currencyId` ) VALUES (?s, ?s, ?s, ?s, ?s, ?i, ?s, ?s, ?s, ?s, ?s, ?s, ?s)", $supplierID, $rfpDate, getUserIDOfCurrentUser(), $paymentTerms, $reasonForRFP, $refDocID, $carrier, $etaDate, $supplierQuotationAttachmentFilename, $supplierQuotationNo, $tracking, $shippingCost, $supplierCurrencyID);
            $modifiedDocumentId = $db->insertId();
        //    recordConsultationRFPHistory($modifiedDocumentId, OT_NEW_RFP,logActivity(OT_NEW_RFP) );

            // Add supplier quotation info to history if available
            $quotationInfo = !empty($supplierQuotationAttachmentFilename) ? $supplierQuotationAttachmentFilename : (!empty($supplierQuotationNo) ? $supplierQuotationNo : null);
            if ($quotationInfo) {
                recordConsultationRFPHistory($modifiedDocumentId, OT_NEW_RFP, logActivity(OT_NEW_RFP), $quotationInfo);
            } else {
                recordConsultationRFPHistory($modifiedDocumentId, OT_NEW_RFP, logActivity(OT_NEW_RFP));
            }

        } else {

            // Fetch existing RFP data to compare
            $existingRfp = "";
            $res = $db->query("SELECT supplierQuotationAttachment, supplierQuotationNumber FROM consultation_rfp_documents WHERE rfpId = ?s", $rfpID);
            while($row = mysqli_fetch_assoc($res)) {
                $existingRfp = $row;
            }

            $res = $db->query("UPDATE `consultation_rfp_documents` SET 
                `supplierId` = ?s, 
                `rfpDateCreated` = ?s, 
                `salesPersonId` = ?s, 
                `paymentTermId` = ?s, 
                `rfpReason` = ?s, 
                `saleOrderId` = ?i, 
                `shippingMethod` = ?s, 
                `etaDate` = ?s, 
                `supplierQuotationAttachment` = ?s, 
                `supplierQuotationNumber` = ?s, 
                `trackingNumber` = ?s,
                `shippingCost` = ?s,
                `currencyId` = ?s
                WHERE `rfpId` = ?s",
                $supplierID,
                $rfpDate,
                getUserIDOfCurrentUser(),
                $paymentTerms,
                $reasonForRFP,
                $refDocID,
                $carrier,
                $etaDate,
                $supplierQuotationAttachmentFilename,
                $supplierQuotationNo,
                $tracking,
                $shippingCost,
                $supplierCurrencyID,
                $rfpID
            );

            $modifiedDocumentId = $rfpID;

            // recordConsultationRFPHistory($modifiedDocumentId, OT_EDIT_RFP,logActivity(OT_EDIT_RFP) );
            // Check if quotation fields have changed
            $quotationInfo = null;
            if (!empty($supplierQuotationAttachmentFilename) && $supplierQuotationAttachmentFilename !== $existingRfp['supplierQuotationAttachment']) {
                $quotationInfo = $supplierQuotationAttachmentFilename;
            } elseif (!empty($supplierQuotationNo) && $supplierQuotationNo !== $existingRfp['supplierQuotationNumber']) {
                $quotationInfo = $supplierQuotationNo;
            }

            if ($quotationInfo) {
                recordConsultationRFPHistory($modifiedDocumentId, OT_EDIT_RFP_WITH_ATTACHMENT, logActivity(OT_EDIT_RFP_WITH_ATTACHMENT), $quotationInfo);
            } else {
                recordConsultationRFPHistory($modifiedDocumentId, OT_EDIT_RFP, logActivity(OT_EDIT_RFP));
            }
        }

    } catch (Exception $e) {
        error_log("Error inserting document info: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => "Inserting Document Info: " . $e->getMessage()]);
        exit();
    }

    // Process the lineitem data
    //Delete all the lineItems before inserting.
    $res = $db->query("DELETE FROM consultation_rfp_line_items WHERE rfpId = ?s", $modifiedDocumentId);
    foreach ($lineItems as $key => $values) {

        $serviceItemID = $values['serviceItemID'];
        $qty = $values['qty'];
        $uom = $values['uom'];
        $cost = isset($values['cost']) && $values['cost'] !== '' ? floatval($values['cost']) : null;
        $subTotal = isset($values['subTotal']) && $values['subTotal'] !== '' ? floatval($values['subTotal']) : null;

        if (!empty($serviceItemID)) {
            try {
                $res = $db->query("INSERT INTO `consultation_rfp_line_items` 
                                                    (`rfpId`, `itemId`, `quantity`, `UOM`, `costPrice`, `subTotal`, `isDeleted`, `active`)
                                                    VALUES ( ?s, ?s, ?s, ?s, ?s, ?s, 0, 1)",
                    $modifiedDocumentId,
                    $serviceItemID,
                    $qty,
                    $uom,
                    $cost,
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

