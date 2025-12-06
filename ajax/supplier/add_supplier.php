<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";

// Fetch companyId from session
$companyId = $_SESSION['SES_SELECTED_COMPANY'];

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

//INSERT INTO `grandmastererpdb`.`suppliers` (`companyName`, `companyNameAr`, `addressLine1`, `addressLine2`, `cityId`, `stateId`, `postalCode`, `countryId`, `vatNumber`, `phone`, `email`, `website`, `salesPaymentTermId`, `purchasePaymentTermId`, `currencyId`, `status`) VALUES ('asdf', 'asdf', 'asdf', 'asdf', 1, 1, '1', 1, '1', '1', '1', '1', 1, 1, 1, 'Inactive');

    $res = $db->query("INSERT INTO `suppliers` (   
                         `companyId`,
                         `companyName`, 
                         `companyNameAr`, 
                         `companyCRNumber`, 
                         `addressLine1`,
                         `addressLine2`,
                         `cityId`,
                         `stateId`,
                         `postalCode`,
                         `countryId`,
                         `vatNumber`,
                         `phone`,
                         `email`,
                         `website`,
                         `salesPaymentTermId`,
                         `purchasePaymentTermId`,
                         `currencyId`,
                         `status`
                         ) VALUES (
                                   
                       ?s, ?s,
                       ?s,?s,
                        ?s,?s,?s,?s,?s,?s,?s,?s,?s,?s,?s,?s,?s,?s)",
        $companyId,
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
        $supplierStatus
    );

    if ($res) {
        // Success message
//        echo json_encode(["status" => "SUCCESS", "message" => "Customer added successfully!"]);
        echo "SUCCESS|Supplier added successfully!";
    } else {
        // Safeguard: Unexpected scenario
        //throw new Exception("Query execution returned false with no specific error.");
        echo "ERROR|Query execution returned false with no specific error!";
    }
} catch (Exception $e) {
    // Log the error (you can implement a logger or store it in a database/log file)
    error_log("Error inserting supplier: " . $e->getMessage());

    // Send a JSON error response (or handle it based on your application)
    echo "ERROR|Failed to add the supplier. Error: " . $e->getMessage();
}