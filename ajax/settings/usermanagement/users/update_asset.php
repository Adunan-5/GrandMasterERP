<?php
include_once __DIR__ . "/../../../../includes/baseIncludes.php";

// Fetch companyId from session
$companyId = $_SESSION['SES_SELECTED_COMPANY'];

$assetId        = filter_var($_POST['assetID'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$assetName        = filter_var($_POST['editAssetName'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$description = filter_var($_POST['description'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$assetStatus      = filter_var($_POST['assetStatus'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

try {

    // Insert data into the database
    $res = $db->query(
        "UPDATE `company_assets` 
         SET `assetName` = ?s, 
             `description` = ?s, 
             `active` = ?s 
         WHERE `assetId` = ?s",
        $assetName,
        $description,
        $assetStatus,
        $assetId
    );

    if ($res) {
        // Success message
        echo "SUCCESS|Asset updated successfully!";
    } else {
        // Safeguard: Unexpected scenario
        echo "ERROR|Query execution returned false with no specific error!";
    }
} catch (Exception $e) {
    // Log the error (you can implement a logger or store it in a database/log file)
    error_log("Error updating term: " . $e->getMessage());

    // Send a JSON error response (or handle it based on your application)
    echo "ERROR|Failed to update the term. Error: " . $e->getMessage();
}