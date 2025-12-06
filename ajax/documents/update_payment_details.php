<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";
// Read the raw POST data
$rawData = file_get_contents("php://input");

// Decode the JSON data into a PHP array
$data = json_decode($rawData, true);

if ($data) {
    // Extract the repeater data and additional data
    $salesOrderId        = $data['salesOrderID'];
    $totalAmount   = $data['totalAmount'];
    $accountsReceivableID   = $data['accountsReceivableID'];
    $paymentDate      = $data['paymentDate'];
    $amountReceived = $data['amountReceived'];
    $paymentMode     = $data['paymentMode'];
    $reference = $data['reference'];
    $remarks     = $data['remarks'];
    $companyId = intval($_SESSION['SES_SELECTED_COMPANY']);

    try {
        if (!empty($accountsReceivableID)) {

            $cashOrBankAccountID = getAccountIDByName('Cash / Bank');
            $arAccountID = getAccountIDByName('Accounts Receivable');

            //Create a new document
            $res                = $db->query("INSERT INTO `payments` (`type`, `referenceNumber`, `amount`, `paymentMethodId`, `paidOn`, `remarks`, `salesPersonId`, `companyId`)
            VALUES (?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s)",
                'INCOMING', $reference, $amountReceived, $paymentMode, $paymentDate, $remarks, getUserIDOfCurrentUser(), $companyId);
            $paymentId = $db->insertId();

            $res = $db->query("INSERT INTO `payment_links` (`paymentId`, `accountType`, `accountId`, `amount`) VALUES (?s, ?s, ?s, ?s)",
                $paymentId, 'RECEIVABLE', $accountsReceivableID, $amountReceived);

            $res = $db->query("INSERT INTO `journal_entries` (`entryDate`, `description`, `referenceId`,`referenceType`, `companyId`) VALUES (now(), ?s, ?s, ?s, ?s)", "Customer Payment for the Sales Order #" . getSalesOrderNumberFromDocumentID($salesOrderId), $paymentId, "CUSTOMERPAYMENT", $companyId);
            $journalEntryId = $db->insertId();
            $res = $db->query("INSERT INTO `journal_entry_lines` (`journalEntryId`, `accountId`, `debit`, `credit`, `description`) VALUES (?s, ?s, ?s, ?s, ?s)", $journalEntryId, $cashOrBankAccountID, $amountReceived, 0, "Payment Received from Customer for the Sales Order #" . getSalesOrderNumberFromDocumentID($salesOrderId));
            $res = $db->query("INSERT INTO `journal_entry_lines` (`journalEntryId`, `accountId`, `debit`, `credit`, `description`) VALUES (?s, ?s, ?s, ?s, ?s)", $journalEntryId, $arAccountID, 0, $amountReceived, "Reducing the Accounts Receivable for the Sales Order #" . getSalesOrderNumberFromDocumentID($salesOrderId));

            // Get current payable info
            $receivable = $db->getRow("SELECT `totalAmount`, `receivedAmount` FROM `accounts_receivable` WHERE `id` = ?s", $accountsReceivableID);

            // Calculate new paid amount
            $newReceivedAmount = $receivable['receivedAmount'] + $amountReceived;

            // Determine status
            if ($newReceivedAmount >= $receivable['totalAmount']) {
                $status = 'RECEIVED';
            } elseif ($newReceivedAmount > 0) {
                $status = 'PARTIAL';
            } else {
                $status = 'PENDING';
            }

            // Update accounts_payable
            $res = $db->query("UPDATE `accounts_receivable` 
                   SET `receivedAmount` = ?s, `status` = ?s 
                   WHERE `id` = ?s",
                $newReceivedAmount, $status, $accountsReceivableID);
        }

    } catch (Exception $e) {
        error_log("Error inserting document info: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => "Inserting Document Info: " . $e->getMessage()]);
        exit();
    }

    echo json_encode(['status' => 'success', 'message' => 'Payment details saved successfully.']);

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing data']);
}


