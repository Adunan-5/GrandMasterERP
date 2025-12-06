<?php
// Suppress error display in production (errors are logged)
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Set timezone to Saudi Arabia
date_default_timezone_set('Asia/Riyadh');

header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

try {
    $companyId = intval($_SESSION['SES_SELECTED_COMPANY']);
    $quarter = isset($_GET['quarter']) ? intval($_GET['quarter']) : ceil(date('n')/3); // Default to current quarter
    $year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

    // Validate quarter (1-4)
    if ($quarter < 1 || $quarter > 4) {
        throw new Exception("Invalid quarter specified");
    }

    // Get account IDs
    $vatPayableId = getAccountIDByName('VAT Payable');
    if (!$vatPayableId) {
        throw new Exception("VAT Payable account not found.");
    }
    $vatReceivableId = getAccountIDByName('VAT Receivable');
    if (!$vatReceivableId) {
        throw new Exception("VAT Receivable account not found.");
    }

    // Calculate quarter date range (Saudi timezone)
    $startMonth = ($quarter - 1) * 3 + 1;
    $endMonth = $startMonth + 2;
    $startDate = "$year-" . str_pad($startMonth, 2, '0', STR_PAD_LEFT) . "-01";
    $endMonthFirstDay = "$year-" . str_pad($endMonth, 2, '0', STR_PAD_LEFT) . "-01";
    $lastDayOfEndMonth = date('t', strtotime($endMonthFirstDay));
    $endDate = "$year-" . str_pad($endMonth, 2, '0', STR_PAD_LEFT) . "-$lastDayOfEndMonth 23:59:59";

    // 1. Get VAT Payable transactions (sales with VAT)
    $payableQuery = "
        SELECT 
            c.companyName AS customer_name,
            id.invoiceId AS invoice_id,
            jel.credit AS vat_amount
        FROM journal_entry_lines jel
        JOIN journal_entries je ON je.id = jel.journalEntryId
        LEFT JOIN invoice_documents id ON id.invoiceId = je.referenceId AND je.referenceType = 'INVOICE'
        LEFT JOIN customers c ON c.customerId = id.customerId
        WHERE jel.accountId = ?s
        AND je.companyId = ?s
        AND je.entryDate BETWEEN ?s AND ?s
        ORDER BY je.entryDate";

    $vatPayableTransactions = $db->getAll($payableQuery, $vatPayableId, $companyId, $startDate, $endDate);

    // 2. Get VAT Receivable transactions (purchases with VAT, excluding shipping amounts)
    $receivableQuery = "
        SELECT 
            s.companyName AS supplier_name,
            po.poId AS purchase_order_number,
            jel.debit AS vat_amount
        FROM journal_entry_lines jel
        JOIN journal_entries je ON je.id = jel.journalEntryId
        LEFT JOIN purchase_orders po ON po.poId = je.referenceId AND je.referenceType IN ('PURCHASEORDER', 'EXPENSE')
        LEFT JOIN suppliers s ON s.supplierId = po.supplierId
        WHERE jel.accountId = ?s
        AND je.companyId = ?s
        AND je.entryDate BETWEEN ?s AND ?s
        AND je.referenceType != 'SHIPPINGAMOUNT'
        ORDER BY je.entryDate";

    $vatReceivableTransactions = $db->getAll($receivableQuery, $vatReceivableId, $companyId, $startDate, $endDate);

    // 3. Get Shipping transactions (VAT on shipping amounts)
//    $shippingQuery = "
//        SELECT
//            pp.carrier as carrier_name,
//            pp.gdnId as gdn_number,
//            jel.debit AS vat_amount
//        FROM journal_entry_lines jel
//        JOIN journal_entries je ON je.id = jel.journalEntryId
//        LEFT JOIN pick_and_pack_documents pp ON pp.gdnId = je.referenceId and je.referenceType = 'SHIPPINGAMOUNT'
//        WHERE jel.accountId = ?s
//        AND je.companyId = ?s
//        AND je.entryDate BETWEEN ?s AND ?s
//        ORDER BY je.entryDate";
//
//    $shippingTransactions = $db->getAll($shippingQuery, $vatReceivableId, $companyId, $startDate, $endDate);

    // 4. Combine transactions into the desired format
    $data = [];
    foreach ($vatPayableTransactions as $txn) {
        $data[] = [
            'party' => $txn['customer_name'] ?? "-",
            'vatPayable' => number_format($txn['vat_amount'], 2),
            'vatReceivable' => "-",
            'reference' => getInvoiceNumberFromInvoiceID($txn['invoice_id']) ?? "-"
        ];
    }
    foreach ($vatReceivableTransactions as $txn) {
        $data[] = [
            'party' => $txn['supplier_name'] ?? "-",
            'vatPayable' => "-",
            'vatReceivable' => number_format($txn['vat_amount'], 2),
            'reference' => getPONumberFromDocumentID($txn['purchase_order_number']) ?? "-"
        ];
    }
//    foreach ($shippingTransactions as $txn) {
//        $data[] = [
//            'party' => $txn['carrier_name'] ?? "-",
//            'customer' => "-",
//            'vatPayable' => "-",
//            'vatReceivable' => number_format($txn['vat_amount'], 2),
//            'reference' => getGDNNumberFromDocumentID($txn['gdn_number']) ?? "-"
//        ];
//    }

    // 5. Output the response
    echo json_encode([
        "data" => $data
    ]);

} catch (Exception $e) {
    error_log("VAT Report Error: " . $e->getMessage() . " | Quarter: $quarter, Year: $year, Company ID: $companyId");
    echo json_encode([
        'error' => 'An error occurred while generating the VAT report',
        'details' => $e->getMessage()
    ]);
}
?>