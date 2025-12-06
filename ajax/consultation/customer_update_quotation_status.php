<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";

$documentId = filter_var($_POST['documentID'], FILTER_SANITIZE_NUMBER_INT);
$status = filter_var($_POST['status'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$rejectReason = filter_var($_POST['rejectReason'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

try {
    if (!empty($documentId)) {

        if ($status == QUOTATION_STATUS_CUSTOMER_ACCEPTED) {

            $res = $db->query("UPDATE `consultation_key_documents` SET `quotationStatus`= ?s, `quotationAcceptedByCustomer` = 'APPROVED', `quotationAcceptedByCustomerDate` = NOW()  WHERE `documentId`=?s", $status, $documentId);
            recordKeyDocumentHistory($documentId, $status,logActivity($status) );
            echo "SUCCESS|Customer Approved";
            exit();
        }
        if ($status == QUOTATION_STATUS_CUSTOMER_REJECTED) {

            $keyDocument = new ConsultationKeyDocument();
            $keyDocument->loadById($documentId);


            $res = $db->query("UPDATE `consultation_key_documents` SET `quotationStatus`= ?s, `quotationAcceptedByCustomer` = 'REJECTED', `quotationAcceptedByCustomerDate` = NOW()   WHERE `documentId`=?s", $status, $documentId);
            $res = $db->query("INSERT INTO `consultation_quotation_customer_reject_reason` (`documentId`, `rejectReason`, `rejectedBy`) VALUES (?s, ?s, ?s)", $documentId, $rejectReason, $keyDocument->customerId);
            recordKeyDocumentHistory($documentId, $status,logActivity($status), $rejectReason );
            echo "SUCCESS|Quotation has been rejected by customer";
            exit();
        }

    }

} catch (Exception $e) {
    error_log("Error inserting document info: " . $e->getMessage());
    echo "ERROR|" . $e->getMessage();
    exit();
}