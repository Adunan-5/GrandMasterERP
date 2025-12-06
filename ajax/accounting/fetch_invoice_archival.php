<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

try {
    $companyId = intval($_SESSION['SES_SELECTED_COMPANY']);

    // Get Account IDs
    $accountsReceivableID = getAccountIDByName('Accounts Receivable');
    if (!$accountsReceivableID) {
        throw new Exception("Accounts Receivable account not found.");
    }
    $accountsPayableID = getAccountIDByName('Accounts Payable');
    if (!$accountsPayableID) {
        throw new Exception("Accounts Payable account not found.");
    }

    // Get all quarters with relevant transactions
    $quartersQuery = "
        SELECT DISTINCT DATE_FORMAT(entryDate, '%Y-%m-01') AS month
        FROM journal_entries
        WHERE companyId = ?s
        AND (
            EXISTS (
                SELECT 1 FROM journal_entry_lines jel
                WHERE jel.journalEntryId = journal_entries.id
                AND jel.accountId IN (?s, ?s)
            )
        )
        ORDER BY month";
    $months = $db->getCol($quartersQuery, $companyId, $accountsReceivableID, $accountsPayableID);

    $quarters = [];
    foreach ($months as $month) {
        $quarter = ceil((int) substr($month, 5, 2) / 3);
        $year = substr($month, 0, 4);
        $quarterKey = "$year-Q$quarter";
        if (!in_array($quarterKey, $quarters)) {
            $quarters[] = $quarterKey;
        }
    }

    $data = [];

    foreach ($quarters as $quarterKey) {
        list($year, $q) = explode('-Q', $quarterKey);
        $quarterNum = (int) $q;

        // Determine the start and end dates of the quarter
        $startMonth = ($quarterNum - 1) * 3 + 1; // Q1: 1 (Jan), Q2: 4 (Apr), etc.
        $endMonth = $startMonth + 2; // Q1: 3 (Mar), Q2: 6 (Jun), etc.
        $startDate = "$year-" . str_pad($startMonth, 2, '0', STR_PAD_LEFT) . "-01";
        $endMonthFirstDay = "$year-" . str_pad($endMonth, 2, '0', STR_PAD_LEFT) . "-01";
        $endMonthLastDay = date('t', strtotime($endMonthFirstDay)); // Last day of the month
        $endDate = "$year-" . str_pad($endMonth, 2, '0', STR_PAD_LEFT) . "-$endMonthLastDay";

        // Calculate Sales Invoices, Purchase Invoices, and count of Sales Invoices for the quarter
        $quarterQuery = "
            SELECT 
                (SELECT COALESCE(SUM(jel.credit), 0)
                 FROM journal_entry_lines jel
                 JOIN journal_entries je ON je.id = jel.journalEntryId
                 WHERE je.companyId = ?s
                 AND jel.accountId = ?s
                 AND je.entryDate BETWEEN ?s AND ?s) AS sales_invoices,
                (SELECT COALESCE(SUM(jel.debit), 0)
                 FROM journal_entry_lines jel
                 JOIN journal_entries je ON je.id = jel.journalEntryId
                 WHERE je.companyId = ?s
                 AND jel.accountId = ?s
                 AND je.entryDate BETWEEN ?s AND ?s) AS purchase_invoices,
                (SELECT COUNT(DISTINCT je.id)
                 FROM journal_entry_lines jel
                 JOIN journal_entries je ON je.id = jel.journalEntryId
                 WHERE je.companyId = ?s
                 AND jel.accountId = ?s
                 AND je.referenceType = 'INVOICE'
                 AND je.entryDate BETWEEN ?s AND ?s) AS sales_invoice_count,
                 (SELECT COUNT(DISTINCT je.id)
                 FROM journal_entry_lines jel
                 JOIN journal_entries je ON je.id = jel.journalEntryId
                 WHERE je.companyId = ?s
                 AND jel.accountId = ?s
                 AND je.referenceType = 'PURCHASEORDER'
                 AND je.entryDate BETWEEN ?s AND ?s) AS purchase_invoice_count";

        $result = $db->getRow($quarterQuery, $companyId, $accountsReceivableID, $startDate, "$endDate 23:59:59", $companyId, $accountsPayableID, $startDate, "$endDate 23:59:59", $companyId, $accountsReceivableID, $startDate, "$endDate 23:59:59", $companyId, $accountsPayableID, $startDate, "$endDate 23:59:59");

        $salesInvoices = $result['sales_invoices'] ?? 0;
        $purchaseInvoices = $result['purchase_invoices'] ?? 0;
        $salesInvoiceCount = $result['sales_invoice_count'] ?? 0;
        $purchaseInvoiceCount = $result['purchase_invoice_count'] ?? 0;

        $quarterDate = "$year-" . str_pad($startMonth, 2, '0', STR_PAD_LEFT) . "-01";
        $quarterNames = getBilingualQuarter($quarterDate) ?? [
            'english' => "Q$quarterNum",
            'english_range' => '',
            'arabic' => "الربع " . ($quarterNum == 1 ? "الأول" : ($quarterNum == 2 ? "الثاني" : ($quarterNum == 3 ? "الثالث" : "الرابع"))),
            'year' => $year
        ];

        $data[] = [
            'englishQuarter' => $quarterNames['english'],
            'englishQuarterRange' => $quarterNames['english_range'],
            'arabicQuarter' => $quarterNames['arabic'],
            'year' => $quarterNames['year'],
            'salesInvoices' => number_format($salesInvoices, 2),
            'purchaseInvoices' => number_format($purchaseInvoices, 2),
            'rawSalesInvoices' => $salesInvoices,
            'rawPurchaseInvoices' => $purchaseInvoices,
            'salesInvoiceCount' => (int) $salesInvoiceCount,
            'purchaseInvoiceCount' => (int) $purchaseInvoiceCount
        ];
    }

    echo json_encode([
        "data" => $data
    ]);

} catch (Exception $e) {
    error_log("Error generating Invoice Archival report: " . $e->getMessage());
    echo json_encode([
        "error" => "An error occurred while generating the Invoice Archival report."
    ]);
}
?>