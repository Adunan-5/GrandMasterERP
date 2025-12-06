<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

$supplierID = "";
if(isset($_POST['supplierID']))
    $supplierID = filter_var($_POST['supplierID'], FILTER_SANITIZE_NUMBER_INT);
if(empty($supplierID)) $supplierID = filter_var($_GET['supplierID'], FILTER_SANITIZE_NUMBER_INT);

$supplier = new Supplier();
$supplier->loadById($supplierID);

$returnObject = get_object_vars($supplier);
$returnObject['fullAddress']  = getFormattedAddress($supplier);
$returnObject['city']  = getCityFromID($supplier->cityId);
$returnObject['state']  = getStateFromID($supplier->stateId);
$returnObject['country']  = getCountryFromID($supplier->countryId);

echo json_encode($returnObject);