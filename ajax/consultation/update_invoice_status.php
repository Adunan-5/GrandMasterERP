<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";

$documentId   = filter_var($_POST['documentID'], FILTER_SANITIZE_NUMBER_INT);
$status       = filter_var($_POST['status'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$rejectReason = filter_var($_POST['rejectReason'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

$companyId = intval($_SESSION['SES_SELECTED_COMPANY']);

try {
    if (!empty($documentId)) {

        if ($status == INVOICE_STATUS_ACCOUNTANT_APPROVAL) {
            $res = $db->query("UPDATE `invoice_documents` SET `invoiceStatus`= ?s WHERE `invoiceId`=?s", $status, $documentId);
//            recordKeyDocumentHistory($documentId, $status,logActivity($status) );
            echo "SUCCESS|Sent for Approval";
            exit();
        }

        if ($status == INVOICE_STATUS_ACCOUNTANT_APPROVED) {

            $invoiceNumberPrefix = PREFIX_INVOICE;
            $invoiceDATA = "";
            $res     = $db->query("SELECT * FROM invoice_documents WHERE invoiceId = ?s", $documentId);
            while ($row = mysqli_fetch_assoc($res)) {
                $invoiceDATA = $row;
            }
            if(empty($invoiceDATA['invoiceNumber'])){
                $invoiceNumber = getNextNewInvoiceNumber();
            } else {
                $invoiceNumber = $invoiceDATA['invoiceNumber'];
            }

            $sdnNumberPrefix = PREFIX_SDN;
            $sdnDATA = "";
            $res = $db->query("SELECT * FROM consultation_service_delivery_notes WHERE invoiceId = ?s", $documentId);
            while ($row = mysqli_fetch_assoc($res)) {
                $sdnDATA = $row;
            }
            if(empty($sdnDATA['sdnNumber'])){
                $sdnNumber = getNextNewSDNNumber();
                $db->query("INSERT INTO consultation_service_delivery_notes (invoiceId, sdnNumberPrefix, sdnNumber, companyId) VALUES (?s, ?s, ?s, ?s)", $documentId, $sdnNumberPrefix, $sdnNumber, $companyId);
            } else {
                $sdnNumber = $sdnDATA['sdnNumber'];
            }
            $res = $db->query("UPDATE `invoice_documents` SET `invoiceStatus`= ?s, `invoiceNumber`=?s, `invoiceNumberPrefix`=?s, `accountantInvoiceApproved` = 'APPROVED' WHERE `invoiceId`=?s", $status, $invoiceNumber, $invoiceNumberPrefix, $documentId);
//            recordKeyDocumentHistory($documentId, $status,logActivity($status) );

            $fullInvoiceNumber = getInvoiceNumberFromInvoiceID($documentId);

            echo "SUCCESS|Invoice Approved Successfully";
            exit();
        }

        if ($status == INVOICE_STATUS_ACCOUNTANT_REJECTED) {
            $res = $db->query("UPDATE `invoice_documents` SET `invoiceStatus`= ?s, `accountantInvoiceApproved` = 'REJECTED' WHERE `invoiceId`=?s", $status, $documentId);
            $res = $db->query("INSERT INTO `invoice_reject_reason_accountant` (`invoiceId`, `rejectReason`, `rejectedBy`) VALUES (?s, ?s, ?s)", $documentId, $rejectReason, getAuthenticatedUser()->userID);
//            recordKeyDocumentHistory($documentId, $status,logActivity($status) );
            echo "SUCCESS|Invoice has been Rejected";
            exit();
        }
    }

} catch (Exception $e) {
    error_log("Error inserting document info: " . $e->getMessage());
    echo "ERROR|" . $e->getMessage();
    exit();
}