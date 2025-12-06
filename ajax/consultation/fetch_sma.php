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
                      `consultation_sma`.*,
                      `customers`.`companyName`,
                      `customers`.`companyNameAr`,
                      CONCAT(COALESCE(`users`.`firstName`, ''), ' ', COALESCE(`users`.`lastName`, '')) AS `salesPersonName`
                    FROM
                      `consultation_sma`
                      LEFT JOIN `customers` ON `consultation_sma`.`customerID` =
                    `customers`.`customerId`
                      LEFT JOIN `users` ON `consultation_sma`.`createdBy` = `users`.`userID`";



    // Prepare parameters
    //    $params = ['Active']; // Base parameter for status

    // Execute query
    //    $res = $db->query($baseQuery, ...$params);


    $res = $db->query($baseQuery);

    // Fetch data
    $data = [];
    while ($row = mysqli_fetch_assoc($res)) {
        //modifiers
        $smaID = $row['smaID'];
        $row['smaNumber'] = getSMANumberFromSMAID($smaID);

//        $row['totalAmount'] =   getTotalAmountByConsultationDocumentID($quotationId)['totalAmountAfterVAT'];
//        $row['totalAmount'] =   "0.00";




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