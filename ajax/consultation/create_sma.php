<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

// Read raw JSON input
$input = json_decode(file_get_contents('php://input'), true);

$customerID = $input['customerID']?? '';
$proposalID = $input['proposalID']?? '';
$smaTemplateID = $input['smaTemplateID']?? '';
$smaAmount = $input['smaAmount']?? '';

$resTemplate = $db->query("SELECT * FROM consultation_sma_templates WHERE smaTemplateID=?s", $smaTemplateID);
$rowTemplate =mysqli_fetch_assoc($resTemplate);

if (!empty($proposalID)) {

    try {
        //Create sma
        $db->query("START TRANSACTION");
        $res = $db->query("INSERT INTO `consultation_sma` 
                                    (`customerID`, 
                                     `associatedProposalID`, 
                                     `createdBy`, 
                                     `content`, 
                                     `status`,
                                     `totalAmount`, 
                                     `smaTemplateID`)
                            VALUES (?s,?s,?s,?s,?s,?s,?s)",
            $customerID,
            $proposalID,
            getUserIDOfCurrentUser(),
            $rowTemplate['content'],
            'DRAFT',
            $smaAmount,
            $smaTemplateID
        );

        $db->query("COMMIT");

    } catch (Exception $e) {
        $db->query("ROLLBACK");
        error_log("Error Adding SMA: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => "Error Adding sma." . $e->getMessage()]);
        exit();
    }
    echo json_encode(['status' => 'success', 'message' => 'SMA Added Successfully.']);
    exit();

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing data']);
    exit();
}