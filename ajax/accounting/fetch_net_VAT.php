<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

try {
    $companyId = intval($_SESSION['SES_SELECTED_COMPANY']);

    // Get Account IDs
    $vatPayableAccountID = getAccountIDByName('VAT Payable');
    $vatReceivableAccountID = getAccountIDByName('VAT Receivable');

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
    $months = $db->getCol($quartersQuery, $companyId, $vatPayableAccountID, $vatReceivableAccountID);

    // Map months to quarters
    $quarters = [];
    foreach ($months as $month) {
        $quarter = ceil((int) substr($month, 5, 2) / 3); // e.g., Jan (01) → Q1, Apr (04) → Q2
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

        // Determine the start and end months of the quarter
        $startMonth = ($quarterNum - 1) * 3 + 1; // Q1: 1 (Jan), Q2: 4 (Apr), etc.
        $endMonth = $startMonth + 2; // Q1: 3 (Mar), Q2: 6 (Jun), etc.
        $startDate = "$year-" . str_pad($startMonth, 2, '0', STR_PAD_LEFT) . "-01";
        $endDate = "$year-" . str_pad($endMonth, 2, '0', STR_PAD_LEFT) . "-01";

        // Calculate VAT Payable and VAT Receivable for the quarter
        $quarterQuery = "
            SELECT 
                (SELECT COALESCE(SUM(jel.credit), 0)
                 FROM journal_entry_lines jel
                 JOIN journal_entries je ON je.id = jel.journalEntryId
                 WHERE je.companyId = ?s
                 AND jel.accountId = ?s
                 AND DATE_FORMAT(je.entryDate, '%Y-%m-01') BETWEEN ?s AND ?s) AS vat_payable,
                (SELECT COALESCE(SUM(jel.debit), 0)
                 FROM journal_entry_lines jel
                 JOIN journal_entries je ON je.id = jel.journalEntryId
                 WHERE je.companyId = ?s
                 AND jel.accountId = ?s
                 AND DATE_FORMAT(je.entryDate, '%Y-%m-01') BETWEEN ?s AND ?s) AS vat_receivable";

        $result = $db->getRow($quarterQuery, $companyId, $vatPayableAccountID, $startDate, $endDate, $companyId, $vatReceivableAccountID, $startDate, $endDate);

        $vatPayable = $result['vat_payable'] ?? 0;
        $vatReceivable = $result['vat_receivable'] ?? 0;
        $netVat = $vatPayable - $vatReceivable;

        $quarterDate = "$year-" . str_pad($startMonth, 2, '0', STR_PAD_LEFT) . "-01";
        $quarterNames = getBilingualQuarter($quarterDate) ?? [
            'english' => "Q$quarterNum",
            'arabic' => "الربع " . ($quarterNum == 1 ? "الأول" : ($quarterNum == 2 ? "الثاني" : ($quarterNum == 3 ? "الثالث" : "الرابع"))),
            'year' => $year
        ];

        $data[] = [
            'englishQuarter' => $quarterNames['english'],
            'englishQuarterRange' => $quarterNames['english_range'],
            'arabicQuarter' => $quarterNames['arabic'],
            'year' => $quarterNames['year'],
            'vatPayable' => number_format($vatPayable, 2),
            'vatReceivable' => number_format($vatReceivable, 2),
            'netVat' => number_format($netVat, 2),
            'rawVatPayable' => $vatPayable,
            'rawVatReceivable' => $vatReceivable,
            'rawNetVat' => $netVat
        ];
    }

    echo json_encode([
        "data" => $data
    ]);

} catch (Exception $e) {
    error_log("Error generating VAT report: " . $e->getMessage());
    echo json_encode([
        "error" => "An error occurred while generating the VAT report."
    ]);
}
?>