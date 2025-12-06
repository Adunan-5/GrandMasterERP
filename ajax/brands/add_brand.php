<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";

$brandName = filter_var($_POST['brandName'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$image = $_FILES['brandImage'];

try {
    $defaultImageName = "no-image-available-placeholder.png"; // Default image file name
    $uploadDir = __DIR__ . "/../../uploads/brand-images/";
    $imageName = $defaultImageName; // Default to the placeholder image

    // Check if an image was uploaded
    if (isset($image) && $image['error'] === UPLOAD_ERR_OK) {
        // Validate image type
        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $imageExtension = pathinfo($image['name'], PATHINFO_EXTENSION);
        if (!in_array(strtolower($imageExtension), $allowedExtensions)) {
            throw new Exception("Invalid image type. Only jpg, jpeg, and png are allowed.");
        }

        // Generate a unique image name
        $imageName = $brandName . "_" . bin2hex(random_bytes(4)) . "." . $imageExtension;
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

    // Insert data into the database
    $res = $db->query(
        "INSERT INTO `brands` (
            `brandName`,
            `image`
        ) VALUES (
            ?s, 
            ?s
        )",
        $brandName,
        $imageName
    );

    if ($res) {
        // Success message
        echo "SUCCESS|Brand added successfully!";
    } else {
        // Safeguard: Unexpected scenario
        echo "ERROR|Query execution returned false with no specific error!";
    }
} catch (Exception $e) {
    // Log the error (you can implement a logger or store it in a database/log file)
    error_log("Error inserting brand: " . $e->getMessage());

    // Send a JSON error response (or handle it based on your application)
    echo "ERROR|Failed to add the brand. Error: " . $e->getMessage();
}