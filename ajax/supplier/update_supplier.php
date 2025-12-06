<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";

$supplierId = filter_var($_POST['supplierId'], FILTER_VALIDATE_INT);
$supplierName = filter_var($_POST['supplierName'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$supplierNameAR = filter_var($_POST['supplierNameAR'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$companyCRNumber = filter_var($_POST['companyCRNumber'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$supplierEmail = filter_var($_POST['supplierEmail'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$supplierContact = filter_var($_POST['supplierContact'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$supplierAddress1 = filter_var($_POST['supplierAddress1'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$supplierAddress2 = filter_var($_POST['supplierAddress2'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$supplierCountry = filter_var($_POST['supplierCountry'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$supplierState = filter_var($_POST['supplierState'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$supplierCity = filter_var($_POST['supplierCity'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$supplierPostalCode = filter_var($_POST['supplierPostalCode'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$supplierWebsite = filter_var($_POST['supplierWebsite'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$supplierVATNumber = filter_var($_POST['supplierVATNumber'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$supplierPurchasePaymentTerm = filter_var($_POST['supplierPurchasePaymentTerm'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$supplierSalesPaymentTerm = filter_var($_POST['supplierSalesPaymentTerm'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$supplierCurrency = filter_var($_POST['supplierCurrency'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$supplierStatus = filter_var($_POST['supplierStatus'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

try {
    if (!$supplierId) {
        throw new Exception("Invalid or missing supplier ID.");
    }

    $res = $db->query(
        "UPDATE `suppliers` SET 
            `companyName` = ?s, 
            `companyNameAr` = ?s, 
            `companyCRNumber` = ?s, 
            `addressLine1` = ?s, 
            `addressLine2` = ?s, 
            `cityId` = ?s, 
            `stateId` = ?s, 
            `postalCode` = ?s, 
            `countryId` = ?s, 
            `vatNumber` = ?s, 
            `phone` = ?s, 
            `email` = ?s, 
            `website` = ?s, 
            `salesPaymentTermId` = ?s, 
            `purchasePaymentTermId` = ?s, 
            `currencyId` = ?s, 
            `status` = ?s 
        WHERE `supplierId` = ?i",
        $supplierName,
        $supplierNameAR,
        $companyCRNumber,
        $supplierAddress1,
        $supplierAddress2,
        $supplierCity,
        $supplierState,
        $supplierPostalCode,
        $supplierCountry,
        $supplierVATNumber,
        $supplierContact,
        $supplierEmail,
        $supplierWebsite,
        $supplierSalesPaymentTerm,
        $supplierPurchasePaymentTerm,
        $supplierCurrency,
        $supplierStatus,
        $supplierId
    );

    if ($res) {
        // Success message
        echo "SUCCESS|Supplier updated successfully!";
    } else {
        // Safeguard: Unexpected scenario
        echo "ERROR|Query execution returned false with no specific error!";
    }
} catch (Exception $e) {
    // Log the error (you can implement a logger or store it in a database/log file)
    error_log("Error updating supplier: " . $e->getMessage());

    // Send a JSON error response (or handle it based on your application)
    echo "ERROR|Failed to update the supplier. Error: " . $e->getMessage();
}