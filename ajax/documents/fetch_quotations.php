<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

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
                      `key_documents`.*,
                      `customers`.`companyName`,
                      `customers`.`companyNameAr`,
                      `payment_terms`.`termName`,
                      CONCAT(COALESCE(`users`.`firstName`, ''), ' ', COALESCE(`users`.`lastName`, '')) AS `salesPersonName`
                    FROM
                      `key_documents`
                      LEFT JOIN `customers` ON `key_documents`.`customerId` =
                    `customers`.`customerId`
                      LEFT JOIN `payment_terms` ON `key_documents`.`paymentTermId` =
                    `payment_terms`.`termId`
                      LEFT JOIN `users` ON `key_documents`.`salesPersonId` = `users`.`userID`
                  WHERE status = 'Active' AND (orderId = 0 OR orderId IS NULL)  $filterQuery ";



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
        $row['quotationNumber'] = getQuotationNumberFromDocumentID($quotationId);
        $row['totalAmount'] =   getTotalAmountByDocumentID($quotationId)['totalAmountAfterVAT'];

        // ---- Calculate Margin% for this quotation ----
        $costQuery = "
            SELECT 
                SUM(sp.cost * li.quantity) AS total_cost
            FROM line_items li
            JOIN spareparts sp ON li.itemid = sp.sparepartId
            WHERE li.documentid = $quotationId
        ";
        $costRes = $db->query($costQuery);
        $costRow = mysqli_fetch_assoc($costRes);
        $totalCost = (float)$costRow['total_cost'];

        $totalSales = (float)$row['totalAmount']; // using total after VAT
        $margin = ($totalSales > 0) 
            ? round((($totalSales - $totalCost) / $totalSales) * 100, 2) 
            : 0;

        $row['margin'] = $margin . '%';
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