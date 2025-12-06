<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";

$documentId = filter_var($_POST['documentID'], FILTER_SANITIZE_NUMBER_INT);
$status = filter_var($_POST['status'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$rejectReason = filter_var($_POST['rejectReason'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

try {
    if (!empty($documentId)) {

        if ($status == CONSULTATION_PROPOSAL_STATUS_CUSTOMER_ACCEPTED) {

                $res = $db->query("UPDATE `consultation_proposals` SET `status` = ?s WHERE `proposalID` = ?s", CONSULTATION_PROPOSAL_STATUS_CUSTOMER_ACCEPTED, $documentId);

            echo "SUCCESS|Customer Approved";
            exit();
        }
        if ($status == CONSULTATION_PROPOSAL_STATUS_CUSTOMER_REJECTED) {

            $res    = $db->query("UPDATE `consultation_proposals` SET `status` = ?s WHERE `proposalID` = ?s", CONSULTATION_PROPOSAL_STATUS_CUSTOMER_REJECTED, $documentId);
            $res    = $db->query("INSERT INTO consultation_proposal_customer_reject_reason (documentId, rejectReason) VALUES(?s, ?s)", $documentId, $rejectReason );

            echo "SUCCESS|Quotation has been rejected by customer";
            exit();
        }

    }

} catch (Exception $e) {
    error_log("Error inserting document info: " . $e->getMessage());
    echo "ERROR|" . $e->getMessage();
    exit();
}