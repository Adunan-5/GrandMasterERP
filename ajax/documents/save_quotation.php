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
    $quotationDate       = $data['quotationDate'];
    $quotationExpiryDate = $data['quotationExpiryDate'];
    $paymentTerms        = $data['paymentTerms'];
    $documentId          = $data['documentId'];
    $quotationNumber     = $data['quotationNumber'];
    $poNumber            = $data['poNumber'] ?? "";
    $poAttachment        = $data['poAttachment'] ?? "";

    //check if the quotation is new or an update
    //INSERT INTO `grandmastererpdb`.`key_documents` (`customerId`, `quotationDateIssued`, `quotationDateExpiry`, `salesPersonId`, `paymentTermId`) VALUES (1, '2024-11-30 03:22:10', '2024-11-30 03:22:22', 1, 1);
    //UPDATE `grandmastererpdb`.`key_documents` SET `quotationDateIssued`='2024-12-01 03:22:10', `quotationDateExpiry`='2024-12-01 03:22:22', `paymentTermId`=2 WHERE  `documentId`=2;

    $modifiedDocumentId = 0;

    try {
        if (empty($documentId)) {
            //Create a new document
            $res                = $db->query("INSERT INTO `key_documents` (`customerId`, `quotationDateIssued`, `quotationDateExpiry`, `salesPersonId`, `paymentTermId`) VALUES (?s, ?s, ?s, ?s, ?s)", $customerID, $quotationDate, $quotationExpiryDate, getUserIDOfCurrentUser(), $paymentTerms);
            $modifiedDocumentId = $db->insertId();
            recordKeyDocumentHistory($modifiedDocumentId, OT_NEW_QUOTATION,logActivity(OT_NEW_QUOTATION) );

        } else {

            if (empty($poAttachment)) {
                $res = $db->query("UPDATE `key_documents` SET `quotationDateIssued`= ?s, `quotationDateExpiry`=?s, `paymentTermId`=?s, `PONumber`=?s WHERE `documentId`=?s", $quotationDate, $quotationExpiryDate, $paymentTerms, $poNumber, $documentId);
                recordKeyDocumentHistory($documentId, OT_SAVE_QUOTATION,logActivity(OT_SAVE_QUOTATION) );
            } else {
                $res = $db->query("UPDATE `key_documents` SET `quotationDateIssued`= ?s, `quotationDateExpiry`=?s, `paymentTermId`=?s, `PONumber`=?s, `POAttachment`=?s WHERE `documentId`=?s", $quotationDate, $quotationExpiryDate, $paymentTerms, $poNumber, $poAttachment, $documentId);
                recordKeyDocumentHistory($documentId, OT_SAVE_QUOTATION_WITH_ATTACHMENT,logActivity(OT_SAVE_QUOTATION_WITH_ATTACHMENT), $poAttachment );
            }
            $modifiedDocumentId = $documentId;
        }

    } catch (Exception $e) {
        error_log("Error inserting document info: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => "Inserting Document Info: " . $e->getMessage()]);
        exit();
    }

    // Process the lineitem data
    //Delete all the lineItems before inserting.
    $res = $db->query("DELETE FROM line_items WHERE documentId = ?s", $modifiedDocumentId);
    foreach ($lineItems as $key => $values) {
        // Add your processing logic for each row here
        $sparepartdescription = $values['sparepartdescription'];
        $qty                  = isset($values['qty']) && $values['qty'] !== '' ? intval($values['qty']) : 1;
        $unitPrice            = $values['unitPrice'];
        $vatPercentage        = $values['vatPercentage'];
        $vatAmount            = $values['vatAmount'];
        $subTotal             = $values['subTotal'];
        $hsCode               = $values['hsCode'];
        $hsPercentage         = isset($values['hsPercentage']) && $values['hsPercentage'] !== '' ? floatval(($values['hsPercentage'])) : 0.00;
        $discountPercentage = isset($values['discountPercentage']) && $values['discountPercentage'] !== '' ? floatval($values['discountPercentage']) : 0.00;
        $discountAmount       = $values['discountAmount'];
        $sparepartitem        = $values['sparepartitem'];
        $sparepartitemName    = "";
        $uom                  = $values['uom'];
        $eta                  = empty($values['eta']) ? null : $values['eta'];


        if (!empty($sparepartitem)) {
            try {
                $res = $db->query("INSERT INTO `line_items` (`documentId`, `itemId`, `itemName`, `itemType`, `quantity`, `unitPrice`, `UOM`, `discountPercentage`, `discountAmount`, `hsCode`, `hsPercentage`, `vatPercentage`, `vatAmount`, `subTotal`, `isDeleted`, `active`, `eta`) 
                            VALUES ( ?s, ?s, ?s, 'SPAREPART', ?s, ?s, ?s,?s,?s,?s,?s,?s,?s,?s, 0, 1,?s)",
                    $modifiedDocumentId,
                    $sparepartitem,
                    $sparepartitemName,
                    $qty,
                    $unitPrice,
                    $uom,
                    $discountPercentage,
                    $discountAmount,
                    $hsCode,
                    $hsPercentage,
                    $vatPercentage,
                    $vatAmount,
                    $subTotal, $eta);

                //INSERT INTO `grandmastererpdb`.`line_items` (`quotationId`, `itemId`, `itemName`, `itemType`, `quantity`, `unitPrice`, `UOM`, `discountPercentage`, `discountAmount`, `hsCode`, `hsPercentage`, `vatPercentage`, `vatAmount`, `subTotal`, `createdAt`, `updatedAt`, `isDeleted`, `active`) VALUES (123, 12, 12, 'SPAREPART', 1, 23, 1, 12, 234, '23', 12, 23, 234, 24234, '2024-12-01 07:56:49', '2024-12-01 07:56:56', 0, 1);
            } catch (Exception $e) {
                error_log("Error inserting line items: " . $e->getMessage());
                echo json_encode(['status' => 'error', 'message' => "Line Items Error: " . $e->getMessage()]);
            }
        }
    }

    echo json_encode(['status' => 'success', 'message' => 'Quotation saved successfully.', 'documentId' => $modifiedDocumentId]);

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing data']);
}


