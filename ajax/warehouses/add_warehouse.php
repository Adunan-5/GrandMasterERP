<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";

// Input fields from POST
$warehouseName = filter_var($_POST['warehouseName'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$warehouseCode = filter_var($_POST['warehouseCode'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$warehouseEmail = filter_var($_POST['warehouseEmail'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$warehouseContact = filter_var($_POST['warehouseContact'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$warehouseAddress1 = filter_var($_POST['warehouseAddress1'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$warehouseAddress2 = filter_var($_POST['warehouseAddress2'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$warehouseCountry = filter_var($_POST['warehouseCountry'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$warehouseState = filter_var($_POST['warehouseState'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$warehouseCity = filter_var($_POST['warehouseCity'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$warehousePostalCode = filter_var($_POST['warehousePostalCode'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

try {
    // Validate required fields
    if (empty($warehouseName)) {
        throw new Exception("Warehouse name is required.");
    }

    // Insert warehouse data into the database
    $res = $db->query(
        "INSERT INTO `warehouses` (`warehouseName`, `warehouseCode`, `addressLine1`, `addressLine2`, `cityId`, `stateId`, `countryId`, `postalCode`, `phone`, `email`) VALUES (?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s)",
        $warehouseName,
        $warehouseCode,
        $warehouseAddress1,
        $warehouseAddress2,
        $warehouseCity,
        $warehouseState,
        $warehouseCountry,
        $warehousePostalCode,
        $warehouseContact,
        $warehouseEmail
    );

    if ($res) {
        // Success message
        echo "SUCCESS|Warehouse added successfully!";
    } else {
        // Safeguard: Unexpected scenario
        echo "ERROR|Query execution returned false with no specific error!";
    }
} catch (Exception $e) {
    // Log the error
    error_log("Error inserting warehouse: " . $e->getMessage());

    // Send an error response
    echo "ERROR|Failed to add the warehouse. Error: " . $e->getMessage();
}
