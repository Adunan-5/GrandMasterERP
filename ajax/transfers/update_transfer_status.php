<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";

$documentId   = filter_var($_POST['documentID'], FILTER_SANITIZE_NUMBER_INT);
$status       = filter_var($_POST['status'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$rejectReason = filter_var($_POST['rejectReason'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

//$lineItems = $transferData['lineItems']['line-item'];

try {
    if (!empty($documentId)) {



        if ($status == TRANSFER_STATUS_PROCUREMENT_MANAGER_APPROVAL) {
            $res = $db->query("UPDATE `transfer_documents` SET `transferStatus`= ?s WHERE `transferId`=?s", $status, $documentId);
//            recordKeyDocumentHistory($documentId, $status,logActivity($status) );
            echo "SUCCESS|Sent for Approval";
            exit();
        }

        if ($status == TRANSFER_STATUS_PROCUREMENT_MANAGER_APPROVED) {

            $transferNumberPrefix = PREFIX_TRANSFER;
            $transferDATA = "";
            $res     = $db->query("SELECT * FROM transfer_documents WHERE active = 1 and transferId = ?s", $documentId);
            while ($row = mysqli_fetch_assoc($res)) {
                $transferDATA = $row;
            }
            if(empty($transferDATA['transferNumber'])){
                $transferNumber = getNextNewTransferNumber();
            } else {
                $transferNumber = $transferDATA['transferNumber'];
            }
            $res = $db->query("UPDATE `transfer_documents` SET `transferStatus`= ?s, `transferNumber`=?s, `transferNumberPrefix`=?s, `procurementManagerTransferApproved` = 'APPROVED' WHERE `transferId`=?s", $status, $transferNumber, $transferNumberPrefix, $documentId);
//            recordKeyDocumentHistory($documentId, $status,logActivity($status) );
            echo "SUCCESS|Transfer Approved Successfully";
            exit();
        }

        if ($status == TRANSFER_STATUS_PROCUREMENT_MANAGER_REJECTED) {
            $res = $db->query("UPDATE `transfer_documents` SET `transferStatus`= ?s, `procurementManagerTransferApproved` = 'REJECTED' WHERE `transferId`=?s", $status, $documentId);
            $res = $db->query("INSERT INTO `transfer_reject_reason_procurement_manager` (`transferId`, `rejectReason`, `rejectedBy`) VALUES (?s, ?s, ?s)", $documentId, $rejectReason, getAuthenticatedUser()->userID);
//            recordKeyDocumentHistory($documentId, $status,logActivity($status) );
            echo "SUCCESS|Transfer has been Rejected";
            exit();
        }

        if($status == TRANSFER_STATUS_SENT_TO_PICK_AND_PACK) {

            $orderStatus = ORDER_WH_STATUS_RESERVED;

            $res = $db->query("SELECT * FROM transfer_documents WHERE transferId = ?s", $documentId);
            $row = mysqli_fetch_assoc($res);
            $transferId = $row['transferId'];
            $originWHId = $row['originWHId'];

            if($row['transferStatus'] == TRANSFER_STATUS_PROCUREMENT_MANAGER_APPROVED){
                $res = $db->query("UPDATE `transfer_documents` SET `transferStatus`= ?s, `orderWHStatus` = ?s WHERE `transferId`=?s", $status, $orderStatus, $documentId);
                $res = $db->query("UPDATE `transfer_line_items` SET `orderWHStatus` = ?s WHERE `transferId`=?s", $orderStatus, $transferId);

//                $shipmentNumberPrefix = PREFIX_SHIPMENT;
//                $shipmentNumber = getNextNewShipmentNumber();
                $orderNumber = getTransferNumberFromDocumentID($transferId);

//                $res = $db->query("INSERT INTO `order_management_documents` (`orderNumber`, `orderWHStatus`, `originWHId`, `destinationWHId`, `customerId`, `transactionType`, `shipmentNumberPrefix`, `shipmentNumber`, `salesPersonId`, `documentId`)
//                                   VALUES (?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s)",
//                    $orderNumber, $orderStatus, $row['originWHId'], $row['destinationWHId'], null, 'TRANSFER', $shipmentNumberPrefix, $shipmentNumber, $row['salesPersonId'], $documentId);
//                $orderManagementId = $db->insertId();
//
//                $res = $db->query("INSERT INTO `shipments` (`shipmentNumberPrefix`, `shipmentNumber`, `documentType`, `documentId`) VALUES (?s, ?s, ?s, ?s)",
//                    $shipmentNumberPrefix, $shipmentNumber, 'TRANSFER', $orderManagementId);

                $res = $db->query("INSERT INTO `order_management_documents` (`orderNumber`, `orderWHStatus`, `originWHId`, `destinationWHId`, `customerId`, `transactionType`, `salesPersonId`, `documentId`) 
                                   VALUES (?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s)",
                    $orderNumber, $orderStatus, $row['originWHId'], $row['destinationWHId'], null, 'TRANSFER', $row['salesPersonId'], $documentId);
                $orderManagementId = $db->insertId();

                $lineItemsRes = $db->query("SELECT * FROM transfer_line_items WHERE transferId = ?s AND active = 1", $documentId);
                while ($lineItem = mysqli_fetch_assoc($lineItemsRes)) {
                    $shipmentNumberPrefix = PREFIX_SHIPMENT;
                    $shipmentNumber = getNextNewShipmentNumber();

                    $db->query("INSERT INTO `order_management_line_items` (`orderManagementId`, `itemId`, `quantity`, `eta`, `aisle`, `bin`, `lotSerial`, `orderWHStatus`) 
                                VALUES (?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s)",
                        $orderManagementId, $lineItem['itemId'], $lineItem['quantity'], $lineItem['eta'], $lineItem['aisle'], $lineItem['bin'], $lineItem['lotSerial'], $orderStatus);
                    $db->query("INSERT INTO `pick_and_pack_line_items` (`orderManagementId`, `itemId`, `quantity`, `orderWHStatus`, `originWHId`, `shipmentNumberPrefix`, `shipmentNumber`) 
                    VALUES (?s, ?s, ?s, ?s, ?s, ?s, ?s)",
                        $orderManagementId, $lineItem['itemId'], $lineItem['quantity'], $orderStatus, $originWHId, $shipmentNumberPrefix, $shipmentNumber
                    );
                }
                echo "SUCCESS|Transfer has been sent to Pick & Pack";
            } else {
                echo "DONOTSENDSUCCESS|Transfer status has been changed";
            }
            exit();
        }
    }

} catch (Exception $e) {
    error_log("Error inserting document info: " . $e->getMessage());
    echo "ERROR|" . $e->getMessage();
    exit();
}