<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

try {
    $companyId = intval($_SESSION['SES_SELECTED_COMPANY']);

    // Get date range from POST (optional, defaults to current year)
    $startDate = isset($_POST['startDate']) ? $_POST['startDate'] : '2025-01-01';
    $endDate = isset($_POST['endDate']) ? $_POST['endDate'] : '2025-12-31';

    // Validate date format
    if (!DateTime::createFromFormat('Y-m-d', $startDate) || !DateTime::createFromFormat('Y-m-d', $endDate)) {
        throw new Exception("Invalid date format. Use YYYY-MM-DD.");
    }

    // Get Sales Revenue account ID
    $salesRevenueId = getAccountIDByName('Sales Revenue');

    // Get all months with sales transactions within the date range
    $monthsQuery = "
        SELECT DISTINCT DATE_FORMAT(je.entryDate, '%Y-%m-01') AS month
        FROM journal_entries je
        JOIN journal_entry_lines jel ON je.id = jel.journalEntryId
        WHERE je.companyId = ?s
        AND jel.accountId = ?s
        AND jel.credit > 0
        AND je.entryDate BETWEEN ?s AND ?s
        ORDER BY month";
    $months = $db->getCol($monthsQuery, $companyId, $salesRevenueId, $startDate, $endDate);

    $data = [];
    $cumulativeTurnover = 0;

    foreach ($months as $month) {
        // Calculate turnover (Sales Revenue credits) for the month
        $turnoverQuery = "
            SELECT COALESCE(SUM(jel.credit), 0) AS turnover
            FROM journal_entry_lines jel
            JOIN journal_entries je ON je.id = jel.journalEntryId
            WHERE je.companyId = ?s
            AND jel.accountId = ?s
            AND jel.credit > 0
            AND DATE_FORMAT(je.entryDate, '%Y-%m-01') = ?s";
        $turnover = $db->getOne($turnoverQuery, $companyId, $salesRevenueId, $month) ?? 0;
        $cumulativeTurnover += $turnover;

        // Get bilingual month names
        $monthNames = getBilingualMonth($month);

        $data[] = [
            'englishMonth' => $monthNames['english'],
            'arabicMonth' => $monthNames['arabic'],
            'year' => $monthNames['year'],
            'turnover' => number_format($turnover, 2),
            'cumulativeTurnover' => number_format($cumulativeTurnover, 2),
            'rawTurnover' => $turnover,
            'rawCumulativeTurnover' => $cumulativeTurnover
        ];
    }

    echo json_encode([
        "data" => $data
    ]);

} catch (Exception $e) {
    error_log("Error generating turnover report: " . $e->getMessage());
    echo json_encode([
        "error" => "An error occurred while generating the turnover report."
    ]);
}
?>