<?php
function generateSsha512Password($password)
{
    // Generate a random salt of 16 bytes (can be any length you choose)
    $salt = openssl_random_pseudo_bytes(16);

    // Hash the password with the salt using SHA-512
    $hash = hash('sha512', $password . $salt, true);

    // Combine the hash and the salt
    $hashWithSalt = $hash . $salt;

    // Base64 encode the result
    $base64HashWithSalt = base64_encode($hashWithSalt);

    // Prefix with {SSHA512} to indicate the hashing algorithm
    return '{SSHA512}' . $base64HashWithSalt;
}

function verifySsha512Password($providedPassword, $storedHash)
{
    // Remove the {SSHA512} prefix
    if (strpos($storedHash, '{SSHA512}') === 0) {
        $storedHash = substr($storedHash, 9);
    } else {
        return false; // Invalid hash format
    }

    // Decode the base64 encoded stored hash
    $decodedHash = base64_decode($storedHash);

    // Extract the actual hash and salt
    $hashLength = 64; // 512 bits = 64 bytes
    $hash       = substr($decodedHash, 0, $hashLength);
    $salt       = substr($decodedHash, $hashLength);

    // Hash the provided password with the extracted salt
    $hashedPassword = hash('sha512', $providedPassword . $salt, true);

    // Compare the provided password hash with the stored hash
    return hash_equals($hashedPassword, $hash);
}


function encryptString($data)
{
    global $secretkey;
    $cipher = "AES-256-CBC";
    $iv     = substr(hash('sha256', $secretkey), 0, 16); // Generate a 16-byte IV

    $encrypted = openssl_encrypt($data, $cipher, $secretkey, 0, $iv);
    return base64_encode($encrypted); // Encode the result to make it URL-safe
}

function decryptString($encryptedData)
{
    global $secretkey;
    $cipher = "AES-256-CBC";
    $iv     = substr(hash('sha256', $secretkey), 0, 16); // Generate a 16-byte IV

    $decodedData = base64_decode($encryptedData); // Decode the base64 data
    return openssl_decrypt($decodedData, $cipher, $secretkey, 0, $iv);
}

function formatDate($mysqlDate, $includeTime = true)
{
    // Ensure the input is not null or empty
    if (empty($mysqlDate)) {
        return null;
    }

    // Convert MySQL date string to a timestamp
    $timestamp = strtotime($mysqlDate);

    // Format with or without time based on $includeTime
    if ($includeTime) {
        return date("M j, Y, g:i A", $timestamp); // e.g., Aug 17, 2020, 5:48 PM
    } else {
        return date("M j, Y", $timestamp); // e.g., Aug 17, 2020
    }
}

function formatDateShort($mysqlDate, $includeTime = true)
{
    // Ensure the input is not null or empty
    if (empty($mysqlDate)) {
        return null;
    }

    // Convert MySQL date string to a timestamp
    $timestamp = strtotime($mysqlDate);

    // Format with or without time based on $includeTime
    if ($includeTime) {
        return date("M j, Y, g:i A", $timestamp); // e.g., Aug 17, 2020, 5:48 PM
    } else {
        return date("j/m/y", $timestamp); // e.g., Aug 17, 2020
    }
}


function formatDateRelativeTime($timestamp)
{

    $dateTime = new DateTime($timestamp);
    $now      = new DateTime();
    $diff     = $now->getTimestamp() - $dateTime->getTimestamp();

    // Define time intervals in seconds
    $intervals = [
        'year'   => 31536000,
        'month'  => 2592000,
        'week'   => 604800,
        'day'    => 86400,
        'hour'   => 3600,
        'minute' => 60,
        'second' => 1
    ];

    // Determine the correct relative time string
    foreach ($intervals as $key => $value) {
        if ($diff >= $value) {
            $count        = floor($diff / $value);
            $relativeTime = "$count $key" . ($count > 1 ? 's' : '') . " ago";
            break;
        }
    }

    // Default to "Just now" if difference is very small
    if (!isset($relativeTime)) {
        $relativeTime = "Just now";
    }

    // Format time in 12-hour AM/PM format
    $formattedTime = $dateTime->format('h:i A');

    return "$relativeTime ($formattedTime)";
}

function getCountryFromID($countryID)
{
    global $db; // Use global $db
    $result = $db->getRow("SELECT name FROM countries WHERE id = ?i", $countryID);
    return $result ? $result['name'] : null;
}

function getStateFromID($stateID)
{
    global $db; // Use global $db
    $result = $db->getRow("SELECT name FROM states WHERE id = ?i", $stateID);
    return $result ? $result['name'] : null;
}

function getCityFromID($cityID)
{
    global $db; // Use global $db
    $result = $db->getRow("SELECT name FROM cities WHERE id = ?i", $cityID);
    return $result ? $result['name'] : null;
}

function getCustomerCodeFromID($customerID) {
    global $db;
    $res = $db->getRow("SELECT customerCode FROM customers WHERE customerId = ?s", $customerID);
    return "GMM-" . $res['customerCode'];
}

function getFormattedAddress($customer)
{

    $addressLine1 = $customer->addressLine1;
    $addressLine2 = $customer->addressLine2;
    $city         = getCityFromID($customer->cityId);
    $state        = getStateFromID($customer->stateId);
    $country      = getCountryFromID($customer->countryId);
    $postalCode   = $customer->postalCode;

    // Initialize formatted address
    $formattedAddress = [];

    if (!empty($addressLine1)) {
        $formattedAddress[] = $addressLine1;
    }

    // Add addressLine2 only if it's not empty
    if (!empty($addressLine2)) {
        $formattedAddress[] = $addressLine2;
    }

    if (!empty($city)) {
        $formattedAddress[] = $city;
    }

    if (!empty($state)) {
        $formattedAddress[] = $state;
    }

    if (!empty($country)) {
        $formattedAddress[] = $country;
    }

    if (!empty($postalCode)) {
        $formattedAddress[] = $postalCode;
    }

    return implode(', ', $formattedAddress);
}

function getEcommerceCustomerFormattedAddress($customer)
{
    $formattedAddresses = array();
    foreach ($customer->ecommerceAddress as $address) {
        $addressParts = array();

        if (!empty($address['addressLine1'])) {
            $addressParts[] = $address['addressLine1'];
        }

        if (!empty($address['addressLine2'])) {
            $addressParts[] = $address['addressLine2'];
        }

        if (!empty($address['city'])) {
            $addressParts[] = $address['city'];
        }

        if (!empty($address['state'])) {
            $addressParts[] = $address['state'];
        }

        if (!empty($address['country'])) {
            $addressParts[] = $address['country'];
        }

        if (!empty($address['postalCode'])) {
            $addressParts[] = $address['postalCode'];
        }

        $formattedAddresses[] = implode(', ', $addressParts); // Add this line to store each address
    }

    return $formattedAddresses;
}

function getFormattedCustomerAddressByID($customerId)
{
    $customer = new Customer();
    $customer->loadById($customerId);

    return getFormattedAddress($customer);
}


//User Related
function getDisplayNameFromUserID($userID)
{
    global $db;
    $res         = $db->query("SELECT * FROM users WHERE userID = ?s", $userID);
    $returnValue = "";
    while ($row = mysqli_fetch_assoc($res)) {
        $returnValue = $row['firstName'] . " " . $row['lastName'];
    }
    return $returnValue;
}

function getProfilePicFromUserID($userID)
{
    global $db;
    $res = $db->query("SELECT profilePic FROM users WHERE userID = ?s", $userID);

    // Fetching the result from the database
    if ($row = mysqli_fetch_assoc($res)) {
        // Check if profilePic is null or empty
        if (empty($row['profilePic'])) {
            // If profilePic is empty or null, use the default picture
            return PROFILE_PIC_FOLDER . DEFAULT_PROFILE_IMAGE;
        } else {
            // Concatenate the base domain with the actual profilePic value from the database
            return PROFILE_PIC_FOLDER . $row['profilePic'];
        }
    }
}

function getDisplayNameOfCurrentUser()
{
    $authenticatedUser = $_SESSION[LoggedInUser];
    return $authenticatedUser->getFullName();
}

function getUserIDOfCurrentUser()
{
    $authenticatedUser = $_SESSION[LoggedInUser];
    return $authenticatedUser->userID;
}

function getAuthenticatedUser(): ?AuthenticatedUser
{
    global $authenticatedUser;

    // Check if the user is already set in the global variable
    if (isset($authenticatedUser) && $authenticatedUser instanceof AuthenticatedUser) {
        return $authenticatedUser;
    }

    // Check if the session has a saved user object
    if (isset($_SESSION[LoggedInUser])) {
        $authenticatedUser = $_SESSION[LoggedInUser];
        if ($authenticatedUser instanceof AuthenticatedUser) {
            return $authenticatedUser;
        }
    }

    return null;
}

function getUOMFromID($uomID)
{
    global $db;
    $res = $db->query("SELECT * FROM uoms WHERE active = 1 AND uomId = ?s", $uomID);
    return mysqli_fetch_assoc($res);
}

function getUOMNameFromID($uomID)
{
    global $db;
    $res = $db->query("SELECT uomName FROM uoms WHERE active = 1 AND uomId = ?s", $uomID);
    return mysqli_fetch_assoc($res)["uomName"];
}

function getUOMIDByName($uomName)
{
    global $db;
    $res = $db->query("SELECT uomId FROM uoms WHERE active = 1 AND uomName = ?s", $uomName);
    return mysqli_fetch_assoc($res)["uomId"];
}

function getCustomerNameFromID($customerID)
{
    global $db;
    $res = $db->query("SELECT companyName FROM customers WHERE status = 'Active' AND customerId = ?s", $customerID);
    return mysqli_fetch_assoc($res)["companyName"];
}

function getSupplierNameFromID($supplierID)
{
    global $db;
    $res = $db->query("SELECT companyName FROM suppliers WHERE status = 'Active' AND supplierId = ?s", $supplierID);
    return mysqli_fetch_assoc($res)["companyName"];
}

function getCurrencyNameFromID($currencyID)
{
    global $db;
    $res = $db->query("SELECT currencyName FROM currencies WHERE active = 1 and currencyId = ?s", $currencyID);
    return mysqli_fetch_assoc($res)["currencyName"];
}

function getCurrencyFromID($currencyID)
{
    global $db;
    $res = $db->query("SELECT currency FROM currencies WHERE active = 1 and currencyId = ?s", $currencyID);
    return mysqli_fetch_assoc($res)["currency"];
}

function getCurrencyIDFromName($currencyName)
{
    global $db;
    $res = $db->query("SELECT currencyId FROM currencies WHERE active = 1 and currency = ?s", $currencyName);
    return mysqli_fetch_assoc($res)["currencyId"];
}

function getHSCodeForSparepartID($sparepartID)
{
    global $db;
    $res = $db->query("SELECT hsCode FROM spareparts WHERE active = 1 AND sparepartId = ?s", $sparepartID);
    return mysqli_fetch_assoc($res)["hsCode"];
}

function getPaymentMethodNameFromID($paymentMethodID)
{
    global $db;
    $res = $db->query("SELECT methodName FROM payment_methods WHERE active = 1 AND id = ?s", $paymentMethodID);
    return mysqli_fetch_assoc($res)["methodName"];
}

function getPaymentTermsIDByName($paymentTermsName)
{
    global $db;
    $res = $db->query("SELECT termId FROM payment_terms WHERE active = 1 AND termName = ?s", $paymentTermsName);
    return mysqli_fetch_assoc($res)["termId"];
}

