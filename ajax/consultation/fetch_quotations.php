<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";
include_once __DIR__ . "/../../includes/auth_check.php";

try {


    $filter = filter_input(INPUT_GET, 'filter', FILTER_SANITIZE_SPECIAL_CHARS);
    $filterQuery = '';
    if(!empty( $filter))
    {
        //'NEW','AWAITING APPROVAL','APPROVED','REJECTED','SENT TO CUSTOMER','CUSTOMER ACCEPTED','CUSTOMER REJECTED','AWAITING SALES MANAGER SO APPROVAL','AWAITING ACCOUNTANT SO APPROVAL','SALES MANAGER SO REJECTED','ACCOUNTANT SO REJECTED','SO APPROVED','CONFIRMED','CANCELLED'
        if($filter == 'DRAFT')
        $filterQuery = " AND `quotationStatus` =  'NEW'";

        if($filter == 'CONFIRMED')
        $filterQuery = " AND `quotationStatus` =  'CONFIRMED'";

        if($filter == 'ACTIVE')
        $filterQuery = " AND (`quotationStatus` <>  'CONFIRMED' AND `quotationStatus` <> 'NEW' AND `quotationStatus` <> 'CANCELLED') ";

    }


    $baseQuery = "SELECT
                      `consultation_key_documents`.*,
                      `customers`.`companyName`,
                      `customers`.`companyNameAr`,
                      `payment_terms`.`termName`,
                      CONCAT(COALESCE(`users`.`firstName`, ''), ' ', COALESCE(`users`.`lastName`, '')) AS `salesPersonName`
                    FROM
                      `consultation_key_documents`
                      LEFT JOIN `customers` ON `consultation_key_documents`.`customerId` =
                    `customers`.`customerId`
                      LEFT JOIN `payment_terms` ON `consultation_key_documents`.`paymentTermId` =
                    `payment_terms`.`termId`
                      LEFT JOIN `users` ON `consultation_key_documents`.`salesPersonId` = `users`.`userID`
                  WHERE status = 'Active'  $filterQuery ";



    // Prepare parameters
    //    $params = ['Active']; // Base parameter for status

    // Execute query
    //    $res = $db->query($baseQuery, ...$params);


    $res = $db->query($baseQuery);

    // Fetch data
    $data = [];
    while ($row = mysqli_fetch_assoc($res)) {
        //modifiers
        $quotationId = $row['documentId'];
        $row['quotationNumber'] = getQuotationNumberFromConsultationDocumentID($quotationId);
        $row['totalAmount'] =   getTotalAmountByConsultationDocumentID($quotationId)['totalAmountAfterVAT'];
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