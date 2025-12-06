<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

// Read raw JSON input
$input = json_decode(file_get_contents('php://input'), true);

$proposalID = $input['proposalID'] ?? '';
$customerID = $input['customerID'] ?? '';
$proposalDate = $input['proposalDate'] ?? '';
$validUntil = $input['validUntil'] ?? '';
$proposalTemplateID = $input['proposalTemplateID'] ?? '';
$proposalTitle = $input['proposalTitle'] ?? '';
$scopeOfWork = $input['scopeOfWork'] ?? '';
$lineItems = $input['lineItems'] ?? '';




$resTemplate = $db->query("SELECT * FROM consultation_proposal_templates WHERE proposalTemplateID=?s", $proposalTemplateID);
$rowTemplate =mysqli_fetch_assoc($resTemplate);

if (!empty($customerID)) {

    try {
        //Update proposal
        $db->query("START TRANSACTION");
        $res = $db->query("UPDATE `consultation_proposals` SET
                             `customerID` = ?s,
                             `preparedOn` = ?s,
                             `validUntil` = ?s,
                             `proposalTemplateID` = ?s,
                             `proposalTitle` = ?s,
                             `contentIntroduction` = ?s,
                             `contentWhyChooseUs` = ?s,
                             `contentScopeOfWork` = ?s,
                             `contentProjectTimeLine` = ?s,
                             `contentClientResponsibilities` = ?s,
                             `contentTermsAndConditions` = ?s,
                             `contentNextSteps` = ?s,
                             `createdBy` = ?s  WHERE `proposalID` = ?s",
            $customerID,
            $proposalDate,
            $validUntil,
            $proposalTemplateID,
            $proposalTitle,
            $rowTemplate['contentIntroduction'],
            $rowTemplate['contentWhyChooseUs'],
            $scopeOfWork,
            '',
            $rowTemplate['contentClientResponsibilities'],
            $rowTemplate['contentTermsAndConditions'],
            $rowTemplate['contentNextSteps'],
            getUserIDOfCurrentUser(),
            $proposalID
        );

        //First clean up the line items
        $res = $db->query("DELETE FROM consultation_proposal_line_items WHERE `documentId` = ?s", $proposalID);
        foreach ($lineItems as $key=> $values){
            $res = $db->query("INSERT INTO consultation_proposal_line_items (documentId, itemId, quantity, unitPrice, UOM) values (?s,?s,?s,?s,?s )", $proposalID, $values['service'], $values['qty'], $values['rate'], $values['uom']);
        }

        $db->query("COMMIT");

    } catch (Exception $e) {
        $db->query("ROLLBACK");
        error_log("Error updating proposal: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => "Error updating proposal." . $e->getMessage()]);
        exit();
    }
    echo json_encode(['status' => 'success', 'message' => 'Proposal Saved Successfully.']);
    exit();

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing data']);
    exit();
}