<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";
// Read the raw POST data
$rawData = file_get_contents("php://input");

// Decode the JSON data into a PHP array
$data = json_decode($rawData, true);



if ($data) {

    $bolID = $data['bolID'];
    $shipmentID        = $data['shipmentID'];
    $orderManagementID   = $data['orderManagementID'];
    $originWHID   = $data['originWHID'];
    $destinationWHID      = $data['destinationWHID'];
    $customerID = $data['customerID'];
    $carrier     = $data['carrier'];
    $carrierWayBill = $data['carrierWayBill'];
    $shippingDate = $data['shippingDate'];
    $packageValue = $data['packageValue'];
    $boxQty = $data['boxQty'];
    $taxes = $data['taxes'];
    $subTotal = $data['subTotal'];
    $customduties = $data['customduties'];
    $foreigntransactionfees = $data['foreigntransactionfees'];
    $insurance = $data['insurance'];
    $surcharge = $data['surcharge'];
    $other = $data['other'];
    $shippingTotal = $data['shippingTotal'];


    try {
        if (empty($bolID)) {
            //Create a new document
            $res                = $db->query("INSERT INTO `bills_of_lading_documents` (`orderManagementId`, `shipmentId`, `originWHID`, `destinationWHID`, `customerId`, `carrier`,`carrierWaybill`, `shippingDate`, `packageValue`, `boxQty`, `taxes`, `subTotal`, `customDuties`, `foreignTransactionFee`, `insurance`, `surcharge`, `other`, `shippingTotal`, `salesPersonId`) VALUES (?s, ?s, ?s, ?s, ?s, ?s, ?s,?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s)",
                $orderManagementID, $shipmentID, $originWHID,  $destinationWHID, $customerID, $carrier, $carrierWayBill, $shippingDate, $packageValue, $boxQty, $taxes, $subTotal, $customduties, $foreigntransactionfees, $insurance, $surcharge,$other, $shippingTotal, getUserIDOfCurrentUser());
            $modifiedDocumentId = $db->insertId();
//            recordKeyDocumentHistory($modifiedDocumentId, OT_NEW_QUOTATION,logActivity(OT_NEW_QUOTATION) );

        } else {

            $res = $db->query("UPDATE `bills_of_lading_documents` SET 
                        `orderManagementId` = ?s, 
                        `shipmentId` = ?s, 
                        `originWHID` = ?s, 
                        `destinationWHID` = ?s, 
                        `customerId` = ?s, 
                        `carrier` = ?s,
                        `carrierWaybill` = ?s, 
                        `shippingDate` = ?s, 
                        `packageValue` = ?s, 
                        `boxQty` = ?s, 
                        `taxes` = ?s,
                        `subTotal` = ?s,
                        `customDuties` = ?s,
                        `foreignTransactionFee` = ?s,
                        `insurance` = ?s,
                        `surcharge` = ?s,
                        `other` = ?s,
                        `shippingTotal` = ?s,
                        `salesPersonId` = ?s
                        WHERE `bolID` = ?s",
                $orderManagementID,
                $shipmentID,
                $originWHID,
                $destinationWHID,
                $customerID,
                $carrier,
                $carrierWayBill,
                $shippingDate,
                $packageValue,
                $boxQty,
                $taxes,
                $subTotal,
                $customduties,
                $foreigntransactionfees,
                $insurance,
                $surcharge,
                $other,
                $shippingTotal,
                getUserIDOfCurrentUser(),
                $bolID
            );
//            recordKeyDocumentHistory($documentId, OT_SAVE_QUOTATION_WITH_ATTACHMENT, logActivity(OT_SAVE_QUOTATION_WITH_ATTACHMENT), $poAttachment);
            $modifiedDocumentId = $bolID;
        }

    } catch (Exception $e) {
        error_log("Error inserting document info: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => "Error Saving Document: " . $e->getMessage()]);
        exit();
    }


    echo json_encode(['status' => 'success', 'message' => 'Bill of Lading  drafted successfully.', 'documentId' => $modifiedDocumentId]);

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing data']);
}

