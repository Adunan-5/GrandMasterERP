<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

try {
    $companyId = intval($_SESSION['SES_SELECTED_COMPANY']);

    // Handle optional filters: dateFrom and dateTo (for quarter or custom range)
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
                cat.name AS coaTypeName,
                cat.id AS coaTypeId,
                IFNULL(SUM(jel.debit), 0) AS totalDebit,
                IFNULL(SUM(jel.credit), 0) AS totalCredit
              FROM chart_of_accounts coa
              LEFT JOIN coa_types cat ON coa.coaTypeId = cat.id
              LEFT JOIN journal_entry_lines jel ON coa.id = jel.accountId
              LEFT JOIN journal_entries je ON je.id = jel.journalEntryId AND je.companyId = ?s
              $whereDate
              GROUP BY coa.id, coa.accountName, cat.name, cat.id
              ORDER BY cat.name, coa.accountName";

    $res = $db->query($query, ...$params);

    $data = [];
    $totalDebit = 0;
    $totalCredit = 0;

    while ($row = mysqli_fetch_assoc($res)) {
        $row['accountType'] = $row['coaTypeName'];
        $row['balanceType'] = ($row['totalDebit'] - $row['totalCredit']) >= 0 ? 'Debit' : 'Credit';
        $row['outstandingBalance'] = number_format(abs($row['totalDebit'] - $row['totalCredit']), 2, '.', '');

        $totalDebit += $row['totalDebit'];
        $totalCredit += $row['totalCredit'];

        $data[] = $row;
    }

    $response = [
        "data" => $data,
        "summary" => [
            "totalDebit" => number_format($totalDebit, 2, '.', ''),
            "totalCredit" => number_format($totalCredit, 2, '.', '')
        ]
    ];

    echo json_encode($response);
} catch (Exception $e) {
    echo json_encode([
        "error" => "An error occurred: " . $e->getMessage()
    ]);
}
