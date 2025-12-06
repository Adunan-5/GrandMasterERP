<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";

$documentId   = filter_var($_POST['documentID'], FILTER_SANITIZE_NUMBER_INT);
$status       = filter_var($_POST['status'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$rejectReason = filter_var($_POST['rejectReason'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

try {
    if (!empty($documentId)) {



        if ($status == RFP_STATUS_PROCUREMENT_MANAGER_APPROVAL) {
            $res = $db->query("UPDATE `rfp_documents` SET `rfpStatus`= ?s WHERE `rfpId`=?s", $status, $documentId);
            recordRFPHistory($documentId, $status,logActivity($status) );
            echo "SUCCESS|Sent for Approval";
            exit();
        }

        if ($status == RFP_STATUS_PROCUREMENT_MANAGER_APPROVED) {

            $rfpNumberPrefix = PREFIX_RFP;
            $rfpDATA = "";
            $res     = $db->query("SELECT * FROM rfp_documents WHERE active = 1 and rfpId = ?s", $documentId);
            while ($row = mysqli_fetch_assoc($res)) {
                $rfpDATA = $row;
            }
            if(empty($rfpDATA['rfpNumber'])){
                $rfpNumber = getNextNewRFPNumber();
            } else {
                $rfpNumber = $rfpDATA['rfpNumber'];
            }
            $res = $db->query("UPDATE `rfp_documents` SET `rfpStatus`= ?s, `rfpNumber`=?s, `rfpNumberPrefix`=?s, `procurementManagerRFPApproved` = 'APPROVED' WHERE `rfpId`=?s", $status, $rfpNumber, $rfpNumberPrefix, $documentId);
            recordRFPHistory($documentId, $status,logActivity($status), "RFP Number generated - " . getRFPNumberFromDocumentID($documentId) );
            echo "SUCCESS|RFP Approved Successfully";
            exit();
        }

        if ($status == RFP_STATUS_PROCUREMENT_MANAGER_REJECTED) {
            $res = $db->query("UPDATE `rfp_documents` SET `rfpStatus`= ?s, `procurementManagerRFPApproved` = 'REJECTED' WHERE `rfpId`=?s", $status, $documentId);
            $res = $db->query("INSERT INTO `rfp_reject_reason_procurement_manager` (`rfpId`, `rejectReason`, `rejectedBy`) VALUES (?s, ?s, ?s)", $documentId, $rejectReason, getAuthenticatedUser()->userID);
            recordRFPHistory($documentId, $status,logActivity($status), $rejectReason);
            echo "SUCCESS|RFP has been Rejected";
            exit();
        }

        if($status == RFP_STATUS_SENT_TO_SUPPLIER) {
            $res = $db->query("SELECT * FROM rfp_documents WHERE rfpId = ?s", $documentId);
            $row = mysqli_fetch_assoc($res);
            if($row['rfpStatus'] == RFP_STATUS_PROCUREMENT_MANAGER_APPROVED){
                $res = $db->query("UPDATE `rfp_documents` SET `rfpStatus`= ?s WHERE `rfpId`=?s", $status, $documentId);
                echo "SUCCESS|RFP has been sent to supplier";
                recordRFPHistory($documentId, $status,logActivity($status) );
            } else {
                echo "DONOTSENDSUCCESS|RFP status has been changed";
            }
            exit();
        }
    }

} catch (Exception $e) {
    error_log("Error inserting document info: " . $e->getMessage());
    echo "ERROR|" . $e->getMessage();
    exit();
}