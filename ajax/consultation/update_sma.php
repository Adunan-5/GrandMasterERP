<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

// Read raw JSON input
$input = json_decode(file_get_contents('php://input'), true);

$smaID = $input['smaID'] ?? '';
$smaDate = $input['smaDate'] ?? '';
$smaTemplateID = $input['smaTemplateID'] ?? '';
$smaAmount = $input['smaAmount'] ?? '';
$smaDiscountType = $input['discountType'] ?? 'percentage';
$smaDiscount = isset($input['smaDiscount']) && trim($input['smaDiscount']) !== '' ? $input['smaDiscount'] : null;


$resTemplate = $db->query("SELECT * FROM consultation_sma_templates WHERE smaTemplateID=?s", $smaTemplateID);
$rowTemplate =mysqli_fetch_assoc($resTemplate);

if (!empty($smaID)) {

    try {
        //Update sma
        $db->query("START TRANSACTION");
        $res = $db->query("UPDATE `consultation_sma` SET
                             `dateCreated` = ?s,
                             `smaTemplateID` = ?s,
                             `totalAmount` = ?s,
                             `discountType` = ?s,
                             `smaDiscount` = ?s  WHERE `smaID` = ?s",
            $smaDate,
            $smaTemplateID,
            $smaAmount,
            $smaDiscountType,
            $smaDiscount,
            $smaID
        );

        $db->query("COMMIT");

    } catch (Exception $e) {
        $db->query("ROLLBACK");
        error_log("Error updating SMA: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => "Error updating sma." . $e->getMessage()]);
        exit();
    }
    echo json_encode(['status' => 'success', 'message' => 'SMA Saved Successfully.']);
    exit();

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing data']);
    exit();
}