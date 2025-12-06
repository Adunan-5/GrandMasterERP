<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

// Read raw JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Access values

$smaTemplateID = $input['smaTemplateID'] ?? '';
$templateName  = $input['templateName'] ?? '';
$content       = $input['content'] ?? '';
$active        = $input['isActive'] ?? '';

if (!empty($smaTemplateID)) {

    try {
        //Create a new template
        $res = $db->query("UPDATE `consultation_sma_templates` SET 
                            `templateName` = ?s,
                            `content` = ?s, 
                            `active` = ?s
                           WHERE `smaTemplateID` = ?s",
            $templateName,
            $content,
            $active,
            $smaTemplateID);

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