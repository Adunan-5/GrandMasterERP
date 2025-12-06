<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

// Read raw JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Access values

$proposalTemplateID            = $input['proposalTemplateID'] ?? '';
$templateName                  = $input['templateName'] ?? '';
$contentIntroduction           = $input['contentIntroduction'] ?? '';
$contentWhyChooseUs            = $input['contentWhyChooseUs'] ?? '';
$contentClientResponsibilities = $input['contentClientResponsibilities'] ?? '';
$contentTermsAndConditions     = $input['contentTermsAndConditions'] ?? '';
$contentNextSteps              = $input['contentNextSteps'] ?? '';
$active                        = $input['isActive'] ?? '';

if (!empty($proposalTemplateID)) {

    try {
        //Create a new template
        $res = $db->query("UPDATE `consultation_proposal_templates` SET 
                            `templateName` = ?s,
                            `contentIntroduction` = ?s,
                            `contentWhyChooseUs` = ?s,
                            `contentClientResponsibilities` = ?s,
                            `contentTermsAndConditions` = ?s,
                            `contentNextSteps` = ?s, 
                            `active` = ?s
                           WHERE `proposalTemplateID` = ?s",
            $templateName,
            $contentIntroduction,
            $contentWhyChooseUs,
            $contentClientResponsibilities,
            $contentTermsAndConditions,
            $contentNextSteps,
            $active,
            $proposalTemplateID);

    } catch (Exception $e) {
        error_log("Error inserting template: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => "Error updating template." . $e->getMessage()]);
        exit();
    }
    echo json_encode(['status' => 'success', 'message' => 'Template Updating Successfully.']);
    exit();

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing data']);
    exit();
}