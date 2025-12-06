<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

try {
    $companyId = intval($_SESSION['SES_SELECTED_COMPANY']);

    $query = "SELECT 
                coa.id AS accountId,
                coa.accountName,
                cat.name AS coaTypeName,
                cat.id AS coaTypeId,
                SUM(jel.debit) AS totalDebit,
                SUM(jel.credit) AS totalCredit,
                (SUM(jel.debit) - SUM(jel.credit)) AS outstandingBalance
              FROM chart_of_accounts coa
              LEFT JOIN coa_types cat ON coa.coaTypeId = cat.id
              LEFT JOIN journal_entry_lines jel ON coa.id = jel.accountId
              LEFT JOIN journal_entries je ON je.id = jel.journalEntryId AND je.companyId = ?s
              GROUP BY coa.id, coa.accountName, cat.name, cat.id
              ORDER BY cat.name, coa.accountName";

    $res = $db->query($query, $companyId);

    // Fetch data
    $data = [];
    while ($row = mysqli_fetch_assoc($res)) {

        $row['accountType'] = $row['coaTypeName'];
        $row['totalDebit'] = empty($row['totalDebit']) ? 0 : $row['totalDebit'];
        $row['totalCredit'] = empty($row['totalCredit']) ? 0 : $row['totalCredit'];
        $row['outstandingBalance'] = empty($row['outstandingBalance']) ? 0 : $row['outstandingBalance'];
        $row['balanceType'] = $row['outstandingBalance'] >= 0 ? 'Debit' : 'Credit';
        $data[] = $row;
    }

    $response = [
        "data" => $data
    ];

    echo json_encode($response);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => "An error occurred: " . $e->getMessage()
    ]);
}