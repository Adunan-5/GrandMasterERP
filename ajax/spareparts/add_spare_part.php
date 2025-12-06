<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";

// Input fields from POST
//$partNumber = filter_var($_POST['partNumber'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$internalReference    = filter_var($_POST['internalReference'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$name                 = filter_var($_POST['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$image                = $_FILES['image'];
$description          = filter_var($_POST['description'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$brandId              = filter_var($_POST['brandId'], FILTER_VALIDATE_INT);
$leadTime             = filter_var($_POST['leadTime'], FILTER_VALIDATE_INT);
$origin               = filter_var($_POST['origin'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$salesPrice           = filter_var($_POST['salesPrice'], FILTER_VALIDATE_FLOAT);
$uom                  = filter_var($_POST['uom'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$cost                 = filter_var($_POST['cost'], FILTER_VALIDATE_FLOAT);
$hsCode               = filter_var($_POST['hsCode'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$hsPercentage         = filter_var($_POST['hsPercentage'], FILTER_VALIDATE_FLOAT);
$hsPercentage         = empty($hsPercentage) ? 0.00 : $hsPercentage;
$publishedOnEcommerce = filter_var($_POST['publishedOnEcommerce'], FILTER_VALIDATE_INT);
$notes                = filter_var($_POST['notes'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

$itemType = filter_var($_POST['itemType'] ?? 'SPAREPART', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
if (!in_array($itemType, ['SPAREPART', 'MACHINE'])) {
    $itemType = 'SPAREPART'; // Fallback to default
}

// Handle serial number (only for MACHINE)
$serialNumber = null;
if ($itemType === 'MACHINE') {
    $serialNumber = filter_var($_POST['serialNumber'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    if ($serialNumber === '') {
        echo "ERROR|Serial number is required for machine items.";
        exit;
    }
}

try {
    // Validate brandId
    if (!$brandId) {
        throw new Exception("Invalid brand selected.");
    }

    // Ensure leadTime is either a valid integer or NULL (if invalid, set to NULL)
    if ($leadTime === false || $leadTime === "") {
        $leadTime = 14;
    }

    // Default image path
    // $defaultImage = "no-image-placeholder.png"; // Adjust the file name/path as per your setup
    $defaultImage = NO_IMAGE_SPARE_PART; // Use constant from globals.php

    // Check if an image was uploaded
    $imageName = $defaultImage;                 // Set default image name
    if (isset($image) && $image['error'] === UPLOAD_ERR_OK) {
        // Validate image type
        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $imageExtension    = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
        if (!in_array($imageExtension, $allowedExtensions)) {
            throw new Exception("Invalid image type. Only jpg, jpeg, and png are allowed.");
        }

        // Generate a unique image name
        $imageName  = $name . "_" . bin2hex(random_bytes(4)) . "." . $imageExtension;
        $uploadDir  = __DIR__ . "/../../uploads/sparepart-images/";
        $uploadPath = $uploadDir . $imageName;

        // Ensure the upload directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Move the uploaded file to the designated folder
        if (!move_uploaded_file($image['tmp_name'], $uploadPath)) {
            throw new Exception("Failed to move the uploaded image.");
        }
    }

    // Insert spare part data into the database
    $res = $db->query(
        "INSERT INTO `spareparts` (            
            `internalReference`,
            `serialNumber`,
            `name`,
            `image`,
            `description`,
            `brandId`,
            `leadTime`,
            `origin`,
            `salesPrice`,
            `uomId`,
            `cost`,
            `hsCode`,
            `hsPercentage`,
            `itemType`,
            `publishedOnEcommerce`,
            `notes`,
            `createdBy`
        ) VALUES (
            ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s
        )",
        $internalReference,
        $serialNumber,
        $name,
        $imageName, // Insert default or uploaded image name
        $description,
        $brandId,
        $leadTime,  // Pass NULL if no valid lead time
        $origin,
        $salesPrice,
        $uom,
        $cost,
        $hsCode,
        $hsPercentage,
        $itemType,
        $publishedOnEcommerce,
        $notes,
        getUserIDOfCurrentUser()
    );

    $sparepartId = $db->insertId();

    // Determine activity constants dynamically
    $activityType = ($itemType === 'MACHINE') ? OT_NEW_MACHINE : OT_NEW_SPAREPART;
    $activityMsg  = ($itemType === 'MACHINE')
        ? "Machine Created - " . $serialNumber
        : "Sparepart Created - " . getPartNumberForSparepartID($sparepartId);

    // recordSparepartHistory($sparepartId, OT_NEW_SPAREPART,logActivity(OT_NEW_SPAREPART), "Sparepart Created - " . getPartNumberForSparepartID($sparepartId) );
    recordSparepartHistory($sparepartId, $activityType, logActivity($activityType), $activityMsg);

    if ($res) {
        // Success message
        // echo "SUCCESS|Spare part added successfully!";
        $successMessage = ($itemType === 'MACHINE')
            ? "Machine added successfully!"
            : "Spare part added successfully!";
        echo "SUCCESS|$successMessage";
    } else {
        // Safeguard: Unexpected scenario
        echo "ERROR|Query execution returned false with no specific error!";
    }
} catch (Exception $e) {
    // Log the error (you can implement a logger or store it in a database/log file)
    error_log("Error inserting spare part: " . $e->getMessage());

    // Send a JSON error response (or handle it based on your application)
    echo "ERROR|Failed to add the spare part. Error: " . $e->getMessage();
}