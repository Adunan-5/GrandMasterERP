<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";
include_once __DIR__ . "/../../includes/auth_check.php";

$quotation_to      = filter_var($_POST['quotation-to'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$quotation_cc      = filter_var($_POST['quotation-cc'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$quotation_bcc      = filter_var($_POST['quotation-bcc'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$quotation_subject = filter_var($_POST['quotation-subject'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$quotation_message = $_POST['quotation-message'];
$documentID        = filter_var($_POST['documentID'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

//echo "<pre>";
//print_r($_POST);
//echo "</pre>";
$keyDocument = new ConsultationKeyDocument();
$keyDocument->loadById($documentID);
$customerPassword = generateRandomCustomerDocumentPassword();
$res              = $db->query("UPDATE `consultation_key_documents` SET `customerPassword`=?s, `quotationAcceptedByCustomer`='NEUTRAL' WHERE  `documentId`=?s", $customerPassword, $documentID);

$templateFilePath = __DIR__ . '/../../emailtemplates/consultationquotation.html';
// Check if the file exists
if (file_exists($templateFilePath)) {
    // Read the file contents
    $emailTemplate = file_get_contents($templateFilePath);

    // Optional: Handle errors if the file could not be read
    if ($emailTemplate === false) {
        die("Error reading the email template file.");
    }
} else {
    die("The email template file does not exist.");
}

$emailTemplate = str_replace("{{DOCUMENT_PASSWORD}}", $customerPassword, $emailTemplate);
$emailTemplate = str_replace("{{QUOTATION_DOCUMENT_ID}}", $documentID, $emailTemplate);

$quotation_message = $emailTemplate;

// $sendEmailResult = sendEmailWithAttachment($quotation_to, $quotation_subject, $quotation_message, "", "", $quotation_cc, $quotation_bcc);
// //echo $sendEmailResult;
// if($sendEmailResult === true) {
//     echo "SUCCESS|Email Sent";
// }
// else{
//     echo "FAILED|" . $sendEmailResult;
// }

sendEmailWithAttachment($quotation_to, $quotation_subject, $quotation_message, "", "", $quotation_cc, $quotation_bcc);
echo "SUCCESS|Email Sent";