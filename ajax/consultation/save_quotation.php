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
    $timelineData = isset($data['timeline']) ? $data['timeline'] : [];

    $customerID          = $data['customerID'];
    $quotationDate       = $data['quotationDate'];
    $quotationExpiryDate = $data['quotationExpiryDate'];
    $paymentTerms        = $data['paymentTerms'];
    $documentId          = $data['documentId'];
    $quotationNumber     = $data['quotationNumber'];
    $poNumber            = $data['poNumber'] ?? "";
    $poAttachment        = $data['poAttachment'] ?? "";
    $proposalID          = isset($data['proposalID']) ? $data['proposalID'] : null;
    $contentScopeOfWork        = $data['contentScopeOfWork'] ?? "";
    $quotationTitle        = $data['quotationTitle'] ?? "";

    $modifiedDocumentId = 0;

    try {
        if (empty($documentId)) {
            //Create a new document
            $res                = $db->query("INSERT INTO `consultation_key_documents` (`customerId`, `quotationDateIssued`, `quotationDateExpiry`, `salesPersonId`, `paymentTermId`, `associatedProposalID`, `quotationTitle`, `contentScopeOfWork`) VALUES (?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s)", $customerID, $quotationDate, $quotationExpiryDate, getUserIDOfCurrentUser(), $paymentTerms, $proposalID, $quotationTitle, $contentScopeOfWork);
            $modifiedDocumentId = $db->insertId();
            recordConsultationKeyDocumentHistory($modifiedDocumentId, OT_NEW_QUOTATION,logActivity(OT_NEW_QUOTATION) );

        } else {

            if (empty($poAttachment)) {
                $res = $db->query("UPDATE `consultation_key_documents` SET `quotationDateIssued`= ?s, `quotationDateExpiry`=?s, `paymentTermId`=?s, `PONumber`=?s, `quotationTitle`=?s, `contentScopeOfWork`=?s WHERE `documentId`=?s", $quotationDate, $quotationExpiryDate, $paymentTerms, $poNumber, $quotationTitle, $contentScopeOfWork, $documentId);
                recordConsultationKeyDocumentHistory($documentId, OT_SAVE_QUOTATION,logActivity(OT_SAVE_QUOTATION) );
            } else {
                $res = $db->query("UPDATE `consultation_key_documents` SET `quotationDateIssued`= ?s, `quotationDateExpiry`=?s, `paymentTermId`=?s, `PONumber`=?s, `POAttachment`=?s, `quotationTitle`=?s, `contentScopeOfWork`=?s WHERE `documentId`=?s", $quotationDate, $quotationExpiryDate, $paymentTerms, $poNumber, $poAttachment, $quotationTitle, $contentScopeOfWork, $documentId);
                recordConsultationKeyDocumentHistory($documentId, OT_SAVE_QUOTATION_WITH_ATTACHMENT,logActivity(OT_SAVE_QUOTATION_WITH_ATTACHMENT), $poAttachment );
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
    $res = $db->query("DELETE FROM consultation_line_items WHERE documentId = ?s", $modifiedDocumentId);
    foreach ($lineItems as $key => $values) {
        // Add your processing logic for each row here
        $sparepartdescription = $values['serviceitemdescription'];
        $qty                  = $values['qty'];
        $unitPrice            = $values['price'];
        $vatPercentage        = $values['vatPercentage'];
        $vatAmount            = $values['vatAmount'];
        $subTotal             = $values['subTotal'];
        $discountPercentage   = $values['discountPercentage'];
        $discountAmount       = $values['discountAmount'];
        $sparepartitem        = isset($values['serviceitem']) ? $values['serviceitem'] : $values['itemID'];
        $sparepartitemName    = "";
        $uom                  = $values['uom'];
        $eta                  = empty($values['eta']) ? null : $values['eta'];


        if (!empty($sparepartitem)) {
            try {
                $res = $db->query("INSERT INTO `consultation_line_items` (
                          `documentId`, 
                          `itemId`,
                          `itemName`,
                          `itemType`,
                          `quantity`,
                          `unitPrice`,
                          `UOM`,
                          `discountPercentage`,
                          `discountAmount`,
                          `vatPercentage`,
                          `vatAmount`,
                          `subTotal`,
                          `isDeleted`,
                          `active`) 
                            VALUES ( ?s, ?s, ?s, 'SERVICE', ?s, ?s, ?s,?s,?s,?s,?s,?s, 0, 1)",
                    $modifiedDocumentId,
                    $sparepartitem,
                    $sparepartitemName,
                    $qty,
                    $unitPrice,
                    $uom,
                    $discountPercentage,
                    $discountAmount,
                    $vatPercentage,
                    $vatAmount,
                    $subTotal);
            } catch (Exception $e) {
                error_log("Error inserting line items: " . $e->getMessage());
                echo json_encode(['status' => 'error', 'message' => "Line Items Error: " . $e->getMessage()]);
            }
        }
    }

    // Process the timeline data
    try {
        // First, delete existing timeline entries for this document
        $res = $db->query("DELETE FROM consultation_project_timeline WHERE documentId = ?s", $modifiedDocumentId);
        
        // Then insert new timeline entries
        foreach ($timelineData as $timelineItem) {
            if (!empty($timelineItem['phase']) && !empty($timelineItem['durationDays'])) {
                $phase = $timelineItem['phase'];
                $durationDays = intval($timelineItem['durationDays']);
                
                $res = $db->query("INSERT INTO `consultation_project_timeline` (`documentId`, `phase`, `durationDays`) VALUES (?s, ?s, ?s)", 
                    $modifiedDocumentId, $phase, $durationDays);
            }
        }
        
    } catch (Exception $e) {
        error_log("Error processing timeline data: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => "Timeline Error: " . $e->getMessage()]);
        exit();
    }

    echo json_encode(['status' => 'success', 'message' => 'Quotation saved successfully.', 'documentId' => $modifiedDocumentId]);

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing data']);
    exit();
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