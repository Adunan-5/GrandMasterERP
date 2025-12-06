<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

try {

    $baseQuery = "SELECT `consultation_rfp_documents`.*,
                      `suppliers`.`companyName`,
                      `suppliers`.`companyNameAr`,
                      `payment_terms`.`termName`,
                      CONCAT(COALESCE(`users`.`firstName`, ''), ' ', COALESCE(`users`.`lastName`, '')) AS `salesPersonName`
                    FROM
                      `consultation_rfp_documents`
                      LEFT JOIN `suppliers` ON `consultation_rfp_documents`.`supplierId` =
                    `suppliers`.`supplierId`
                      LEFT JOIN `payment_terms` ON `consultation_rfp_documents`.`paymentTermId` =
                    `payment_terms`.`termId`
                      LEFT JOIN `users` ON `consultation_rfp_documents`.`salesPersonId` = `users`.`userID`
                  WHERE status = 'Active'";


    $res = $db->query($baseQuery);

    // Fetch data
    $data = [];
    while ($row = mysqli_fetch_assoc($res)) {
        //modifiers
        $rfpId = $row['rfpId'];
        $row['rfpNumber'] = getRFPNumberFromDocumentID($rfpId);
//        $row['saleOrderId'] = getSalesOrderNumberFromDocumentID($rfpId);
        $row['saleOrderId'] = (!isset($row['saleOrderId']) || $row['saleOrderId'] == 0) ? '-' : getSalesOrderNumberFromRFPRefDocID($row['saleOrderId']);
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