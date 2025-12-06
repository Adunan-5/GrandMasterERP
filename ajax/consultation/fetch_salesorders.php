<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";
include_once __DIR__ . "/../../includes/auth_check.php";

try {

    $baseQuery = "SELECT
                      `consultation_key_documents`.*,
                      `customers`.`companyName`,
                      `customers`.`companyNameAr`,
                      `payment_terms`.`termName`,
                      CONCAT(`users`.`firstName`, ' ', `users`.`lastName`) AS `salesPersonName`
                    FROM
                      `consultation_key_documents`
                      LEFT JOIN `customers` ON `consultation_key_documents`.`customerId` =
                    `customers`.`customerId`
                      LEFT JOIN `payment_terms` ON `consultation_key_documents`.`paymentTermId` =
                    `payment_terms`.`termId`
                      LEFT JOIN `users` ON `consultation_key_documents`.`salesPersonId` = `users`.`userID`
                  WHERE status = 'Active' AND `quotationStatus` = 'CONFIRMED'";

    // Execute query
    // $res = $db->query($baseQuery, ...$params);
    $res = $db->query($baseQuery);

    // Fetch data
    $data = [];
    while ($row = mysqli_fetch_assoc($res)) {
        //modifiers
        //Quotation and sales order calculations are same as of now.
        $quotationId = $row['documentId'];
        $row['quotationNumber'] = getQuotationNumberFromConsultationDocumentID($quotationId);
        $row['totalAmount'] =  getTotalAmountByConsultationDocumentID($quotationId)['totalAmountAfterVAT'];
        $data[] = $row;
    }


    // Response format for DataTables
    $response = [
        "data" => $data
    ];

    // Output JSON response
    echo json_encode($response);
} catch (Exception $e) {
    // Handle error
    echo json_encode([
        "error" => "An error occurred: " . $e->getMessage()
    ]);
}