/*
Sample Data received from client

{
    "customerID": "1",
    "quotationDate": "2024-12-01",
    "quotationExpiryDate": "2024-12-31",
    "paymentTerms": "3",
    "lineItems":
    {
        "line-item":
        [
            {
                "sparepartdescription": "CONTROL BOARD",
                "qty": "1",
                "unitPrice": "25.00",
                "vatPercentage": "15",
                "vatAmount": "3.75",
                "subTotal": "25.00",
                "hsCode": "0",
                "hsPercentage": "0.00",
                "discountPercentage": "0",
                "discountAmount": "0.00",
                "sparepartitem": "1",
                "uom": "1",
                "tax-1-input": "0%",
                "tax-2-input": "0%",
                "eta": "2024-12-10"
            },
            {
                "sparepartdescription": "Replacement Parts Kit, Includes: (1) G030 Gear, S11/U-12 Stainless Steel, (1) K032, - Knife S11/I-12 Heat Treat, (1) R085 - Ring, S11 O, Black 70 Duro #Or18700 And (1) S276 - Screw, S11 Knurl (6 Kits Per Case)",
                "qty": "1",
                "unitPrice": "250.00",
                "vatPercentage": "15",
                "vatAmount": "37.50",
                "subTotal": "250.00",
                "hsCode": "0",
                "hsPercentage": "0.00",
                "discountPercentage": "0",
                "discountAmount": "0.00",
                "sparepartitem": "4",
                "uom": "1",
                "tax-1-input": null,
                "tax-2-input": null,
                "eta": "2024-12-10"
            }
        ]
    }
}
 */