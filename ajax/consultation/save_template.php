<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

// Read raw JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Access values
$templateName                  = $input['templateName'] ?? '';
$contentIntroduction           = $input['contentIntroduction'] ?? '';
$contentWhyChooseUs            = $input['contentWhyChooseUs'] ?? '';
$contentClientResponsibilities = $input['contentClientResponsibilities'] ?? '';
$contentTermsAndConditions     = $input['contentTermsAndConditions'] ?? '';
$contentNextSteps              = $input['contentNextSteps'] ?? '';

if (!empty($templateName)) {

    try {
        //Create a new template
        $res = $db->query("INSERT INTO `consultation_proposal_templates` 
    (
     `templateName`, 
     `contentIntroduction`,     
     `contentWhyChooseUs`,
     `contentClientResponsibilities`,
     `contentTermsAndConditions`,
     `contentNextSteps`) VALUES (?s,?s,?s,?s,?s,?s)",
            $templateName,
            $contentIntroduction,
            $contentWhyChooseUs,
            $contentClientResponsibilities,
            $contentTermsAndConditions,
            $contentNextSteps
        );

    } catch (Exception $e) {
        error_log("Error inserting template: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => "Error creating template." . $e->getMessage()]);
        exit();
    }
    echo json_encode(['status' => 'success', 'message' => 'Template Created Successfully.']);
    exit();

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing data']);
    exit();
}