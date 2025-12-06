<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../includes/baseIncludes.php";

// Read the raw POST data
$rawData = file_get_contents('php://input');

// Set up logging
$timestamp = date('Ymd_His');
$debugLogFile = "webhook_debug_$timestamp.log";
$logFile = "order_processing_errors.log";
$payloadFile = "webhook_payloads.json"; // Corrected file name for webhook payloads
$webhookSecret = WEBHOOK_SECRET_KEY;

// Verify webhook signature
$secret = getWoocommerceWebHookSecret($webhookSecret);
$signature = base64_encode(hash_hmac('sha256', $rawData, $secret, true));
$receivedSignature = $_SERVER['HTTP_X_WC_WEBHOOK_SIGNATURE'] ?? '';

if (!hash_equals($signature, $receivedSignature)) {
    $errorMessage = ['status' => 'error', 'message' => 'Webhook signature mismatch'];
    file_put_contents($debugLogFile, "[$timestamp] Webhook signature mismatch\n", FILE_APPEND);
    file_put_contents($debugLogFile, "[$timestamp] Received Signature: $receivedSignature\n", FILE_APPEND);
    file_put_contents($debugLogFile, "[$timestamp] Expected Signature: $signature\n", FILE_APPEND);

    // Log the signature mismatch
    $logData = [
        'step' => 'signature_verification',
        'timestamp' => date('c'),
        'error' => 'Webhook signature mismatch',
        'receivedSignature' => $receivedSignature,
        'expectedSignature' => $signature
    ];
    file_put_contents($logFile, json_encode($logData, JSON_PRETTY_PRINT) . "\n", FILE_APPEND);

    echo json_encode($errorMessage);
    exit;
}

// Decode the JSON data
$data = json_decode($rawData, true);

// Append the webhook payload to the JSON file
if ($data) {
    // Read existing payloads from the JSON file
    $existingPayloads = [];
    if (file_exists($payloadFile)) {
        $existingData = file_get_contents($payloadFile);
        $existingPayloads = json_decode($existingData, true) ?: [];
    }

    // Append the new payload
    $existingPayloads[] = $data;

    // Write back to the JSON file in pretty format with error handling
    $writeResult = file_put_contents($payloadFile, json_encode($existingPayloads, JSON_PRETTY_PRINT));
    if ($writeResult === false) {
        $logData = [
            'step' => 'write_payload_to_json',
            'timestamp' => date('c'),
            'error' => 'Failed to write webhook payload to ' . $payloadFile,
            'rawData' => $rawData
        ];
        file_put_contents($logFile, json_encode($logData, JSON_PRETTY_PRINT) . "\n", FILE_APPEND);
    }
}

