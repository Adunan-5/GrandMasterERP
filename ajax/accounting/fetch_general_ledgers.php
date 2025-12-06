<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

try {

    $companyId = intval($_SESSION['SES_SELECTED_COMPANY']);

    $baseQuery = "SELECT 
    coa.accountName,
    je.entryDate,
    jel.description,
    jel.debit,
    jel.credit,
    (
        SELECT SUM(jel2.debit - jel2.credit)
        FROM journal_entry_lines jel2
        JOIN journal_entries je2 ON je2.id = jel2.journalEntryId
        WHERE jel2.accountId = jel.accountId
        AND (je2.entryDate < je.entryDate 
             OR (je2.entryDate = je.entryDate AND je2.id < je.id) 
             OR (je2.entryDate = je.entryDate AND je2.id = je.id AND jel2.id <= jel.id))
    ) AS runningBalance
FROM journal_entry_lines jel
JOIN journal_entries je ON je.id = jel.journalEntryId
JOIN chart_of_accounts coa ON coa.id = jel.accountId
WHERE je.companyId = ?s
ORDER BY coa.accountName, je.entryDate, je.id, jel.id;
";

$res = $db->query($baseQuery, $companyId);

 // Fetch data
 $data = [];
 while ($row = mysqli_fetch_assoc($res)) {

    $row['entryDate'] = formatDate($row['entryDate']);
    $data[] = $row;
}

    // Response format for DataTables
    $response = [
        "data" => $data
    ];

    echo json_encode($response);
} catch (Exception $e) {
    echo json_encode([
        "error" => "An error occurred: " . $e->getMessage()
    ]);
}