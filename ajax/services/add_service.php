<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";

// $serviceName        = filter_var($_POST['serviceName'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
// $serviceDescription = filter_var($_POST['serviceDescription'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$serviceName        = trim($_POST['serviceName']);
$serviceDescription = trim($_POST['serviceDescription']);
$serviceUOM      = filter_var($_POST['serviceUOM'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$serviceCategory        = filter_var($_POST['serviceCategory'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$serviceCost = filter_var($_POST['serviceCost'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$servicePrice = filter_var($_POST['servicePrice'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$serviceStatus      = filter_var($_POST['serviceStatus'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

// Ensure numeric values are not empty
$serviceCost  = !empty($serviceCost) ? $serviceCost : 0.00;
$servicePrice = !empty($servicePrice) ? $servicePrice : 0.00;

try {

    // Insert data into the database
    $res = $db->query(
        "INSERT INTO `spareparts` (
            `name`,
            `description`,
            `uomId`,
            `categoryId`,
            `salesPrice`,
            `cost`,
            `itemType`,
            `active`
        ) VALUES (
            ?s, 
            ?s, 
            ?s,
            ?s, 
            ?s, 
            ?s,
            ?s,
            ?s
        )",
        $serviceName,
        $serviceDescription,
        $serviceUOM,
        $serviceCategory,
        $servicePrice,
        $serviceCost,
        'SERVICE',
        $serviceStatus
    );

    if ($res) {
        // Success message
        echo "SUCCESS|Service added successfully!";
    } else {
        // Safeguard: Unexpected scenario
        echo "ERROR|Query execution returned false with no specific error!";
    }
} catch (Exception $e) {
    // Log the error (you can implement a logger or store it in a database/log file)
    error_log("Error adding service: " . $e->getMessage());

    // Send a JSON error response (or handle it based on your application)
    echo "ERROR|Failed to add the service. Error: " . $e->getMessage();
}