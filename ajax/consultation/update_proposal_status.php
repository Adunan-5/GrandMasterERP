<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

// Read raw JSON input
$input = json_decode(file_get_contents('php://input'), true);

$proposalID = $input['proposalID'] ?? '';
$status     = $input['status'] ?? '';

if (!empty($proposalID)) {
    try {
        //Update proposal status
        $db->query("START TRANSACTION");
        //'DRAFT','NEW','AWAITING APPROVAL','APPROVED','REJECTED','SENT TO CUSTOMER','CUSTOMER ACCEPTED','CUSTOMER REJECTED','CONFIRMED','CANCELLED'

        if ($status == CONSULTATION_PROPOSAL_STATUS_AWAITING_APPROVAL) {

            $associatedSMAID = getSMAIDForProposalID($proposalID);
            if (empty($associatedSMAID)) {
                echo json_encode(['status' => 'error', 'message' => 'No SMA has been added. Please add the SMA to send for approval.']);
            } else {
                $res = $db->query("UPDATE `consultation_proposals` SET `status` = ?s WHERE `proposalID` = ?s", CONSULTATION_PROPOSAL_STATUS_AWAITING_APPROVAL, $proposalID);

                $res = $db->query("UPDATE `consultation_sma` SET `status`=?s WHERE  `smaID`=?s", CONSULTATION_PROPOSAL_STATUS_AWAITING_APPROVAL, $associatedSMAID);

                echo json_encode(['status' => 'success', 'message' => 'Proposal Sent for Approval']);
            }


        }

        if ($status == CONSULTATION_PROPOSAL_STATUS_SENT_TO_CUSTOMER) {
            $res = $db->query("UPDATE `consultation_proposals` SET `status` = ?s WHERE `proposalID` = ?s", CONSULTATION_PROPOSAL_STATUS_SENT_TO_CUSTOMER, $proposalID);
            echo json_encode(['status' => 'success', 'message' => 'Proposal Sent to Customer']);
        }

        if ($status == CONSULTATION_PROPOSAL_STATUS_APPROVED) {


            //Check if there is an SMA and it is approved?
            $associatedSMAID = getSMAIDForProposalID($proposalID);
            $smaStatus       = getSMAStatusForSMAID($associatedSMAID);

            if ($smaStatus == CONSULTATION_PROPOSAL_STATUS_APPROVED) {
                //If sma is approved, it should proceed.
                $proposalNumberPrefix = PREFIX_CONSULTATION_PROPOSAL;
                $res                  = $db->query("select proposalNumber From consultation_proposals WHERE proposalID = ?s", $proposalID);
                $row                  = mysqli_fetch_assoc($res);
                $proposalNumber       = $row['proposalNumber'];
                if (empty($proposalNumber)) {
                    $proposalNumber = getNextNewProposalNumber();
                }

                $res = $db->query("UPDATE `consultation_proposals` SET `status` = ?s , proposalNumberPrefix = ?s, proposalNumber = ?s WHERE `proposalID` = ?s", CONSULTATION_PROPOSAL_STATUS_APPROVED, $proposalNumberPrefix, $proposalNumber, $proposalID);
                echo json_encode(['status' => 'success', 'message' => 'Proposal Approved']);
            } else {
                //If sma is not approved throw error message
                echo json_encode(['status' => 'error', 'message' => 'SMA is not approved. Please approve it first.']);
            }

        }

        if ($status == CONSULTATION_PROPOSAL_STATUS_REJECTED) {
            $rejectReason = $input['rejectReason'];
            $res          = $db->query("UPDATE `consultation_proposals` SET `status` = ?s WHERE `proposalID` = ?s", CONSULTATION_PROPOSAL_STATUS_REJECTED, $proposalID);
            $res          = $db->query("INSERT INTO consultation_proposal_reject_reason (documentId, rejectReason, rejectedBy) VALUES(?s, ?s, ?s)", $proposalID, $rejectReason, getUserIDOfCurrentUser());
            echo json_encode(['status' => 'success', 'message' => 'Proposal Rejected']);

        }

        //Confirm the proposal
        if ($status == CONSULTATION_PROPOSAL_STATUS_CONFIRMED) {

            $res = $db->query("UPDATE `consultation_proposals` SET `status` = ?s  WHERE `proposalID` = ?s", CONSULTATION_PROPOSAL_STATUS_CONFIRMED, $proposalID);
            echo json_encode(['status' => 'success', 'message' => 'Proposal Confirmed']);

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