function fetchOutstandingBalanceForSupplierFromSupplierID($supplierID):array {
    global $db;
    $res = $db->query("SELECT SUM(totalAmount) as totalDue, SUM(paidAmount) as totalPaid, SUM(totalAmount - paidAmount) as totalBalance FROM accounts_payable 
                        WHERE supplierId = ?s ", $supplierID);
    return mysqli_fetch_assoc($res);

}

function getOrderCountForSupplier($supplierID) {
    global $db;
    $res = $db->query("SELECT COUNT(poId) as orderCount FROM purchase_orders 
                       WHERE supplierId = ?s 
                       AND active = 1
                       AND poStatus IN ('ACCOUNTANT APPROVED', 'PO SENT TO SUPPLIER', 'GOODS RECEIVED')",
        $supplierID);
    $row = mysqli_fetch_assoc($res);
    return $row['orderCount'];
}


/**
 *
 * Document Related
 *
 */
function getQuotationNumberFromDocumentID($documentID)
{
    global $db;
    $quotationNumber = "DRAFT";
    $res             = $db->query("SELECT * FROM key_documents WHERE documentId = ?s", $documentID);
    $row             = mysqli_fetch_assoc($res);
    $quotationNumber = $row['quotationNumberPrefix'] . $row['quotationNumber'];
    if (empty($quotationNumber)) $quotationNumber = PREFIX_QUOTATION . "DRAFT-" . $row['documentId'];
    return $quotationNumber;
}

function getProposalNumberFromProposalID($proposalID)
{
    global $db;
    $proposalNumber = "DRAFT";
    $res             = $db->query("SELECT * FROM consultation_proposals WHERE proposalID = ?s", $proposalID);
    $row             = mysqli_fetch_assoc($res);
    $proposalNumber = $row['proposalNumberPrefix'] . $row['proposalNumber'];
    if (empty($proposalNumber)) $proposalNumber = PREFIX_CONSULTATION_PROPOSAL . "DRAFT-" . $row['proposalID'];
    return $proposalNumber;
}

function getRejectReasonForProposalID($proposalID)
{
    global $db;

    $res          = $db->query("SELECT * FROM consultation_proposal_reject_reason WHERE documentId = ?s ORDER BY rejectedAt DESC LIMIT 1", $proposalID);
    $rejectReason = "";
    while ($row = mysqli_fetch_assoc($res)) {
        $rejectReason = $row['rejectReason'];
    }

    return $rejectReason;

}

function getCustomerRejectReasonForProposalID($proposalID)
{
    global $db;

    $res          = $db->query("SELECT * FROM consultation_proposal_customer_reject_reason WHERE documentId = ?s ORDER BY rejectedAt DESC LIMIT 1", $proposalID);
    $rejectReason = "";
    while ($row = mysqli_fetch_assoc($res)) {
        $rejectReason = $row['rejectReason'];
    }

    return $rejectReason;

}

function getSMANumberFromSMAID($smaID)
{
    global $db;
    $smaNumber = "DRAFT";
    $res             = $db->query("SELECT * FROM consultation_sma WHERE smaID = ?s", $smaID);
    $row             = mysqli_fetch_assoc($res);
    $smaNumber = $row['smaNumberPrefix'] . $row['smaNumber'];
    if (empty($smaNumber)) $smaNumber = PREFIX_CONSULTATION_SMA . "DRAFT-" . $row['smaID'];
    return $smaNumber;
}

function getSMAStatusForSMAID($smaID)
{
    global $db;
    $smaStatus = "DRAFT";
    $res             = $db->query("SELECT * FROM consultation_sma WHERE smaID = ?s", $smaID);
    $row             = mysqli_fetch_assoc($res);
    $smaStatus = $row['status'];
    return $smaStatus;
}

function getSMAIDForProposalID($proposalID){
    global $db;
    $resSMA          = $db->query("SELECT * FROM consultation_sma WHERE associatedProposalID = ?s", $proposalID);
    $associatedSMAID = '';
    while ($rowSMA = mysqli_fetch_assoc($resSMA)) {
        $associatedSMAID = $rowSMA['smaID'];
    }
    return $associatedSMAID;
}

function getQuotationIDForProposalID($proposalID){
    global $db;
    $res          = $db->query("SELECT * FROM consultation_key_documents WHERE associatedProposalID = ?s", $proposalID);
    $associatedProposalID = '';
    while ($row = mysqli_fetch_assoc($res)) {
        $associatedProposalID = $row['documentId'];
    }
    return $associatedProposalID;
}


function getProposalIDForSMAID($smaID){
    global $db;
    $resSMA          = $db->query("SELECT * FROM consultation_sma WHERE smaID = ?s", $smaID);
    $associatedProposalID = '';
    while ($rowSMA = mysqli_fetch_assoc($resSMA)) {
        $associatedProposalID = $rowSMA['associatedProposalID'];
    }
    return $associatedProposalID;
}

function getProposalTitleFromProposalID($proposalID)
{
    global $db;
    $res             = $db->query("SELECT * FROM consultation_proposals WHERE proposalID = ?s", $proposalID);
    $row             = mysqli_fetch_assoc($res);
    $proposalTitle = $row['proposalTitle'];
    if (empty($proposalTitle)) $proposalTitle = "DRAFT-" . $row['proposalID'];
    return $proposalTitle;
}

function getInvoiceNumberFromInvoiceID($invoiceID)
{
    global $db;
    $invoiceNumber = "DRAFT";
    $res             = $db->query("SELECT * FROM invoice_documents WHERE invoiceId = ?s", $invoiceID);
    $row             = mysqli_fetch_assoc($res);
    $invoiceNumber = $row['invoiceNumberPrefix'] . $row['invoiceNumber'];
    if (empty($invoiceNumber)) $invoiceNumber = PREFIX_INVOICE . "DRAFT-" . $row['invoiceId'];
    return $invoiceNumber;
}

function getSDNNumberFromInvoiceID($invoiceID)
{
    global $db;
    $sdnNumber = "DRAFT";
    $res             = $db->query("SELECT * FROM consultation_service_delivery_notes WHERE invoiceId = ?s", $invoiceID);
    $row             = mysqli_fetch_assoc($res);
    $sdnNumber = $row['sdnNumberPrefix'] . $row['sdnNumber'];
    if (empty($sdnNumber)) $sdnNumber = PREFIX_SDN . "DRAFT-" . $row['sdnId'];
    return $sdnNumber;
}

function getGDNNumberFromInvoiceID($invoiceID)
{
    global $db;
    $invoiceNumber = "DRAFT";
    $res             = $db->query("SELECT * FROM invoice_documents WHERE invoiceId = ?s", $invoiceID);
    $row             = mysqli_fetch_assoc($res);
    $invoiceNumber = $row['invoiceNumberPrefix'] . $row['invoiceNumber'];
    if (empty($invoiceNumber)) $invoiceNumber = PREFIX_INVOICE . "DRAFT-" . $row['invoiceId'];
    return $invoiceNumber;
}

function getRFPNumberFromDocumentID($documentID)
{
    global $db;
    $rfpNumber = "DRAFT";

    $res = $db->query("SELECT * FROM rfp_documents WHERE rfpId = ?s", $documentID);
    $row = mysqli_fetch_assoc($res);

    if ($row) { // Check if a row exists
        $rfpNumber = $row['rfpNumberPrefix'] . $row['rfpNumber'];
        if (empty($rfpNumber)) {
            $rfpNumber = PREFIX_RFP . "DRAFT-" . $row['rfpId'];
        }
    } else {
        // Handle case where no record is found (optional)
        $rfpNumber = PREFIX_RFP . "DRAFT-" . $documentID; // Or return a default value/error
    }

    return $rfpNumber;
}

function getRFPNumberFromConsultationDocumentID($documentID)
{
    global $db;
    $rfpNumber = "DRAFT";

    $res = $db->query("SELECT * FROM consultation_rfp_documents WHERE rfpId = ?s", $documentID);
    $row = mysqli_fetch_assoc($res);

    if ($row) { // Check if a row exists
        $rfpNumber = $row['rfpNumberPrefix'] . $row['rfpNumber'];
        if (empty($rfpNumber)) {
            $rfpNumber = PREFIX_RFP_CONSULTATION . "DRAFT-" . $row['rfpId'];
        }
    } else {
        // Handle case where no record is found (optional)
        $rfpNumber = PREFIX_RFP_CONSULTATION . "DRAFT-" . $documentID; // Or return a default value/error
    }

    return $rfpNumber;
}

function getTransferNumberFromDocumentID($documentID)
{
    global $db;
    $transferNumber = "DRAFT";

    $res = $db->query("SELECT * FROM transfer_documents WHERE transferId = ?s", $documentID);
    $row = mysqli_fetch_assoc($res);

    if ($row) { // Check if a row exists
        $transferNumber = $row['transferNumberPrefix'] . $row['transferNumber'];
        if (empty($transferNumber)) {
            $transferNumber = PREFIX_TRANSFER . "DRAFT-" . $row['transferId'];
        }
    } else {
        // Handle case where no record is found (optional)
        $transferNumber = PREFIX_TRANSFER . "DRAFT-" . $documentID; // Or return a default value/error
    }

    return $transferNumber;
}

function getShipmentNumberFromDocumentID($documentID, $transactionType)
{
    global $db;
    $shipmentNumber = "-";
    $res      = $db->query("SELECT * FROM order_management_documents WHERE documentId = ?s AND transactionType = ?s", $documentID, $transactionType);
    $row      = mysqli_fetch_assoc($res);
    $shipmentNumber = $row['shipmentNumberPrefix'] . $row['shipmentNumber'];
    if (empty($shipmentNumber)) $shipmentNumber = '-';
    return $shipmentNumber;
}

function getShipmentNumberFromOrderAndItemID($orderID, $itemID)
{
    global $db;
    $shipmentNumber = "-";
    $res      = $db->query("SELECT * FROM pick_and_pack_items WHERE orderManagementId = ?s AND itemId = ?s", $orderID, $itemID);
    $row      = mysqli_fetch_assoc($res);
    $shipmentNumber = $row['shipmentNumberPrefix'] . $row['shipmentNumber'];
    if (empty($shipmentNumber)) $shipmentNumber = '-';
    return $shipmentNumber;
}

function getShipmentNumberFromOrderAndIPickAndPackID($orderID, $pickAndPackID)
{
    global $db;
    $shipmentNumber = "-";
    $res      = $db->query("SELECT * FROM pick_and_pack_line_items WHERE orderManagementId = ?s AND pickAndPackLineItemId = ?s", $orderID, $pickAndPackID);
    $row      = mysqli_fetch_assoc($res);
    $shipmentNumber = $row['shipmentNumberPrefix'] . $row['shipmentNumber'];
    if (empty($shipmentNumber)) $shipmentNumber = '-';
    return $shipmentNumber;
}

function getGDNNumberFromGDNID($gdnID)
{
    global $db;
    $gdnNumber = "-";

    $res = $db->query("SELECT * FROM pick_and_pack_documents WHERE gdnId = ?s", $gdnID);
    $row = mysqli_fetch_assoc($res);

    if ($row) {
        $gdnNumber = $row['gdnNumberPrefix'] . $row['gdnNumber'];
        if (empty($gdnNumber)) {
            $gdnNumber = '-';
        }
    } else {
        $gdnNumber = '-';
    }

    return $gdnNumber;
}


function getWarehouseNameFromID($warehouseID)
{
    global $db;
    $res = $db->query("SELECT warehouseName FROM warehouses WHERE warehouseId = ?s", $warehouseID);
    $row = mysqli_fetch_assoc($res);
    return $row ? $row['warehouseName'] : '';
}

function getWarehouseCodeFromID($warehouseID)
{
    global $db;
    $res = $db->query("SELECT warehouseCode FROM warehouses WHERE warehouseId = ?s", $warehouseID);
    $row = mysqli_fetch_assoc($res);
    return $row ? $row['warehouseCode'] : '';
}

function getPONumberFromDocumentID($documentID)
{
    global $db;
    $poNumber = "DRAFT";
    $res      = $db->query("SELECT * FROM purchase_orders WHERE poId = ?s", $documentID);
    $row      = mysqli_fetch_assoc($res);
    $poNumber = $row['poNumberPrefix'] . $row['poNumber'];
    if (empty($poNumber)) $poNumber = PREFIX_PO . "DRAFT-" . $row['poId'];
    return $poNumber;
}

function getPONumberFromConsultationDocumentID($documentID)
{
    global $db;
    $poNumber = "DRAFT";
    $res      = $db->query("SELECT * FROM consultation_purchase_orders WHERE poId = ?s", $documentID);
    $row      = mysqli_fetch_assoc($res);
    $poNumber = $row['poNumberPrefix'] . $row['poNumber'];
    if (empty($poNumber)) $poNumber = PREFIX_PO_CONSULTATION . "DRAFT-" . $row['poId'];
    return $poNumber;
}

function getSupplierInvoiceFromPoID($poID)
{
    global $db;
    $result = [
        'supplierInvoiceAttachment' => '',
        'supplierInvoiceNo' => ''
    ];
    $res = $db->query("SELECT * FROM purchase_orders WHERE poId = ?s", $poID);
    $row = mysqli_fetch_assoc($res);
    $result['supplierInvoiceAttachment'] = $row['supplierInvoiceAttachment'] ?? '';
    $result['supplierInvoiceNo'] = $row['supplierInvoiceNo'] ?? '';
    return $result;
}

function getGDNNumberFromDocumentID($documentID)
{
    global $db;
    $gdnNumber = "DRAFT";
    $res      = $db->query("SELECT * FROM pick_and_pack_documents WHERE gdnId = ?s", $documentID);
    $row      = mysqli_fetch_assoc($res);
    $gdnNumber = $row['gdnNumberPrefix'] . $row['gdnNumber'];
    if (empty($gdnNumber)) $gdnNumber = PREFIX_GDN . "DRAFT-" . $row['gdnId'];
    return $gdnNumber;
}

function getQuotationNumberFromConsultationDocumentID($documentID)
{
    global $db;
    $quotationNumber = "DRAFT";
    $res             = $db->query("SELECT * FROM consultation_key_documents WHERE documentId = ?s", $documentID);
    $row             = mysqli_fetch_assoc($res);
    $quotationNumber = $row['quotationNumberPrefix'] . $row['quotationNumber'];
    if (empty($quotationNumber)) $quotationNumber = PREFIX_CONSULTATION_QUOTATION . "DRAFT-" . $row['documentId'];
    return $quotationNumber;
}

function getConsultationQuotationTitleFromDocumentID($documentID)
{
    global $db;
    $res             = $db->query("SELECT * FROM consultation_key_documents WHERE documentId = ?s", $documentID);
    $row             = mysqli_fetch_assoc($res);
    $quotationTitle = $row['quotationTitle'];
    if (empty($quotationTitle)) $quotationTitle = getQuotationNumberFromConsultationDocumentID($documentID);
    return $quotationTitle;
}

function getProformaInvoiceNumberFromConsultationDocumentID($documentID)
{
    global $db;
    $proformaInvoiceNumber = "DRAFT";
    $res             = $db->query("SELECT * FROM consultation_key_documents WHERE documentId = ?s", $documentID);
    $row             = mysqli_fetch_assoc($res);
    $proformaInvoiceNumber = $row['proformaInvoiceNumberPrefix'] . $row['proformaInvoiceNumber'];
    if (empty($proformaInvoiceNumber)) $proformaInvoiceNumber = PREFIX_CONSULTATION_PROFORMA_INVOICE . "DRAFT";
    return $proformaInvoiceNumber;
}

function getSalesOrderNumberFromDocumentID($documentID)
{
    global $db;
    $quotationNumber = "DRAFT";
    $res             = $db->query("SELECT * FROM key_documents WHERE documentId = ?s", $documentID);
    $row             = mysqli_fetch_assoc($res);

    $quotationNumber = $row['saleOrderNumberPrefix'] . $row['saleOrderNumber'];
    return $quotationNumber;
}

function getOrderManagementIDFromSalesOrderID($documentID)
{
    global $db;
    $orderManagementID = "";
    $row = $db->getRow("SELECT orderManagementId 
                        FROM order_management_documents 
                        WHERE documentId = ?s 
                        AND transactionType = ?s",
        $documentID, 'SALESORDER');

    if ($row && isset($row['orderManagementId'])) {
        $orderManagementID = $row['orderManagementId'];
    }

    return $orderManagementID;
}

function getSalesOrderIDFromInvoiceID($invoiceID)
{
    global $db;
    $saleOrderID = "";
    $row = $db->getRow("SELECT saleOrderId 
                        FROM invoice_documents 
                        WHERE invoiceId = ?s",
        $invoiceID);

    if ($row && isset($row['saleOrderId'])) {
        $saleOrderID = $row['saleOrderId'];
    }

    return $saleOrderID;
}

function getSalesOrderNumberFromRFPRefDocID($rfpRefDocID)
{
    global $db;
    $res             = $db->query("SELECT * FROM key_documents WHERE documentId = ?s", $rfpRefDocID);
    $row             = mysqli_fetch_assoc($res);

    $salesOrderNumber = $row['saleOrderNumberPrefix'] . $row['saleOrderNumber'];
    return $salesOrderNumber;
}


function getSalesOrderNumberFromConsultationDocumentID($documentID)
{
    global $db;
    $quotationNumber = "DRAFT";
    $res             = $db->query("SELECT * FROM consultation_key_documents WHERE documentId = ?s", $documentID);
    $row             = mysqli_fetch_assoc($res);

    $quotationNumber = $row['saleOrderNumberPrefix'] . $row['saleOrderNumber'];
    return $quotationNumber;
}

function getNextNewQuotationNumber()
{
    global $db;
    $quotationNumber = "0";
    $res             = $db->query("SELECT COALESCE(MAX(CAST(quotationNumber AS UNSIGNED)), 0) + 1 AS nextQuotationNumber
                        FROM key_documents
                        WHERE quotationNumber IS NOT NULL;");
    $row             = mysqli_fetch_assoc($res);
    $quotationNumber = $row['nextQuotationNumber'];
    return $quotationNumber;
}

function getNextNewProposalNumber()
{
    global $db;
    $proposalNumber = "0";
    $res             = $db->query("SELECT COALESCE(MAX(CAST(proposalNumber AS UNSIGNED)), 0) + 1 AS nextProposalNumber
                        FROM consultation_proposals
                        WHERE proposalNumber IS NOT NULL;");
    $row             = mysqli_fetch_assoc($res);
    $proposalNumber = $row['nextProposalNumber'];
    return $proposalNumber;
}

function getNextNewSMANumber()
{
    global $db;
    $smaNumber = "0";
    $res             = $db->query("SELECT COALESCE(MAX(CAST(smaNumber AS UNSIGNED)), 0) + 1 AS nextSMANumber
                        FROM consultation_sma
                        WHERE smaNumber IS NOT NULL;");
    $row             = mysqli_fetch_assoc($res);
    $smaNumber = $row['nextSMANumber'];
    return $smaNumber;
}

function getNextNewRFPNumber()
{
    global $db;
    $rfpNumber = "0";
    $res       = $db->query("SELECT COALESCE(MAX(CAST(rfpNumber  AS UNSIGNED)), 0) + 1 AS nextrfpNumber
                        FROM rfp_documents
                        WHERE rfpNumber IS NOT NULL;");
    $row       = mysqli_fetch_assoc($res);
    $rfpNumber = $row['nextrfpNumber'];
    return $rfpNumber;
}

function getNextNewConsultationRFPNumber()
{
    global $db;
    $rfpNumber = "0";
    $res       = $db->query("SELECT COALESCE(MAX(CAST(rfpNumber  AS UNSIGNED)), 0) + 1 AS nextrfpNumber
                        FROM consultation_rfp_documents
                        WHERE rfpNumber IS NOT NULL;");
    $row       = mysqli_fetch_assoc($res);
    $rfpNumber = $row['nextrfpNumber'];
    return $rfpNumber;
}

function getNextNewPONumber()
{
    global $db;
    $poNumber = "0";
    $res      = $db->query("SELECT COALESCE(MAX(CAST(poNumber  AS UNSIGNED)), 0) + 1 AS nextPONumber
                        FROM purchase_orders
                        WHERE poNumber IS NOT NULL;");
    $row      = mysqli_fetch_assoc($res);
    $poNumber = $row['nextPONumber'];
    return $poNumber;
}

function getNextNewConsultationPONumber()
{
    global $db;
    $poNumber = "0";
    $res      = $db->query("SELECT COALESCE(MAX(CAST(poNumber  AS UNSIGNED)), 0) + 1 AS nextPONumber
                        FROM consultation_purchase_orders
                        WHERE poNumber IS NOT NULL;");
    $row      = mysqli_fetch_assoc($res);
    $poNumber = $row['nextPONumber'];
    return $poNumber;
}

function getNextNewTransferNumber()
{
    global $db;
    $transferNumber = "0";
    $res       = $db->query("SELECT COALESCE(MAX(CAST(transferNumber  AS UNSIGNED)), 0) + 1 AS nextTransferNumber
                        FROM transfer_documents
                        WHERE transferNumber IS NOT NULL;");
    $row       = mysqli_fetch_assoc($res);
    $transferNumber = $row['nextTransferNumber'];
    return $transferNumber;
}

function getNextNewShipmentNumber() {
    global $db;
    $shipmentNumber = "0";
    $res = $db->query("SELECT COALESCE(MAX(CAST(shipmentNumber AS UNSIGNED)), 0) + 1 AS nextShipmentNumber
                       FROM pick_and_pack_line_items
                       WHERE shipmentNumber IS NOT NULL;");
    $row = mysqli_fetch_assoc($res);
    $shipmentNumber = $row['nextShipmentNumber'];
    return $shipmentNumber;
}

function getNextNewGDNNumber()
{
    global $db;
    $gdnNumber = "0";
    $res       = $db->query("SELECT COALESCE(MAX(CAST(gdnNumber  AS UNSIGNED)), 0) + 1 AS nextgdnNumber
                        FROM pick_and_pack_documents
                        WHERE gdnNumber IS NOT NULL;");
    $row       = mysqli_fetch_assoc($res);
    $gdnNumber = $row['nextgdnNumber'];
    return $gdnNumber;
}

function getNextNewInvoiceNumber()
{
    global $db;
    $invoiceNumber = "0";
    $res       = $db->query("SELECT COALESCE(MAX(CAST(invoiceNumber  AS UNSIGNED)), 0) + 1 AS nextInvoiceNumber
                        FROM invoice_documents
                        WHERE invoiceNumber IS NOT NULL;");
    $row       = mysqli_fetch_assoc($res);
    $invoiceNumber = $row['nextInvoiceNumber'];
    return $invoiceNumber;
}

function getNextNewSDNNumber()
{
    global $db;
    $sdnNumber = "0";
    $res       = $db->query("SELECT COALESCE(MAX(CAST(sdnNumber  AS UNSIGNED)), 0) + 1 AS nextSDNNumber
                        FROM consultation_service_delivery_notes
                        WHERE sdnNumber IS NOT NULL;");
    $row       = mysqli_fetch_assoc($res);
    $sdnNumber = $row['nextSDNNumber'];
    return $sdnNumber;
}

function getTotalAmountByConsultationInvoiceID($invoiceID): array
{
    global $db;

    // Initialize totals with string representations of decimals
    $totalAmountBeforeVAT = '0.00';
    $totalDiscountAmount  = '0.00';
    $totalVATAmount       = '0.00';
    $totalAmountAfterVAT  = '0.00';

    $res = $db->query("SELECT * FROM consultation_invoice_line_items WHERE invoiceId = ?s", $invoiceID);

    while ($row = mysqli_fetch_assoc($res)) {
        $qty                = $row['quantity'];
        $unitPrice          = $row['unitPrice'];
        $vatPercentage      = $row['vatPercentage'];
        $discountPercentage = $row['discountPercentage'];

        // Calculate base subtotal
        $baseSubtotal = bcmul($qty, $unitPrice, 6);

        // Apply discount
        $discountAmount     = bcmul($baseSubtotal, bcdiv($discountPercentage, '100', 6), 6);
        $discountedSubtotal = bcsub($baseSubtotal, $discountAmount, 6);

        // Calculate VAT amount (based on the discounted subtotal)
        $vatAmount = bcmul($discountedSubtotal, bcdiv($vatPercentage, '100', 6), 6);

        // Add totals
        $totalAmountBeforeVAT = bcadd($totalAmountBeforeVAT, $discountedSubtotal, 6);
        $totalDiscountAmount  = bcadd($totalDiscountAmount, $discountAmount, 6);
        $totalVATAmount       = bcadd($totalVATAmount, $vatAmount, 6);
        $totalAmountAfterVAT  = bcadd($totalAmountAfterVAT, bcadd($discountedSubtotal, $vatAmount, 6), 6);

    }

    // Format totals for output (to 2 decimal places)
    $totalAmountBeforeVAT = number_format($totalAmountBeforeVAT, 2, '.', '');
    $totalDiscountAmount  = number_format($totalDiscountAmount, 2, '.', '');
    $totalVATAmount       = number_format($totalVATAmount, 2, '.', '');
    $totalAmountAfterVAT  = number_format($totalAmountAfterVAT, 2, '.', '');

    $returnArray                         = array();
    $returnArray['totalAmountBeforeVAT'] = $totalAmountBeforeVAT;
    $returnArray['totalDiscountAmount']  = $totalDiscountAmount;
    $returnArray['totalVATAmount']       = $totalVATAmount;
    $returnArray['totalAmountAfterVAT']  = $totalAmountAfterVAT;

    return $returnArray;
}

function getNextNewConsultationQuotationNumber()
{
    global $db;
    $quotationNumber = "0";
    $res             = $db->query("SELECT COALESCE(MAX(CAST(quotationNumber AS UNSIGNED)), 0) + 1 AS nextQuotationNumber
                        FROM consultation_key_documents
                        WHERE quotationNumber IS NOT NULL;");
    $row             = mysqli_fetch_assoc($res);
    $quotationNumber = $row['nextQuotationNumber'];
    return $quotationNumber;
}

//Sales Order New Number
function getNextNewSalesOrderNumber()
{
    global $db;
    $soNumber = "0";
    $res      = $db->query("SELECT COALESCE(MAX(CAST(saleOrderNumber AS UNSIGNED)), 0) + 1 AS nextSalesOrderNumber
                        FROM key_documents
                        WHERE saleOrderNumber IS NOT NULL;");
    $row      = mysqli_fetch_assoc($res);
    $soNumber = $row['nextSalesOrderNumber'];
    return $soNumber;
}

function getNextNewConsultationSalesOrderNumber()
{
    global $db;
    $soNumber = "0";
    $res      = $db->query("SELECT COALESCE(MAX(CAST(saleOrderNumber AS UNSIGNED)), 0) + 1 AS nextSalesOrderNumber
                        FROM consultation_key_documents
                        WHERE saleOrderNumber IS NOT NULL;");
    $row      = mysqli_fetch_assoc($res);
    $soNumber = $row['nextSalesOrderNumber'];
    return $soNumber;
}

function getNextNewConsultationProformaInvoiceNumber()
{
    global $db;
    $proformaInvoiceNumber = "0";
    $res      = $db->query("SELECT COALESCE(MAX(CAST(proformaInvoiceNumber AS UNSIGNED)), 0) + 1 AS nextProformaInvoiceNumber
                        FROM consultation_key_documents
                        WHERE proformaInvoiceNumber IS NOT NULL;");
    $row      = mysqli_fetch_assoc($res);
    $proformaInvoiceNumber = $row['nextProformaInvoiceNumber'];
    return $proformaInvoiceNumber;
}

function getNextNewConsultationProjectNumber()
{
    global $db;
    $projectNumber = "0";
    $res      = $db->query("SELECT COALESCE(MAX(CAST(projectNumber AS UNSIGNED)), 0) + 1 AS nextProjectNumber
                        FROM consultation_projects
                        WHERE projectNumber IS NOT NULL;");
    $row      = mysqli_fetch_assoc($res);
    $projectNumber = $row['nextProjectNumber'];
    return $projectNumber;
}

function getNextNewConsultationProjectTaskNumber()
{
    global $db;
    $taskNumber = "0";
    $res      = $db->query("SELECT COALESCE(MAX(CAST(taskNumber AS UNSIGNED)), 0) + 1 AS nextTaskNumber
                        FROM consultation_project_tasks
                        WHERE taskNumber IS NOT NULL;");
    $row      = mysqli_fetch_assoc($res);
    $taskNumber = $row['nextTaskNumber'];
    return $taskNumber;
}

function getProjectNumberFromProjectID($projectID)
{
    global $db;
    $projectNumber = "DRAFT";
    $res             = $db->query("SELECT * FROM consultation_projects WHERE projectId = ?s", $projectID);
    $row             = mysqli_fetch_assoc($res);
    $projectNumber = $row['projectNumberPrefix'] . $row['projectNumber'];
    if (empty($projectNumber)) $projectNumber = PREFIX_CONSULTATION_PROJECT . "DRAFT-" . $row['projectId'];
    return $projectNumber;
}

function getProjectNameFromProjectID($projectID)
{
    global $db;
    $projectName = "";
    $res             = $db->query("SELECT * FROM consultation_projects WHERE projectId = ?s", $projectID);
    $row             = mysqli_fetch_assoc($res);
    $projectName = $row['projectTitle'];
    return $projectName;
}

function getProjectAssignees($projectId) {
    global $db;
    
    $query = "SELECT u.userId, u.firstName, u.lastName 
              FROM consultation_project_assignees pa
              JOIN users u ON pa.userId = u.userId
              WHERE pa.projectId = ?i";
    
    $assignees = $db->getAll($query, $projectId);
    
    return $assignees ?: [];
}

function getTaskNumberFromTaskID($taskId)
{
    global $db;
    $taskNumber = "DRAFT";
    $res             = $db->query("SELECT * FROM consultation_project_tasks WHERE taskId = ?s", $taskId);
    $row             = mysqli_fetch_assoc($res);
    $taskNumber = $row['taskNumberPrefix'] . $row['taskNumber'];
    if (empty($taskNumber)) $taskNumber = PREFIX_CONSULTATION_PROJECT_TASK . "DRAFT-" . $row['taskId'];
    return $taskNumber;
}

function getTotalAmountByDocumentID($documentID): array
{
    global $db;

    // Initialize totals with string representations of decimals
    $totalAmountBeforeVAT = '0.00';
    $totalDiscountAmount  = '0.00';
    $totalVATAmount       = '0.00';
    $totalShippingAmount   = '0.00';
    $totalAmountAfterVAT  = '0.00';
    $totalAmountAfterVATAndShipping = '0.00';

    $res = $db->query("SELECT * FROM line_items WHERE documentId = ?s", $documentID);

    while ($row = mysqli_fetch_assoc($res)) {
        $qty                = $row['quantity'];
        $uomId              = $row['UOM'];
        $uomRatio           = getUOMFromID($uomId)['ratio'];
        $unitPrice          = $row['unitPrice'];
        $vatPercentage      = $row['vatPercentage'];
        $discountPercentage = $row['discountPercentage'];
        $shippingSubTotal   = $row['shippingSubTotal'];

        // Calculate base subtotal
        $baseSubtotal = bcmul(bcmul($qty, $uomRatio, 6), $unitPrice, 6);

        // Apply discount
        $discountAmount     = bcmul($baseSubtotal, bcdiv($discountPercentage, '100', 6), 6);
        $discountedSubtotal = bcsub($baseSubtotal, $discountAmount, 6);

        // Calculate VAT amount (based on the discounted subtotal)
        $vatAmount = bcmul($discountedSubtotal, bcdiv($vatPercentage, '100', 6), 6);

        // Add totals
        $totalAmountBeforeVAT = bcadd($totalAmountBeforeVAT, $discountedSubtotal, 6);
        $totalDiscountAmount  = bcadd($totalDiscountAmount, $discountAmount, 6);
        $totalVATAmount       = bcadd($totalVATAmount, $vatAmount, 6);
        $totalShippingAmount  = bcadd($totalShippingAmount, $shippingSubTotal, 6);
        $totalAmountAfterVAT  = bcadd($totalAmountAfterVAT, bcadd($discountedSubtotal, $vatAmount, 6), 6);

    }

    $totalAmountAfterVATAndShipping = bcadd($totalAmountAfterVAT, $totalShippingAmount, 6);

    // Format totals for output (to 2 decimal places)
    $totalAmountBeforeVAT = number_format($totalAmountBeforeVAT, 2, '.', '');
    $totalDiscountAmount  = number_format($totalDiscountAmount, 2, '.', '');
    $totalVATAmount       = number_format($totalVATAmount, 2, '.', '');
    $totalShippingAmount = number_format($totalShippingAmount, 2, '.', '');
    $totalAmountAfterVAT  = number_format($totalAmountAfterVAT, 2, '.', '');
    $totalAmountAfterVATAndShipping = number_format($totalAmountAfterVATAndShipping, 2, '.', '');

    $returnArray                         = array();
    $returnArray['totalAmountBeforeVAT'] = $totalAmountBeforeVAT;
    $returnArray['totalDiscountAmount']  = $totalDiscountAmount;
    $returnArray['totalVATAmount']       = $totalVATAmount;
    $returnArray['totalShippingAmount']  = $totalShippingAmount;
    $returnArray['totalAmountAfterVAT']  = $totalAmountAfterVAT;
    $returnArray['totalAmountAfterVATAndShipping']  = $totalAmountAfterVATAndShipping;

    return $returnArray;
}

function getTotalAmountByInvoiceID($invoiceID): array
{
    global $db;

    // Initialize totals with string representations of decimals
    $totalAmountBeforeVAT = '0.00';
    $totalDiscountAmount  = '0.00';
    $totalVATAmount       = '0.00';
    $totalAmountAfterVAT  = '0.00';

    $res = $db->query("SELECT * FROM invoice_line_items WHERE invoiceId = ?s", $invoiceID);

    while ($row = mysqli_fetch_assoc($res)) {
        $qty                = $row['invoicedQuantity'];
        $uomId              = $row['UOM'];
        $uomRatio           = getUOMFromID($uomId)['ratio'];
        $unitPrice          = $row['unitPrice'];
        $vatPercentage      = $row['vatPercentage'];
        $discountPercentage = $row['discountPercentage'];

        // Calculate base subtotal
        $baseSubtotal = bcmul(bcmul($qty, $uomRatio, 6), $unitPrice, 6);

        // Apply discount
        $discountAmount     = bcmul($baseSubtotal, bcdiv($discountPercentage, '100', 6), 6);
        $discountedSubtotal = bcsub($baseSubtotal, $discountAmount, 6);

        // Calculate VAT amount (based on the discounted subtotal)
        $vatAmount = bcmul($discountedSubtotal, bcdiv($vatPercentage, '100', 6), 6);

        // Add totals
        $totalAmountBeforeVAT = bcadd($totalAmountBeforeVAT, $discountedSubtotal, 6);
        $totalDiscountAmount  = bcadd($totalDiscountAmount, $discountAmount, 6);
        $totalVATAmount       = bcadd($totalVATAmount, $vatAmount, 6);
        $totalAmountAfterVAT  = bcadd($totalAmountAfterVAT, bcadd($discountedSubtotal, $vatAmount, 6), 6);

    }

    // Format totals for output (to 2 decimal places)
    $totalAmountBeforeVAT = number_format($totalAmountBeforeVAT, 2, '.', '');
    $totalDiscountAmount  = number_format($totalDiscountAmount, 2, '.', '');
    $totalVATAmount       = number_format($totalVATAmount, 2, '.', '');
    $totalAmountAfterVAT  = number_format($totalAmountAfterVAT, 2, '.', '');

    $returnArray                         = array();
    $returnArray['totalAmountBeforeVAT'] = $totalAmountBeforeVAT;
    $returnArray['totalDiscountAmount']  = $totalDiscountAmount;
    $returnArray['totalVATAmount']       = $totalVATAmount;
    $returnArray['totalAmountAfterVAT']  = $totalAmountAfterVAT;

    return $returnArray;
}

function getTotalAmountForAllInvoices(): array {
    global $db;
    $totalAmountBeforeVAT = '0.00';
    $totalDiscountAmount  = '0.00';
    $totalVATAmount       = '0.00';
    $totalAmountAfterVAT  = '0.00';

    // Fetch all invoices
    $invoices = $db->getAll("SELECT invoiceId, companyId FROM invoice_documents");

    foreach ($invoices as $invoice) {
        $invoiceID  = $invoice['invoiceId'];
        $companyId  = $invoice['companyId'];

        // Decide which function to use based on company
        if ($companyId == 1) {
            $invoiceTotals = getTotalAmountByInvoiceID($invoiceID);
        } else {
            $invoiceTotals = getTotalAmountByConsultationInvoiceID($invoiceID);
        }

        // Accumulate
        $totalAmountBeforeVAT = bcadd($totalAmountBeforeVAT, $invoiceTotals['totalAmountBeforeVAT'], 6);
        $totalDiscountAmount  = bcadd($totalDiscountAmount, $invoiceTotals['totalDiscountAmount'], 6);
        $totalVATAmount       = bcadd($totalVATAmount, $invoiceTotals['totalVATAmount'], 6);
        $totalAmountAfterVAT  = bcadd($totalAmountAfterVAT, $invoiceTotals['totalAmountAfterVAT'], 6);
    }

    // Final formatted output
    return [
        'totalAmountBeforeVAT' => number_format($totalAmountBeforeVAT, 2, '.', ''),
        'totalDiscountAmount'  => number_format($totalDiscountAmount, 2, '.', ''),
        'totalVATAmount'       => number_format($totalVATAmount, 2, '.', ''),
        'totalAmountAfterVAT'  => number_format($totalAmountAfterVAT, 2, '.', '')
    ];
}

function getAccountBalance($accountId, $companyId) {
    global $db;

    $balance = '0.00';

    // Validate inputs
    if (!is_int($accountId) || $accountId <= 0 || !is_int($companyId) || $companyId <= 0) {
        return $balance;
    }

    // Get account type to determine balance calculation
    $accountType = $db->getOne(
        "SELECT ct.name 
         FROM chart_of_accounts coa 
         JOIN coa_types ct ON coa.coaTypeId = ct.id 
         WHERE coa.id = ?i",
        $accountId
    );

    if ($accountType === null) {
        return $balance; // Account not found or no transactions
    }

    // Determine balance calculation based on account type
    $balanceExpression = 'SUM(jel.debit - jel.credit)';
    $reverseTypes = ['Liability', 'Equity', 'Revenue'];
    if (in_array($accountType, $reverseTypes)) {
        $balanceExpression = 'SUM(jel.credit - jel.debit)';
    }

    // Query to get the balance
    $balance = $db->getOne(
        "SELECT COALESCE($balanceExpression, 0.00) AS balance
         FROM journal_entry_lines jel
         JOIN journal_entries je ON je.id = jel.journalEntryId
         WHERE jel.accountId = ?i
           AND je.companyId = ?i",
        $accountId, $companyId
    );

    // Format balance to 2 decimal places
    $balance = number_format($balance, 2, '.', '');
    $accountName = getAccountNameByID($accountId);

    return $balance . $accountName;
}

function getTotalInvoices() {
    global $db;
    $res = $db->query("SELECT COUNT(*) as invoiceCount FROM invoice_documents");
    $row = mysqli_fetch_assoc($res);
    return $row['invoiceCount'];

}
function getTotalApprovedInvoices() {
    global $db;
    $status = INVOICE_STATUS_ACCOUNTANT_APPROVED;
    $res = $db->query("SELECT COUNT(*) as approvedInvoiceCount FROM invoice_documents WHERE invoiceStatus = ?s", $status);
    $row = mysqli_fetch_assoc($res);
    return $row['approvedInvoiceCount'];
}

function getTotalAmountByPOID($poID)
{
    global $db;

    // Initialize totals with string representations of decimals
    $totalAmount = '0.00';

    $res = $db->query("SELECT * FROM po_line_items WHERE poId = ?s", $poID);

    while ($row = mysqli_fetch_assoc($res)) {
        $qty       = $row['quantity'];
        $uomId     = $row['UOM'];
        $uomRatio  = getUOMFromID($uomId)['ratio'];
        $costPrice = $row['costPrice'];

        // Calculate base subtotal
        $baseSubtotal = bcmul(bcmul($qty, $uomRatio, 6), $costPrice, 6);

        // Add totals
        $totalAmount += $baseSubtotal;

    }

    // Format totals for output (to 2 decimal places)
    $totalAmount = number_format($totalAmount, 2, '.', '');

    return $totalAmount;
}

function getTotalAmountWithShippingByPOID($poID)
{
    global $db;
    $baseSubTotal = getTotalAmountByPOID($poID);
    $res = $db->query("SELECT * FROM purchase_orders where poId = ?s", $poID);
    $subTotal = '0.00';
    $vatAmount = '0.00';
    while ($row = mysqli_fetch_assoc($res)) {
        $subTotal = !empty($row['subTotal']) ? $row['subTotal'] : '0.00';
        $vatAmount = !empty($row['vatAmount']) ? $row['vatAmount'] : '0.00';
    }
    $totalAmount = bcadd($baseSubTotal, $subTotal, 2);
    $totalAmount = bcadd($totalAmount, $vatAmount, 2);
    $totalAmount = number_format($totalAmount, 2, '.', '');
    return $totalAmount;
}

function getTotalAmountByConsultationPOID($poID)
{
    global $db;

    // Initialize totals with string representations of decimals
    $totalAmount = '0.00';

    $res = $db->query("SELECT * FROM consultation_po_line_items WHERE poId = ?s", $poID);

    while ($row = mysqli_fetch_assoc($res)) {
        $qty       = $row['quantity'];
        $uomId     = $row['UOM'];
        $uomRatio  = getUOMFromID($uomId)['ratio'];
        $costPrice = $row['costPrice'];

        // Calculate base subtotal
        $baseSubtotal = bcmul(bcmul($qty, $uomRatio, 6), $costPrice, 6);

        // Add totals
        $totalAmount += $baseSubtotal;

    }

    // Format totals for output (to 2 decimal places)
    $totalAmount = number_format($totalAmount, 2, '.', '');

    return $totalAmount;
}

function getTotalAmountWithShippingByConsultationPOID($poID)
{
    global $db;
    $baseSubTotal = getTotalAmountByConsultationPOID($poID);
    $res = $db->query("SELECT * FROM consultation_purchase_orders where poId = ?s", $poID);
    $subTotal = '0.00';
    $vatAmount = '0.00';
    while ($row = mysqli_fetch_assoc($res)) {
        $subTotal = !empty($row['subTotal']) ? $row['subTotal'] : '0.00';
        $vatAmount = !empty($row['vatAmount']) ? $row['vatAmount'] : '0.00';
    }
    $totalAmount = bcadd($baseSubTotal, $subTotal, 2);
    $totalAmount = bcadd($totalAmount, $vatAmount, 2);
    $totalAmount = number_format($totalAmount, 2, '.', '');
    return $totalAmount;
}


function getTotalAmountByConsultationDocumentID($documentID): array
{
    global $db;

    // Initialize totals with string representations of decimals
    $totalAmountBeforeVAT = '0.00';
    $totalDiscountAmount  = '0.00';
    $totalVATAmount       = '0.00';
    $totalAmountAfterVAT  = '0.00';

    $res = $db->query("SELECT * FROM consultation_line_items WHERE documentId = ?s", $documentID);

    while ($row = mysqli_fetch_assoc($res)) {
        $qty                = $row['quantity'];
        $uomId              = $row['UOM'];
        $unitPrice          = $row['unitPrice'];
        $vatPercentage      = $row['vatPercentage'];
        $discountPercentage = $row['discountPercentage'];

        // Calculate base subtotal
        $baseSubtotal = bcmul($qty, $unitPrice, 6);

        // Apply discount
        $discountAmount     = bcmul($baseSubtotal, bcdiv($discountPercentage, '100', 6), 6);
        $discountedSubtotal = bcsub($baseSubtotal, $discountAmount, 6);

        // Calculate VAT amount (based on the discounted subtotal)
        $vatAmount = bcmul($discountedSubtotal, bcdiv($vatPercentage, '100', 6), 6);

        // Add totals
        $totalAmountBeforeVAT = bcadd($totalAmountBeforeVAT, $discountedSubtotal, 6);
        $totalDiscountAmount  = bcadd($totalDiscountAmount, $discountAmount, 6);
        $totalVATAmount       = bcadd($totalVATAmount, $vatAmount, 6);
        $totalAmountAfterVAT  = bcadd($totalAmountAfterVAT, bcadd($discountedSubtotal, $vatAmount, 6), 6);

    }

    // Format totals for output (to 2 decimal places)
    $totalAmountBeforeVAT = number_format($totalAmountBeforeVAT, 2, '.', '');
    $totalDiscountAmount  = number_format($totalDiscountAmount, 2, '.', '');
    $totalVATAmount       = number_format($totalVATAmount, 2, '.', '');
    $totalAmountAfterVAT  = number_format($totalAmountAfterVAT, 2, '.', '');

    $returnArray                         = array();
    $returnArray['totalAmountBeforeVAT'] = $totalAmountBeforeVAT;
    $returnArray['totalDiscountAmount']  = $totalDiscountAmount;
    $returnArray['totalVATAmount']       = $totalVATAmount;
    $returnArray['totalAmountAfterVAT']  = $totalAmountAfterVAT;

    return $returnArray;
}


function getItemDescriptionForSparepartID($sparepartID)
{
    global $db;
    $res = $db->query("select description FROM spareparts WHERE sparepartId = ?s", $sparepartID);
    while ($row = mysqli_fetch_assoc($res)) {
        return $row['description'];
    }
}

function getItemBrandNameForSparepartID($sparepartID)
{
    global $db;
    $query = "SELECT b.brandName 
              FROM spareparts s
              INNER JOIN brands b ON s.brandId = b.brandId
              WHERE s.sparepartId = ?i";

    $brandName = $db->getOne($query, $sparepartID);
    return $brandName ? $brandName : "-";
}

function getImageForSparepartID($sparepartID)
{
    global $db;

    $res = $db->query("SELECT image FROM spareparts WHERE sparepartId = ?s", $sparepartID);
    while ($row = mysqli_fetch_assoc($res)) {
        return !empty($row['image']) ? $row['image'] : 'no-image-sparepart.png';
    }
    return 'no-image-sparepart.png';
}

function getItemTypeForSparepartID($sparepartID)
{
    global $db;

    $res = $db->query("SELECT itemType FROM spareparts WHERE sparepartId = ?s", $sparepartID);
    while ($row = mysqli_fetch_assoc($res)) {
        return !empty($row['itemType']) ? $row['itemType'] : 'SPAREPART';
    }
    return 'SPAREPART';
}

function getItemInfoForConsultationServiceItemID($itemId)
{
    global $db;
    $res = $db->query("select * FROM consultation_services_items WHERE itemId = ?s", $itemId);
    while ($row = mysqli_fetch_assoc($res)) {
        return $row;
    }
}

function getItemDescriptionForConsultationServiceItemID($itemId)
{
    global $db;
    $res = $db->query("select description FROM consultation_services_items WHERE itemId = ?s", $itemId);
    while ($row = mysqli_fetch_assoc($res)) {
        return $row['description'];
    }
}

function getPartNumberForSparepartID($sparepartID)
{
    global $db;
    $res = $db->query("select partNumber FROM spareparts WHERE sparepartId = ?s", $sparepartID);
    while ($row = mysqli_fetch_assoc($res)) {
        return $row['partNumber'];
    }
}

function getDetailsForSparepartID($sparepartID)
{
    global $db;
    $res = $db->query("SELECT partNumber, serialNumber, name, itemType FROM spareparts WHERE sparepartId = ?s", $sparepartID);
    while ($row = mysqli_fetch_assoc($res)) {
        if ($row['itemType'] === 'SPAREPART') {
            return $row['partNumber'];
        } elseif ($row['itemType'] === 'MACHINE') {
            return $row['serialNumber'] ?: ''; // Fallback to empty if serialNumber is NULL/empty
        } elseif ($row['itemType'] === 'SERVICE') {
            return $row['name'];
        } else {
            // Fallback for unknown types: use partNumber if available
            return $row['partNumber'] ?: $row['name'] ?: '';
        }
    }
    return ''; // No row found
}

function getInternalReferenceNumberForSparepartID($sparepartID)
{
    global $db;
    $res = $db->query("select internalReference FROM spareparts WHERE sparepartId = ?s", $sparepartID);
    while ($row = mysqli_fetch_assoc($res)) {
        return $row['internalReference'];
    }
}

function getCustomerNameByCustomerID($customerID) {
    global $db;
    $res = $db->query("SELECT * FROM `customers` WHERE customerId = ?s", $customerID);
    $row = mysqli_fetch_assoc($res);

    return $row['companyName'];
}

function getCustomerNameByDocumentID($documentId) {
    global $db;
    $res = $db->query("SELECT customerId FROM `consultation_key_documents` where documentId = ?s ", $documentId);
    $row = mysqli_fetch_assoc($res);
    return getCustomerNameByCustomerID($row['customerId']);
}

function getCustomerNameByProjectID($projectID) {
    global $db;
    $res = $db->query("SELECT documentId FROM `consultation_projects` WHERE projectId = ?s", $projectID);
    $row = mysqli_fetch_assoc($res);

    return getCustomerNameByDocumentID($row['documentId']);
}

function getSparepartsIDFromItemName($itemName)
{
    global $db;
    $res = $db->query("select sparepartId FROM spareparts WHERE name = ?s", $itemName);
    while ($row = mysqli_fetch_assoc($res)) {
        return $row['sparepartId'];
    }
}

function getBrandIDByName($brandName)
{
    global $db;
    $res = $db->query("SELECT brandId FROM brands WHERE brandName = ?s", $brandName);
    while ($row = mysqli_fetch_assoc($res)) {
        return $row['brandId'];
    }
}

function getBrandNameByID($brandID)
{
    global $db;
    $res = $db->query("SELECT brandName FROM brands WHERE brandId = ?s", $brandID);
    while ($row = mysqli_fetch_assoc($res)) {
        return $row['brandName'];
    }
}

function getUnitPriceForSparepartID($sparepartID)
{
    global $db;
    $res = $db->query("select salesPrice FROM spareparts WHERE sparepartId = ?s", $sparepartID);
    while ($row = mysqli_fetch_assoc($res)) {
        return $row['salesPrice'];
    }
}

function getItemNameForItemID($itemId)
{
    global $db;

    // Fetch itemName from consultation_services_items where itemId matches
    $res = $db->query("SELECT itemName FROM consultation_services_items WHERE itemId = ?s", $itemId);

    if ($row = mysqli_fetch_assoc($res)) {
        return $row['itemName'];
    }
}

function getItemDescriptionForItemID($itemId)
{
    global $db;

    // Fetch description from consultation_services_items where itemId matches
    $res = $db->query("SELECT description FROM consultation_services_items WHERE itemId = ?s", $itemId);

    if ($row = mysqli_fetch_assoc($res)) {
        return $row['description'];
    }
}

function getRejectReasonForQuotationID($quotationID)
{
    global $db;

    $res          = $db->query("SELECT * FROM quotation_reject_reason WHERE documentId = ?s ORDER BY rejectedAt DESC LIMIT 1", $quotationID);
    $rejectReason = "";
    while ($row = mysqli_fetch_assoc($res)) {
        $rejectReason = $row['rejectReason'];
    }

    return $rejectReason;

}

function getRejectReasonForPOID($poID)
{
    global $db;

    $res          = $db->query("SELECT * FROM po_reject_reason_sp_manager WHERE poId = ?s ORDER BY rejectedAt DESC LIMIT 1", $poID);
    $rejectReason = "";
    while ($row = mysqli_fetch_assoc($res)) {
        $rejectReason = $row['rejectReason'];
    }

    return $rejectReason;

}

function getRejectReasonForConsultationPOID($poID)
{
    global $db;

    $res          = $db->query("SELECT * FROM consultation_po_reject_reason_sp_manager WHERE poId = ?s ORDER BY rejectedAt DESC LIMIT 1", $poID);
    $rejectReason = "";
    while ($row = mysqli_fetch_assoc($res)) {
        $rejectReason = $row['rejectReason'];
    }

    return $rejectReason;

}

function getRejectReasonForRFPID($rfpID)
{
    global $db;

    $res          = $db->query("SELECT * FROM rfp_reject_reason_procurement_manager WHERE rfpId = ?s ORDER BY rejectedAt DESC LIMIT 1", $rfpID);
    $rejectReason = "";
    while ($row = mysqli_fetch_assoc($res)) {
        $rejectReason = $row['rejectReason'];
    }

    return $rejectReason;

}

function getRejectReasonForConsultationRFPID($rfpID)
{
    global $db;

    $res          = $db->query("SELECT * FROM consultation_rfp_reject_reason_procurement_manager WHERE rfpId = ?s ORDER BY rejectedAt DESC LIMIT 1", $rfpID);
    $rejectReason = "";
    while ($row = mysqli_fetch_assoc($res)) {
        $rejectReason = $row['rejectReason'];
    }

    return $rejectReason;

}

function getRejectReasonForTransferID($transferID)
{
    global $db;

    $res          = $db->query("SELECT * FROM transfer_reject_reason_procurement_manager WHERE transferId = ?s ORDER BY rejectedAt DESC LIMIT 1", $transferID);
    $rejectReason = "";
    while ($row = mysqli_fetch_assoc($res)) {
        $rejectReason = $row['rejectReason'];
    }

    return $rejectReason;

}

function getAccountanRejectReasontForPOID($poID)
{
    global $db;

    $res          = $db->query("SELECT * FROM po_reject_reason_accountant WHERE poId = ?s ORDER BY rejectedAt DESC LIMIT 1", $poID);
    $rejectReason = "";
    while ($row = mysqli_fetch_assoc($res)) {
        $rejectReason = $row['rejectReason'];
    }

    return $rejectReason;

}

function getAccountanRejectReasontForConsultationPOID($poID)
{
    global $db;

    $res          = $db->query("SELECT * FROM consultation_po_reject_reason_accountant WHERE poId = ?s ORDER BY rejectedAt DESC LIMIT 1", $poID);
    $rejectReason = "";
    while ($row = mysqli_fetch_assoc($res)) {
        $rejectReason = $row['rejectReason'];
    }

    return $rejectReason;

}

function getRejectReasonForGDNID($gdnID)
{
    global $db;

    $res          = $db->query("SELECT * FROM pick_and_pack_reject_reason_procurement_manager WHERE gdnId = ?s ORDER BY rejectedAt DESC LIMIT 1", $gdnID);
    $rejectReason = "";
    while ($row = mysqli_fetch_assoc($res)) {
        $rejectReason = $row['rejectReason'];
    }

    return $rejectReason;

}

function getRejectReasonForInvoiceID($invoiceID)
{
    global $db;

    $res          = $db->query("SELECT * FROM invoice_reject_reason_accountant WHERE invoiceId = ?s ORDER BY rejectedAt DESC LIMIT 1", $invoiceID);
    $rejectReason = "";
    while ($row = mysqli_fetch_assoc($res)) {
        $rejectReason = $row['rejectReason'];
    }

    return $rejectReason;

}

function getCustomerRejectReasonForQuotationID($quotationID)
{
    global $db;

    $res          = $db->query("SELECT * FROM quotation_customer_reject_reason WHERE documentId = ?s ORDER BY rejectedAt DESC LIMIT 1", $quotationID);
    $rejectReason = "";
    while ($row = mysqli_fetch_assoc($res)) {
        $rejectReason = $row['rejectReason'];
    }

    return $rejectReason;

}

function getEcommerceOrderRejectReasonForQuotationID($quotationID)
{
    global $db;

    $res          = $db->query("SELECT * FROM ecommerce_order_reject_reason WHERE documentId = ?s ORDER BY rejectedAt DESC LIMIT 1", $quotationID);
    $rejectReason = "";
    while ($row = mysqli_fetch_assoc($res)) {
        $rejectReason = $row['rejectReason'];
    }

    return $rejectReason;

}

function getEcommerceOrderAccountantRejectReasonForQuotationID($quotationID)
{
    global $db;

    $res          = $db->query("SELECT * FROM ecommerce_order_reject_reason_accountant WHERE documentId = ?s ORDER BY rejectedAt DESC LIMIT 1", $quotationID);
    $rejectReason = "";
    while ($row = mysqli_fetch_assoc($res)) {
        $rejectReason = $row['rejectReason'];
    }

    return $rejectReason;

}

function getEcommerceCustomerRejectReasonForDocumentID($documentID)
{
    global $db;

    $res          = $db->query("SELECT * FROM ecommerce_order_customer_reject_reason WHERE documentId = ?s ORDER BY rejectedAt DESC LIMIT 1", $documentID);
    $rejectReason = "";
    while ($row = mysqli_fetch_assoc($res)) {
        $rejectReason = $row['rejectReason'];
    }

    return $rejectReason;

}

function getSMSORejectReasonForQuotationID($quotationID)
{
    global $db;

    $res          = $db->query("SELECT * FROM quotation_reject_reason_sm_so WHERE documentId = ?s ORDER BY rejectedAt DESC LIMIT 1", $quotationID);
    $rejectReason = "";
    while ($row = mysqli_fetch_assoc($res)) {
        $rejectReason = $row['rejectReason'];
    }

    return $rejectReason;

}

function getAccountantSORejectReasonForQuotationID($quotationID)
{
    global $db;

    $res          = $db->query("SELECT * FROM quotation_reject_reason_accountant_so WHERE documentId = ?s ORDER BY rejectedAt DESC LIMIT 1", $quotationID);
    $rejectReason = "";
    while ($row = mysqli_fetch_assoc($res)) {
        $rejectReason = $row['rejectReason'];
    }
    return $rejectReason;
}


function generateRandomCustomerDocumentPassword($length = 9)
{
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $password   = '';
    $maxIndex   = strlen($characters) - 1;

    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[random_int(0, $maxIndex)];
    }

    return $password;
}


function getCompanyNameByID($companyId)
{
    global $db;
    $companyName = $db->getOne("SELECT companyLabel from companies WHERE companyId = ?s", $companyId);
    return $companyName;
}

function getAccountIDByName($accountName)
{
    global $db;
    $accountID = $db->getOne("SELECT id from chart_of_accounts WHERE accountName = ?s", $accountName);
    return $accountID;
}

function getAccountNameByID($accountID)
{
    global $db;
    $accountName = $db->getOne("SELECT accountName from chart_of_accounts WHERE id = ?s", $accountID);
    return $accountName;
}

function getAccountTypeIDByName($accountTypeName)
{
    global $db;
    $accountTypeID = $db->getOne("SELECT id from coa_types WHERE name = ?s", $accountTypeName);
    return $accountTypeID;
}

function getAccountTypeNameByID($accountTypeID)
{
    global $db;
    $accountTypeName = $db->getOne("SELECT name from coa_types WHERE id = ?s", $accountTypeID);
    return $accountTypeName;
}


/**
 * Records the history of a key document operation.
 *
 * @param string $keyDocumentID The ID of the key document being operated on.
 * @param string $operationType The type of operation being performed (e.g., 'save', 'update').
 * @param string|null $relatedActivityLogId The ID of the related activity log, if any.
 * @param string $remarks Additional remarks or details about the operation (default is an empty string).
 *
 * @return bool Returns true if the history record was successfully created.
 */
function recordKeyDocumentHistory(string $keyDocumentID, string $operationType, string $relatedActivityLogId = null, string $remarks = ""): bool
{
    global $db;
    if (isset($_SESSION[IsLoggedIn]) && $_SESSION[IsLoggedIn] == true) {
        $userId = getUserIDOfCurrentUser();
    } else {
        $userId = '0';
    }

    //INSERT INTO `grandmastererpdb`.`key_document_history` (`keyDocumentId`, `updatedAt`, `updatedBy`, `operationType`, `remark`) VALUES (29, '2024-12-13 22:32:08', 1, 'save_quotation', '-');
    $res = $db->query("INSERT INTO `key_document_history` (`keyDocumentId`, `updatedBy`,`relatedActivityLogId`, `operationType`, `remark`) VALUES (?s, ?s,?s, ?s,?s)", $keyDocumentID, $userId, $relatedActivityLogId, $operationType, $remarks);
    return true;
}

function recordNotification(int $recipient_userId, string $title, string $message, string $url): bool
{
    global $db;
    
    if (isset($_SESSION[IsLoggedIn]) && $_SESSION[IsLoggedIn] == true) {
        $sender_userId = getUserIDOfCurrentUser(); 
    } else {
        $sender_userId = 0;
    }
    
    $res = $db->query(
        "INSERT INTO `notifications` (`sender_userId`, `recipient_userId`, `title`, `message`, `url`) 
         VALUES (?s, ?s, ?s, ?s, ?s)",
        $sender_userId,
        $recipient_userId,
        $title,
        $message,
        $url
    );
    
    return true;
}


function recordConsultationKeyDocumentHistory(string $keyDocumentID, string $operationType, string $relatedActivityLogId = null, string $remarks = ""): bool
{
    global $db;
    if (isset($_SESSION[IsLoggedIn]) && $_SESSION[IsLoggedIn] == true) {
        $userId = getUserIDOfCurrentUser();
    } else {
        $userId = '0';
    }

    //INSERT INTO `grandmastererpdb`.`key_document_history` (`keyDocumentId`, `updatedAt`, `updatedBy`, `operationType`, `remark`) VALUES (29, '2024-12-13 22:32:08', 1, 'save_quotation', '-');
    $res = $db->query("INSERT INTO `consultation_key_document_history` (`keyDocumentId`, `updatedBy`,`relatedActivityLogId`, `operationType`, `remark`) VALUES (?s, ?s,?s, ?s,?s)", $keyDocumentID, $userId, $relatedActivityLogId, $operationType, $remarks);
    return true;
}

function recordProjectHistory(string $projectId, string $operationType, string $relatedActivityLogId = null, string $remarks = ""): bool
{
    global $db;
    if (isset($_SESSION[IsLoggedIn]) && $_SESSION[IsLoggedIn] == true) {
        $userId = getUserIDOfCurrentUser();
    } else {
        $userId = '0';
    }
    
    //INSERT INTO `grandmastererpdb`.`consultation_project_history` (`projectId`, `updatedAt`, `updatedBy`, `relatedActivityLogId`, `operationType`, `remark`) VALUES (29, '2024-12-13 22:32:08', 1, 'save_quotation', '-');
    $res = $db->query("INSERT INTO `consultation_project_history` (`projectId`, `updatedBy`,`relatedActivityLogId`, `operationType`, `remark`) VALUES (?s, ?s,?s, ?s,?s)", $projectId, $userId, $relatedActivityLogId, $operationType, $remarks);
    return true;
}

function recordSparepartHistory(string $sparepartId, string $operationType, string $relatedActivityLogId = null, string $remarks = ""): bool
{
    global $db;
    if (isset($_SESSION[IsLoggedIn]) && $_SESSION[IsLoggedIn] == true) {
        $userId = getUserIDOfCurrentUser();
    } else {
        $userId = '0';
    }
    $res = $db->query("INSERT INTO `spareparts_history` (`sparepartId`, `updatedBy`,`relatedActivityLogId`, `operationType`, `remark`) VALUES (?s, ?s,?s, ?s,?s)", $sparepartId, $userId, $relatedActivityLogId, $operationType, $remarks);
    return true;
}

function recordRFPHistory(string $rfpId, string $operationType, string $relatedActivityLogId = null, string $remarks = ""): bool
{
    global $db;
    if (isset($_SESSION[IsLoggedIn]) && $_SESSION[IsLoggedIn] == true) {
        $userId = getUserIDOfCurrentUser();
    } else {
        $userId = '0';
    }
    $res = $db->query("INSERT INTO `rfp_document_history` (`rfpId`, `updatedBy`,`relatedActivityLogId`, `operationType`, `remark`) VALUES (?s, ?s,?s, ?s,?s)", $rfpId, $userId, $relatedActivityLogId, $operationType, $remarks);
    return true;
}

function recordConsultationRFPHistory(string $rfpId, string $operationType, string $relatedActivityLogId = null, string $remarks = ""): bool
{
    global $db;
    if (isset($_SESSION[IsLoggedIn]) && $_SESSION[IsLoggedIn] == true) {
        $userId = getUserIDOfCurrentUser();
    } else {
        $userId = '0';
    }
    $res = $db->query("INSERT INTO `consultation_rfp_document_history` (`rfpId`, `updatedBy`,`relatedActivityLogId`, `operationType`, `remark`) VALUES (?s, ?s,?s, ?s,?s)", $rfpId, $userId, $relatedActivityLogId, $operationType, $remarks);
    return true;
}

function recordPOHistory(string $poId, string $operationType, string $relatedActivityLogId = null, string $remarks = ""): bool
{
    global $db;
    if (isset($_SESSION[IsLoggedIn]) && $_SESSION[IsLoggedIn] == true) {
        $userId = getUserIDOfCurrentUser();
    } else {
        $userId = '0';
    }
    $res = $db->query("INSERT INTO `po_document_history` (`poId`, `updatedBy`,`relatedActivityLogId`, `operationType`, `remark`) VALUES (?s, ?s,?s, ?s,?s)", $poId, $userId, $relatedActivityLogId, $operationType, $remarks);
    return true;
}

function recordConsultationPOHistory(string $poId, string $operationType, string $relatedActivityLogId = null, string $remarks = ""): bool
{
    global $db;
    if (isset($_SESSION[IsLoggedIn]) && $_SESSION[IsLoggedIn] == true) {
        $userId = getUserIDOfCurrentUser();
    } else {
        $userId = '0';
    }
    $res = $db->query("INSERT INTO `consultation_po_document_history` (`poId`, `updatedBy`,`relatedActivityLogId`, `operationType`, `remark`) VALUES (?s, ?s,?s, ?s,?s)", $poId, $userId, $relatedActivityLogId, $operationType, $remarks);
    return true;
}

function checkIfSalesOrderCancelable($salesOrderId, $companyId = 1)
{
    global $db;

    // Check if linked in RFP
    $rfpRes = $db->query("SELECT rfpId FROM rfp_documents WHERE saleOrderId = ?s", $salesOrderId);
    if ($db->numRows($rfpRes) > 0) {
        return false; // Already linked to RFP
    }

    // Check if linked in Invoice (must match companyId too)
    $invRes = $db->query("SELECT invoiceId FROM invoice_documents WHERE saleOrderId = ?s AND companyId = ?s", $salesOrderId, $companyId);
    if ($db->numRows($invRes) > 0) {
        return false; // Already invoiced
    }

    return true; // Eligible for cancel
}

function checkIfRFPEligibleForPO($rfpID)
{
    global $db;
    $res = $db->query("select * FROM rfp_line_items WHERE rfpId = ?s AND (costPrice IS NULL OR costPrice = '0')", $rfpID);

    $rowCount = $db->numRows($res);
    if($rowCount > 0)
        return false;

    return true;
}

function checkIfAllLineItemsStaged($orderManagementId) {
    global $db;
    $res = $db->query("SELECT * FROM order_management_line_items WHERE orderManagementId = ?s AND (orderWHStatus = 'STAGED' OR orderWHStatus = 'RETURNED')", $orderManagementId);

    return ($db->numRows($res) > 0);
}

function checkIfAllLineItemsProcessed($orderID)
{
    global $db;

    // Fetch all line items for the order
    $lineItems = $db->query("SELECT itemId, quantity FROM order_management_line_items WHERE orderManagementId = ?s", $orderID);

    // Track picked qtys
    $pickedQtyMap = [];

    // Get total picked qty per itemId
    $pickedRes = $db->query("SELECT itemId, SUM(quantity) AS totalPicked FROM pick_and_pack_line_items WHERE orderManagementId = ?s GROUP BY itemId", $orderID);
    while ($row = mysqli_fetch_assoc($pickedRes)) {
        $pickedQtyMap[$row['itemId']] = (int)$row['totalPicked'];
    }

    // Now compare each item's picked quantity to ordered quantity
    while ($row = mysqli_fetch_assoc($lineItems)) {
        $itemId = $row['itemId'];
        $orderedQty = (int)$row['quantity'];
        $pickedQty = isset($pickedQtyMap[$itemId]) ? (int)$pickedQtyMap[$itemId] : 0;

        if ($pickedQty < $orderedQty) {
            return false; // Not fully processed
        }
    }

    return true; // All line items fully processed
}


function checkIfTransferHasEmptyReasons($transferId)
{
    global $db;
    $res = $db->query("SELECT * FROM transfer_line_items WHERE transferId = ?s AND (transferReason IS NULL OR transferReason = '' AND transferReason != 0)", $transferId);

    return ($db->numRows($res) > 0);
}

function getDisplayTransactionType($transactionType) {
    switch ($transactionType) {
        case 'SALESORDER':
            return 'Sales Order';
        case 'TRANSFER':
            return 'Transfer';
        case 'BPSALES':
            return 'BP Sales';
        case 'EORDER':
            return 'E-Order';
        default:
            return htmlspecialchars($transactionType);
    }
}

function getCarrierFromGDNID($gdnId) {
    global $db;

    $res = $db->query("SELECT carrier FROM pick_and_pack_documents WHERE gdnId = ?s", $gdnId);

    if ($row = mysqli_fetch_assoc($res)) {
        return $row['carrier'];
    }
}

function getCarrierWayBillFromGDNID($gdnId) {
    global $db;

    // Fetch itemName from consultation_services_items where itemId matches
    $res = $db->query("SELECT carrierWaybill FROM pick_and_pack_documents WHERE gdnId = ?s", $gdnId);

    if ($row = mysqli_fetch_assoc($res)) {
        return $row['carrierWaybill'];
    }
}


//Inventory
function getWarehouseInfoFromID($warehouseID)
{
    global $db;
    $res = $db->query("select * from warehouses WHERE warehouseId = ?s", $warehouseID);
    return $row = mysqli_fetch_assoc($res);
}


function getStockOnHandQtyForSparePart($sparepartID, $warehouseID = 0)
{
    global $db;
    $filterQuery = "";
    if ($warehouseID <> 0) {
        $filterQuery .= "AND warehouseId = $warehouseID ";
    }
    $res = $db->query("select sum(quantity) as stockOnHand from inventory_stock WHERE sparepartId = ?s $filterQuery", $sparepartID);
    $row = mysqli_fetch_assoc($res);
    return $row['stockOnHand'];

}

function getSparepartLocationDetails($sparepartID, $warehouseID = 0) {
    global $db;
    $filterQuery = "";
    if ($warehouseID <> 0) {
        $filterQuery .= "AND warehouseId = $warehouseID ";
    }
    $res = $db->query("select * from inventory_stock WHERE sparepartId = ?s $filterQuery", $sparepartID);
    return $row = mysqli_fetch_assoc($res);
}

function getIncomingSpareparts($sparepartID, $warehouseID = 0) {
    global $db;
    $filterQuery = "";
    if ($warehouseID != 0) {
        $filterQuery .= "AND warehouseId = $warehouseID ";
    }

    $res = $db->query(
        "SELECT po.*, li.quantity as incomingQty
         FROM purchase_orders po 
         JOIN po_line_items li ON po.poId = li.poId
         WHERE li.itemId = ?s
         AND po.poStatus = 'ACCOUNTANT APPROVED'
         AND li.itemReceived = 0
         $filterQuery
         LIMIT 1",
        $sparepartID
    );

    $row = mysqli_fetch_assoc($res);
    return $row ? $row : ['incomingQty' => '-'];
}

function getAvailableQtyForSparePart($sparepartID, $warehouseID = 0)
{
    global $db;
    $filterQuery = "";

    if ($warehouseID <> 0) {
        $filterQuery .= "AND warehouseId = $warehouseID ";
    }
    $stockOnHandQTY = getStockOnHandQtyForSparePart($sparepartID, $warehouseID);
    $res            = $db->query("SELECT 
    COALESCE(SUM(reservedQuantity), 0) AS totalReservedQuantity
FROM inventory_reservations
WHERE status = 'RESERVED'
AND sparepartId = ?s $filterQuery", $sparepartID);

    $row         = mysqli_fetch_assoc($res);
    $reservedQTY = $row['totalReservedQuantity'];

    return (int)$stockOnHandQTY - (int)$reservedQTY;
}


function convertToArabicNumbers($number)
{
    $western = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    $arabic  = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];

    return str_replace($western, $arabic, $number);
}


function convertToHijri($gregorianDate)
{
    $timestamp  = strtotime($gregorianDate);
    $hijriYear  = date("Y", $timestamp) - 579; // Approximate Hijri year
    $hijriMonth = date("m", $timestamp);
    $hijriDay   = date("d", $timestamp);

    return sprintf("%04d-%02d-%02d", $hijriYear, $hijriMonth, $hijriDay);
}

function gregorianToHijri($dateString, $timezone = 'Asia/Riyadh') {
    // Create DateTime object from input with the specified timezone
    $date = new DateTime($dateString, new DateTimeZone($timezone));
    
    // Create IntlDateFormatter for Hijri (Islamic, Umm al-Qura) with the same timezone
    $formatter = new IntlDateFormatter(
        'en_US@calendar=islamic-umalqura',
        IntlDateFormatter::FULL,
        IntlDateFormatter::NONE,
        $timezone,  // Use the same timezone
        IntlDateFormatter::TRADITIONAL,
        'yyyy-MM-dd'
    );
    
    // Format the Gregorian date to Hijri
    $hijriDate = $formatter->format($date);
    
    return $hijriDate;
}

function formatHijriDate($mysqlDate, $includeTime = true) {
    if (empty($mysqlDate)) {
        return null;
    }

    // First convert to Hijri
    $hijriDate = gregorianToHijri($mysqlDate);
    
    // Parse the Hijri date (format: YYYY-MM-DD)
    $parts = explode('-', $hijriDate);
    if (count($parts) != 3) {
        return $hijriDate; // Return as-is if format unexpected
    }

    // Arabic Hijri month names
    $hijriMonths = [
        '01' => 'محرم',
        '02' => 'صفر',
        '03' => 'ربيع الأول',
        '04' => 'ربيع الثاني',
        '05' => 'جمادى الأولى',
        '06' => 'جمادى الآخرة',
        '07' => 'رجب',
        '08' => 'شعبان',
        '09' => 'رمضان',
        '10' => 'شوال',
        '11' => 'ذو القعدة',
        '12' => 'ذو الحجة'
    ];

    $monthName = $hijriMonths[$parts[1]] ?? $parts[1];
    
    // Format with or without time
    if ($includeTime) {
        $time = date("g:i A", strtotime($mysqlDate));
        return "$monthName {$parts[2]}, {$parts[0]}, $time"; // e.g., Muharram 16, 1447, 5:48 PM
    } else {
        return "$monthName {$parts[2]}, {$parts[0]}"; // e.g., Muharram 16, 1447
    }
}

function formatHijriDateShort($mysqlDate, $includeTime = true) {
    if (empty($mysqlDate)) {
        return null;
    }

    // First convert to Hijri
    $hijriDate = gregorianToHijri($mysqlDate);
    
    // Parse the Hijri date (format: YYYY-MM-DD)
    $parts = explode('-', $hijriDate);
    if (count($parts) != 3) {
        return $hijriDate; // Return as-is if format unexpected
    }

    // Format with or without time
    if ($includeTime) {
        $time = date("g:i A", strtotime($mysqlDate));
        return "{$parts[2]}/{$parts[1]}/{$parts[0]}, $time"; // e.g., 16/01/1447, 5:48 PM
    } else {
        return "{$parts[2]}/{$parts[1]}/{$parts[0]}"; // e.g., 16/01/1447
    }
}

function getBilingualMonth($dateStr) {
    $englishMonths = [
        'January', 'February', 'March', 'April',
        'May', 'June', 'July', 'August',
        'September', 'October', 'November', 'December'
    ];

    $arabicMonths = [
        'يناير', 'فبراير', 'مارس', 'أبريل',
        'مايو', 'يونيو', 'يوليو', 'أغسطس',
        'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'
    ];

    $monthNum = date('n', strtotime($dateStr)) - 1;
    $year = date('Y', strtotime($dateStr));

    return [
        'english' => $englishMonths[$monthNum] . ' ' . $year,
        'arabic' => $arabicMonths[$monthNum] . ' ' . $year,
        'month_number' => $monthNum + 1,
        'year' => $year
    ];
}

function getBilingualQuarter($dateStr) {
    $englishQuarters = [
        'Q1', 'Q2', 'Q3', 'Q4'
    ];

    $englishQuarterRanges = [
        'Jan-Mar', 'Apr-Jun', 'Jul-Sep', 'Oct-Dec'
    ];

    $arabicQuarters = [
        'الربع الأول', 'الربع الثاني', 'الربع الثالث', 'الربع الرابع'
    ];

    // Get the month number (1-12) and year from the date string
    $monthNum = date('n', strtotime($dateStr)); // e.g., January → 1, April → 4
    $year = date('Y', strtotime($dateStr));

    // Calculate the quarter (1-4)
    $quarterNum = ceil($monthNum / 3); // e.g., Jan (1) → Q1, Apr (4) → Q2
    $quarterIndex = $quarterNum - 1; // Array index (0-3)

    return [
        'english' => $englishQuarters[$quarterIndex],
        'english_range' => $englishQuarterRanges[$quarterIndex],
        'arabic' => $arabicQuarters[$quarterIndex],
        'quarter_number' => $quarterNum,
        'year' => $year
    ];
}

function getQuarterRange($quarter) {
    $englishRanges = [
        'Q1' => 'Jan-Mar',
        'Q2' => 'Apr-Jun',
        'Q3' => 'Jul-Sep',
        'Q4' => 'Oct-Dec'
    ];

    return $englishRanges[$quarter];
}

function getWoocommerceWebHookSecret($optionName)
{
    global $db;
    $webhookSecret = $db->getOne("SELECT optionValue FROM options WHERE optionName = ?s", $optionName);
    return $webhookSecret;
}

function numberToEnglishWords($number) {
    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'negative ';
    $decimal     = ' and ';
    $dictionary  = [
        0 => 'zero',
        1 => 'one',
        2 => 'two',
        3 => 'three',
        4 => 'four',
        5 => 'five',
        6 => 'six',
        7 => 'seven',
        8 => 'eight',
        9 => 'nine',
        10 => 'ten',
        11 => 'eleven',
        12 => 'twelve',
        13 => 'thirteen',
        14 => 'fourteen',
        15 => 'fifteen',
        16 => 'sixteen',
        17 => 'seventeen',
        18 => 'eighteen',
        19 => 'nineteen',
        20 => 'twenty',
        30 => 'thirty',
        40 => 'forty',
        50 => 'fifty',
        60 => 'sixty',
        70 => 'seventy',
        80 => 'eighty',
        90 => 'ninety',
        100 => 'hundred',
        1000 => 'thousand',
        1000000 => 'million',
        1000000000 => 'billion'
    ];

    if (!is_numeric($number)) {
        return false;
    }
    if ($number < 0) {
        return $negative . numberToEnglishWords(abs($number));
    }

    $string = $fraction = null;
    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= ' ' . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = (int) ($number / 100);
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' hundred';
            if ($remainder) {
                $string .= $conjunction . numberToEnglishWords($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = numberToEnglishWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= numberToEnglishWords($remainder);
            }
            break;
    }

    if ($fraction !== null && is_numeric($fraction)) {
        $fraction = str_pad($fraction, 2, '0', STR_PAD_RIGHT);
        $fractionValue = (int)$fraction;

        $string .= " Riyal";
        if ($fractionValue > 0) {
            $fractionWords = numberToEnglishWords($fractionValue);
            $string .= $decimal . $fractionWords . " Halala";
        }
    }

    return ucfirst($string);
}

function numberToArabicWords($number) {
    $arabicOnes = [
        "", "واحد", "اثنان", "ثلاثة", "أربعة", "خمسة", "ستة", "سبعة", "ثمانية", "تسعة",
        "عشرة", "أحد عشر", "اثنا عشر", "ثلاثة عشر", "أربعة عشر", "خمسة عشر", "ستة عشر", "سبعة عشر", "ثمانية عشر", "تسعة عشر"
    ];

    $arabicTens = [
        "", "", "عشرون", "ثلاثون", "أربعون", "خمسون", "ستون", "سبعون", "ثمانون", "تسعون"
    ];

    $arabicHundreds = [
        "", "مائة", "مئتان", "ثلاثمائة", "أربعمائة", "خمسمائة", "ستمائة", "سبعمائة", "ثمانمائة", "تسعمائة"
    ];

    // Currency words with correct Arabic endings for numbers (can be extended/improved for grammar)
    $currencyRiyal = "ريالًا";
    $currencyHalala = "هللة";

    $result = '';

    if (!is_numeric($number)) {
        return false; // or throw error based on your preference
    }

    // Split integer and fractional parts (halalas)
    if (strpos((string)$number, '.') !== false) {
        list($integerPart, $fractionalPart) = explode('.', (string)$number);
        $fractionalPart = substr($fractionalPart . "00", 0, 2); // ensure 2 digits max for halala
    } else {
        $integerPart = $number;
        $fractionalPart = null;
    }

    // Inner function to convert integer numbers (reusing your original logic)
    $convertInteger = function($num) use (&$convertInteger, $arabicOnes, $arabicTens, $arabicHundreds) {
        $num = (int)$num;
        $res = '';

        if ($num == 0) {
            return "صفر";
        }

        // Handle thousands
        if ($num >= 1000) {
            $thousand = floor($num / 1000);
            $num = $num % 1000;
            if ($thousand == 1) {
                $res .= "ألف";
            } elseif ($thousand == 2) {
                $res .= "ألفان";
            } elseif ($thousand < 10) {
                $res .= $arabicOnes[$thousand] . " آلاف";
            } else {
                $res .= $convertInteger($thousand) . " ألف";
            }
            if ($num > 0) {
                $res .= " و";
            }
        }

        // Handle hundreds
        if ($num >= 100) {
            $hundred = floor($num / 100);
            $num = $num % 100;
            $res .= $arabicHundreds[$hundred];
            if ($num > 0) {
                $res .= " و";
            }
        }

        // Handle tens and ones
        if ($num > 0) {
            if ($num < 20) {
                $res .= $arabicOnes[$num];
            } else {
                $ten = floor($num / 10);
                $one = $num % 10;
                if ($one > 0) {
                    $res .= $arabicOnes[$one] . " و";
                }
                $res .= $arabicTens[$ten];
            }
        }

        return $res;
    };

    $result .= trim($convertInteger($integerPart)) . ' ' . $currencyRiyal;

    // Convert fractional part (halala) if present and > 0
    if ($fractionalPart !== null && (int)$fractionalPart > 0) {
        $result .= " و" . trim($convertInteger($fractionalPart)) . ' ' . $currencyHalala;
    }

    return $result;
}

function getAssetNameByID($assetID)
{
    global $db; // Use global $db
    $result = $db->getRow("SELECT assetName FROM company_assets WHERE assetId = ?i", $assetID);
    return $result ? $result['assetName'] : null;
}

function to12Hour($time) {
    return $time ? date("g:i A", strtotime($time)) : '';
}

/**
 * Check if the given user (or current session user) has a specific permission
 *
 * @param string $feature     e.g. 'quotations', 'projects'
 * @param string $capability  e.g. 'view', 'create', 'edit', 'delete'
 * @param int|null $userID    optional, default = current logged-in user
 * @return bool
 */
function has_permission($feature, $capability, $userID = null)
{
    global $db;

    // Normalize feature name
    $feature = strtolower(trim($feature));
    $capability = strtolower(trim($capability));

    // 1️⃣ Determine user
    if ($userID === null && isset($_SESSION['LoggedInUser'])) {
        $user = $_SESSION['LoggedInUser'];
        $userID = (int)$user->userID;
    }

    if (!$userID) {
        return false; // no user context
    }

    // 2️⃣ Get user’s roles
    $roles = [];
    $res = $db->query("SELECT roleId FROM user_role_mapping WHERE userId = ?i", $userID);
    while ($row = mysqli_fetch_assoc($res)) {
        $roles[] = (int)$row['roleId'];
    }

    // 3️⃣ CEO (Super Admin) has full access (roleId = 1)
    if (in_array(1, $roles)) {
        return true;
    }

    // 4️⃣ Gather all base role permissions
    $rolePermissions = [];

    foreach ($roles as $roleId) {
        $row = $db->getRow("SELECT permissions FROM user_roles WHERE roleId = ?i", $roleId);
        if (!empty($row['permissions'])) {
            $decoded = json_decode($row['permissions'], true);
            if (is_array($decoded)) {
                foreach ($decoded as $module => $actions) {
                    $moduleKey = strtolower(trim($module));
                    if (!isset($rolePermissions[$moduleKey])) {
                        $rolePermissions[$moduleKey] = [];
                    }
                    $rolePermissions[$moduleKey] = array_unique(
                        array_merge($rolePermissions[$moduleKey], array_map('strtolower', $actions))
                    );
                }
            }
        }
    }

    // 5️⃣ Apply user-specific overrides
    $userPerms = $db->getAll("SELECT module, capabilities FROM user_permissions WHERE userId = ?i", $userID);
    foreach ($userPerms as $row) {
        $moduleKey = strtolower(trim($row['module']));
        $capJSON = json_decode($row['capabilities'], true);

        if (isset($capJSON['added']) && is_array($capJSON['added'])) {
            foreach ($capJSON['added'] as $addedCap) {
                $rolePermissions[$moduleKey][] = strtolower($addedCap);
            }
        }

        if (isset($capJSON['revoked']) && is_array($capJSON['revoked'])) {
            $rolePermissions[$moduleKey] = array_diff(
                $rolePermissions[$moduleKey] ?? [],
                array_map('strtolower', $capJSON['revoked'])
            );
        }
    }

    // 6️⃣ Final check
    return isset($rolePermissions[$feature]) && in_array($capability, $rolePermissions[$feature]);
}

/**
 * Alias function for readability (like Perfex’s staff_can)
 */
function user_can($capability, $feature, $userID = null)
{
    return has_permission($feature, $capability, $userID);
}

/**
 * CEO (Superadmin) check
 *
 * @param int|null $userID
 * @return bool
 */
function is_ceo($userID = null)
{
    global $db;

    if ($userID === null && isset($_SESSION['LoggedInUser'])) {
        $user = $_SESSION['LoggedInUser'];
        $roles = $user->roles;
    } else {
        $roles = [];
        $res = $db->query("SELECT roleId FROM user_role_mapping WHERE userId = ?i", $userID);
        while ($row = mysqli_fetch_assoc($res)) {
            $roles[] = (int)$row['roleId'];
        }
    }

    return in_array(1, $roles);
}