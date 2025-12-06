<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";

$documentId   = filter_var($_POST['documentID'], FILTER_SANITIZE_NUMBER_INT);
$status       = filter_var($_POST['status'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$rejectReason = filter_var($_POST['rejectReason'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

try {
    if (!empty($documentId)) {

        if ($status == QUOTATION_STATUS_AWAITING_APPROVAL) {
            $res = $db->query("UPDATE `consultation_key_documents` SET `quotationStatus`= ?s WHERE `documentId`=?s", $status, $documentId);
            recordConsultationKeyDocumentHistory($documentId, $status,logActivity($status) );
            echo "SUCCESS|Sent for Approval";
            exit();
        }

        if ($status == QUOTATION_STATUS_CANCELLED) {
            $res = $db->query("UPDATE `consultation_key_documents` SET `quotationStatus`= ?s, `cancelReason`= ?s WHERE `documentId`=?s", $status, $rejectReason, $documentId);
            recordConsultationKeyDocumentHistory($documentId, $status,logActivity($status), $rejectReason);
            echo "SUCCESS|Quotation has been cancelled";
            exit();
        }

        //Sales Manager approval
        if ($status == QUOTATION_STATUS_APPROVED) {

            $quotationNumberPrefix = PREFIX_CONSULTATION_QUOTATION;
            $keyDocument           = new ConsultationKeyDocument();
            $keyDocument->loadById($documentId);
            if (empty($keyDocument->quotationNumber)) {
                $quotationNumber = getNextNewConsultationQuotationNumber();
            } else {
                $quotationNumber = $keyDocument->quotationNumber;
            }

            $res = $db->query("UPDATE `consultation_key_documents` SET `quotationStatus`= ?s, `quotationNumber`=?s, `quotationNumberPrefix`=?s WHERE `documentId`=?s", $status, $quotationNumber, $quotationNumberPrefix, $documentId);
            recordConsultationKeyDocumentHistory($documentId, $status,logActivity($status) , "Quotation number generated - " . $quotationNumberPrefix . $quotationNumber);
            echo "SUCCESS|Quotation has been Approved!";
            exit();
        }


        //Sales Manager approval for SO
        if ($status == QUOTATION_STATUS_AWAITING_SALES_MANAGER_SO_APPROVAL) {

            $res = $db->query("UPDATE `consultation_key_documents` SET `quotationStatus`= ?s WHERE `documentId`=?s", $status, $documentId);
            recordConsultationKeyDocumentHistory($documentId, $status, logActivity($status) );
            echo "SUCCESS|Sent for SO Approval with sales manager";
            exit();
        }

        //Sales Manager REJECTED for SO
        if ($status == QUOTATION_STATUS_SALES_MANAGER_SO_REJECTED) {

            $res = $db->query("UPDATE `consultation_key_documents` SET `quotationStatus`= ?s WHERE `documentId`=?s", $status, $documentId);
            $res = $db->query("INSERT INTO `consultation_quotation_reject_reason_sm_so` (`documentId`, `rejectReason`, `rejectedBy`) VALUES (?s, ?s, ?s)", $documentId, $rejectReason, getAuthenticatedUser()->userID);
            recordConsultationKeyDocumentHistory($documentId, $status, logActivity($status) , $rejectReason);
            echo "SUCCESS|Salesorder has been rejected";
            exit();
        }


        //Accountant REJECTED for SO
        if ($status == QUOTATION_STATUS_ACCOUNTANT_SO_REJECTED) {

            $res = $db->query("UPDATE `consultation_key_documents` SET `quotationStatus`= ?s WHERE `documentId`=?s", $status, $documentId);
            $res = $db->query("INSERT INTO `consultation_quotation_reject_reason_accountant_so` (`documentId`, `rejectReason`, `rejectedBy`) VALUES (?s, ?s, ?s)", $documentId, $rejectReason, getAuthenticatedUser()->userID);
            recordConsultationKeyDocumentHistory($documentId, $status, logActivity($status) , $rejectReason);
            echo "SUCCESS|Salesorder has been rejected";
            exit();
        }

        //Accountant approval for SO
        if ($status == QUOTATION_STATUS_AWAITING_ACCOUNTANT_SO_APPROVAL) {

            $res = $db->query("UPDATE `consultation_key_documents` SET `quotationStatus`= ?s, `salesManagerSOApproved` = 'APPROVED' WHERE `documentId`=?s", $status, $documentId);
            recordConsultationKeyDocumentHistory($documentId, $status,logActivity($status) );
            echo "SUCCESS|Sent for SO Approval with Accountant";
            exit();
        }

        //Accountant approval for SO
        if ($status == QUOTATION_STATUS_SO_APPROVED) {

            $res = $db->query("UPDATE `consultation_key_documents` SET `quotationStatus`= ?s, `accountantSOApproved` = 'APPROVED' WHERE `documentId`=?s", $status, $documentId);
            recordConsultationKeyDocumentHistory($documentId, $status,logActivity($status) );
            echo "SUCCESS|SO Approved";
            exit();
        }


        //Quotation Confirmed for sales order
        if ($status == QUOTATION_STATUS_CONFIRMED) {

            $salesOrderNumberPrefix = PREFIX_CONSULTATION_SALESORDER;
            $keyDocument           = new ConsultationKeyDocument();
            $keyDocument->loadById($documentId);
            if (empty($keyDocument->saleOrderNumber)) {
                $salesOrderNumber = getNextNewConsultationSalesOrderNumber();
            } else {
                $salesOrderNumber = $keyDocument->saleOrderNumber;
            }

            $res = $db->query("UPDATE `consultation_key_documents` SET `quotationStatus`= ?s, `saleOrderDateIssued` = now(), `saleOrderNumber`=?s, `saleOrderNumberPrefix`=?s WHERE `documentId`=?s", $status, $salesOrderNumber, $salesOrderNumberPrefix, $documentId);
            recordConsultationKeyDocumentHistory($documentId, $status,logActivity($status), "SalesOrder Number - " . $salesOrderNumberPrefix . $salesOrderNumber );
            echo "SUCCESS|Quotation Confirmed. SalesOrder# " . $salesOrderNumberPrefix . $salesOrderNumber;
            exit();
        }

        if ($status == QUOTATION_STATUS_SENT_TO_CUSTOMER) {
            $res = $db->query("UPDATE `consultation_key_documents` SET `quotationStatus`= ?s, `quotationAcceptedByCustomer`='NEUTRAL',`salesManagerSOApproved`='NEUTRAL', `accountantSOApproved`='NEUTRAL'  WHERE `documentId`=?s", $status, $documentId);
            recordConsultationKeyDocumentHistory($documentId, $status,logActivity($status) );
            echo "SUCCESS|Email has been sent to customer";
            exit();
        }


        if ($status == QUOTATION_STATUS_REJECTED) {
            $res = $db->query("UPDATE `consultation_key_documents` SET `quotationStatus`= ?s  WHERE `documentId`=?s", $status, $documentId);
            $res = $db->query("INSERT INTO `consultation_quotation_reject_reason` (`documentId`, `rejectReason`, `rejectedBy`) VALUES (?s, ?s, ?s)", $documentId, $rejectReason, getAuthenticatedUser()->userID);
            recordConsultationKeyDocumentHistory($documentId, $status,logActivity($status) , $rejectReason);
            echo "SUCCESS|Quotation has been rejected";
            exit();
        }

        if ($status == QUOTATION_STATUS_PROFORMA_ISSUED) {

            $proformaInvoiceNumberPrefix = PREFIX_CONSULTATION_PROFORMA_INVOICE;
            $keyDocument           = new ConsultationKeyDocument();
            $keyDocument->loadById($documentId);
            if (empty($keyDocument->proformaInvoiceNumber)) {
                $proformaInvoiceNumber = getNextNewConsultationProformaInvoiceNumber();
            } else {
                $proformaInvoiceNumber = $keyDocument->proformaInvoiceNumber;
            }

            $res = $db->query("UPDATE `consultation_key_documents` SET `quotationStatus`= ?s, `proformaInvoiceDateIssued` = now(), `proformaInvoiceNumber`=?s, `proformaInvoiceNumberPrefix`=?s WHERE `documentId`=?s", $status, $proformaInvoiceNumber, $proformaInvoiceNumberPrefix, $documentId);
            recordConsultationKeyDocumentHistory($documentId, $status,logActivity($status), "Proforma Invoice Number - " . $proformaInvoiceNumberPrefix . $proformaInvoiceNumber );
            echo "SUCCESS|Proforma Invoice generated Successfully. Proforma Invoice# " . $proformaInvoiceNumberPrefix . $proformaInvoiceNumber;
            exit();
        }
    }

} catch (Exception $e) {
    error_log("Error inserting document info: " . $e->getMessage());
    echo "ERROR|" . $e->getMessage();
    exit();
}