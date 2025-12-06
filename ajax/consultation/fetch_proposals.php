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
                      `consultation_proposals`.*,
                      `customers`.`companyName`,
                      `customers`.`companyNameAr`,
                      `payment_terms`.`termName`,
                      CONCAT(COALESCE(`users`.`firstName`, ''), ' ', COALESCE(`users`.`lastName`, '')) AS `salesPersonName`
                    FROM
                      `consultation_proposals`
                      LEFT JOIN `customers` ON `consultation_proposals`.`customerID` =
                    `customers`.`customerId`
                      LEFT JOIN `payment_terms` ON `consultation_proposals`.`paymentTermId` =
                    `payment_terms`.`termId`
                      LEFT JOIN `users` ON `consultation_proposals`.`createdBy` = `users`.`userID`";



    // Prepare parameters
    //    $params = ['Active']; // Base parameter for status

    // Execute query
    //    $res = $db->query($baseQuery, ...$params);


    $res = $db->query($baseQuery);

    // Fetch data
    $data = [];
    while ($row = mysqli_fetch_assoc($res)) {
        //modifiers
        $proposalID = $row['proposalID'];
        $row['proposalNumber'] = getProposalNumberFromProposalID($proposalID);

//        $row['totalAmount'] =   getTotalAmountByConsultationDocumentID($quotationId)['totalAmountAfterVAT'];
        $row['totalAmount'] =   "0.00";

        //To save network traffic
$row['contentIntroduction'] = '';
$row['contentWhyChooseUs'] = '';
$row['contentScopeOfWork'] = '';
$row['contentProjectTimeLine'] = '';
$row['contentClientResponsibilities'] = '';
$row['contentTermsAndConditions'] = '';
$row['contentNextSteps'] = '';


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