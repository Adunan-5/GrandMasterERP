<?php

header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

// Assuming you already have the formatDate function in your helper file
// Example: formatDate($mysqlDate, $includeTime = true)

try {
    // Query to fetch key document details along with history information
    $query = "
        SELECT DISTINCT
            key_documents.documentId,
            key_documents.quotationNumberPrefix,
            key_documents.quotationNumber,
            key_documents.saleOrderNumberPrefix,
            key_documents.saleOrderNumber,
            key_documents.customerId,
            key_documents.quotationDateIssued,
            key_documents.quotationDateExpiry,
            key_documents.saleOrderDateIssued,
            key_documents.quotationAcceptedByCustomer,
            key_documents.quotationAcceptedByCustomerDate,
            key_documents.salesPersonId,
            key_documents.quotationStatus,
            key_documents.saleOrderStatus,
            key_documents.PONumber,
            key_documents.POAttachment,
            key_documents.paymentTermId,
            key_documents.totalAmount,
            key_documents.customerPassword,
            key_documents.salesManagerSOApproved,
            key_documents.accountantSOApproved,
            -- Fetch customer name from the customers table
            customers.companyName,
            -- Fetch salesperson name from the users table
            users.userName AS salesPersonName,
            -- Fetch updatedBy name from the users table
            updatedUser.userName AS updatedByName,
            key_document_history.historyId,
            key_document_history.updatedAt,
            key_document_history.updatedBy,
            key_document_history.operationType,
            key_document_history.relatedActivityLogId,
            key_document_history.remark
        FROM
            key_documents
        LEFT JOIN
            key_document_history ON key_documents.documentId = key_document_history.keyDocumentId
        LEFT JOIN
            customers ON key_documents.customerId = customers.customerId
        LEFT JOIN
            users AS users ON key_documents.salesPersonId = users.userId
        LEFT JOIN
            users AS updatedUser ON key_document_history.updatedBy = updatedUser.userId
        ORDER BY
            key_document_history.updatedAt DESC
    ";

    // Execute query
    $res = $db->query($query);

    $data = [];
    $documentTimelines = []; // To hold timelines grouped by documentId

    while ($row = mysqli_fetch_assoc($res)) {
        // Check if this documentId already exists in the array, if not, initialize an empty array
        if (!isset($documentTimelines[$row['documentId']])) {
            $documentTimelines[$row['documentId']] = [
                'documentId' => $row['documentId'],
                'quotationNumberPrefix' => $row['quotationNumberPrefix'],
                'quotationNumber' => $row['quotationNumber'],
                'customerId' => $row['customerId'],
                'customerName' => $row['companyName'],
                'quotationAcceptedByCustomer' => $row['quotationAcceptedByCustomer'],
                'quotationAcceptedByCustomerDate' => formatDate($row['quotationAcceptedByCustomerDate']),
                'salesPersonName' => $row['salesPersonName'],
                'quotationStatus' => $row['quotationStatus'],
                'timeline' => [] // Initialize empty timeline for this document
            ];

            // Add the quotation created event as the first event in the timeline
            $documentTimelines[$row['documentId']]['timeline'][] = [
                'event' => 'Quotation Created',
                'date' => formatDate($row['quotationDateIssued']),
                'updatedBy' => 'System',
            ];
        }

        // Add the history event to the timeline for this document
        $documentTimelines[$row['documentId']]['timeline'][] = [
            'event' => $row['operationType'],
            'date' => formatDate($row['updatedAt']),
            'updatedBy' => $row['updatedByName'],
        ];

        // Add the quotation status change event (if it exists)
        if (!empty($row['quotationStatus'])) {
            $documentTimelines[$row['documentId']]['timeline'][] = [
                'event' => 'Quotation Status Changed to: ' . $row['quotationStatus'],
                'date' => formatDate($row['updatedAt']),
                'updatedBy' => $row['updatedByName'],
            ];
        }
    }

    // Prepare the final response data
    $response = [
        "data" => array_values($documentTimelines) // Convert associative array to indexed array
    ];

    // Output JSON response
    echo json_encode($response);

} catch (Exception $e) {
    // Handle error
    echo json_encode([
        "error" => "An error occurred: " . $e->getMessage()
    ]);
}
