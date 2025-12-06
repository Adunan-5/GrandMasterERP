<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

$warehouseID = "";
if(isset($_POST['warehouseID']))
    $warehouseID = filter_var($_POST['warehouseID'], FILTER_SANITIZE_NUMBER_INT);
if(empty($warehouseID)) $warehouseID = filter_var($_GET['warehouseID'], FILTER_SANITIZE_NUMBER_INT);

$warehouse = new Warehouse();
$warehouse->loadById($warehouseID);

$returnObject = get_object_vars($warehouse);
$returnObject['fullAddress']  = getFormattedAddress($warehouse);
$returnObject['city']  = getCityFromID($warehouse->cityId);
$returnObject['state']  = getStateFromID($warehouse->stateId);
$returnObject['country']  = getCountryFromID($warehouse->countryId);

echo json_encode($returnObject);