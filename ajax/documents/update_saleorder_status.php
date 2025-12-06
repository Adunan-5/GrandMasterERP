<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";

$documentId   = filter_var($_POST['documentID'], FILTER_SANITIZE_NUMBER_INT);
$status       = filter_var($_POST['status'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$rejectReason = filter_var($_POST['rejectReason'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

$companyId = intval($_SESSION['SES_SELECTED_COMPANY']);

try {
    if (!empty($documentId)) {

        if($status == SALESORDER_STATUS_CONFIRMED) {

            $keyDocument           = new KeyDocument();
            $keyDocument->loadById($documentId);

            $res = $db->query("UPDATE `key_documents` SET `saleOrderStatus`= ?s WHERE `documentId` = ?s", $status, $documentId);

//            $arAccountID = getAccountIDByName('Accounts Receivable');
//            $salesRevenueAccountID = getAccountIDByName('Sales Revenue');
//            $vatPayableAccountID = getAccountIDByName('VAT Payable');
//
//            $totalAmountBeforeVAT = getTotalAmountByDocumentID($documentId)['totalAmountBeforeVAT'];
//            $totalVATAmount = getTotalAmountByDocumentID($documentId)['totalVATAmount'];
//            $total = getTotalAmountByDocumentID($documentId)['totalAmountAfterVAT'];
//
//            $res = $db->query("INSERT INTO `accounts_receivable` (`customerId`, `saleOrderId`, `totalAmount`, `companyId`) VALUES (?s, ?s, ?s, ?s)", $keyDocument->customerId, $documentId, getTotalAmountByDocumentID($documentId)['totalAmountAfterVAT'], $companyId);
//            $res = $db->query("INSERT INTO `journal_entries` (`entryDate`, `description`, `referenceId`,`referenceType`, `companyId`) VALUES (now(), ?s, ?s, ?s, ?s)", "Sales Order #" . getSalesOrderNumberFromDocumentID($documentId), $documentId, "SALESORDER", $companyId);
//            $journalEntryId = $db->insertId();
//            $res = $db->query("INSERT INTO `journal_entry_lines` (`journalEntryId`, `accountId`, `debit`, `credit`, `description`) VALUES (?s, ?s, ?s, ?s, ?s)", $journalEntryId, $arAccountID, $total, 0, "Accounts Receivable for the Sales Order #" . getSalesOrderNumberFromDocumentID($documentId));
//            $res = $db->query("INSERT INTO `journal_entry_lines` (`journalEntryId`, `accountId`, `debit`, `credit`, `description`) VALUES (?s, ?s, ?s, ?s, ?s)", $journalEntryId, $salesRevenueAccountID, 0, $totalAmountBeforeVAT, "Sales Revenue for the Sales Order #" . getSalesOrderNumberFromDocumentID($documentId));
//            $res = $db->query("INSERT INTO `journal_entry_lines` (`journalEntryId`, `accountId`, `debit`, `credit`, `description`) VALUES (?s, ?s, ?s, ?s, ?s)", $journalEntryId, $vatPayableAccountID, 0, $totalVATAmount, "VAT Payable for the Sales Order #" . getSalesOrderNumberFromDocumentID($documentId));

            echo "SUCCESS|Sales Order has been Confirmed";
            exit();
        }

        if($status == SALESORDER_STATUS_CANCELLED) {

            $res = $db->query("UPDATE `key_documents` SET `saleOrderStatus`= ?s, `salesOrderCancelReason`= ?s WHERE `documentId` = ?s", $status, $rejectReason, $documentId);

            echo "SUCCESS|Sales Order has been Cancelled";
            exit();
        }

        if($status == ORDER_WH_STATUS_ALLOCATED) {
            $res = $db->query("UPDATE `key_documents` SET `orderWHStatus`= ?s, `saleOrderStatus` = 'SENT TO WH'  WHERE `documentId`=?s", $status, $documentId);
            $res = $db->query("UPDATE `line_items` SET `orderWHStatus`= ?s  WHERE `documentId`=?s", $status, $documentId);;
            $res = $db->query("SELECT * FROM `key_documents` WHERE `quotationStatus` = 'CONFIRMED' AND(`saleOrderStatus` IS NOT NULL OR saleOrderStatus <> '') AND documentId = ?s", $documentId);
            $row = mysqli_fetch_assoc($res);

            $customerId = $row['customerId'];

            $orderNumber = getSalesOrderNumberFromDocumentID($documentId);
            $insertRes = $db->query("INSERT INTO `order_management_documents` (`orderNumber`, `orderWHStatus`, `customerId`, `transactionType`, `salesPersonId`, `documentId`) 
                                   VALUES (?s, ?s, ?s, ?s, ?s, ?s)",
                $orderNumber, $status, $customerId, 'SALESORDER', getUserIDOfCurrentUser(), $documentId);

            $orderManagementId = $db->insertId();
            $lineItemsRes = $db->query("SELECT * FROM line_items WHERE documentId = ?s AND active = 1", $documentId);
            while ($lineItem = mysqli_fetch_assoc($lineItemsRes)) {
                $db->query("INSERT INTO `order_management_line_items` (`orderManagementId`, `itemId`, `quantity`, `eta`, `orderWHStatus`) 
                                VALUES (?s, ?s, ?s, ?s, ?s)",
                    $orderManagementId, $lineItem['itemId'], $lineItem['quantity'], $lineItem['eta'], $status);
            }
            echo "SUCCESS|Order Sent to WH";

        }

    }
} catch (Exception $e) {
    error_log("Error inserting document info: " . $e->getMessage());
    echo "ERROR|" . $e->getMessage();
    exit();
}