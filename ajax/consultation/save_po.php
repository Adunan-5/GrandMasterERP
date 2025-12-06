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
    $supplierID   = $data['supplierID'];
    $poDate      = $data['poDate'];
    $paymentTerms = $data['paymentTerms'];
    $supplierCurrency = $data['supplierCurrency'];
    $rfpID     = $data['rfpID'];
    $rfpReason = $data['rfpReason'];
    $refDocID     = $data['refDocID'];
    $vatAmount = $data['vatAmount'];
    $carrier = $data['carrier'];
    $etaDate = $data['etaDate'];
    $supplierQuotationAttachmentFilename = $data['supplierQuotationAttachmentFileName'];
    $supplierQuotationNo = $data['supplierQuotationNo'];
    $tracking = $data['tracking'];
    $shippingCost = $data['shippingCost'];
    $taxes = $data['taxes'];
    $paymentRefNo = $data['paymentRefNo'];
    $paymentRefAttachmentFileName = $data['paymentRefAttachmentFileName'];
    $paymentDate = $data['paymentDate'];
    $supplierInvoiceNo = $data['supplierInvoiceNo'];
    $supplierInvoiceAttachmentFileName = $data['supplierInvoiceAttachmentFileName'];
    $supplierInvoiceDate = $data['supplierInvoiceDate'];
    $customduties = $data['customduties'];
    $foreigntransactionfees = $data['foreigntransactionfees'];
    $insurance = $data['insurance'];
    $surcharge = $data['surcharge'];
    $other = $data['other'];
    $shippingSubTotal = $data['shippingSubTotal'];


    $rfpID = (int)trim($rfpID);


    try {
        if (empty($poID)) {
            //Create a new document
            $res                = $db->query(
                "INSERT INTO `consultation_purchase_orders` (`rfpId`, `supplierId`, `paymentTermId`, `currencyId`, `poDateCreated`, `salesPersonId`, `rfpReason`, `saleOrderId`, `vatAmount`, `shippingMethod`, `shippingCost`, `taxes`, `customDuties`, `foreignTransactionFee`, `insurance`, `surcharge`, `other`, `subTotal`, `etaDate`, `supplierQuotationAttachment`, `supplierQuotationNumber`, `trackingNumber`, `paymentRefNo`, `paymentDate`)
            VALUES (?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s)",
                $rfpID, $supplierID, $paymentTerms, $supplierCurrency, $poDate, getUserIDOfCurrentUser(), $rfpReason, $refDocID, $vatAmount, $carrier, $shippingCost, $taxes, $customduties, $foreigntransactionfees, $insurance, $surcharge, $other, $shippingSubTotal, $etaDate, $supplierQuotationAttachmentFilename, $supplierQuotationNo, $tracking, $paymentRefNo, $paymentDate);

                $modifiedDocumentId = $db->insertId();
                $res = $db->query("UPDATE consultation_rfp_documents SET rfpStatus = ?s WHERE rfpId = ?s", RFP_STATUS_PO_CREATED, $rfpID);

            recordConsultationRFPHistory($rfpID, OT_PO_CREATED, logActivity(OT_PO_CREATED));
            recordConsultationPOHistory($modifiedDocumentId, OT_NEW_PO, logActivity(OT_NEW_PO), "New PO Created from " . getRFPNumberFromConsultationDocumentID($rfpID));

        } else {

            // Fetch existing PO data to compare for attachments
            $existingPO = "";
            $res = $db->query("SELECT supplierQuotationAttachment, paymentRefAttachment, supplierInvoiceAttachment FROM consultation_purchase_orders WHERE poId = ?s", $poID);
            while($row = mysqli_fetch_assoc($res)) {
                $existingPO = $row;
            }

            $res = $db->query(
                "UPDATE `consultation_purchase_orders` 
        SET 
            `rfpId` = ?s,
            `poDateCreated` = ?s,
            `supplierId` = ?s,
            `paymentTermId` = ?s,
            `currencyId` = ?s,
            `salesPersonId` = ?s,
            `rfpReason` = ?s,
            `saleOrderId` = ?s,
            `vatAmount` = ?s,
            `shippingMethod` = ?s,
            `shippingCost` = ?s,
            `taxes` = ?s,
            `customDuties` = ?s,
            `foreignTransactionFee` = ?s,
            `insurance` = ?s,
            `surcharge` = ?s,
            `other` = ?s,
            `subTotal` = ?s,
            `etaDate` = ?s,
            `supplierQuotationAttachment` = ?s,
            `supplierQuotationNumber` = ?s,
            `trackingNumber` = ?s,
            `paymentRefNo` = ?s,
            `paymentRefAttachment` = ?s,
            `paymentDate` = ?s,
            `supplierInvoiceNo` = ?s,
            `supplierInvoiceAttachment` = ?s,
            `supplierInvoiceDate` = ?s
        WHERE `poId` = ?s",
                $rfpID,
                $poDate,
                $supplierID,
                $paymentTerms,
                $supplierCurrency,
                getUserIDOfCurrentUser(),
                $rfpReason,
                $refDocID,
                $vatAmount,
                $carrier,
                $shippingCost,
                $taxes,
                $customduties,
                $foreigntransactionfees,
                $insurance,
                $surcharge,
                $other,
                $shippingSubTotal,
                $etaDate,
                $supplierQuotationAttachmentFilename,
                $supplierQuotationNo,
                $tracking,
                $paymentRefNo,
                $paymentRefAttachmentFileName,
                $paymentDate,
                $supplierInvoiceNo,
                $supplierInvoiceAttachmentFileName,
                $supplierInvoiceDate,
                $poID
            );
            $modifiedDocumentId = $poID;

            // Check if any attachment fields have changed
            if (!empty($paymentRefAttachmentFileName) && $paymentRefAttachmentFileName !== $existingPO['paymentRefAttachment']) {
                // Payment reference attachment was added/changed
                recordConsultationPOHistory(
                    $modifiedDocumentId, 
                    OT_EDIT_PO_WITH_PAYMENT_ATTACHMENT, 
                    logActivity(OT_EDIT_PO_WITH_PAYMENT_ATTACHMENT), 
                    $paymentRefAttachmentFileName
                );
            } elseif (!empty($supplierInvoiceAttachmentFileName) && $supplierInvoiceAttachmentFileName !== $existingPO['supplierInvoiceAttachment']) {
                // Supplier invoice attachment was added/changed
                recordConsultationPOHistory(
                    $modifiedDocumentId, 
                    OT_EDIT_PO_INVOICE_ATTACHMENT, 
                    logActivity(OT_EDIT_PO_INVOICE_ATTACHMENT), 
                    $supplierInvoiceAttachmentFileName
                );
            } else {
                // No relevant attachments changed - record standard edit
                recordConsultationPOHistory($modifiedDocumentId, OT_EDIT_PO, logActivity(OT_EDIT_PO));
            }
        }

    } catch (Exception $e) {
        error_log("Error inserting document info: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => "Inserting Document Info: " . $e->getMessage()]);
        exit();
    }

    // Process the lineitem data
    //Delete all the lineItems before inserting.
    $res = $db->query("DELETE FROM consultation_po_line_items WHERE poId = ?s", $modifiedDocumentId);
    foreach ($lineItems as $key => $values) {

        $serviceItemID = $values['serviceItemID'];
        $qty = $values['qty'];
        $uom = $values['uomId'];
        $cost = isset($values['cost']) && $values['cost'] !== '' ? floatval($values['cost']) : null;
        $subTotal = isset($values['subTotal']) && $values['subTotal'] !== '' ? floatval($values['subTotal']) : null;

        if (!empty($serviceItemID)) {
            try {
                $res = $db->query("INSERT INTO `consultation_po_line_items` 
                                                    (`poId`, `itemId`, `quantity`,`costPrice`, `UOM`, `subTotal`, `active`)
                                                    VALUES ( ?s, ?s, ?s, ?s, ?s, ?s, 1)",
                    $modifiedDocumentId,
                    $serviceItemID,
                    $qty,
                    $cost,
                    $uom,
                    $subTotal,
                );
            } catch (Exception $e) {
                error_log("Error inserting line items: " . $e->getMessage());
                echo json_encode(['status' => 'error', 'message' => "Line Items Error: " . $e->getMessage()]);
            }
        }
    }

    echo json_encode(['status' => 'success', 'message' => 'PO saved successfully.', 'documentId' => $modifiedDocumentId]);

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing data']);
}


