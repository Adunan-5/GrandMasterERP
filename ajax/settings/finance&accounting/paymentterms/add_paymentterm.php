<?php
include_once __DIR__ . "/../../../../includes/baseIncludes.php";

// Fetch companyId from session
$companyId = $_SESSION['SES_SELECTED_COMPANY'];

$termName        = filter_var($_POST['termName'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$termValue = filter_var($_POST['termValue'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$termStatus      = filter_var($_POST['termStatus'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

try {

    // Insert data into the database
    $res = $db->query(
        "INSERT INTO `payment_terms` (
            `termName`,
            `termValue`,
            `companyId`,
            `active`
        ) VALUES (
            ?s, 
            ?s,
            ?s,
            ?s
        )",
        $termName,
        $termValue,
        $companyId,
        $termStatus
    );

    if ($res) {
        // Success message
        echo "SUCCESS|Term added successfully!";
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