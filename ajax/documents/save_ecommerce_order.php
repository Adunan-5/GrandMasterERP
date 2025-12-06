<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

// Read and decode raw POST JSON data
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

// Validate input
if (!$data || !isset($data['lineItems']['line-item']) || !isset($data['documentId'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing data']);
    exit();
}

// Extract data
$lineItems       = $data['lineItems']['line-item'];
$customerID      = $data['customerID'] ?? null;
$orderDate       = $data['orderDate'] ?? null;
$paymentTerms    = $data['paymentTerms'] ?? null;
$documentId      = $data['documentId'];
$carrier         = $data['carrier'] ?? '';
$trackingNumber  = $data['trackingNumber'] ?? '';

try {
    // Update key_documents
    $db->query(
        "UPDATE `key_documents` SET `paymentTermId` = ?s, `carrier` = ?s, `trackingNumber` = ?s WHERE `documentId` = ?s",
        $paymentTerms, $carrier, $trackingNumber, $documentId
    );
} catch (Exception $e) {
    error_log("Error updating document info: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => "Document update error: " . $e->getMessage()]);
    exit();
}

// Process each line item
foreach ($lineItems as $values) {
    $subTotal              = $values['subTotal'];
    $sparepartitem         = $values['sparepartitem'] ?? '';
    $shippingVatPercentage = $values['shippingVatPercentage'] ?? 15;
    $shippingAmount        = $values['shippingAmount'] ?? 0;
    $shippingSubTotal      = $values['shippingSubTotal'] ?? 0;

    if (!empty($sparepartitem)) {
        try {
            $db->query(
                "UPDATE `line_items` SET `shippingVatPercentage` = ?s, `shippingAmount` = ?s, `shippingSubTotal` = ?s, `subTotal` = ?s 
                WHERE `documentId` = ?s AND `itemId` = ?s",
                $shippingVatPercentage, $shippingAmount, $shippingSubTotal, $subTotal, $documentId, $sparepartitem
            );
        } catch (Exception $e) {
            error_log("Error updating line item: " . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => "Line item update error: " . $e->getMessage()]);
            exit();
        }
    }
}

// Final response
echo json_encode([
    'status' => 'success',
    'message' => 'Order updated successfully.',
    'documentId' => $documentId
]);
