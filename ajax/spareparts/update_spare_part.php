<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";

// Input validation
$sparePartId          = filter_var($_POST['sparepartId'], FILTER_VALIDATE_INT);
$itemType             = isset($_POST['itemType']) ? strtoupper($_POST['itemType']) : 'SPAREPART';
$partNumber           = filter_var($_POST['partNumber'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$internalReference    = filter_var($_POST['internalReference'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$name                 = filter_var($_POST['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$image                = $_FILES['image']; // Change from $_POST to $_FILES for file uploads
$description          = filter_var($_POST['description'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$brandId              = filter_var($_POST['sparepartBrand'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$leadTime             = filter_var($_POST['leadTime'], FILTER_VALIDATE_FLOAT);
// $leadTime             = filter_var($_POST['leadTime'], FILTER_VALIDATE_INT);
$origin               = filter_var($_POST['origin'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$salesPrice           = filter_var($_POST['salesPrice'], FILTER_VALIDATE_FLOAT);
$uomId                = filter_var($_POST['uomId'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$cost                 = filter_var($_POST['cost'], FILTER_VALIDATE_FLOAT);
$hsCode               = filter_var($_POST['hsCode'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$hsPercentage         = filter_var($_POST['hsPercentage'], FILTER_VALIDATE_FLOAT);
$publishedOnEcommerce = filter_var($_POST['published'], FILTER_VALIDATE_BOOLEAN);
$notes                = filter_var($_POST['notes'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

// Ensure that leadTime is either a valid integer or NULL
if ($leadTime === false || $leadTime === "") {
    $leadTime = null; // Set to NULL if invalid or empty
}

try {
    if (!$sparePartId) {
        throw new Exception("Invalid or missing spare part ID.");
    }

    // Fetch existing record
    $currentData = $db->getRow("SELECT * FROM `spareparts` WHERE `sparePartId` = ?i", $sparePartId);
    if (!$currentData) {
        throw new Exception("Spare part not found for the given ID.");
    }

    $newImageName = null;

    // Check if an image was uploaded
    if (isset($image) && $image['error'] === UPLOAD_ERR_OK)
    {
        // Validate image type
        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $imageExtension    = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
        if (!in_array($imageExtension, $allowedExtensions)) {
            throw new Exception("Invalid image type. Only jpg, jpeg, and png are allowed.");
        }

        // Generate a unique image name
        $newImageName = $name . "_" . microtime(false) . "." . $imageExtension;
        $uploadDir    = __DIR__ . "/../../uploads/sparepart-images/";
        $uploadPath   = $uploadDir . $newImageName;

        // Ensure the upload directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Move the uploaded file to the designated folder
        if (!move_uploaded_file($image['tmp_name'], $uploadPath)) {
            throw new Exception("Failed to move the uploaded image.");
        }
    } else {
        // No image uploaded, use default image
        // $newImageName = NO_IMAGE_SPARE_PART;
        $newImageName = $currentData['image']; // Keep existing image if no new image is uploaded
    }

    // Select which column to update based on itemType
    $numberColumn = ($itemType === "MACHINE") ? "serialNumber" : "partNumber";

    // Now, proceed with updating the spare part record, using the $newImageName (either the uploaded image or the default image)
    $res = $db->query(
        "UPDATE `spareparts` SET 
            `$numberColumn` = ?s,
            `internalReference` = ?s, 
            `name` = ?s, 
            `image` = ?s, 
            `description` = ?s, 
            `brandId` = ?s, 
            `leadTime` = ?s,  -- Use NULL or valid lead time
            `origin` = ?s, 
            `salesPrice` = ?s, 
            `uomId` = ?s, 
            `cost` = ?s, 
            `hsCode` = ?s, 
            `hsPercentage` = ?s, 
            `publishedOnEcommerce` = ?s, 
            `notes` = ?s,
            `updatedBy` = ?s
        WHERE `sparePartId` = ?i",
        $partNumber,
        $internalReference,
        $name,
        $newImageName, // Store the new or default image name in the database
        $description,
        $brandId,
        $leadTime,     // Pass NULL if no valid lead time
        $origin,
        $salesPrice,
        $uomId,
        $cost,
        $hsCode,
        $hsPercentage,
        $publishedOnEcommerce ? 1 : 0,
        $notes,
        getUserIDOfCurrentUser(),
        $sparePartId
    );

    // Collect change log
    $changes = [];

    function logChange(&$changes, $label, $old, $new) {
        // Normalize numeric values before comparing
        if (is_numeric($old) && is_numeric($new)) {
            $old = round((float)$old, 2);
            $new = round((float)$new, 2);
        }

        if ((string)$old !== (string)$new) {
            $changes[] = "$label changed from '$old' to '$new'";
        }
    }

    if ($itemType === "MACHINE") {
        logChange($changes, "Serial Number", $currentData['serialNumber'], $partNumber);
    } else {
        logChange($changes, "Part Number", $currentData['partNumber'], $partNumber);
    }

    $oldBrandName = getBrandNameByID($currentData['brandId']);
    $newBrandName = getBrandNameByID($brandId);

    $oldUOMName = getUOMNameFromID($currentData['uomId']);
    $newUOMName = getUOMNameFromID($uomId);

    // logChange($changes, "Part Number", $currentData['partNumber'], $partNumber);
    logChange($changes, "Internal Reference", $currentData['internalReference'], $internalReference);
    logChange($changes, "Name", $currentData['name'], $name);
    logChange($changes, "Description", $currentData['description'], $description);
    logChange($changes, "Brand", $oldBrandName, $newBrandName);
    logChange($changes, "Lead Time", $currentData['leadTime'], $leadTime);
    logChange($changes, "Origin", $currentData['origin'], $origin);
    logChange($changes, "Sales Price", $currentData['salesPrice'], $salesPrice);
    logChange($changes, "UOM", $oldUOMName, $newUOMName);
    logChange($changes, "Cost", $currentData['cost'], $cost);
    logChange($changes, "HS Code", $currentData['hsCode'], $hsCode);
    logChange($changes, "HS %", $currentData['hsPercentage'], $hsPercentage);
    logChange($changes, "Published", $currentData['publishedOnEcommerce'], $publishedOnEcommerce ? 1 : 0);
    logChange($changes, "Notes", $currentData['notes'], $notes);
    logChange($changes, "Image", $currentData['image'], $newImageName);

    // $remarks = count($changes) > 0
    //     ? "Sparepart Edited:\n" . implode("\n", $changes)
    //     : "Sparepart Edited: No significant changes";

    $remarks = count($changes) > 0
        ? ucfirst(strtolower($itemType)) . " Edited:\n" . implode("\n", $changes)
        : ucfirst(strtolower($itemType)) . " Edited: No significant changes";


    $activityType = ($itemType === 'MACHINE') ? OT_EDIT_MACHINE : OT_EDIT_SPAREPART;
    $activityMsg  = ($itemType === 'MACHINE')
        ? "Machine Updated - " . $partNumber
        : "Sparepart Updated - " . getPartNumberForSparepartID($sparePartId);

    recordSparepartHistory($sparePartId, $activityType, logActivity($activityType), $remarks);

    // recordSparepartHistory($sparePartId, OT_EDIT_SPAREPART,logActivity(OT_EDIT_SPAREPART), $remarks);

    if ($res) {
        // Success message
        // echo "SUCCESS|Spare part updated successfully!";
        $successMessage = ($itemType === 'MACHINE')
            ? "Machine updated successfully!"
            : "Spare part updated successfully!";
        echo "SUCCESS|$successMessage";
    } else {
        // Safeguard: Unexpected scenario
        echo "ERROR|Query execution returned false with no specific error!";
    }
} catch (Exception $e) {
    // Log the error (implement a logger or store it in a database/log file)
    error_log("Error updating spare part: " . $e->getMessage());

    // Send a JSON error response (or handle it based on your application)
    echo "ERROR|Failed to update the spare part. Error: " . $e->getMessage();
}
?>