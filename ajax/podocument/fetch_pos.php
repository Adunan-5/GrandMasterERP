<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

try {

    $baseQuery = "SELECT
                      `purchase_orders`.*,
                      `suppliers`.`companyName`,
                      `suppliers`.`companyNameAr`,
                      `payment_terms`.`termName`,
                      CONCAT(COALESCE(`users`.`firstName`, ''), ' ', COALESCE(`users`.`lastName`, '')) AS `salesPersonName`
                    FROM
                      `purchase_orders`
                      LEFT JOIN `suppliers` ON `purchase_orders`.`supplierId` =
                    `suppliers`.`supplierId`
                      LEFT JOIN `payment_terms` ON `purchase_orders`.`paymentTermId` =
                    `payment_terms`.`termId`
                      LEFT JOIN `users` ON `purchase_orders`.`salesPersonId` = `users`.`userID`
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
        $poId = $row['poId'];
        $row['poNumber'] = getPONumberFromDocumentID($poId);
        $row['rfpId'] = getRFPNumberFromDocumentID($row['rfpId']);
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