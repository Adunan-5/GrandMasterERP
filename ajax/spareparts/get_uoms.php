<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";

// Get the search term
$term = filter_input(INPUT_GET, 'term', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

// Fetch matching spare parts
$query = "SELECT `uomId` AS `value`,
`uomName` AS `label`,
`categoryId` AS `categoryID`,
`type` AS `type`,
`ratio` AS `ratio`,
`roundingPrecision` AS `roundingPrecision`  FROM uoms WHERE active = 1 LIMIT 10";

$results = $db->getAll($query);

// Return the results as JSON
header('Content-Type: application/json');
echo json_encode($results);