<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";

$documentId = filter_var($_POST['documentID'], FILTER_SANITIZE_NUMBER_INT);
$status = filter_var($_POST['status'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$rejectReason = filter_var($_POST['rejectReason'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

try {
    if (!empty($documentId)) {

        if ($status == ORDER_STATUS_CUSTOMER_ACCEPTED) {

            $salesOrderNumberPrefix = PREFIX_SALESORDER;
            $keyDocument           = new KeyDocument();
            $keyDocument->loadById($documentId);
            if (empty($keyDocument->saleOrderNumber)) {
                $salesOrderNumber = getNextNewSalesOrderNumber();
            } else {
                $salesOrderNumber = $keyDocument->saleOrderNumber;
            }

            $res = $db->query("UPDATE `key_documents` SET `orderStatus`= ?s, 
                        `orderAcceptedByCustomer` = 'APPROVED', 
                        `orderAcceptedByCustomerDate` = NOW(),
                       `saleOrderStatus`= ?s, 
                       `saleOrderDateIssued` = now(), 
                       `saleOrderNumber`=?s, 
                       `saleOrderNumberPrefix`=?s 
                       WHERE `documentId`=?s",
                $status,'ACTIVE', $salesOrderNumber, $salesOrderNumberPrefix, $documentId);
            recordKeyDocumentHistory($documentId, $status,logActivity($status),"SalesOrder Number - " . $salesOrderNumberPrefix . $salesOrderNumber );
            echo "SUCCESS|Customer Approved";
            exit();
        }
        if ($status == ORDER_STATUS_CUSTOMER_REJECTED) {

            $keyDocument = new KeyDocument();
            $keyDocument->loadById($documentId);


            $res = $db->query("UPDATE `key_documents` SET `orderStatus`= ?s, `orderAcceptedByCustomer` = 'REJECTED', `orderAcceptedByCustomerDate` = NOW()   WHERE `documentId`=?s", $status, $documentId);
            $res = $db->query("INSERT INTO `ecommerce_order_customer_reject_reason` (`documentId`, `rejectReason`, `rejectedBy`) VALUES (?s, ?s, ?s)", $documentId, $rejectReason, $keyDocument->customerId);
            recordKeyDocumentHistory($documentId, $status,logActivity($status), $rejectReason );
            echo "SUCCESS|Order has been rejected by customer";
            exit();
        }

    }

} catch (Exception $e) {
    error_log("Error inserting document info: " . $e->getMessage());
    echo "ERROR|" . $e->getMessage();
    exit();
}