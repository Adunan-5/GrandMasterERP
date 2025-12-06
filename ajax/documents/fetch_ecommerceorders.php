<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

try {

    $baseQuery = "SELECT
                      `key_documents`.*,
                      `customers`.`companyName`,
                      `payment_terms`.`termName`
                    FROM
                      `key_documents`
                      LEFT JOIN `customers` ON `key_documents`.`customerId` =
                    `customers`.`customerId`
                      LEFT JOIN `payment_terms` ON `key_documents`.`paymentTermId` =
                    `payment_terms`.`termId`
                  WHERE status = 'Active' AND orderId > 0 ";

    // Execute query
    // $res = $db->query($baseQuery, ...$params);
    $res = $db->query($baseQuery);

    // Fetch data
    $data = [];
    while ($row = mysqli_fetch_assoc($res)) {
        //modifiers
        //Quotation and sales order calculations are same as of now.
        $quotationId = $row['documentId'];
        $row['quotationNumber'] = getQuotationNumberFromDocumentID($quotationId);
        $row['totalAmount'] =   getTotalAmountByDocumentID($quotationId)['totalAmountAfterVAT'];
        $row['salesorderNumber'] = (!empty($row['saleOrderNumber']))
            ? $row['saleOrderNumberPrefix'] . $row['saleOrderNumber']
            : '-';
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