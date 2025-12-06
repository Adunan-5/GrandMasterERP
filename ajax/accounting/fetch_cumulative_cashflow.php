<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

try {
    $companyId = intval($_SESSION['SES_SELECTED_COMPANY']);

    // Get Account Type IDs and Account IDs
    $expenseId = getAccountTypeIDByName('Expense');
    $vatPayableAccountID = getAccountIDByName('VAT Payable');

    // Get all months with relevant transactions
    $monthsQuery = "
        SELECT DISTINCT DATE_FORMAT(entryDate, '%Y-%m-01') AS month 
        FROM journal_entries 
        WHERE companyId = ?s 
        UNION 
        SELECT DISTINCT DATE_FORMAT(paidOn, '%Y-%m-01') AS month 
        FROM payments 
        WHERE companyId = ?s
        ORDER BY month";
    $months = $db->getCol($monthsQuery, $companyId, $companyId);

    $data = [];
    $cumulative = 0;

    foreach ($months as $month) {
        // Calculate components for net cash flow
        $monthQuery = "
            SELECT 
                (SELECT COALESCE(SUM(amount), 0)
                 FROM payments p
                 WHERE p.companyId = ?s
                 AND p.type = 'INCOMING'
                 AND DATE_FORMAT(p.paidOn, '%Y-%m-01') = ?s) AS revenue,
                (SELECT COALESCE(SUM(jel.credit), 0)
                 FROM journal_entry_lines jel
                 JOIN journal_entries je ON je.id = jel.journalEntryId
                 WHERE je.companyId = ?s
                 AND jel.accountId = ?s
                 AND DATE_FORMAT(je.entryDate, '%Y-%m-01') = ?s) AS vat_payable,
                (SELECT COALESCE(SUM(amount), 0)
                 FROM payments p
                 WHERE p.companyId = ?s
                 AND p.type = 'OUTGOING'
                 AND DATE_FORMAT(p.paidOn, '%Y-%m-01') = ?s) AS supplier_payments,
                (SELECT COALESCE(SUM(jel.debit - jel.credit), 0)
                 FROM journal_entry_lines jel
                 JOIN journal_entries je ON je.id = jel.journalEntryId
                 JOIN chart_of_accounts coa ON coa.id = jel.accountId
                 JOIN coa_types ct ON ct.id = coa.coaTypeId
                 WHERE je.companyId = ?s
                 AND ct.id = ?s
                 AND DATE_FORMAT(je.entryDate, '%Y-%m-01') = ?s) AS other_expenses";

        $result = $db->getRow($monthQuery, $companyId, $month, $companyId, $vatPayableAccountID, $month, $companyId, $month, $companyId, $expenseId, $month);

        $revenue = $result['revenue'] ?? 0;
        $vatPayable = $result['vat_payable'] ?? 0;
        $supplierPayments = $result['supplier_payments'] ?? 0;
        $otherExpenses = $result['other_expenses'] ?? 0;
        $expenses = $supplierPayments + $otherExpenses;

        // Net cash flow = Total Revenue (including VAT) - VAT Payable - Expenses
        $netFlow = $revenue - $vatPayable - $expenses;
        $cumulative += $netFlow;

        // Get bilingual month names
        $monthNames = getBilingualMonth($month);

        $data[] = [
            'englishMonth' => $monthNames['english'],
            'arabicMonth' => $monthNames['arabic'],
            'year' => $monthNames['year'],
            'revenue' => number_format($revenue, 2),
            'vatPayable' => number_format($vatPayable, 2),
            'expenses' => number_format($expenses, 2),
            'netCashFlow' => number_format($netFlow, 2),
            'cumulativeCashFlow' => number_format($cumulative, 2),
            'rawRevenue' => $revenue,
            'rawVatPayable' => $vatPayable,
            'rawExpenses' => $expenses,
            'rawNet' => $netFlow,
            'rawCumulative' => $cumulative
        ];
    }

    echo json_encode([
        "data" => $data
    ]);

} catch (Exception $e) {
    error_log("Error generating cash flow: " . $e->getMessage());
    echo json_encode([
        "error" => "An error occurred while generating the cash flow statement."
    ]);
}
?>