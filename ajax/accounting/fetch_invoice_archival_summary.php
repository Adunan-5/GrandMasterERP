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
    $accountsReceivableID = getAccountIDByName('Accounts Receivable');
    if (!$accountsReceivableID) {
        throw new Exception("Accounts Receivable account not found.");
    }
    $accountsPayableID = getAccountIDByName('Accounts Payable');
    if (!$accountsPayableID) {
        throw new Exception("Accounts Payable account not found.");
    }

    // Calculate quarter date range (Saudi timezone)
    $startMonth = ($quarter - 1) * 3 + 1;
    $endMonth = $startMonth + 2;
    $startDate = "$year-" . str_pad($startMonth, 2, '0', STR_PAD_LEFT) . "-01";
    $endMonthFirstDay = "$year-" . str_pad($endMonth, 2, '0', STR_PAD_LEFT) . "-01";
    $lastDayOfEndMonth = date('t', strtotime($endMonthFirstDay));
    $endDate = "$year-" . str_pad($endMonth, 2, '0', STR_PAD_LEFT) . "-$lastDayOfEndMonth 23:59:59";

    // 1. Get Sales Invoices (Accounts Receivable, credit amounts)
    $salesQuery = "
        SELECT 
            c.companyName AS customer_name,
            id.invoiceId AS invoice_id,
            jel.debit AS invoice_amount
        FROM journal_entry_lines jel
        JOIN journal_entries je ON je.id = jel.journalEntryId
        LEFT JOIN invoice_documents id ON id.invoiceId = je.referenceId AND je.referenceType = 'INVOICE'
        LEFT JOIN customers c ON c.customerId = id.customerId
        WHERE jel.accountId = ?s
        AND je.companyId = ?s
        AND je.referenceType = 'INVOICE'
        AND je.entryDate BETWEEN ?s AND ?s
        ORDER BY je.entryDate";

    $salesInvoices = $db->getAll($salesQuery, $accountsReceivableID, $companyId, $startDate, $endDate);
    if ($salesInvoices === false) {
        throw new Exception("Failed to fetch sales invoices: " . $db->lastError());
    }

    // 2. Get Purchase Invoices (Accounts Payable, debit amounts)
    $purchaseQuery = "
        SELECT 
            s.companyName AS supplier_name,
            po.poId AS purchase_order_number,
            jel.credit AS invoice_amount
        FROM journal_entry_lines jel
        JOIN journal_entries je ON je.id = jel.journalEntryId
        LEFT JOIN purchase_orders po ON po.poId = je.referenceId AND je.referenceType = 'PURCHASEORDER'
        LEFT JOIN suppliers s ON s.supplierId = po.supplierId
        WHERE jel.accountId = ?s
        AND je.companyId = ?s
        AND je.referenceType = 'PURCHASEORDER'
        AND je.entryDate BETWEEN ?s AND ?s
        ORDER BY je.entryDate";

    $purchaseInvoices = $db->getAll($purchaseQuery, $accountsPayableID, $companyId, $startDate, $endDate);
    if ($purchaseInvoices === false) {
        throw new Exception("Failed to fetch purchase invoices: " . $db->lastError());
    }

    // 3. Combine transactions into the desired format
    $data = [];
    foreach ($salesInvoices as $txn) {
        $data[] = [
            'party' => $txn['customer_name'] ?? "-",
            'invoiceAmount' => number_format($txn['invoice_amount'], 2),
            'file' => '/ajax/invoice/generate_pdf.php?invoiceID=' . $txn['invoice_id'],
            'invoiceNo' => getInvoiceNumberFromInvoiceID($txn['invoice_id']),
            'type' => "Sales Invoice",
            'id' => $txn['invoice_id'],
            'reference' => getInvoiceNumberFromInvoiceID($txn['invoice_id']) ?? "-"
        ];
    }
    foreach ($purchaseInvoices as $txn) {
        $data[] = [
            'party' => $txn['supplier_name'] ?? "-",
            'invoiceAmount' => number_format($txn['invoice_amount'], 2),
            'file' => '/uploads/' . getSupplierInvoiceFromPoID($txn['purchase_order_number'])['supplierInvoiceAttachment'],
            'invoiceNo' => getSupplierInvoiceFromPoID($txn['purchase_order_number'])['supplierInvoiceNo'],
            'type' =>  "Purchase Invoice",
            'id' => $txn['purchase_order_number'],
            'reference' => getPONumberFromDocumentID($txn['purchase_order_number']) ?? "-"
        ];
    }

    // 4. Output the response
    echo json_encode([
        "data" => $data
    ]);

} catch (Exception $e) {
    error_log("Invoice Archival Summary Error: " . $e->getMessage() . " | Quarter: $quarter, Year: $year, Company ID: $companyId" . " in " . $e->getFile() . " on line " . $e->getLine());
    echo json_encode([
        'error' => 'An error occurred while generating the Invoice Archival Summary report',
        'details' => $e->getMessage()
    ]);
}
?>