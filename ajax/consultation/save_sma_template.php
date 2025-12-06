<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

// Read raw JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Access values
$templateName                  = $input['templateName'] ?? '';
$content           = $input['content'] ?? '';
//$contentWhyChooseUs            = $input['contentWhyChooseUs'] ?? '';
//$contentClientResponsibilities = $input['contentClientResponsibilities'] ?? '';
//$contentTermsAndConditions     = $input['contentTermsAndConditions'] ?? '';
//$contentNextSteps              = $input['contentNextSteps'] ?? '';

if (!empty($templateName)) {

    try {
        //Create a new template
        $res = $db->query("INSERT INTO `consultation_sma_templates` 
    (
     `templateName`, 
     `content`) VALUES (?s,?s)",
            $templateName,
            $content
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