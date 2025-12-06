<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";

$documentId   = filter_var($_POST['documentID'], FILTER_SANITIZE_NUMBER_INT);
$status       = filter_var($_POST['status'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$rejectReason = filter_var($_POST['rejectReason'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

$companyId = intval($_SESSION['SES_SELECTED_COMPANY']);

try {
    if (!empty($documentId)) {

        if ($status == ORDER_STATUS_AWAITING_APPROVAL) {
            $res = $db->query("UPDATE `key_documents` SET `orderStatus`= ?s WHERE `documentId`=?s", $status, $documentId);
            recordKeyDocumentHistory($documentId, $status,logActivity($status) );
            echo "SUCCESS|Sent for Approval";
            exit();
        }

        if ($status == ORDER_STATUS_APPROVED) {

            $res = $db->query("UPDATE `key_documents` SET `orderStatus`= ?s WHERE `documentId`=?s", $status,  $documentId);
            recordKeyDocumentHistory($documentId, $status,logActivity($status));
            echo "SUCCESS|E-commerce Order has been Approved!";
            exit();
        }

        if ($status == ORDER_STATUS_REJECTED) {
            $res = $db->query("UPDATE `key_documents` SET `orderStatus`= ?s  WHERE `documentId`=?s", $status, $documentId);
            $res = $db->query("INSERT INTO `ecommerce_order_reject_reason` (`documentId`, `rejectReason`, `rejectedBy`) VALUES (?s, ?s, ?s)", $documentId, $rejectReason, getAuthenticatedUser()->userID);
            recordKeyDocumentHistory($documentId, $status,logActivity($status) , $rejectReason);
            echo "SUCCESS|E-commerce Order has been rejected";
            exit();
        }

        if ($status == ORDER_STATUS_ACCOUNTANT_APPROVED) {

            $res = $db->query("UPDATE `key_documents` SET `orderStatus`= ?s WHERE `documentId`=?s", $status,  $documentId);
            recordKeyDocumentHistory($documentId, $status,logActivity($status));
            echo "SUCCESS|E-commerce Order has been Approved by Accountant!";
            exit();
        }

        if ($status == ORDER_STATUS_ACCOUNTANT_REJECTED) {

            $res = $db->query("UPDATE `key_documents` SET `orderStatus`= ?s WHERE `documentId`=?s", $status, $documentId);
            $res = $db->query("INSERT INTO `ecommerce_order_reject_reason_accountant` (`documentId`, `rejectReason`, `rejectedBy`) VALUES (?s, ?s, ?s)", $documentId, $rejectReason, getAuthenticatedUser()->userID);
            recordKeyDocumentHistory($documentId, $status, logActivity($status) , $rejectReason);
            echo "SUCCESS|E-commerce Order has been rejected by Accountant";
            exit();
        }

        if ($status == ORDER_STATUS_SENT_TO_CUSTOMER) {

            $res = $db->query("UPDATE `key_documents` SET `orderStatus`= ?s, `orderAcceptedByCustomer`='NEUTRAL',`salesManagerSOApproved`='NEUTRAL', `accountantSOApproved`='NEUTRAL'  WHERE `documentId`=?s", $status, $documentId);
            recordKeyDocumentHistory($documentId, $status,logActivity($status) );
            echo "SUCCESS|Email has been sent to customer";
            exit();
        }


    }

} catch (Exception $e) {
    error_log("Error inserting document info: " . $e->getMessage());
    echo "ERROR|" . $e->getMessage();
    exit();
}