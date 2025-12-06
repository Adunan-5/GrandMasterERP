<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";

$documentId = filter_var($_POST['documentID'], FILTER_SANITIZE_NUMBER_INT);
$status = filter_var($_POST['status'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$rejectReason = filter_var($_POST['rejectReason'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

try {
    if (!empty($documentId)) {

        // Dynamic base URL for emails (works across local/testing/prod)
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $base_url = $protocol . '://' . $_SERVER['HTTP_HOST'] . '/';

        if ($status == QUOTATION_STATUS_CUSTOMER_ACCEPTED) {

            $res = $db->query("UPDATE `key_documents` SET `quotationStatus`= ?s, `quotationAcceptedByCustomer` = 'APPROVED', `quotationAcceptedByCustomerDate` = NOW()  WHERE `documentId`=?s", $status, $documentId);
            // Notification Logic: Send to ROLE_SUPERADMIN (1) and ROLE_SALES_EXECUTIVE (4)
            $roleIds = [ROLE_SUPERADMIN, ROLE_SALES_EXECUTIVE]; // [1, 4]
            $recipientsQuery = "
                SELECT DISTINCT u.userID 
                FROM users u 
                JOIN user_role_mapping urm ON u.userID = urm.userId 
                WHERE urm.roleId IN (" . implode(',', $roleIds) . ") 
                AND u.active = 1 
            ";
            $recipientsRes = $db->query($recipientsQuery);
            
            $title = "QUOTATION CUSTOMER ACCEPTED";
            $quotationNumber = getQuotationNumberFromDocumentID($documentId);
            $message = "Quotation #$quotationNumber has been accepted by the customer.";
            $url = $base_url . "quotation/edit/" . $documentId;
    
            while ($recipient = mysqli_fetch_assoc($recipientsRes)) {
                sendNotificationWithEmail($recipient['userID'], $title, $message, $url);
            }
            recordKeyDocumentHistory($documentId, $status,logActivity($status) );
            echo "SUCCESS|Customer Approved";
            exit();
        }
        if ($status == QUOTATION_STATUS_CUSTOMER_REJECTED) {

            $keyDocument = new KeyDocument();
            $keyDocument->loadById($documentId);


            $res = $db->query("UPDATE `key_documents` SET `quotationStatus`= ?s, `quotationAcceptedByCustomer` = 'REJECTED', `quotationAcceptedByCustomerDate` = NOW()   WHERE `documentId`=?s", $status, $documentId);
            $res = $db->query("INSERT INTO `quotation_customer_reject_reason` (`documentId`, `rejectReason`, `rejectedBy`) VALUES (?s, ?s, ?s)", $documentId, $rejectReason, $keyDocument->customerId);
            // Notification Logic: Send to ROLE_SUPERADMIN (1) and ROLE_SALES_EXECUTIVE (4)
            $roleIds = [ROLE_SUPERADMIN, ROLE_SALES_EXECUTIVE]; // [1, 4]
            $recipientsQuery = "
                SELECT DISTINCT u.userID 
                FROM users u 
                JOIN user_role_mapping urm ON u.userID = urm.userId 
                WHERE urm.roleId IN (" . implode(',', $roleIds) . ") 
                AND u.active = 1 
            ";
            $recipientsRes = $db->query($recipientsQuery);
            
            $title = "QUOTATION CUSTOMER REJECTED";
            $quotationNumber = getQuotationNumberFromDocumentID($documentId);
            $message = "Quotation #$quotationNumber has been rejected by the customer. Reason: $rejectReason";
            $url = $base_url . "quotation/edit/" . $documentId;
    
            while ($recipient = mysqli_fetch_assoc($recipientsRes)) {
                sendNotificationWithEmail($recipient['userID'], $title, $message, $url);
            }
            recordKeyDocumentHistory($documentId, $status,logActivity($status), $rejectReason );
            echo "SUCCESS|Quotation has been rejected by customer";
            exit();
        }

    }

} catch (Exception $e) {
    error_log("Error inserting document info: " . $e->getMessage());
    echo "ERROR|" . $e->getMessage();
    exit();
}