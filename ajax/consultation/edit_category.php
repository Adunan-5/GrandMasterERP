<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";

$categoryID          = filter_var($_POST['categoryID'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$categoryName        = filter_var($_POST['categoryName'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$categoryDescription = filter_var($_POST['categoryDescription'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$categoryStatus      = filter_var($_POST['categoryStatus'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

try {

    // Insert data into the database
    $res = $db->query(
        "UPDATE `consultation_services_category` SET `categoryName`=?s, `description`=?s, `active`=?s WHERE  `categoryId`=?s",
        $categoryName,
        $categoryDescription,
        $categoryStatus,
        $categoryID
    );

    if ($res) {
        // Success message
        echo "SUCCESS|Category updated successfully!";
    } else {
        // Safeguard: Unexpected scenario
        echo "ERROR|Query execution returned false with no specific error!";
    }
} catch (Exception $e) {
    // Log the error (you can implement a logger or store it in a database/log file)
    error_log("Error updating category: " . $e->getMessage());

    // Send a JSON error response (or handle it based on your application)
    echo "ERROR|Failed to update the category. Error: " . $e->getMessage();
}