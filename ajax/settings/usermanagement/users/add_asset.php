<?php
include_once __DIR__ . "/../../../../includes/baseIncludes.php";

// Fetch companyId from session
$companyId = $_SESSION['SES_SELECTED_COMPANY'];

$assetName        = filter_var($_POST['assetName'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$description = filter_var($_POST['description'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$assetStatus      = filter_var($_POST['assetStatus'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

try {

    // Insert data into the database
    $res = $db->query(
        "INSERT INTO `company_assets` (
            `assetName`,
            `description`,
            `active`
        ) VALUES (
            ?s, 
            ?s,
            ?s
        )",
        $assetName,
        $description,
        $assetStatus
    );

    if ($res) {
        // Success message
        echo "SUCCESS|Asset added successfully!";
    } else {
        // Safeguard: Unexpected scenario
        echo "ERROR|Query execution returned false with no specific error!";
    }
} catch (Exception $e) {
    // Log the error (you can implement a logger or store it in a database/log file)
    error_log("Error adding term: " . $e->getMessage());

    // Send a JSON error response (or handle it based on your application)
    echo "ERROR|Failed to add the term. Error: " . $e->getMessage();
}