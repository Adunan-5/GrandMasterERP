<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

// Read raw JSON input
$input = json_decode(file_get_contents('php://input'), true);

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
        //Create new proposal
        $db->query("START TRANSACTION");
        $res = $db->query("INSERT INTO `consultation_proposals`
                            (
                             `customerID`,
                             `preparedOn`,
                             `validUntil`,
                             `contentIntroduction`,
                             `contentWhyChooseUs`,
                             `contentScopeOfWork`,
                             `contentProjectTimeLine`,
                             `contentClientResponsibilities`,
                             `contentTermsAndConditions`,
                             `contentNextSteps`,
                             `status`,
                             `createdBy`
                             ) VALUES (?s,?s,?s,?s,?s,?s,?s,?s,?s,?s,?s,?s)",
            $customerID,
            $proposalDate,
            $validUntil,
            $rowTemplate['contentIntroduction'],
            $rowTemplate['contentWhyChooseUs'],
            $scopeOfWork,
            '',
            $rowTemplate['contentClientResponsibilities'],
            $rowTemplate['contentTermsAndConditions'],
            $rowTemplate['contentNextSteps'],
            'DRAFT',
            getUserIDOfCurrentUser()
        );

        $newProposalID = $db->insertId();


        foreach ($lineItems as $key=> $values){
            $res = $db->query("INSERT INTO consultation_proposal_line_items (documentId, itemId, quantity, unitPrice, UOM) values (?s,?s,?s,?s,?s )", $newProposalID, $values['service'], $values['qty'], $values['rate'], $values['uom']);
        }

        $db->query("COMMIT");

    } catch (Exception $e) {
        $db->query("ROLLBACK");
        error_log("Error inserting template: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => "Error creating proposal." . $e->getMessage()]);
        exit();
    }
    echo json_encode(['status' => 'success', 'message' => 'Proposal Created Successfully.']);
    exit();

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing data']);
    exit();
}