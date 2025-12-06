<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

try {
    // Query to fetch warehouse details
    $query = "SELECT
  `warehouses`.*,
  `countries`.`name` AS `country`,
  `states`.`name` AS `state`,
  `cities`.`name` AS `city`,
  `countries`.`iso2` AS `countryCode`
FROM
  `warehouses`
  LEFT JOIN `cities` ON `warehouses`.`cityId` = `cities`.`id`
  LEFT JOIN `states` ON `warehouses`.`stateId` = `states`.`id`
  LEFT JOIN `countries` ON `warehouses`.`countryId` = `countries`.`id`
                  WHERE active = '1'";

    // Execute query
    $res = $db->query($query);

    // Fetch data
    $data = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $data[] = $row;
    }

    // Response format
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
