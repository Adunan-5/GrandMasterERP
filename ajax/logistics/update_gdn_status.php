<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";

$documentId   = filter_var($_POST['documentID'], FILTER_SANITIZE_NUMBER_INT);
$status       = filter_var($_POST['status'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$rejectReason = filter_var($_POST['rejectReason'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$companyId = intval($_SESSION['SES_SELECTED_COMPANY']);

try {
    if (!empty($documentId)) {



        if ($status == GDN_STATUS_PROCUREMENT_MANAGER_APPROVAL) {
            $res = $db->query("UPDATE `pick_and_pack_documents` SET `gdnStatus`= ?s WHERE `gdnId`=?s", $status, $documentId);
//            recordKeyDocumentHistory($documentId, $status,logActivity($status) );
            echo "SUCCESS|Sent for Approval";
            exit();
        }

        if ($status == GDN_STATUS_PROCUREMENT_MANAGER_APPROVED) {
//
//            $orderWHStatus = ORDER_WH_STATUS_READY;
//
//            $ppres = $db->query("SELECT * FROM pick_and_pack_documents WHERE gdnId = ?s", $documentId);
//            $pprow = mysqli_fetch_assoc($ppres);
//
//            $orderManagementID = $pprow['orderManagementId'];

            $gdnNumberPrefix = PREFIX_GDN;
            $gdnDATA = "";
            $res     = $db->query("SELECT * FROM pick_and_pack_documents WHERE active = 1 and gdnId = ?s", $documentId);
            while ($row = mysqli_fetch_assoc($res)) {
                $gdnDATA = $row;
            }
            if(empty($gdnDATA['gdnNumber'])){
                $gdnNumber = getNextNewGDNNumber();
            } else {
                $gdnNumber = $gdnDATA['gdnNumber'];
            }

            $gdnNumberFull = $gdnNumberPrefix . $gdnNumber;

            $res = $db->query("UPDATE `pick_and_pack_documents` SET `gdnStatus`= ?s, `procurementManagerGDNApproved` = 'APPROVED', `gdnNumber`=?s, `gdnNumberPrefix`=?s  WHERE `gdnId`=?s", $status, $gdnNumber, $gdnNumberPrefix, $documentId);
//            $res = $db->query("UPDATE `pick_and_pack_line_items`
//                                                SET `orderWHStatus` = ?s WHERE `orderManagementId` = ?s AND `gdnId` = ?s",
//                $orderWHStatus, $orderManagementID, $documentId);
//            recordKeyDocumentHistory($documentId, $status,logActivity($status) );

            // ===== ACCOUNTING ENTRIES =====
            $shippingAmount = $gdnDATA['shippingCost'] ?? 0;
            $taxes = $gdnDATA['taxes'] ?? 0;
            $customDuties = $gdnDATA['customDuties'] ?? 0;
            $foreignTransactionFee = $gdnDATA['foreignTransactionFee'] ?? 0;
            $insurance = $gdnDATA['insurance'] ?? 0;
            $surcharge = $gdnDATA['surcharge'] ?? 0;
            $other = $gdnDATA['other'] ?? 0;

            $totalPayable = $shippingAmount + $customDuties + $foreignTransactionFee + $insurance + $surcharge + $other;

            $shippingExpenseID = getAccountIDByName('Shipping Expense');
            $customDutyID = getAccountIDByName('Custom Duty Expense');
            $bankChargeID = getAccountIDByName('Bank Charges');
            $insuranceID = getAccountIDByName('Insurance');
            $surchargeID = getAccountIDByName('Surcharge');
            $otherCostID = getAccountIDbyName('Other Import Costs');
            $vatReceivableID = getAccountIDbyName('VAT Receivable');
            $accountsPayableID = getAccountIDbyName('Accounts Payable');

            $db->query("INSERT INTO journal_entries (entryDate, description, referenceId, referenceType, companyId) 
                            VALUES(now(), ?s, ?s, ?s, ?s)",
                            "Shipping Amount for the GDN #". $gdnNumberFull, $documentId, "SHIPPINGAMOUNT", $companyId);
            $journalEntryId = $db->insertId();

            if($shippingAmount > 0) {
                $db->query("INSERT INTO journal_entry_lines (journalEntryId, accountId, debit, credit, description)
                                VALUES(?s, ?s, ?s, ?s, ?s)", $journalEntryId, $shippingExpenseID, $shippingAmount, 0, "Shipping Cost for the GDN #". $gdnNumberFull);
            }

            if($customDuties > 0) {
                $db->query("INSERT INTO journal_entry_lines (journalEntryId, accountId, debit, credit, description)
                                VALUES(?s, ?s, ?s, ?s, ?s)", $journalEntryId, $customDutyID, $customDuties, 0, "Custom Duties for the GDN #". $gdnNumberFull);
            }

            if($taxes > 0) {
                $db->query("INSERT INTO journal_entry_lines (journalEntryId, accountId, debit, credit, description)
                                VALUES(?s, ?s, ?s, ?s, ?s)", $journalEntryId, $vatReceivableID, $taxes, 0, "VAT Amount for the GDN #". $gdnNumberFull);
            }

            if($foreignTransactionFee > 0) {
                $db->query("INSERT INTO journal_entry_lines (journalEntryId, accountId, debit, credit, description)
                                VALUES(?s, ?s, ?s, ?s, ?s)", $journalEntryId, $bankChargeID, $foreignTransactionFee, 0, "Foreign Transaction Fee for the GDN #". $gdnNumberFull);
            }

            if($insurance > 0) {
                $db->query("INSERT INTO journal_entry_lines (journalEntryId, accountId, debit, credit, description)
                                VALUES(?s, ?s, ?s, ?s, ?s)", $journalEntryId, $insuranceID, $insurance, 0, "Insurance Amount for the GDN #". $gdnNumberFull);
            }

            if($surcharge > 0) {
                $db->query("INSERT INTO journal_entry_lines (journalEntryId, accountId, debit, credit, description)
                                VALUES(?s, ?s, ?s, ?s, ?s)", $journalEntryId, $surchargeID, $surcharge, 0, "Surcharge for the GDN #". $gdnNumberFull);
            }

            if($other > 0) {
                $db->query("INSERT INTO journal_entry_lines (journalEntryId, accountId, debit, credit, description)
                                VALUES(?s, ?s, ?s, ?s, ?s)", $journalEntryId, $otherCostID, $other, 0, "Other Import Cost for the GDN #". $gdnNumberFull);
            }

            if($totalPayable > 0) {
                $db->query("INSERT INTO journal_entry_lines (journalEntryId, accountId, debit, credit, description)
                                VALUES(?s, ?s, ?s, ?s, ?s)", $journalEntryId, $accountsPayableID, 0, $totalPayable, "Total Accounts Payable for the GDN #". $gdnNumberFull);
            }


            echo "SUCCESS|Goods Delivery Note has been Approved";
            exit();
        }

        if ($status == GDN_STATUS_PROCUREMENT_MANAGER_REJECTED) {
            $res = $db->query("UPDATE `pick_and_pack_documents` SET `gdnStatus`= ?s, `procurementManagerGDNApproved` = 'REJECTED' WHERE `gdnId`=?s", $status, $documentId);
            $res = $db->query("INSERT INTO `pick_and_pack_reject_reason_procurement_manager` (`gdnId`, `rejectReason`, `rejectedBy`) VALUES (?s, ?s, ?s)", $documentId, $rejectReason, getAuthenticatedUser()->userID);
//            recordKeyDocumentHistory($documentId, $status,logActivity($status) );
            echo "SUCCESS|Goods Delivery Note has been Rejected";
            exit();
        }

        if($status == GDN_CREATED) {
            $gdnNumberPrefix = PREFIX_GDN;
            $gdnDATA = "";
            $res     = $db->query("SELECT * FROM pick_and_pack_documents WHERE active = 1 and gdnId = ?s", $documentId);
            while ($row = mysqli_fetch_assoc($res)) {
                $gdnDATA = $row;
            }
            if(empty($gdnDATA['gdnNumber'])){
                $gdnNumber = getNextNewGDNNumber();
            } else {
                $gdnNumber = $gdnDATA['gdnNumber'];
            }
            $res = $db->query("UPDATE `pick_and_pack_documents` SET `gdnStatus`= ?s, `gdnNumber`=?s, `gdnNumberPrefix`=?s WHERE `gdnId`=?s", $status, $gdnNumber, $gdnNumberPrefix, $documentId);
//            recordKeyDocumentHistory($documentId, $status,logActivity($status) );
            echo "SUCCESS|Goods Delivery Note has been Created";
            exit();
        }
    }

} catch (Exception $e) {
    error_log("Error inserting document info: " . $e->getMessage());
    echo "ERROR|" . $e->getMessage();
    exit();
}