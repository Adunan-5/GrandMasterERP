<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

try {
    $companyId = intval($_SESSION['SES_SELECTED_COMPANY']);
    // Base query
    $baseQuery = "SELECT
  `suppliers`.*,
  `countries`.`name` AS `country`,
  `states`.`name` AS `state`,
  `cities`.`name` AS `city`,
  `countries`.`iso2` AS `countryCode`
FROM
  `suppliers`
  LEFT JOIN `cities` ON `suppliers`.`cityId` = `cities`.`id`
  LEFT JOIN `states` ON `suppliers`.`stateId` = `states`.`id`
  LEFT JOIN `countries` ON `suppliers`.`countryId` = `countries`.`id`
                  WHERE status = 'Active' and companyId = $companyId";

    $res = $db->query($baseQuery);


    $data = [];
    while ($row = mysqli_fetch_assoc($res)) {
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