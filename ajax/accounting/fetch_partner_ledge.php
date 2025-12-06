<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

try {
    $companyId = intval($_SESSION['SES_SELECTED_COMPANY']);

    $dateFrom = $_GET['dateFrom'] ?? null;
    $dateTo = $_GET['dateTo'] ?? null;

    $whereDate = '';
    $params = [$companyId, $companyId]; // Two companyId placeholders for UNION

    if (!empty($dateFrom) && !empty($dateTo)) {
        $whereDate = "AND je.entryDate BETWEEN ?s AND ?s";
        $params[] = $dateFrom;
        $params[] = $dateTo;
        $params[] = $dateFrom; // Duplicate for second SELECT
        $params[] = $dateTo;
    }

    $query = "SELECT 
                je.entryDate,
                je.referenceType,
                jel.debit,
                jel.credit,
                coa.accountName AS accountName,
                ar.customerId,
                c.companyName AS partnerName,
                ar.totalAmount,
                ar.receivedAmount,
                ar.dueDate,
                ar.status
              FROM journal_entries je
              LEFT JOIN journal_entry_lines jel ON je.id = jel.journalEntryId
              LEFT JOIN chart_of_accounts coa ON jel.accountId = coa.id
              LEFT JOIN accounts_receivable ar ON je.referenceId = ar.invoiceId 
                  AND je.referenceType IN ('INVOICE', 'CUSTOMERPAYMENT')
              LEFT JOIN customers c ON ar.customerId = c.customerId
              WHERE je.companyId = ?s 
              AND coa.accountName = 'Accounts Receivable'
              AND je.referenceType IN ('INVOICE', 'CUSTOMERPAYMENT')
              $whereDate
              UNION
              SELECT 
                je.entryDate,
                je.referenceType,
                jel.debit,
                jel.credit,
                coa.accountName AS accountName,
                ap.supplierId,
                s.companyName AS partnerName,
                ap.totalAmount,
                ap.paidAmount,
                ap.dueDate,
                ap.status
              FROM journal_entries je
              LEFT JOIN journal_entry_lines jel ON je.id = jel.journalEntryId
              LEFT JOIN chart_of_accounts coa ON jel.accountId = coa.id
              LEFT JOIN accounts_payable ap ON je.referenceId = ap.poId 
                  AND je.referenceType IN ('PURCHASEORDER', 'PURCHASEPAYMENT')
              LEFT JOIN suppliers s ON ap.supplierId = s.supplierId
              WHERE je.companyId = ?s 
              AND coa.accountName = 'Accounts Payable'
              AND je.referenceType IN ('PURCHASEORDER', 'PURCHASEPAYMENT')
              $whereDate
              ORDER BY entryDate";

    $res = $db->query($query, ...$params);

    $partnerLedger = [];
    $totals = [
        "totalReceivables" => 0,
        "totalPayables" => 0
    ];
    $runningBalances = [];

    while ($row = mysqli_fetch_assoc($res)) {
        // Determine partnerId based on account type
        $partnerId = $row['accountName'] === 'Accounts Receivable' ? ($row['customerId'] ?? null) : ($row['supplierId'] ?? null);
        $partnerName = $row['partnerName'] ?? 'Unknown Partner';
        $balance = $row['debit'] - $row['credit'];

        // Use running balance as outstanding if totalAmount is not available
        $outstanding = isset($row['totalAmount']) ? max(0, ($row['totalAmount'] - ($row['receivedAmount'] ?? 0) - ($row['paidAmount'] ?? 0))) : $balance;

        // Group running balance by partnerId and accountName to avoid overlap
        $balanceKey = $partnerId ? $partnerId . '_' . $row['accountName'] : md5($partnerName . $row['accountName']);
        if (!isset($runningBalances[$balanceKey])) {
            $runningBalances[$balanceKey] = 0;
        }
        $runningBalances[$balanceKey] += $balance;

        $partnerLedger[] = [
            "entryDate" => $row['entryDate'],
            "partnerName" => $partnerName,
            "accountName" => $row['accountName'],
            "referenceType" => $row['referenceType'],
            "debit" => number_format($row['debit'], 2, '.', ''),
            "credit" => number_format($row['credit'], 2, '.', ''),
            "runningBalance" => number_format($runningBalances[$balanceKey], 2, '.', ''),
            "outstanding" => number_format($outstanding, 2, '.', ''),
            "dueDate" => $row['dueDate'],
            "status" => $row['status']
        ];

        if ($row['accountName'] === 'Accounts Receivable') {
            $totals["totalReceivables"] += $outstanding > 0 ? $outstanding : 0;
        } elseif ($row['accountName'] === 'Accounts Payable') {
            $totals["totalPayables"] += $outstanding > 0 ? $outstanding : 0;
        }
    }

    // Format final response
    $response = [
        "data" => $partnerLedger,
        "summary" => [
            "totalReceivables" => number_format($totals["totalReceivables"], 2, '.', ''),
            "totalPayables" => number_format($totals["totalPayables"], 2, '.', '')
        ]
    ];

    echo json_encode($response);
} catch (Exception $e) {
    echo json_encode([
        "error" => "An error occurred: " . $e->getMessage()
    ]);
}