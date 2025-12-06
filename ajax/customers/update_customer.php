<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";

$customerId = filter_var($_POST['customerId'], FILTER_VALIDATE_INT);
$customerName = filter_var($_POST['customerName'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$customerNameAR = filter_var($_POST['customerNameAR'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$companyCRNumber = filter_var($_POST['companyCRNumber'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$customerEmail = filter_var($_POST['customerEmail'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$customerContact = filter_var($_POST['customerContact'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$customerAddress1 = filter_var($_POST['customerAddress1'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$customerAddress2 = filter_var($_POST['customerAddress2'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$customerCountry = filter_var($_POST['customerCountry'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$customerState = filter_var($_POST['customerState'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$customerCity = filter_var($_POST['customerCity'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$customerPostalCode = filter_var($_POST['customerPostalCode'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$customerWebsite = filter_var($_POST['customerWebsite'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$customerVATNumber = filter_var($_POST['customerVATNumber'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$customerPurchasePaymentTerm = filter_var($_POST['customerPurchasePaymentTerm'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$customerSalesPaymentTerm = filter_var($_POST['customerSalesPaymentTerm'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$customerCurrency = filter_var($_POST['customerCurrency'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$customerStatus = filter_var($_POST['customerStatus'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

try {
    if (!$customerId) {
        throw new Exception("Invalid or missing customer ID.");
    }

    // Fetch existing customer record (for retaining old filenames)
    $existing = $db->getRow("SELECT vatFileName, companyCRFileName FROM customers WHERE customerId = ?i", $customerId);
    $vatFileName = $existing['vatFileName'] ?? null;
    $crFileName = $existing['companyCRFileName'] ?? null;

    // === VAT File Upload ===
    if (!empty($_FILES['customerVATFile']['name'])) {
        $uploadDir = __DIR__ . '/../../uploads/customer-vat-files/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileExt = pathinfo($_FILES['customerVATFile']['name'], PATHINFO_EXTENSION);
        $newFileName = 'VAT_' . $customerId . '_' . time() . '.' . $fileExt;
        $targetFile = $uploadDir . $newFileName;

        if (move_uploaded_file($_FILES['customerVATFile']['tmp_name'], $targetFile)) {
            // Optionally delete old file
            if (!empty($vatFileName) && file_exists($uploadDir . $vatFileName)) {
                unlink($uploadDir . $vatFileName);
            }
            $vatFileName = $newFileName;
        } else {
            throw new Exception("Failed to upload VAT file.");
        }
    }

    // === CR File Upload ===
    if (!empty($_FILES['customerCRFile']['name'])) {
        $uploadDir = __DIR__ . '/../../uploads/customer-cr-files/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileExt = pathinfo($_FILES['customerCRFile']['name'], PATHINFO_EXTENSION);
        $newFileName = 'CR_' . $customerId . '_' . time() . '.' . $fileExt;
        $targetFile = $uploadDir . $newFileName;

        if (move_uploaded_file($_FILES['customerCRFile']['tmp_name'], $targetFile)) {
            // Optionally delete old file
            if (!empty($crFileName) && file_exists($uploadDir . $crFileName)) {
                unlink($uploadDir . $crFileName);
            }
            $crFileName = $newFileName;
        } else {
            throw new Exception("Failed to upload CR file.");
        }
    }

    $res = $db->query(
        "UPDATE `customers` SET 
            `companyName` = ?s, 
            `companyNameAr` = ?s, 
            `companyCRNumber` = ?s, 
            `companyCRFileName` = ?s,
            `addressLine1` = ?s, 
            `addressLine2` = ?s, 
            `cityId` = ?s, 
            `stateId` = ?s, 
            `postalCode` = ?s, 
            `countryId` = ?s, 
            `vatNumber` = ?s, 
            `vatFileName` = ?s,
            `phone` = ?s, 
            `email` = ?s, 
            `website` = ?s, 
            `salesPaymentTermId` = ?s, 
            `purchasePaymentTermId` = ?s, 
            `currencyId` = ?s, 
            `status` = ?s 
        WHERE `customerId` = ?i",
        $customerName,
        $customerNameAR,
        $companyCRNumber,
        $crFileName,
        $customerAddress1,
        $customerAddress2,
        $customerCity,
        $customerState,
        $customerPostalCode,
        $customerCountry,
        $customerVATNumber,
        $vatFileName,
        $customerContact,
        $customerEmail,
        $customerWebsite,
        $customerSalesPaymentTerm,
        $customerPurchasePaymentTerm,
        $customerCurrency,
        $customerStatus,
        $customerId
    );

    if ($res) {
        // Success message
        echo "SUCCESS|Customer updated successfully!";
    } else {
        // Safeguard: Unexpected scenario
        echo "ERROR|Query execution returned false with no specific error!";
    }
} catch (Exception $e) {
    // Log the error (you can implement a logger or store it in a database/log file)
    error_log("Error updating customer: " . $e->getMessage());

    // Send a JSON error response (or handle it based on your application)
    echo "ERROR|Failed to update the customer. Error: " . $e->getMessage();
}