<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";


$data = json_decode(file_get_contents('php://input'), true);

$sparePartId = null;
$newQuantity = null;
$selectedWarehouseID = null;
$aisle = null;
$bin = null;
$lotSerial = null;

if (is_array($data)) {
    if (isset($data['sparepartID'])) {
        $sparePartId = filter_var($data['sparepartID'], FILTER_VALIDATE_INT);
    }

    if (isset($data['newQuantity'])) {
        $newQuantity = filter_var($data['newQuantity'], FILTER_VALIDATE_INT);
    }

    if (isset($data['warehouseID'])) {
        $selectedWarehouseID = filter_var($data['warehouseID'], FILTER_VALIDATE_INT);
    }

    if (isset($data['aisle'])) {
        $aisle = filter_var($data['aisle'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }

    if (isset($data['bin'])) {
        $bin = filter_var($data['bin'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }

    if (isset($data['lotSerial'])) {
        $lotSerial = filter_var($data['lotSerial'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }
}

if ($sparePartId === false || $sparePartId === null) {

    // Response format
    $response = [
        "status" => "ERROR",
        "message" => "Invalid or missing Spare Part ID."
    ];

    // Output JSON response
    echo json_encode($response);
    exit();
}

if ($selectedWarehouseID === false || $selectedWarehouseID === null) {

    // Response format
    $response = [
        "status" => "ERROR",
        "message" => "Invalid or missing Warehouse."
    ];

    // Output JSON response
    echo json_encode($response);
    exit();
}

if ($newQuantity === false || $newQuantity === null) {

    $response = [
        "status" => "ERROR",
        "message" => "Invalid or missing new Quantity."
    ];

    // Output JSON response
    echo json_encode($response);
    exit();
}

if ($aisle === false || $aisle === null || $aisle === "") {

    $response = [
        "status" => "ERROR",
        "message" => "Missing Aisle."
    ];

    // Output JSON response
    echo json_encode($response);
    exit();
}

if ($bin === false || $bin === null || $bin === "") {

    $response = [
        "status" => "ERROR",
        "message" => "Missing Bin."
    ];

    // Output JSON response
    echo json_encode($response);
    exit();
}

if ($lotSerial === false || $lotSerial === null || $lotSerial === "") {

    $response = [
        "status" => "ERROR",
        "message" => "Missing Lot Serial."
    ];

    // Output JSON response
    echo json_encode($response);
    exit();
}


try {
    //Get the current Quantity
    $currentQuantity = null;
    $res = $db->query("SELECT * FROM inventory_stock WHERE warehouseId = ?s and sparepartId = ?s", $selectedWarehouseID, $sparePartId);

    while($row=mysqli_fetch_assoc($res))
    {
        $currentQuantity = $row['quantity'];
    }

    if(empty($currentQuantity)) $currentQuantity = 0;


    $res = $db->query("START TRANSACTION");

    //ledger entry
    $res = $db->query("INSERT INTO `inventory_ledger` (`sparepartId`, `updatedBy`, `updateReason`, `quantityBeforeUpdate`, `newQuantity`) VALUES (?s, ?s, 'MANUAL', ?s, ?s)",
    $sparePartId, getAuthenticatedUser()->userID,$currentQuantity, $newQuantity    );

    $alreadyExist = false;

    $res = $db->query("SELECT * FROM inventory_stock WHERE  `warehouseId` = ?s AND `sparepartId` = ?s", $selectedWarehouseID, $sparePartId);

    if($db->numRows($res)> 0)
        $alreadyExist = true;

    if($alreadyExist)
    {

        $res = $db->query("UPDATE `inventory_stock` SET `quantity` = ?s, `aisle` = ?s, `bin` = ?s, `lotSerial` = ?s WHERE `warehouseId` = ?s AND `sparepartId` = ?s",
            $newQuantity, $aisle, $bin, $lotSerial, $selectedWarehouseID, $sparePartId);
    }
    else
    {
        $db->query(
            "INSERT INTO `inventory_stock` (`warehouseId`, `sparepartId`, `quantity`, `aisle`, `bin`, `lotSerial`) 
             VALUES (?s, ?s, ?s, ?s, ?s, ?s)",
            $selectedWarehouseID, $sparePartId, $newQuantity, $aisle, $bin, $lotSerial
        );

    }





    $res = $db->query("COMMIT");

    recordSparepartHistory($sparePartId, OT_SPAREPART_QUANTITY_CHANGE, logActivity(OT_SPAREPART_QUANTITY_CHANGE), "Sparepart Quantity Adjusted to " . $newQuantity . " at " . " " . getWarehouseNameFromID($selectedWarehouseID) . " for " . getPartNumberForSparepartID($sparePartId) );


    $response = [
        "status" => "SUCCESS",
        "message" => "Inventory updated successfully"
    ];

    // Output JSON response
    echo json_encode($response);
    exit();



}catch (Exception $e)
{
    $db->query("ROLLBACK");

    // Response format
    $response = [
        "status" => "ERROR",
        "message" => "Error updating inventory.",
        "console" => $e->getMessage()

    ];

    // Output JSON response
    echo json_encode($response);
    exit();
}

?>