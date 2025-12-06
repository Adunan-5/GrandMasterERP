<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";
// Read the raw POST data
$rawData = file_get_contents("php://input");

// Decode the JSON data into a PHP array
$data = json_decode($rawData, true);

if ($data) {
    // Extract the repeater data and additional data
    $lineItems = $data['lineItems']['line-item'];

    $originWHID          = $data['originWHID'];
    $destinationWHID          = $data['destinationWHID'];
    $transferDate       = $data['transferDate'];
    $transferReason       = $data['transferReason'];
    $transferId          = $data['transferID'];
    $transferNumber     = $data['transferNumber'];
    $carrier = $data['carrier'];
    $tracking = $data['tracking'];
    $etaDate = $data['etaDate'];
    $reference = $data['reference'];
    $notes = $data['notes'];

    $modifiedDocumentId = 0;

    try {
        if (empty($transferId)) {
            //Create a new document
            $res                = $db->query("INSERT INTO `transfer_documents` (`originWHId`, `destinationWHId`, `transferDateCreated`, `salesPersonId`, `transferReason`, `shippingMethod`, `etaDate`, `trackingNumber`, `reference`, `notes`) VALUES (?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s)", $originWHID, $destinationWHID, $transferDate, getUserIDOfCurrentUser(), $transferReason, $carrier, $etaDate, $tracking, $reference, $notes);
            $modifiedDocumentId = $db->insertId();

        } else {
                $res = $db->query("UPDATE `transfer_documents` SET `originWHId`= ?s, `destinationWHId`=?s, `transferDateCreated`=?s, `salesPersonId`=?s, `transferReason`=?s, `shippingMethod`=?s, `etaDate`=?s, `trackingNumber`=?s, `reference`=?s, `notes`=?s WHERE `transferId`=?s", $originWHID, $destinationWHID, $transferDate, getUserIDOfCurrentUser(), $transferReason, $carrier, $etaDate, $tracking, $reference, $notes, $transferId);
                $modifiedDocumentId = $transferId;
            }

    } catch (Exception $e) {
        error_log("Error inserting document info: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => "Inserting Document Info: " . $e->getMessage()]);
        exit();
    }

    // Process the lineitem data
    //Delete all the lineItems before inserting.
    $res = $db->query("DELETE FROM transfer_line_items WHERE transferId = ?s", $modifiedDocumentId);
    foreach ($lineItems as $key => $values) {
        // Add your processing logic for each row here
        $sparepartID = $values['sparepartID'];
        $qty = $values['qty'];
        $eta = $values['eta'];
        $transferReason = (isset($values['transferReason']) && $values['transferReason'] !== '') ? $values['transferReason'] : null;
        $aisle = $values['aisle'] ?? null;
        $bin = $values['bin'] ?? null;
        $lot = $values['lot'] ?? null;


        if (!empty($sparepartID)) {
            try {
                $res = $db->query("INSERT INTO `transfer_line_items` 
                                                    (`transferId`, `itemId`, `quantity`, `eta`, `transferReason`, `aisle`, `bin`, `lotSerial`)
                                                    VALUES ( ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s)",
                    $modifiedDocumentId,
                    $sparepartID,
                    $qty,
                    $eta,
                    $transferReason,
                    $aisle,
                    $bin,
                    $lot);
            } catch (Exception $e) {
                error_log("Error inserting line items: " . $e->getMessage());
                echo json_encode(['status' => 'error', 'message' => "Line Items Error: " . $e->getMessage()]);
            }
        }
    }

    echo json_encode(['status' => 'success', 'message' => 'Transfer saved successfully.', 'documentId' => $modifiedDocumentId]);

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing data']);
}
