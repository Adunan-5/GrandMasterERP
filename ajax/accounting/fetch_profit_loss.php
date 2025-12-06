<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

try {
    $companyId = intval($_SESSION['SES_SELECTED_COMPANY']);

    $dateFrom = $_GET['dateFrom'] ?? null;
    $dateTo = $_GET['dateTo'] ?? null;

    $whereDate = '';
    $params = [$companyId];

    if (!empty($dateFrom) && !empty($dateTo)) {
        $whereDate = "AND je.entryDate BETWEEN ?s AND ?s";
        $params[] = $dateFrom;
        $params[] = $dateTo;
    }

    $query = "SELECT 
                coa.id AS accountId,
                coa.accountName,
                cat.name AS accountType,
                IFNULL(SUM(jel.debit), 0) AS totalDebit,
                IFNULL(SUM(jel.credit), 0) AS totalCredit
              FROM chart_of_accounts coa
              LEFT JOIN coa_types cat ON coa.coaTypeId = cat.id
              LEFT JOIN journal_entry_lines jel ON coa.id = jel.accountId
              LEFT JOIN journal_entries je ON je.id = jel.journalEntryId AND je.companyId = ?s
              $whereDate
              GROUP BY coa.id, coa.accountName, cat.name
              ORDER BY cat.name, coa.accountName";

    $res = $db->query($query, ...$params);

    $profitLoss = [];
    $totals = [
        "Revenue" => 0,
        "Expenses" => 0
    ];

    while ($row = mysqli_fetch_assoc($res)) {
        $accountType = $row['accountType'];
        $balance = $row['totalDebit'] - $row['totalCredit'];

        // Classify accounts based on type name
        if (stripos($accountType, 'Income') !== false) {
            $section = 'Revenue';
            $displayBalance = ($balance < 0) ? -$balance : $balance; // Positive when credits exceed debits
        } elseif (stripos($accountType, 'Expense') !== false) {
            $section = 'Expenses';
            $displayBalance = ($balance > 0) ? $balance : -$balance; // Positive when debits exceed credits
        } else {
            continue; // Skip Asset, Equity, and Liability types for P&L
        }

        $profitLoss[] = [
            "accountId" => $row['accountId'],
            "accountName" => $row['accountName'],
            "accountType" => $row['accountType'],
            "balance" => number_format($displayBalance, 2, '.', '')
        ];

        $totals[$section] += $displayBalance;
    }

    // Calculate net profit or loss
    $netProfit = $totals["Revenue"] - $totals["Expenses"];

    // Format final response
    $response = [
        "data" => $profitLoss,
        "summary" => [
            "totalRevenue" => number_format($totals["Revenue"], 2, '.', ''),
            "totalExpenses" => number_format($totals["Expenses"], 2, '.', ''),
            "netProfit" => number_format($netProfit, 2, '.', '')
        ]
    ];

    echo json_encode($response);
} catch (Exception $e) {
    echo json_encode([
        "error" => "An error occurred: " . $e->getMessage()
    ]);
}