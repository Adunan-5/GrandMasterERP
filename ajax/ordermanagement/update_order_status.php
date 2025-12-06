<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";

$documentId   = filter_var($_POST['documentID'], FILTER_SANITIZE_NUMBER_INT);
$status       = filter_var($_POST['status'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$rejectReason = filter_var($_POST['rejectReason'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

try {
    if (!empty($documentId)) {

        if ($status == ORDER_WH_STATUS_READY) {
            try{
                $db->query('START TRANSACTION');
                $res = $db->query("UPDATE `order_management_documents` SET `orderWHStatus`= ?s WHERE `orderManagementId`=?s", $status, $documentId);

                $lineItemsRes = $db->query(
                    "SELECT * FROM `order_management_line_items` WHERE `orderManagementId` = ?s",
                    $documentId
                );

                if ($db->numRows($lineItemsRes) > 0) {
                    $updateRes = $db->query(
                        "UPDATE `order_management_line_items` SET `orderWHStatus` = ?s WHERE `orderManagementId` = ?s",
                        $status,
                        $documentId
                    );
                }
                $db->query("COMMIT");
                echo "SUCCESS|Shipment Confirmed";
                exit();
            } catch (Exception $e) {
                $db->query("ROLLBACK");
                error_log("Error updating orderWHStatus: " . $e->getMessage());
                echo "ERROR|" . $e->getMessage();
                exit();
            }
        }
    }


} catch (Exception $e) {
    error_log("Error confirming shipment: " . $e->getMessage());
    echo "ERROR|" . $e->getMessage();
    exit();
}