<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

try {

    $baseQuery = "SELECT
                      `invoice_documents`.*,
                      `customers`.`companyName`,
                      `customers`.`companyNameAr`,
                      `payment_terms`.`termName`,
                      CONCAT(COALESCE(`users`.`firstName`, ''), ' ', COALESCE(`users`.`lastName`, '')) AS `salesPersonName`
                    FROM
                      `invoice_documents`
                      LEFT JOIN `customers` ON `invoice_documents`.`customerId` =
                    `customers`.`customerId`
                      LEFT JOIN `payment_terms` ON `invoice_documents`.`paymentTermId` =
                    `payment_terms`.`termId`
                      LEFT JOIN `users` ON `invoice_documents`.`salesPersonId` = `users`.`userID`
                  WHERE status = 'Active'";



    // Prepare parameters
    //    $params = ['Active']; // Base parameter for status

    // Execute query
    //    $res = $db->query($baseQuery, ...$params);


    $res = $db->query($baseQuery);

    // Fetch data
    $data = [];
    while ($row = mysqli_fetch_assoc($res)) {
        //modifiers
        $invoiceId = $row['invoiceId'];
        $row['invoiceNumber'] = getInvoiceNumberFromInvoiceID($invoiceId);
        $companyId = $row['companyId'];
        $row['subsidiaryName'] = getCompanyNameByID($companyId);
        if($companyId == 1) {
          $row['totalAmount'] =   getTotalAmountByInvoiceID($invoiceId)['totalAmountAfterVAT'];
        } else if($companyId == 2) {
          $row['totalAmount'] = getTotalAmountByConsultationInvoiceID($invoiceId)['totalAmountAfterVAT'];
        }
        
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