<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";

// Input validation
$warehouseId = filter_var($_POST['warehouseId'], FILTER_VALIDATE_INT);
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
    // Validate inputs
    if (!$warehouseId) {
        throw new Exception("Invalid or missing warehouse ID.");
    }

    if (empty($warehouseName) || empty($warehouseCode)) {
        throw new Exception("Warehouse name and code are required.");
    }

    // Update the warehouse record
    $res = $db->query(
        "UPDATE `warehouses` SET 
            `warehouseName` = ?s, 
            `warehouseCode` = ?s,
            `addressLine1` = ?s,
            `addressLine2` = ?s,
            `cityId` = ?s,
            `stateId` = ?s,
            `countryId` = ?s,
            `postalCode` = ?s,
            `email` = ?s,
            `phone` = ?s            
        WHERE `warehouseId` = ?i",
        $warehouseName,
        $warehouseCode,
        $warehouseAddress1,
        $warehouseAddress2,
        $warehouseCity,
        $warehouseState,
        $warehouseCountry,
        $warehousePostalCode,
        $warehouseEmail,
        $warehouseContact,
        $warehouseId
    );

    if ($res) {
        // Success message
        echo "SUCCESS|Warehouse updated successfully!";
    } else {
        // Safeguard: Unexpected scenario
        echo "ERROR|Query execution returned false with no specific error!";
    }
} catch (Exception $e) {
    // Log the error (you can implement a logger or store it in a database/log file)
    error_log("Error updating warehouse: " . $e->getMessage());

    // Send an error response
    echo "ERROR|Failed to update the warehouse. Error: " . $e->getMessage();
}
?>
