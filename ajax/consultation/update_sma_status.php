<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

// Read raw JSON input
$input = json_decode(file_get_contents('php://input'), true);

$smaID = $input['smaID'] ?? '';
$status     = $input['status'] ?? '';

if (!empty($smaID)) {
    try {
        //Update proposal status
        $db->query("START TRANSACTION");
        //'DRAFT','NEW','AWAITING APPROVAL','APPROVED','REJECTED','SENT TO CUSTOMER','CUSTOMER ACCEPTED','CUSTOMER REJECTED','CONFIRMED','CANCELLED'

        if ($status == CONSULTATION_PROPOSAL_STATUS_SENT_TO_CUSTOMER) {
            $res = $db->query("UPDATE `consultation_proposals` SET `status` = ?s WHERE `proposalID` = ?s", CONSULTATION_PROPOSAL_STATUS_SENT_TO_CUSTOMER, $proposalID);
            echo json_encode(['status' => 'success', 'message' => 'Proposal Sent to Customer']);
        }

        if ($status == CONSULTATION_PROPOSAL_STATUS_APPROVED) {

            $smaNumberPrefix = PREFIX_CONSULTATION_SMA;
            $res = $db->query("select smaNumber From consultation_sma WHERE smaID = ?s", $smaID);
            $row=mysqli_fetch_assoc($res);
            $smaNumber       = $row['smaNumber'];
            if (empty($smaNumber)) {
                $smaNumber = getNextNewSMANumber();
            }

            $res = $db->query("UPDATE `consultation_sma` SET `status` = ?s , smaNumberPrefix = ?s, smaNumber = ?s WHERE `smaID` = ?s", CONSULTATION_PROPOSAL_STATUS_APPROVED, $smaNumberPrefix, $smaNumber, $smaID);
            echo json_encode(['status' => 'success', 'message' => 'SMA Approved']);
        }

        if ($status == CONSULTATION_PROPOSAL_STATUS_REJECTED) {
            $rejectReason = $input['rejectReason'];
            $res          = $db->query("UPDATE `consultation_proposals` SET `status` = ?s WHERE `proposalID` = ?s", CONSULTATION_PROPOSAL_STATUS_REJECTED, $proposalID);
            $res          = $db->query("INSERT INTO consultation_proposal_reject_reason (documentId, rejectReason, rejectedBy) VALUES(?s, ?s, ?s)", $proposalID, $rejectReason, getUserIDOfCurrentUser());
            echo json_encode(['status' => 'success', 'message' => 'Proposal Rejected']);

        }


        $db->query("COMMIT");
        exit();

    } catch (Exception $e) {
        $db->query("ROLLBACK");
        error_log("Error updating proposal: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => "Error updating proposal." . $e->getMessage()]);
        exit();
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing data']);
    exit();
}