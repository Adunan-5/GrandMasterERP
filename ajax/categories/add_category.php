<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";

// $categoryName        = filter_var($_POST['categoryName'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
// $categoryDescription = filter_var($_POST['categoryDescription'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
// $categoryStatus      = filter_var($_POST['categoryStatus'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);


$categoryName        = trim($_POST['categoryName']);
$categoryDescription = trim($_POST['categoryDescription']);
$categoryStatus      = trim($_POST['categoryStatus']);

try {

    // Insert data into the database
    $res = $db->query(
        "INSERT INTO `services_category` (
            `categoryName`,
            `description`,
            `active`
        ) VALUES (
            ?s, 
            ?s, 
            ?s
        )",
        $categoryName,
        $categoryDescription,
        $categoryStatus
    );

    if ($res) {
        // Success message
        echo "SUCCESS|Category added successfully!";
    } else {
        // Safeguard: Unexpected scenario
        echo "ERROR|Query execution returned false with no specific error!";
    }
} catch (Exception $e) {
    // Log the error (you can implement a logger or store it in a database/log file)
    error_log("Error adding category: " . $e->getMessage());

    // Send a JSON error response (or handle it based on your application)
    echo "ERROR|Failed to add the category. Error: " . $e->getMessage();
}