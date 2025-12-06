<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

try {
//    // DataTables parameters
//    $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
//    $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
//    $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
//    $searchValue = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';
//    $orderColumn = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
//    $orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';
//
//    // Define column mapping for ordering (index matches the HTML columns)
//    $columns = ['customerId', 'companyName', 'city', 'state', 'country', 'phone', 'email', 'vatNumber'];

    // Validate order column and direction
//    $orderBy = isset($columns[$orderColumn]) ? $columns[$orderColumn] : 'customerId';
//    $orderDir = ($orderDir === 'desc') ? 'DESC' : 'ASC';

    $companyId = intval($_SESSION['SES_SELECTED_COMPANY']);
    // Base query
    $baseQuery = "SELECT
  `customers`.*,
  `countries`.`name` AS `country`,
  `states`.`name` AS `state`,
  `cities`.`name` AS `city`,
  `countries`.`iso2` AS `countryCode`
FROM
  `customers`
  LEFT JOIN `cities` ON `customers`.`cityId` = `cities`.`id`
  LEFT JOIN `states` ON `customers`.`stateId` = `states`.`id`
  LEFT JOIN `countries` ON `customers`.`countryId` = `countries`.`id`
                  WHERE status = 'Active' and companyId = $companyId";

    // Prepare parameters
//    $params = ['Active']; // Base parameter for status

    // Execute query
//    $res = $db->query($baseQuery, ...$params);
    $res = $db->query($baseQuery);

    // Fetch data
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