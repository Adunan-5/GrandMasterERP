<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

$customerID = "";
if(isset($_POST['customerID']))
$customerID = filter_var($_POST['customerID'], FILTER_SANITIZE_NUMBER_INT);
if(empty($customerID)) $customerID = filter_var($_GET['customerID'], FILTER_SANITIZE_NUMBER_INT);

$customer = new Customer();
$customer->loadById($customerID);

$returnObject = get_object_vars($customer);
$returnObject['fullAddress']  = getFormattedAddress($customer);
$returnObject['city']  = getCityFromID($customer->cityId);
$returnObject['state']  = getStateFromID($customer->stateId);
$returnObject['country']  = getCountryFromID($customer->countryId);



echo json_encode($returnObject);