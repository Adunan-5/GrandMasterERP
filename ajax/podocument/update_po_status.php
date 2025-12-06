<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";

$documentId   = filter_var($_POST['documentID'], FILTER_SANITIZE_NUMBER_INT);
$status       = filter_var($_POST['status'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$rejectReason = filter_var($_POST['rejectReason'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

$companyId = intval($_SESSION['SES_SELECTED_COMPANY']);

try {
    if (!empty($documentId)) {

        if ($status == PO_STATUS_AWAITING_SPAREPARTS_MANAGER_APPROVAL) {
            $res = $db->query("UPDATE `purchase_orders` SET `poStatus`= ?s WHERE `poId`=?s", $status, $documentId);
            recordPOHistory($documentId, $status,logActivity($status) );
            echo "SUCCESS|Sent for Approval";
            exit();
        }

        if ($status == PO_STATUS_SPAREPARTS_MANAGER_APPROVED) {
            $res = $db->query("UPDATE `purchase_orders` SET `poStatus`= ?s, `spManagerPOApproved` = 'APPROVED' WHERE `poId`=?s", $status, $documentId);
            recordPOHistory($documentId, $status,logActivity($status) );
            echo "SUCCESS|Spareparts Manager Approved";
            exit();
        }

        if ($status == PO_STATUS_AWAITING_ACCOUNTANT_APPROVAL) {
            $res = $db->query("UPDATE `purchase_orders` SET `poStatus`= ?s WHERE `poId`=?s", $status, $documentId);
            recordPOHistory($documentId, $status,logActivity($status) );
            echo "SUCCESS|Purchase Order sent to Accountant for Approval";
            exit();
        }

        if ($status == PO_STATUS_ACCOUNTANT_APPROVED) {

            $poDATA         = "";
            $res            = $db->query("SELECT * FROM purchase_orders WHERE active = 1 and poId = ?s", $documentId);
            while ($row = mysqli_fetch_assoc($res)) {
                $poDATA = $row;
            }

            $apAccountID = getAccountIDByName('Accounts Payable');
            $inventoryAccountID = getAccountIDByName('Inventory');
            $vatReceivableAccountID = getAccountIDByName('VAT Receivable');
            $importTaxAccountID = getAccountIDByName('Import Tax');
            $shippingExpenseID = getAccountIDByName('Shipping Expense');
            $customDutyID = getAccountIDByName('Custom Duty Expense');
            $bankChargeID = getAccountIDByName('Bank Charges');
            $insuranceID = getAccountIDByName('Insurance');
            $surchargeID = getAccountIDByName('Surcharge');
            $otherCostID = getAccountIDbyName('Other Import Costs');

            $total = getTotalAmountWithShippingByPOID($documentId);
            $subTotal = $poDATA['subTotal']; 
            $shippingCost = $poDATA['shippingCost'] ?? 0;
            $customDuties = $poDATA['customDuties'] ?? 0;
            $foreignTransactionFee = $poDATA['foreignTransactionFee'] ?? 0;
            $insurance = $poDATA['insurance'] ?? 0;
            $surcharge = $poDATA['surcharge'] ?? 0;
            $other = $poDATA['other'] ?? 0;
            $taxes = $poDATA['taxes'] ?? 0;
            $vatAmount = $poDATA['vatAmount'] ?? 0;
            $inventoryAmount = getTotalAmountByPOID($documentId);

            $poNumberPrefix = PREFIX_PO;
            if (empty($poDATA['poNumber'])) {
                $poNumber = getNextNewPoNumber();
            } else {
                $poNumber = $poDATA['poNumber'];
            }
            $res = $db->query("UPDATE `purchase_orders` SET `poStatus`= ?s, `poNumber`=?s, `poNumberPrefix`=?s, `accountantPOApproved` = 'APPROVED' WHERE `poId`=?s", $status, $poNumber, $poNumberPrefix, $documentId);
            recordPOHistory($documentId, $status,logActivity($status), "PO Number generated - " . getPONumberFromConsultationDocumentID($documentId) );
            
            echo "SUCCESS|Purchase Order has been Approved";
            exit();
        }

        if ($status == PO_STATUS_SPAREPARTS_MANAGER_REJECTED) {
            $res = $db->query("UPDATE `purchase_orders` SET `poStatus`= ?s WHERE `poId`=?s", $status, $documentId);
            $res = $db->query("INSERT INTO `po_reject_reason_sp_manager` (`poId`, `rejectReason`, `rejectedBy`) VALUES (?s, ?s, ?s)", $documentId, $rejectReason, getAuthenticatedUser()->userID);
            recordPOHistory($documentId, $status,logActivity($status), $rejectReason );
            echo "SUCCESS|Purchase Order has been Rejected";
            exit();
        }

        if ($status == PO_STATUS_ACCOUNTANT_REJECTED) {
            $res = $db->query("UPDATE `purchase_orders` SET `poStatus`= ?s WHERE `poId`=?s", $status, $documentId);
            $res = $db->query("INSERT INTO `po_reject_reason_accountant` (`poId`, `rejectReason`, `rejectedBy`) VALUES (?s, ?s, ?s)", $documentId, $rejectReason, getAuthenticatedUser()->userID);
            recordPOHistory($documentId, $status,logActivity($status), $rejectReason );
            echo "SUCCESS|Purchase Order has been Rejected";
            exit();
        }

        if ($status == PO_STATUS_SENT_TO_SUPPLIER) {

            //get the current status first
            $res = $db->query("SELECT * FROM purchase_orders WHERE poId = ?s", $documentId);
            $row=mysqli_fetch_assoc($res);
            if($row['poStatus']== PO_STATUS_ACCOUNTANT_APPROVED)
            {
                $res = $db->query("UPDATE `purchase_orders` SET `poStatus`= ?s  WHERE `poId`=?s", $status, $documentId);
                recordPOHistory($documentId, $status,logActivity($status) );
                echo "SUCCESS|PO status has been changed";
            }
            else{

                echo "DONOTSENDSUCCESS|PO status has been changed";
            }
            exit();
        }

        if($status == PO_STATUS_SUPPLIER_INVOICE_ATTACHED) {

            $poDATA         = "";
            $res            = $db->query("SELECT * FROM purchase_orders WHERE active = 1 and poId = ?s", $documentId);
            while ($row = mysqli_fetch_assoc($res)) {
                $poDATA = $row;
            }

            $res = $db->query("UPDATE `purchase_orders` SET `poStatus` = ?s WHERE `poId`=?s", $status, $documentId);
            recordPOHistory($documentId, $status,logActivity($status) );

            $apAccountID = getAccountIDByName('Accounts Payable');
            $inventoryAccountID = getAccountIDByName('Inventory');
            $vatReceivableAccountID = getAccountIDByName('VAT Receivable');
            $importTaxAccountID = getAccountIDByName('Import Tax');
            $shippingExpenseID = getAccountIDByName('Shipping Expense');
            $customDutyID = getAccountIDByName('Custom Duty Expense');
            $bankChargeID = getAccountIDByName('Bank Charges');
            $insuranceID = getAccountIDByName('Insurance');
            $surchargeID = getAccountIDByName('Surcharge');
            $otherCostID = getAccountIDByName('Other Import Costs');

            $total = getTotalAmountWithShippingByPOID($documentId);
            $subTotal = $poDATA['subTotal'];
            $shippingCost = $poDATA['shippingCost'] ?? 0;
            $customDuties = $poDATA['customDuties'] ?? 0;
            $foreignTransactionFee = $poDATA['foreignTransactionFee'] ?? 0;
            $insurance = $poDATA['insurance'] ?? 0;
            $surcharge = $poDATA['surcharge'] ?? 0;
            $other = $poDATA['other'] ?? 0;
            $taxes = $poDATA['taxes'] ?? 0;
            $vatAmount = $poDATA['vatAmount'] ?? 0;
            $inventoryAmount = getTotalAmountByPOID($documentId);

            $res = $db->query("INSERT INTO `accounts_payable` (`supplierId`, `poId`, `totalAmount`, `companyId`) VALUES (?s, ?s, ?s, ?s)", $poDATA['supplierId'], $documentId, $total, $companyId);

            $res = $db->query("INSERT INTO `journal_entries` (`entryDate`, `description`, `referenceId`,`referenceType`, `companyId`) VALUES (now(), ?s, ?s, ?s, ?s)", "Purchase Order #" . getPONumberFromDocumentID($documentId), $documentId, "PURCHASEORDER", $companyId);

            $journalEntryId = $db->insertId();

            $res = $db->query("INSERT INTO `journal_entry_lines` (`journalEntryId`, `accountId`, `debit`, `credit`, `description`) VALUES (?s, ?s, ?s, ?s, ?s)", $journalEntryId, $inventoryAccountID, $inventoryAmount, 0, "Stocks Purchased via Purchase Order #" . getPONumberFromDocumentID($documentId));

            if($vatAmount > 0) {
                $res = $db->query("INSERT INTO `journal_entry_lines` (`journalEntryId`, `accountId`, `debit`, `credit`, `description`) VALUES (?s, ?s, ?s, ?s, ?s)", $journalEntryId, $vatReceivableAccountID, $vatAmount, 0, "VAT Receivable for Purchase Order #" . getPONumberFromDocumentID($documentId));
            }

            if($shippingCost > 0) {
                $res = $db->query("INSERT INTO `journal_entry_lines` (`journalEntryId`, `accountId`, `debit`, `credit`, `description`) VALUES (?s, ?s, ?s, ?s, ?s)", $journalEntryId, $shippingExpenseID, $shippingCost, 0, "Shipping Cost for Purchase Order #" . getPONumberFromDocumentID($documentId));
            }

            if($taxes > 0) {
                $res = $db->query("INSERT INTO `journal_entry_lines` (`journalEntryId`, `accountId`, `debit`, `credit`, `description`) VALUES (?s, ?s, ?s, ?s, ?s)", $journalEntryId, $importTaxAccountID, $taxes, 0, "Import Tax for Purchase Order #" . getPONumberFromDocumentID($documentId));
            }

            if($customDuties > 0) {
                $res = $db->query("INSERT INTO `journal_entry_lines` (`journalEntryId`, `accountId`, `debit`, `credit`, `description`) VALUES (?s, ?s, ?s, ?s, ?s)", $journalEntryId, $customDutyID, $customDuties, 0, "Custom Duty for Purchase Order #" . getPONumberFromDocumentID($documentId));
            }

            if($foreignTransactionFee > 0) {
                $res = $db->query("INSERT INTO `journal_entry_lines` (`journalEntryId`, `accountId`, `debit`, `credit`, `description`) VALUES (?s, ?s, ?s, ?s, ?s)", $journalEntryId, $bankChargeID, $foreignTransactionFee, 0, "Foreign Transaction Fee for Purchase Order #" . getPONumberFromDocumentID($documentId));
            }

            if($insurance > 0) {
                $res = $db->query("INSERT INTO `journal_entry_lines` (`journalEntryId`, `accountId`, `debit`, `credit`, `description`) VALUES (?s, ?s, ?s, ?s, ?s)", $journalEntryId, $insuranceID, $insurance, 0, "Insurance for Purchase Order #" . getPONumberFromDocumentID($documentId));
            }

            if($surcharge > 0) {
                $res = $db->query("INSERT INTO `journal_entry_lines` (`journalEntryId`, `accountId`, `debit`, `credit`, `description`) VALUES (?s, ?s, ?s, ?s, ?s)", $journalEntryId, $surchargeID, $surcharge, 0, "Surcharge for Purchase Order #" . getPONumberFromDocumentID($documentId));
            }

            if($other > 0) {
                $res = $db->query("INSERT INTO `journal_entry_lines` (`journalEntryId`, `accountId`, `debit`, `credit`, `description`) VALUES (?s, ?s, ?s, ?s, ?s)", $journalEntryId, $otherCostID, $other, 0, "Other Import Costs for Purchase Order #" . getPONumberFromDocumentID($documentId));
            }

            $res = $db->query("INSERT INTO `journal_entry_lines` (`journalEntryId`, `accountId`, `debit`, `credit`, `description`) VALUES (?s, ?s, ?s, ?s, ?s)", $journalEntryId, $apAccountID, 0, $total, "Accounts Payable for the Purchase Order #" . getPONumberFromDocumentID($documentId));
        }
    }

} catch (Exception $e) {
    error_log("Error inserting document info: " . $e->getMessage());
    echo "ERROR|" . $e->getMessage();
    exit();
}