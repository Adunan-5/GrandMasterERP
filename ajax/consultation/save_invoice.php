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

    $companyId = intval($_SESSION['SES_SELECTED_COMPANY']);

    $modifiedDocumentId = 0;
    $allItemsFullyInvoiced = true; // Initialize as true, will be set to false if any item isn't fully invoiced

    try {
        if (empty($invoiceId)) {
            // Create a new document
            $res = $db->query(
                "INSERT INTO `invoice_documents` 
                (`customerId`, `invoiceDateIssued`, `salesPersonId`, `paymentTermId`, `saleOrderId`, `PONumber`, `POAttachment`,`companyId`) 
                VALUES (?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s)",
                $customerID, $invoiceDate, getUserIDOfCurrentUser(), $paymentTerms, $refDocID, $poNumber, $poAttachment,$companyId
            );
            $modifiedDocumentId = $db->insertId();
        } else {
            // Update existing document (added companyId)
            $res = $db->query(
                "UPDATE `invoice_documents` SET 
                    `customerId` = ?s, 
                    `invoiceDateIssued` = ?s, 
                    `salesPersonId` = ?s, 
                    `paymentTermId` = ?s, 
                    `saleOrderId` = ?s, 
                    `PONumber` = ?s, 
                    `POAttachment` = ?s,
                    `companyId` = ?s
                WHERE `invoiceId` = ?s",
                $customerID,
                $invoiceDate,
                getUserIDOfCurrentUser(),
                $paymentTerms,
                $refDocID,
                $poNumber,
                $poAttachment,
                $companyId,
                $invoiceId
            );
            $modifiedDocumentId = $invoiceId;
        }

    } catch (Exception $e) {
        error_log("Error inserting document info: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => "Inserting Document Info: " . $e->getMessage()]);
        exit();
    }

    // Process the lineitem data
    // Delete all the lineItems before inserting.
    $res = $db->query("DELETE FROM consultation_invoice_line_items WHERE invoiceId = ?s", $modifiedDocumentId);

    foreach ($lineItems as $key => $values) {
        $qty = isset($values['qty']) && $values['qty'] !== '' ? intval($values['qty']) : 1;
        $unitPrice = $values['unitPrice'];
        $vatPercentage = $values['vatPercentage'];
        $vatAmount = $values['vatAmount'];
        $subTotal = $values['subTotal'];
        $discountPercentage = isset($values['discountPercentage']) && $values['discountPercentage'] !== '' ? floatval($values['discountPercentage']) : 0.00;
        $discountAmount = $values['discountAmount'];
        $serviceitem = $values['serviceitem'];
        $serviceitemName = "";
        $uom = $values['uom'];

        // Always set isFullyInvoiced to 1 as per your instruction
        $isItemFullyInvoiced = 1;

        if (!empty($serviceitem)) {
            try {
                $res = $db->query(
                    "INSERT INTO `consultation_invoice_line_items` 
                    (`invoiceId`, `itemId`, `itemName`, `itemType`, `quantity`, 
                    `unitPrice`, `UOM`, `discountPercentage`, `discountAmount`, 
                    `vatPercentage`, `vatAmount`, `subTotal`, `isDeleted`, `active`, `isFullyInvoiced`) 
                    VALUES ( ?s, ?s, ?s, 'SERVICE', ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, 0, 1, ?s)",
                    $modifiedDocumentId,
                    $serviceitem,
                    $serviceitemName,
                    $qty,
                    $unitPrice,
                    $uom,
                    $discountPercentage,
                    $discountAmount,
                    $vatPercentage,
                    $vatAmount,
                    $subTotal,
                    $isItemFullyInvoiced
                );
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