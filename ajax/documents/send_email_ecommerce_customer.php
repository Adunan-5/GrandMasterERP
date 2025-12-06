<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";


$quotation_to      = filter_var($_POST['quotation-to'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$quotation_subject = filter_var($_POST['quotation-subject'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
//$quotation_message = filter_var($_POST['quotation-message'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$quotation_message = $_POST['quotation-message'];
$documentID        = filter_var($_POST['documentID'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

$keyDocument = new KeyDocument();
$keyDocument->loadById($documentID);
$customerPassword = generateRandomCustomerDocumentPassword();
$res = $db->query("UPDATE `key_documents` SET `customerPassword`=?s, `orderAcceptedByCustomer`='NEUTRAL' WHERE  `documentId`=?s", $customerPassword, $documentID);



$templateFilePath = __DIR__ . '/../../emailtemplates/sparepartorder.html';
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

$customerMessageAppend = "
<br><br>To accept or reject the order, click on the link below. Please note that, the link is password protected. Use the password <strong>$customerPassword</strong> to access your quotation. Thank you. 
<br><br>
https://ggm.com.co/quotation/view/$documentID  <br><br>";

$quotation_message .= $customerMessageAppend;

$emailTemplate = str_replace("{{DOCUMENT_PASSWORD}}", $customerPassword, $emailTemplate);
$emailTemplate = str_replace("{{QUOTATION_DOCUMENT_ID}}", $documentID, $emailTemplate);

$quotation_message = $emailTemplate;


sendEmailWithAttachment($quotation_to, $quotation_subject, $quotation_message, "", "");

echo "SUCCESS|Email Sent";