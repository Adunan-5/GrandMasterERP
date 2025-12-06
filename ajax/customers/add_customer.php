<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";

// Fetch companyId from session
$companyId = $_SESSION['SES_SELECTED_COMPANY'];

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

// File inputs
$customerVATFile = $_FILES['customerVATFile'] ?? null;
$customerCRFile = $_FILES['customerCRFile'] ?? null;

try {

//INSERT INTO `grandmastererpdb`.`customers` (`companyName`, `companyNameAr`, `addressLine1`, `addressLine2`, `cityId`, `stateId`, `postalCode`, `countryId`, `vatNumber`, `phone`, `email`, `website`, `salesPaymentTermId`, `purchasePaymentTermId`, `currencyId`, `status`) VALUES ('asdf', 'asdf', 'asdf', 'asdf', 1, 1, '1', 1, '1', '1', '1', '1', 1, 1, 1, 'Inactive');

    $vatFileName = null;
    $crFileName = null;

    // Upload VAT File
    if ($customerVATFile && $customerVATFile['error'] === UPLOAD_ERR_OK) {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
        $vatExt = strtolower(pathinfo($customerVATFile['name'], PATHINFO_EXTENSION));

        if (!in_array($vatExt, $allowedExtensions)) {
            throw new Exception("Invalid VAT file type. Allowed types: jpg, jpeg, png, pdf");
        }

        $vatDir = __DIR__ . "/../../uploads/customer-vat-files/";
        if (!is_dir($vatDir)) {
            mkdir($vatDir, 0777, true);
        }

        $vatFileName = preg_replace('/\s+/', '_', $customerName) . "_VAT_" . bin2hex(random_bytes(4)) . "." . $vatExt;
        $vatPath = $vatDir . $vatFileName;

        if (!move_uploaded_file($customerVATFile['tmp_name'], $vatPath)) {
            throw new Exception("Failed to upload VAT file.");
        }
    }

    // Upload CR File
    if ($customerCRFile && $customerCRFile['error'] === UPLOAD_ERR_OK) {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
        $crExt = strtolower(pathinfo($customerCRFile['name'], PATHINFO_EXTENSION));

        if (!in_array($crExt, $allowedExtensions)) {
            throw new Exception("Invalid CR file type. Allowed types: jpg, jpeg, png, pdf");
        }

        $crDir = __DIR__ . "/../../uploads/customer-cr-files/";
        if (!is_dir($crDir)) {
            mkdir($crDir, 0777, true);
        }

        $crFileName = preg_replace('/\s+/', '_', $customerName) . "_CR_" . bin2hex(random_bytes(4)) . "." . $crExt;
        $crPath = $crDir . $crFileName;

        if (!move_uploaded_file($customerCRFile['tmp_name'], $crPath)) {
            throw new Exception("Failed to upload CR file.");
        }
    }

    $res = $db->query("INSERT INTO `customers` (   
                         `companyId`,
                         `companyName`, 
                         `companyNameAr`, 
                         `companyCRNumber`, 
                         `companyCRFileName`,
                         `addressLine1`,
                         `addressLine2`,
                         `cityId`,
                         `stateId`,
                         `postalCode`,
                         `countryId`,
                         `vatNumber`,
                         `vatFileName`,
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
                        ?s,?s,?s,?s,?s,?s,?s,?s,?s,?s,?s,?s,?s,?s,?s,?s)",
        $companyId,
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
        $customerStatus
    );

    if ($res) {
        // Success message
//        echo json_encode(["status" => "SUCCESS", "message" => "Customer added successfully!"]);
        echo "SUCCESS|Customer added successfully!";
    } else {
        // Safeguard: Unexpected scenario
        //throw new Exception("Query execution returned false with no specific error.");
        echo "ERROR|Query execution returned false with no specific error!";
    }
} catch (Exception $e) {
    // Log the error (you can implement a logger or store it in a database/log file)
    error_log("Error inserting customer: " . $e->getMessage());

    // Send a JSON error response (or handle it based on your application)
    echo "ERROR|Failed to add the customer. Error: " . $e->getMessage();
}