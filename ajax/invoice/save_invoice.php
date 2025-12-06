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

    $customerID          = $data['customerID'];
    $invoiceDate       = $data['invoiceDate'];
    $paymentTerms        = $data['paymentTerms'];
    $invoiceId          = $data['invoiceId'];
    $invoiceNumber     = $data['invoiceNumber'];
    $poNumber            = $data['poNumber'] ?? "";
    $poAttachment        = $data['poAttachment'] ?? "";
    $refDocID = !empty($data['refDocID']) ? $data['refDocID'] : null;
    $gdnID = !empty($data['gdnID']) ? $data['gdnID'] : null;

    $modifiedDocumentId = 0;
    $allItemsFullyInvoiced = true; // Initialize as true, will be set to false if any item isn't fully invoiced

    try {
        if (empty($invoiceId)) {
            // Create a new document
            $res = $db->query("INSERT INTO `invoice_documents` (`customerId`, `invoiceDateIssued`, `salesPersonId`, `paymentTermId`, `saleOrderId`, `PONumber`, `POAttachment`, `gdnId`) VALUES (?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s)", $customerID, $invoiceDate, getUserIDOfCurrentUser(), $paymentTerms, $refDocID, $poNumber, $poAttachment, $gdnID);
            $modifiedDocumentId = $db->insertId();
        } else {
            // Update existing document
            $res = $db->query("UPDATE `invoice_documents` SET 
                `customerId` = ?s, 
                `invoiceDateIssued` = ?s, 
                `salesPersonId` = ?s, 
                `paymentTermId` = ?s, 
                `saleOrderId` = ?s, 
                `PONumber` = ?s, 
                `POAttachment` = ?s
                WHERE `invoiceId` = ?s",
                $customerID,
                $invoiceDate,
                getUserIDOfCurrentUser(),
                $paymentTerms,
                $refDocID,
                $poNumber,
                $poAttachment,
                $invoiceId);
            $modifiedDocumentId = $invoiceId;
        }

    } catch (Exception $e) {
        error_log("Error inserting document info: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => "Inserting Document Info: " . $e->getMessage()]);
        exit();
    }

    // Process the lineitem data
    // Delete all the lineItems before inserting.
    $res = $db->query("DELETE FROM invoice_line_items WHERE invoiceId = ?s", $modifiedDocumentId);

    foreach ($lineItems as $key => $values) {
        $sparepartdescription = $values['sparepartdescription'];
        $qty = isset($values['qty']) && $values['qty'] !== '' ? intval($values['qty']) : 1;
        $invoicedQty = isset($values['invoiceQty']) && $values['invoiceQty'] !== '' ? intval($values['invoiceQty']) : 1;
        $unitPrice = $values['unitPrice'];
        $vatPercentage = $values['vatPercentage'];
        $vatAmount = $values['vatAmount'];
        $subTotal = $values['subTotal'];
        $hsCode = $values['hsCode'];
        $hsPercentage = isset($values['hsPercentage']) && $values['hsPercentage'] !== '' ? floatval(($values['hsPercentage'])) : 0.00;
        $discountPercentage = isset($values['discountPercentage']) && $values['discountPercentage'] !== '' ? floatval($values['discountPercentage']) : 0.00;
        $discountAmount = $values['discountAmount'];
        $sparepartitem = $values['sparepartitem'];
        $sparepartitemName = "";
        $uom = $values['uom'];

        // Check if this item is fully invoiced
        $isItemFullyInvoiced = ($qty == $invoicedQty) ? 1 : 0;

        // If any item is not fully invoiced, the whole document isn't fully invoiced
        if (!$isItemFullyInvoiced) {
            $allItemsFullyInvoiced = false;
        }

        if (!empty($sparepartitem)) {
            try {
                $res = $db->query("INSERT INTO `invoice_line_items` 
                    (`invoiceId`, `itemId`, `itemName`, `itemType`, `quantity`, `invoicedQuantity`, 
                    `unitPrice`, `UOM`, `discountPercentage`, `discountAmount`, `hsCode`, `hsPercentage`, 
                    `vatPercentage`, `vatAmount`, `subTotal`, `isDeleted`, `active`, `isFullyInvoiced`) 
                    VALUES ( ?s, ?s, ?s, 'SPAREPART', ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, 0, 1, ?s)",
                    $modifiedDocumentId,
                    $sparepartitem,
                    $sparepartitemName,
                    $qty,
                    $invoicedQty,
                    $unitPrice,
                    $uom,
                    $discountPercentage,
                    $discountAmount,
                    $hsCode,
                    $hsPercentage,
                    $vatPercentage,
                    $vatAmount,
                    $subTotal,
                    $isItemFullyInvoiced);

            } catch (Exception $e) {
                error_log("Error inserting line items: " . $e->getMessage());
                echo json_encode(['status' => 'error', 'message' => "Line Items Error: " . $e->getMessage()]);
            }
        }
    }

    // Update the invoice document's isFullyInvoiced status
    // try {
    //     $db->query("UPDATE `invoice_documents` SET `isFullyInvoiced` = ?s WHERE `invoiceId` = ?s",
    //         $allItemsFullyInvoiced ? 1 : 0,
    //         $modifiedDocumentId
    //     );
    // } catch (Exception $e) {
    //     error_log("Error updating invoice document status: " . $e->getMessage());
    //     // Don't fail the whole operation for this
    // }

    echo json_encode(['status' => 'success', 'message' => 'Invoice saved successfully.', 'documentId' => $modifiedDocumentId]);

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing data']);
}