<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";
// Read the raw POST data
$rawData = file_get_contents("php://input");

// Decode the JSON data into a PHP array
$data = json_decode($rawData, true);

if ($data) {
    // Extract the repeater data and additional data
    $poID        = $data['poID'];
    $totalAmount   = $data['totalAmount'];
    $accountsPayableID   = $data['accountsPayableID'];
    $paymentDate      = $data['paymentDate'];
    $amountPaid = $data['amountPaid'];
    $paymentMode     = $data['paymentMode'];
    $reference = $data['reference'];
    $remarks     = $data['remarks'];
    $companyId = intval($_SESSION['SES_SELECTED_COMPANY']);

    try {
        if (!empty($accountsPayableID)) {

            $cashOrBankAccountID = getAccountIDByName('Cash / Bank');
            $apAccountID = getAccountIDByName('Accounts Payable');
            //Create a new document
            $res                = $db->query("INSERT INTO `payments` (`type`, `referenceNumber`, `amount`, `paymentMethodId`, `paidOn`, `remarks`, `salesPersonId`, `companyId`)
            VALUES (?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s)",
                'OUTGOING', $reference, $amountPaid, $paymentMode, $paymentDate, $remarks, getUserIDOfCurrentUser(), $companyId);
            $paymentId = $db->insertId();

            $res = $db->query("INSERT INTO `payment_links` (`paymentId`, `accountType`, `accountId`, `amount`) VALUES (?s, ?s, ?s, ?s)",
                $paymentId, 'PAYABLE', $accountsPayableID, $amountPaid);

            $res = $db->query("INSERT INTO `journal_entries` (`entryDate`, `description`, `referenceId`,`referenceType`, `companyId`) VALUES (now(), ?s, ?s, ?s, ?s)", "Our Payment for the Purchase Order #" . getPONumberFromDocumentID($poID), $paymentId, "PURCHASEPAYMENT", $companyId);
            $journalEntryId = $db->insertId();
            $res = $db->query("INSERT INTO `journal_entry_lines` (`journalEntryId`, `accountId`, `debit`, `credit`, `description`) VALUES (?s, ?s, ?s, ?s, ?s)", $journalEntryId, $apAccountID, $amountPaid, 0, "Paying Supplier for the Purchase Order #" . getPONumberFromDocumentID($poID));
            $res = $db->query("INSERT INTO `journal_entry_lines` (`journalEntryId`, `accountId`, `debit`, `credit`, `description`) VALUES (?s, ?s, ?s, ?s, ?s)", $journalEntryId, $cashOrBankAccountID, 0, $amountPaid, "Cash/Bank paid for the Purchase Order #" . getPONumberFromDocumentID($poID));

            // Get current payable info
            $payable = $db->getRow("SELECT `totalAmount`, `paidAmount` FROM `accounts_payable` WHERE `id` = ?s", $accountsPayableID);

            // Calculate new paid amount
            $newPaidAmount = $payable['paidAmount'] + $amountPaid;

            // Determine status
            if ($newPaidAmount >= $payable['totalAmount']) {
                $status = 'PAID';
            } elseif ($newPaidAmount > 0) {
                $status = 'PARTIAL';
            } else {
                $status = 'PENDING';
            }

            // Update accounts_payable
            $res = $db->query("UPDATE `accounts_payable` 
                   SET `paidAmount` = ?s, `status` = ?s 
                   WHERE `id` = ?s",
                $newPaidAmount, $status, $accountsPayableID);
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


