<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

// Read raw JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Access values
$templateID                  = $input['templateID'] ?? '';

if (!empty($templateID)) {

    try {

        $res = $db->query("DELETE FROM `consultation_proposal_templates` WHERE proposalTemplateID = ?s" , $templateID        );

    } catch (Exception $e) {
        error_log("Error deleting template: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => "Error deleting template." . $e->getMessage()]);
        exit();
    }
    echo json_encode(['status' => 'success', 'message' => 'Template Deleted Successfully.']);
    exit();

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing data']);
    exit();
}