if ($data) {
    try {
        // Extract customer data
        $customerType = 'E-commerce';
        $customerName = trim(($data['billing']['first_name'] ?? '') . ' ' . ($data['billing']['last_name'] ?? ''));
        $email = $data['billing']['email'] ?? ($data['shipping']['email'] ?? '');
        $paymentTermsName = 'Immediate';
        $salesPaymentTermId = getPaymentTermsIDByName($paymentTermsName);
        $purchasePaymentTermId = getPaymentTermsIDByName($paymentTermsName);
        $currencyName = $data['currency'];
        $currencyId = getCurrencyIDFromName($currencyName);

        // Extract address data
        $addressLine1 = $data['billing']['address_1'] ?? '';
        $addressLine2 = $data['billing']['address_2'] ?? '';
        $city = $data['billing']['city'] ?? '';
        $state = $data['billing']['state'] ?? '';
        $country = $data['billing']['country'] ?? '';
        $postalCode = $data['billing']['postcode'] ?? '';
        $phone = $data['billing']['phone'] ?? ($data['shipping']['phone'] ?? '');

        // Check for existing customer by email
        $customerId = $db->getOne("SELECT customerId FROM customers WHERE email = ?s", $email);

        if (!$customerId) {
            $db->query(
                "INSERT INTO `customers` (
                    `customerType`, `companyName`, `email`, `phone`,
                    `salesPaymentTermId`, `purchasePaymentTermId`, `currencyId`
                ) VALUES (?s, ?s, ?s, ?s, ?s, ?s, ?s)",
                $customerType,
                $customerName,
                $email,
                $phone,
                $salesPaymentTermId,
                $purchasePaymentTermId,
                $currencyId
            );
            $customerId = $db->insertId();
        }

        // Insert billing address into customer_addresses
        $db->query(
            "INSERT INTO `ecommerce_customers_addresses` (
                `customerId`, `addressLine1`, `addressLine2`, 
                `city`, `state`, `country`, `postalCode`, 
                `isBilling`
            ) VALUES (?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s)",
            $customerId,
            $addressLine1,
            $addressLine2,
            $city,
            $state,
            $country,
            $postalCode,
            1 // isBilling
        );

        // Extract order data
        $orderId = $data['id'] ?? null;
        $totalAmount = $data['total'] ?? 0.00;
        $paymentTermId = getPaymentTermsIDByName($paymentTermsName);
        $dateCreated = $data['date_created'] ?? date('Y-m-d\TH:i:s');
        $orderDateIssued = (new DateTime($dateCreated))->format('Y-m-d H:i:s');

        // Check for duplicate order
        $exists = $db->getOne("SELECT COUNT(*) FROM key_documents WHERE orderId = ?s", $orderId);
        if ($exists > 0) {
            // Log duplicate order
            $logData = [
                'step' => 'check_duplicate_order',
                'timestamp' => date('c'),
                'orderId' => $orderId,
                'error' => 'Order already exists'
            ];
            file_put_contents($logFile, json_encode($logData, JSON_PRETTY_PRINT) . "\n", FILE_APPEND);

            echo json_encode(['status' => 'error', 'message' => 'Order already exists']);
            exit;
        }

        // Insert into key_documents
        $db->query(
            "INSERT INTO `key_documents` (
                `orderId`, `customerId`, `paymentTermId`, `salesPersonId`, `orderDateIssued`
            ) VALUES (?s, ?s, ?s, ?s, ?s)",
            $orderId,
            $customerId,
            $paymentTermId,
            1,
            $orderDateIssued
        );
        $documentId = $db->insertId();

        $etaDate = new DateTime($dateCreated);
        $etaDate->modify('+14 days');
        $eta = $etaDate->format('Y-m-d');

        recordKeyDocumentHistory($documentId, OT_NEW_ORDER, logActivity(OT_NEW_ORDER));

        // Process line items
        $lineItems = $data['line_items'] ?? [];
        foreach ($lineItems as $index => $item) {
            $itemName = $item['name'] ?? '';
            $itemId = getSparepartsIDFromItemName($itemName) ?? null;
            $quantity = $item['quantity'] ?? 1;
            $unitPrice = isset($item['price']) ? (string)$item['price'] : '0.00';
            $uom = 1;
            $vatPercentage = 15.00;
            $shippingVatPercentage = 15.00;
            $vatAmount = ($unitPrice * $quantity * $vatPercentage) / 100;
            $subTotal = ($unitPrice * $quantity);
            $discountPercentage = 0.00;
            $discountAmount = 0.00;

            $db->query(
                "INSERT INTO `line_items` (
                    `documentId`, `itemId`, `itemType`, 
                    `quantity`, `unitPrice`, `eta`, `UOM`, 
                    `discountPercentage`, `discountAmount`, 
                    `vatPercentage`, `vatAmount`, `subTotal`, `shippingVatPercentage`, `active`
                ) VALUES (?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s)",
                $documentId,
                $itemId,
                'SPAREPART',
                $quantity,
                $unitPrice,
                $eta,
                $uom,
                $discountPercentage,
                $discountAmount,
                $vatPercentage,
                $vatAmount,
                $subTotal,
                $shippingVatPercentage,
                1
            );
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Order saved successfully.',
            'documentId' => $documentId,
            'customerId' => $customerId
        ]);

    } catch (Exception $e) {
        error_log("Error processing order $orderId: " . $e->getMessage());

        // Log the exception
        $logData = [
            'step' => 'processing_order',
            'timestamp' => date('c'),
            'orderId' => $orderId ?? 'unknown',
            'error' => $e->getMessage(),
            'stackTrace' => $e->getTraceAsString()
        ];
        file_put_contents($logFile, json_encode($logData, JSON_PRETTY_PRINT) . "\n", FILE_APPEND);

        echo json_encode(['status' => 'error', 'message' => "Processing Order: " . $e->getMessage()]);
        exit;
    }
} else {
    // Log invalid data
    $logData = [
        'step' => 'invalid_data',
        'timestamp' => date('c'),
        'error' => 'Invalid or missing data'
    ];
    file_put_contents($logFile, json_encode($logData, JSON_PRETTY_PRINT) . "\n", FILE_APPEND);

    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing data']);
}