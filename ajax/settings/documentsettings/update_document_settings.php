<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";

// Input validation
$documentId         = filter_var($_POST['documentId'], FILTER_VALIDATE_INT);
$titleFontSize      = filter_var($_POST['titleFontSize'], FILTER_VALIDATE_FLOAT);
$titleFontSize_ar   = filter_var($_POST['titleFontSize_ar'], FILTER_VALIDATE_FLOAT);
$titleFontFamily    = filter_var($_POST['titleFontFamily'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$titleFontFamily_ar = filter_var($_POST['titleFontFamily_ar'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$headerPrimaryFontSize      = filter_var($_POST['headerPrimaryFontSize'], FILTER_VALIDATE_FLOAT);
$headerPrimaryFontSize_ar   = filter_var($_POST['headerPrimaryFontSize_ar'], FILTER_VALIDATE_FLOAT);
$headerPrimaryFontFamily    = filter_var($_POST['headerPrimaryFontFamily'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$headerPrimaryFontFamily_ar = filter_var($_POST['headerPrimaryFontFamily_ar'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$headerSecondaryFontSize      = filter_var($_POST['headerSecondaryFontSize'], FILTER_VALIDATE_FLOAT);
$headerSecondaryFontSize_ar   = filter_var($_POST['headerSecondaryFontSize_ar'], FILTER_VALIDATE_FLOAT);
$headerSecondaryFontFamily    = filter_var($_POST['headerSecondaryFontFamily'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$headerSecondaryFontFamily_ar = filter_var($_POST['headerSecondaryFontFamily_ar'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$bodyFontSize      = filter_var($_POST['bodyFontSize'], FILTER_VALIDATE_FLOAT);
$bodyFontSize_ar   = filter_var($_POST['bodyFontSize_ar'], FILTER_VALIDATE_FLOAT);
$bodyFontFamily    = filter_var($_POST['bodyFontFamily'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$bodyFontFamily_ar = filter_var($_POST['bodyFontFamily_ar'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$tableHeaderFontSize      = filter_var($_POST['tableHeaderFontSize'], FILTER_VALIDATE_FLOAT);
$tableHeaderFontSize_ar   = filter_var($_POST['tableHeaderFontSize_ar'], FILTER_VALIDATE_FLOAT);
$tableHeaderFontFamily    = filter_var($_POST['tableHeaderFontFamily'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$tableHeaderFontFamily_ar = filter_var($_POST['tableHeaderFontFamily_ar'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$tableRowsFontSize      = filter_var($_POST['tableRowsFontSize'], FILTER_VALIDATE_FLOAT);
$tableRowsFontSize_ar   = filter_var($_POST['tableRowsFontSize_ar'], FILTER_VALIDATE_FLOAT);
$tableRowsFontFamily    = filter_var($_POST['tableRowsFontFamily'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$tableRowsFontFamily_ar = filter_var($_POST['tableRowsFontFamily_ar'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

try {
    if (!$documentId) {
        throw new Exception("Invalid or missing document ID.");
    }

    // Update settings_document table
    $res = $db->query(
        "UPDATE `settings_document` SET 
            `titleFontSize` = ?s, 
            `titleFontSize_ar` = ?s, 
            `titleFontFamily` = ?s, 
            `titleFontFamily_ar` = ?s, 
            `headerPrimaryFontSize` = ?s, 
            `headerPrimaryFontSize_ar` = ?s, 
            `headerPrimaryFontFamily` = ?s, 
            `headerPrimaryFontFamily_ar` = ?s, 
            `headerSecondaryFontSize` = ?s, 
            `headerSecondaryFontSize_ar` = ?s, 
            `headerSecondaryFontFamily` = ?s, 
            `headerSecondaryFontFamily_ar` = ?s, 
            `bodyFontSize` = ?s, 
            `bodyFontSize_ar` = ?s, 
            `bodyFontFamily` = ?s, 
            `bodyFontFamily_ar` = ?s, 
            `tableHeaderFontSize` = ?s, 
            `tableHeaderFontSize_ar` = ?s, 
            `tableHeaderFontFamily` = ?s, 
            `tableHeaderFontFamily_ar` = ?s, 
            `tableRowsFontSize` = ?s, 
            `tableRowsFontSize_ar` = ?s, 
            `tableRowsFontFamily` = ?s, 
            `tableRowsFontFamily_ar` = ?s
        WHERE `documentId` = ?s",
        $titleFontSize,
        $titleFontSize_ar,
        $titleFontFamily,
        $titleFontFamily_ar,
        $headerPrimaryFontSize,
        $headerPrimaryFontSize_ar,
        $headerPrimaryFontFamily,
        $headerPrimaryFontFamily_ar,
        $headerSecondaryFontSize,
        $headerSecondaryFontSize_ar,
        $headerSecondaryFontFamily,
        $headerSecondaryFontFamily_ar,
        $bodyFontSize,
        $bodyFontSize_ar,
        $bodyFontFamily,
        $bodyFontFamily_ar,
        $tableHeaderFontSize,
        $tableHeaderFontSize_ar,
        $tableHeaderFontFamily,
        $tableHeaderFontFamily_ar,
        $tableRowsFontSize,
        $tableRowsFontSize_ar,
        $tableRowsFontFamily,
        $tableRowsFontFamily_ar,
        $documentId
    );

    if ($res) {
        // Success message
        echo "SUCCESS|Document settings updated successfully!";
    } else {
        // Safeguard: Unexpected scenario
        echo "ERROR|Query execution returned false with no specific error!";
    }
} catch (Exception $e) {
    // Log the error (implement a logger or store it in a database/log file)
    error_log("Error updating document settings: " . $e->getMessage());

    // Send a JSON error response (or handle it based on your application)
    echo "ERROR|Failed to update the document settings. Error: " . $e->getMessage();
}
?>
