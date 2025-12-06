<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";

$documentId   = filter_var($_POST['documentID'], FILTER_SANITIZE_NUMBER_INT);
$status       = filter_var($_POST['status'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$rejectReason = filter_var($_POST['rejectReason'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

$currentUser = getUserIDOfCurrentUser();

$companyId = intval($_SESSION['SES_SELECTED_COMPANY']);

try {
    if (!empty($documentId)) {


        // Dynamic base URL for emails (works across local/testing/prod)
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $base_url = $protocol . '://' . $_SERVER['HTTP_HOST'] . '/';
        
        if ($status == QUOTATION_STATUS_AWAITING_APPROVAL) {
            $res = $db->query("UPDATE `key_documents` SET `quotationStatus`= ?s WHERE `documentId`=?s", $status, $documentId);
            // Notification Logic: Send to ROLE_SUPERADMIN (1) and ROLE_SALES_MANAGER (3)
            $roleIds = [ROLE_SUPERADMIN, ROLE_SALES_MANAGER]; // [1, 3]
            $recipientsQuery = "
                SELECT DISTINCT u.userID 
                FROM users u 
                JOIN user_role_mapping urm ON u.userID = urm.userId 
                WHERE urm.roleId IN (" . implode(',', $roleIds) . ") 
                AND u.active = 1 
                AND u.userID != ?s 
            ";
            $recipientsRes = $db->query($recipientsQuery, $currentUser); // Pass current user to exclude
            
            $title = "NEW QUOTATION AWAITING APPROVAL";
            $senderName = getDisplayNameFromUserID($currentUser);
            $message = "A new quotation has been submitted by $senderName for your approval.";
            $url = $base_url . "quotation/edit/" . $documentId;
    
            while ($recipient = mysqli_fetch_assoc($recipientsRes)) {
                sendNotificationWithEmail($recipient['userID'], $title, $message, $url);
            }
            recordKeyDocumentHistory($documentId, $status,logActivity($status) );
            echo "SUCCESS|Sent for Approval";
            exit();
        }

        if ($status == QUOTATION_STATUS_CANCELLED) {
            $res = $db->query("UPDATE `key_documents` SET `quotationStatus`= ?s, `cancelReason`= ?s WHERE `documentId`=?s", $status, $rejectReason, $documentId);
            recordKeyDocumentHistory($documentId, $status,logActivity($status), $rejectReason);
            echo "SUCCESS|Quotation has been cancelled";
            exit();
        }

        //Sales Manager approval
        if ($status == QUOTATION_STATUS_APPROVED) {

            $quotationNumberPrefix = PREFIX_QUOTATION;
            $keyDocument           = new KeyDocument();
            $keyDocument->loadById($documentId);
            if (empty($keyDocument->quotationNumber)) {
                $quotationNumber = getNextNewQuotationNumber();
            } else {
                $quotationNumber = $keyDocument->quotationNumber;
            }

            $res = $db->query("UPDATE `key_documents` SET `quotationStatus`= ?s, `quotationNumber`=?s, `quotationNumberPrefix`=?s WHERE `documentId`=?s", $status, $quotationNumber, $quotationNumberPrefix, $documentId);

            // Notification Logic: Send to ROLE_SALES_EXECUTIVE (4) and ROLE_SUPERADMIN (1)
            $roleIds = [ROLE_SUPERADMIN, ROLE_SALES_EXECUTIVE]; // [1, 4]
            $recipientsQuery = "
                SELECT DISTINCT u.userID 
                FROM users u 
                JOIN user_role_mapping urm ON u.userID = urm.userId 
                WHERE urm.roleId IN (" . implode(',', $roleIds) . ") 
                AND u.active = 1 
                AND u.userID != ?s 
            ";
            $recipientsRes = $db->query($recipientsQuery, $currentUser); // Pass current user to exclude
            
            $title = "QUOTATION APPROVED";
            $senderName = getDisplayNameFromUserID($currentUser);
            $message = "Quotation # " . getQuotationNumberFromDocumentID($documentId) . " has been approved by $senderName. You can now proceed to send it to the customer.";
            $url = $base_url . "quotation/edit/" . $documentId;
            
            while ($recipient = mysqli_fetch_assoc($recipientsRes)) {
                // recordNotification($recipient['userID'], $title, $message, $url);
                sendNotificationWithEmail($recipient['userID'], $title, $message, $url);
            }
            recordKeyDocumentHistory($documentId, $status,logActivity($status) , "Quotation number generated - " . getQuotationNumberFromDocumentID($documentId));
            echo "SUCCESS|Quotation has been Approved!";
            exit();
        }


        //Sales Manager approval for SO
        if ($status == QUOTATION_STATUS_AWAITING_SALES_MANAGER_SO_APPROVAL) {

            $res = $db->query("UPDATE `key_documents` SET `quotationStatus`= ?s WHERE `documentId`=?s", $status, $documentId);
            // Notification Logic: Send to ROLE_SUPERADMIN (1) and ROLE_SALES_MANAGER (3)
            $roleIds = [ROLE_SUPERADMIN, ROLE_SALES_MANAGER]; // [1, 3]
            $recipientsQuery = "
                SELECT DISTINCT u.userID 
                FROM users u 
                JOIN user_role_mapping urm ON u.userID = urm.userId 
                WHERE urm.roleId IN (" . implode(',', $roleIds) . ") 
                AND u.active = 1 
                AND u.userID != ?s 
            ";
            $recipientsRes = $db->query($recipientsQuery, $currentUser); // Pass current user to exclude
            
            $title = "SALES ORDER AWAITING SALES MANAGER APPROVAL";
            $senderName = getDisplayNameFromUserID($currentUser);
            $quotationNumber = getQuotationNumberFromDocumentID($documentId);
            $message = "Sales order for Quotation #$quotationNumber has been submitted by $senderName for your approval.";
            $url = $base_url . "quotation/edit/" . $documentId;
            
            while ($recipient = mysqli_fetch_assoc($recipientsRes)) {
                sendNotificationWithEmail($recipient['userID'], $title, $message, $url);
            }
            recordKeyDocumentHistory($documentId, $status, logActivity($status) );
            echo "SUCCESS|Sent for SO Approval with sales manager";
            exit();
        }

        //Sales Manager REJECTED for SO
        if ($status == QUOTATION_STATUS_SALES_MANAGER_SO_REJECTED) {

            $res = $db->query("UPDATE `key_documents` SET `quotationStatus`= ?s WHERE `documentId`=?s", $status, $documentId);
            $res = $db->query("INSERT INTO `quotation_reject_reason_sm_so` (`documentId`, `rejectReason`, `rejectedBy`) VALUES (?s, ?s, ?s)", $documentId, $rejectReason, getAuthenticatedUser()->userID);
            // Notification Logic: Send to ROLE_SUPERADMIN (1) and ROLE_SALES_EXECUTIVE (4)
            $roleIds = [ROLE_SUPERADMIN, ROLE_SALES_EXECUTIVE]; // [1, 4]
            $recipientsQuery = "
                SELECT DISTINCT u.userID 
                FROM users u 
                JOIN user_role_mapping urm ON u.userID = urm.userId 
                WHERE urm.roleId IN (" . implode(',', $roleIds) . ") 
                AND u.active = 1 
                AND u.userID != ?s 
            ";
            $recipientsRes = $db->query($recipientsQuery, $currentUser); // Pass current user to exclude
            
            $title = "SALES ORDER REJECTED BY SALES MANAGER";
            $senderName = getDisplayNameFromUserID($currentUser);
            $quotationNumber = getQuotationNumberFromDocumentID($documentId);
            $message = "Sales order for Quotation #$quotationNumber has been rejected by $senderName. Reason: $rejectReason";
            $url = $base_url . "quotation/edit/" . $documentId;
            
            while ($recipient = mysqli_fetch_assoc($recipientsRes)) {
                sendNotificationWithEmail($recipient['userID'], $title, $message, $url);
            }
            recordKeyDocumentHistory($documentId, $status, logActivity($status) , $rejectReason);
            echo "SUCCESS|Salesorder has been rejected";
            exit();
        }


        //Accountant REJECTED for SO
        if ($status == QUOTATION_STATUS_ACCOUNTANT_SO_REJECTED) {

            $res = $db->query("UPDATE `key_documents` SET `quotationStatus`= ?s WHERE `documentId`=?s", $status, $documentId);
            $res = $db->query("INSERT INTO `quotation_reject_reason_accountant_so` (`documentId`, `rejectReason`, `rejectedBy`) VALUES (?s, ?s, ?s)", $documentId, $rejectReason, getAuthenticatedUser()->userID);
            // Notification Logic: Send to ROLE_SUPERADMIN (1) and ROLE_SALES_MANAGER (3)
            $roleIds = [ROLE_SUPERADMIN, ROLE_SALES_MANAGER]; // [1, 3]
            $recipientsQuery = "
                SELECT DISTINCT u.userID 
                FROM users u 
                JOIN user_role_mapping urm ON u.userID = urm.userId 
                WHERE urm.roleId IN (" . implode(',', $roleIds) . ") 
                AND u.active = 1 
                AND u.userID != ?s 
            ";
            $recipientsRes = $db->query($recipientsQuery, $currentUser); // Pass current user to exclude
            
            $title = "SALES ORDER REJECTED BY ACCOUNTANT";
            $senderName = getDisplayNameFromUserID($currentUser);
            $quotationNumber = getQuotationNumberFromDocumentID($documentId);
            $message = "Sales order for Quotation #$quotationNumber has been rejected by $senderName. Reason: $rejectReason";
            $url = $base_url . "quotation/edit/" . $documentId;
            
            while ($recipient = mysqli_fetch_assoc($recipientsRes)) {
                sendNotificationWithEmail($recipient['userID'], $title, $message, $url);
            }
            recordKeyDocumentHistory($documentId, $status, logActivity($status) , $rejectReason);
            echo "SUCCESS|Salesorder has been rejected";
            exit();
        }

        //Accountant approval for SO
        if ($status == QUOTATION_STATUS_AWAITING_ACCOUNTANT_SO_APPROVAL) {

            $res = $db->query("UPDATE `key_documents` SET `quotationStatus`= ?s, `salesManagerSOApproved` = 'APPROVED' WHERE `documentId`=?s", $status, $documentId);
            // Notification Logic: Send to ROLE_SUPERADMIN (1) and ROLE_ACCOUNTS_MANAGER (2)
            $roleIds = [ROLE_SUPERADMIN, ROLE_ACCOUNTS_MANAGER]; // [1, 2]
            $recipientsQuery = "
                SELECT DISTINCT u.userID 
                FROM users u 
                JOIN user_role_mapping urm ON u.userID = urm.userId 
                WHERE urm.roleId IN (" . implode(',', $roleIds) . ") 
                AND u.active = 1 
                AND u.userID != ?s 
            ";
            $recipientsRes = $db->query($recipientsQuery, $currentUser); // Pass current user to exclude
            
            $title = "SALES ORDER AWAITING ACCOUNTANT APPROVAL";
            $senderName = getDisplayNameFromUserID($currentUser);
            $quotationNumber = getQuotationNumberFromDocumentID($documentId);
            $message = "Sales order for Quotation #$quotationNumber has been submitted by $senderName for your approval.";
            $url = $base_url . "quotation/edit/" . $documentId;
            
            while ($recipient = mysqli_fetch_assoc($recipientsRes)) {
                sendNotificationWithEmail($recipient['userID'], $title, $message, $url);
            }
            recordKeyDocumentHistory($documentId, $status,logActivity($status) );
            echo "SUCCESS|Sent for SO Approval with Accountant";
            exit();
        }

        //Accountant approval for SO
        if ($status == QUOTATION_STATUS_SO_APPROVED) {

            $res = $db->query("UPDATE `key_documents` SET `quotationStatus`= ?s, `accountantSOApproved` = 'APPROVED' WHERE `documentId`=?s", $status, $documentId);
            // Notification Logic: Send to ROLE_SUPERADMIN (1) and ROLE_SALES_EXECUTIVE (4)
            $roleIds = [ROLE_SUPERADMIN, ROLE_SALES_EXECUTIVE]; // [1, 4]
            $recipientsQuery = "
                SELECT DISTINCT u.userID 
                FROM users u 
                JOIN user_role_mapping urm ON u.userID = urm.userId 
                WHERE urm.roleId IN (" . implode(',', $roleIds) . ") 
                AND u.active = 1 
                AND u.userID != ?s 
            ";
            $recipientsRes = $db->query($recipientsQuery, $currentUser); // Pass current user to exclude
            
            $title = "SALES ORDER APPROVED";
            $senderName = getDisplayNameFromUserID($currentUser);
            $quotationNumber = getQuotationNumberFromDocumentID($documentId);
            $message = "Sales order for Quotation #$quotationNumber has been approved by $senderName.";
            $url = $base_url . "quotation/edit/" . $documentId;
            
            while ($recipient = mysqli_fetch_assoc($recipientsRes)) {
                sendNotificationWithEmail($recipient['userID'], $title, $message, $url);
            }
            recordKeyDocumentHistory($documentId, $status,logActivity($status) );
            echo "SUCCESS|SO Approved";
            exit();
        }

        //Quotation Confirmed for sales order
        if ($status == QUOTATION_STATUS_CONFIRMED) {

            $salesOrderNumberPrefix = PREFIX_SALESORDER;
            $keyDocument           = new KeyDocument();
            $keyDocument->loadById($documentId);
            if (empty($keyDocument->saleOrderNumber)) {
                $salesOrderNumber = getNextNewSalesOrderNumber();
            } else {
                $salesOrderNumber = $keyDocument->saleOrderNumber;
            }

            $res = $db->query("UPDATE `key_documents` SET `quotationStatus`= ?s,
                                    `saleOrderStatus`= ?s, 
                                    `saleOrderDateIssued` = now(), 
                                    `saleOrderNumber`=?s,
                                     `saleOrderNumberPrefix`=?s 
                       WHERE
                           `documentId`=?s", $status, 'ACTIVE', $salesOrderNumber, $salesOrderNumberPrefix, $documentId);
            recordKeyDocumentHistory($documentId, $status,logActivity($status), "SalesOrder Number - " . $salesOrderNumberPrefix . $salesOrderNumber );
            echo "SUCCESS|Quotation Confirmed. SalesOrder# " . getSalesOrderNumberFromDocumentID($documentId);
            exit();
        }


        if ($status == QUOTATION_STATUS_SENT_TO_CUSTOMER) {

            $res = $db->query("UPDATE `key_documents` SET `quotationStatus`= ?s, `quotationAcceptedByCustomer`='NEUTRAL',`salesManagerSOApproved`='NEUTRAL', `accountantSOApproved`='NEUTRAL'  WHERE `documentId`=?s", $status, $documentId);
            // Notification Logic: Send to superadmin (1), creator (salesPersonId), and sales_manager (3)
            $keyDocument = new KeyDocument();
            $keyDocument->loadById($documentId);
            $creatorId = $keyDocument->salesPersonId; // The original creator/sales person
            
            $roleIds = [ROLE_SUPERADMIN, ROLE_SALES_MANAGER]; // [1, 3] for superadmin and sales managers
            $recipientsQuery = "
                SELECT DISTINCT u.userID 
                FROM users u 
                JOIN user_role_mapping urm ON u.userID = urm.userId 
                WHERE urm.roleId IN (" . implode(',', $roleIds) . ") 
                AND u.active = 1 
                AND u.userID != ?s 
            ";
            $recipientsRes = $db->query($recipientsQuery, $currentUser); // Pass current user to exclude
            
            $title = "QUOTATION SENT TO CUSTOMER";
            $senderName = getDisplayNameFromUserID($currentUser);
            $quotationNumber = getQuotationNumberFromDocumentID($documentId);
            $message = "Quotation #$quotationNumber has been sent to the customer by $senderName. Monitor for acceptance/rejection.";
            $url = "/quotation/edit/" . $documentId;
            
            $url = $base_url . "quotation/edit/" . $documentId;
    
            // Notify creator first (if valid and not self)
            if ($creatorId > 0 && $creatorId != $currentUser) {
                sendNotificationWithEmail($creatorId, $title, $message, $url);
            }
            
            // Notify superadmin and sales managers
            while ($recipient = mysqli_fetch_assoc($recipientsRes)) {
                sendNotificationWithEmail($recipient['userID'], $title, $message, $url);
            }
            recordKeyDocumentHistory($documentId, $status,logActivity($status) );
            echo "SUCCESS|Email has been sent to customer";
            exit();
        }


        if ($status == QUOTATION_STATUS_REJECTED) {
            $res = $db->query("UPDATE `key_documents` SET `quotationStatus`= ?s  WHERE `documentId`=?s", $status, $documentId);
            $res = $db->query("INSERT INTO `quotation_reject_reason` (`documentId`, `rejectReason`, `rejectedBy`) VALUES (?s, ?s, ?s)", $documentId, $rejectReason, getAuthenticatedUser()->userID);

            // Notification Logic: Send to creator (salesPersonId) and all ROLE_SALES_EXECUTIVE (4)
            $keyDocument = new KeyDocument();
            $keyDocument->loadById($documentId);
            $creatorId = $keyDocument->salesPersonId; // The original creator/sales person
            
            $roleIds = [ROLE_SALES_EXECUTIVE]; // [4] for all sales executives
            $recipientsQuery = "
                SELECT DISTINCT u.userID 
                FROM users u 
                JOIN user_role_mapping urm ON u.userID = urm.userId 
                WHERE urm.roleId IN (" . implode(',', $roleIds) . ") 
                AND u.active = 1 
                AND u.userID != ?s 
            ";
            $recipientsRes = $db->query($recipientsQuery, $currentUser); // Pass current user to exclude
            
            $title = "QUOTATION REJECTED";
            $senderName = getDisplayNameFromUserID($currentUser);
            $quotationNumber = getQuotationNumberFromDocumentID($documentId);
            $message = "Quotation #$quotationNumber has been rejected by $senderName. Reason: $rejectReason. Please review and revise.";
            $url = $base_url . "quotation/edit/" . $documentId;
    
            // Notify creator first (if valid and not self)
            if ($creatorId > 0 && $creatorId != $currentUser) {
                sendNotificationWithEmail($creatorId, $title, $message, $url);
            }
            
            // Notify all sales executives
            while ($recipient = mysqli_fetch_assoc($recipientsRes)) {
                sendNotificationWithEmail($recipient['userID'], $title, $message, $url);
            }
            recordKeyDocumentHistory($documentId, $status,logActivity($status) , $rejectReason);
            echo "SUCCESS|Quotation has been rejected";
            exit();
        }
    }

} catch (Exception $e) {
    error_log("Error inserting document info: " . $e->getMessage());
    echo "ERROR|" . $e->getMessage();
    exit();
}