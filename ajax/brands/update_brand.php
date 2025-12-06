<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";

// Input validation
$brandId = filter_var($_POST['brandId'], FILTER_VALIDATE_INT);
$brandName = filter_var($_POST['brandName'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$image = $_FILES['image']; // Handle file uploads

try {
    if (!$brandId) {
        throw new Exception("Invalid or missing brand ID.");
    }

    // Check if an image was uploaded
    $newImageName = null;
    if (isset($image) && $image['error'] === UPLOAD_ERR_OK) {
        // Validate image type
        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $imageExtension = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
        if (!in_array($imageExtension, $allowedExtensions)) {
            throw new Exception("Invalid image type. Only jpg, jpeg, and png are allowed.");
        }

        // Generate a unique image name
        $newImageName = $brandName . "_" . bin2hex(random_bytes(4)) . "." . $imageExtension;
        $uploadDir = __DIR__ . "/../../uploads/brand-images/";
        $uploadPath = $uploadDir . $newImageName;

        // Ensure the upload directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Move the uploaded file to the designated folder
        if (!move_uploaded_file($image['tmp_name'], $uploadPath)) {
            throw new Exception("Failed to move the uploaded image.");
        }
    } else {
        // Fetch the existing image name if no new image is uploaded
        $existingImageQuery = $db->getOne("SELECT `image` FROM `brands` WHERE `brandId` = ?i", $brandId);
        if ($existingImageQuery) {
            $newImageName = $existingImageQuery; // Use the existing image name from the database
        } else {
            throw new Exception("No image provided and no existing image found for the brand.");
        }
    }

    // Update the brand record with the image path
    $res = $db->query(
        "UPDATE `brands` SET 
            `brandName` = ?s, 
            `image` = ?s 
        WHERE `brandId` = ?i",
        $brandName,
        $newImageName, // Store the new or existing image name in the database
        $brandId
    );

    if ($res) {
        // Success message
        echo "SUCCESS|Brand updated successfully!";
    } else {
        // Safeguard: Unexpected scenario
        echo "ERROR|Query execution returned false with no specific error!";
    }
} catch (Exception $e) {
    // Log the error (implement a logger or store it in a database/log file)
    error_log("Error updating brand: " . $e->getMessage());

    // Send a JSON error response (or handle it based on your application)
    echo "ERROR|Failed to update the brand. Error: " . $e->getMessage();
}
?>
