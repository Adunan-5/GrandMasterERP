<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";
// Read the raw POST data
$rawData = file_get_contents("php://input");

// Decode the JSON data into a PHP array
$data = json_decode($rawData, true);



if ($data) {

    $lineItems = $data['lineItems']['line-item'];

    $gdnID = $data['gdnID'];
    $orderManagementID   = $data['orderManagementID'];
    $originWHID   = $data['originWHID'];
    $destinationWHID = !empty($data['destinationWHID']) ? $data['destinationWHID'] : null;
    $customerID = $data['customerID'];
    $transactionType = $data['orderType'];
    $documentID = $data['documentID'];
    $carrier     = $data['carrier'];
    $carrierWayBill = $data['carrierWayBill'];
    $shippingDate = $data['shippingDate'];
    $packageValue = $data['packageValue'];
    $boxQty = $data['boxQty'];
    $taxes = $data['taxes'];
    $shippingSubTotal = $data['shippingSubTotal'];
    $customduties = $data['customduties'];
    $foreigntransactionfees = $data['foreigntransactionfees'];
    $insurance = $data['insurance'];
    $surcharge = $data['surcharge'];
    $other = $data['other'];
    $shippingCost = $data['shippingCost'];


    try {
        if (empty($gdnID)) {
            //Create a new document
            $res                = $db->query("INSERT INTO `pick_and_pack_documents` (`orderManagementId`, `originWHID`, `destinationWHID`, `customerId`, `transactionType`, `documentId`, `carrier`, `carrierWaybill`, `shippingDate`, `packageValue`, `boxQty`, `taxes`, `shippingCost`, `customDuties`, `foreignTransactionFee`, `insurance`, `surcharge`, `other`, `shippingSubTotal`, `salesPersonId`) 
                                                VALUES (?s, ?s, ?s, ?s, ?s, ?s, ?s,?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s)",
                                                $orderManagementID, $originWHID,  $destinationWHID, $customerID, $transactionType, $documentID, $carrier, $carrierWayBill, $shippingDate, $packageValue, $boxQty, $taxes, $shippingCost, $customduties, $foreigntransactionfees, $insurance, $surcharge, $other, $shippingSubTotal, getUserIDOfCurrentUser());
            $modifiedDocumentId = $db->insertId();

            foreach ($lineItems as $key => $values) {

                $sparepartID = $values['sparepartID'];
                $qty = $values['qty'];
                $lot = $values['lot'] ?? null;
                $shipmentNumber = $values['shipment#'];
                $originWHID = $values['wh'];
                $previousWHStatus = $values['status'];
                $orderWHStatus = ORDER_WH_STATUS_READY;

                if (!empty($sparepartID)) {
                    try {
                        $res = $db->query("UPDATE `pick_and_pack_line_items`
                                                SET `gdnId` = ?s, `orderWHStatus` = ?s WHERE `orderManagementId` = ?s AND `itemId` = ?s",
                                            $modifiedDocumentId, $orderWHStatus, $orderManagementID, $sparepartID);

                        $line = $db->getRow("SELECT quantity, releasedQty FROM order_management_line_items WHERE orderManagementId = ?s AND itemId = ?s", $orderManagementID, $sparepartID);

                        if ($line) {
                            $orderedQty = (int)$line['quantity'];
                            $pickedQty = (int)$qty; // qty from Pick and Pack

                            // Determine new status
                            $newStatus = ($pickedQty < $orderedQty) ? ORDER_WH_STATUS_PARTIALLY_READY : ORDER_WH_STATUS_READY;

                            // Update order_management_line_items
                            $db->query("UPDATE `order_management_line_items`
                                            SET `orderWHStatus` = ?s 
                                            WHERE `orderManagementId` = ?s AND `itemId` = ?s",
                                $newStatus, $orderManagementID, $sparepartID);
                        }


//                        if($previousWHStatus == ORDER_WH_STATUS_RESERVED) {
//                            $res = $db->query("UPDATE `order_management_line_items`
//                           SET `orderWHStatus` = ?s
//                           WHERE `orderManagementId` = ?s
//                            AND `itemId` = ?s
//                            AND `orderWHStatus` = 'RESERVED'",
//                    $orderWHStatus,
//                    $orderManagementID,
//                    $sparepartID);
//                        }

//                        $res = $db->query("UPDATE `pick_and_pack_items`
//                           SET `orderWHStatus` = ?s
//                           WHERE `orderManagementId` = ?s
//                            AND `itemId` = ?s
//                            AND `orderWHStatus` = 'RESERVED'",
//                            $orderWHStatus,
//                            $orderManagementID,
//                            $sparepartID);

                    } catch (Exception $e) {
                        error_log("Error inserting line items: " . $e->getMessage());
                        echo json_encode(['status' => 'error', 'message' => "Line Items Error: " . $e->getMessage()]);
                    }
                }
            }
//            recordKeyDocumentHistory($modifiedDocumentId, OT_NEW_QUOTATION,logActivity(OT_NEW_QUOTATION) );

        } else {

            $res = $db->query("UPDATE `pick_and_pack_documents` SET 
                        `orderManagementId` = ?s, 
                        `originWHID` = ?s, 
                        `destinationWHID` = ?s, 
                        `customerId` = ?s, 
                        `carrier` = ?s,
                        `carrierWaybill` = ?s, 
                        `shippingDate` = ?s, 
                        `packageValue` = ?s, 
                        `boxQty` = ?s, 
                        `taxes` = ?s,
                        `shippingCost` = ?s,
                        `customDuties` = ?s,
                        `foreignTransactionFee` = ?s,
                        `insurance` = ?s,
                        `surcharge` = ?s,
                        `other` = ?s,
                        `shippingSubTotal` = ?s,
                        `salesPersonId` = ?s
                        WHERE `gdnId` = ?s",
                $orderManagementID,
                $originWHID,
                $destinationWHID,
                $customerID,
                $carrier,
                $carrierWayBill,
                $shippingDate,
                $packageValue,
                $boxQty,
                $taxes,
                $shippingCost,
                $customduties,
                $foreigntransactionfees,
                $insurance,
                $surcharge,
                $other,
                $shippingSubTotal,
                getUserIDOfCurrentUser(),
                $gdnID
            );
//            recordKeyDocumentHistory($documentId, OT_SAVE_QUOTATION_WITH_ATTACHMENT, logActivity(OT_SAVE_QUOTATION_WITH_ATTACHMENT), $poAttachment);
            $modifiedDocumentId = $gdnID;
        }

        $statuses = $db->getCol("SELECT orderWHStatus FROM order_management_line_items WHERE orderManagementId = ?s", $orderManagementID);

        if (count(array_unique($statuses)) === 1 && $statuses[0] === ORDER_WH_STATUS_READY) {
            $documentStatus = ORDER_WH_STATUS_READY;
        } elseif (in_array(ORDER_WH_STATUS_PARTIALLY_READY, $statuses)) {
            $documentStatus = ORDER_WH_STATUS_PARTIALLY_READY;
        } else {
            $documentStatus = 'RESERVED'; // default fallback
        }

        // Update order_management_documents
        $db->query("UPDATE `order_management_documents` 
            SET `orderWHStatus` = ?s 
            WHERE `orderManagementId` = ?s",
            $documentStatus, $orderManagementID);

        echo json_encode(['status' => 'success', 'message' => 'Pick and Pack drafted successfully.', 'documentId' => $modifiedDocumentId]);


    } catch (Exception $e) {
        error_log("Error inserting document info: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => "Error Saving Document: " . $e->getMessage()]);
        exit();
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing data']);
}

