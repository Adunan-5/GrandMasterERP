<?php
include_once __DIR__ . "/../../../../includes/baseIncludes.php";

try {
    // Validate and sanitize input data
    $userID = filter_var($_POST['userId'], FILTER_VALIDATE_INT);
    $certificateName = filter_var($_POST['userCertificateName'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $certificateFile = $_FILES['userCertificationFile'] ?? null;

    if (!$userID) {
        throw new Exception("Invalid or missing user ID.");
    }

    if (empty($certificateName)) {
        throw new Exception("Certificate name is required.");
    }

    // Handle file upload
    $newCertFileName = null;
    if ($certificateFile && $certificateFile['error'] === UPLOAD_ERR_OK) {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
        $fileExtension = strtolower(pathinfo($certificateFile['name'], PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $allowedExtensions)) {
            throw new Exception("Invalid file type. Only JPG, JPEG, PNG, and PDF are allowed.");
        }

        // Generate unique name
        $newCertFileName = "cert_" . $userID . "_" . bin2hex(random_bytes(4)) . "." . $fileExtension;
        $uploadDir = __DIR__ . "/../../../../uploads/user-certifications/";
        $uploadPath = $uploadDir . $newCertFileName;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (!move_uploaded_file($certificateFile['tmp_name'], $uploadPath)) {
            throw new Exception("Failed to move the uploaded certificate file.");
        }
    } else {
        throw new Exception("Certificate file is required.");
    }

    // Insert into user_certificate_mapping
    $insert = $db->query(
        "INSERT INTO `user_certificate_mapping` (`userId`, `certificateName`, `certificateFile`, `uploadedBy`) 
         VALUES (?s, ?s, ?s, ?s)",
        $userID,
        $certificateName,
        $newCertFileName,
        getUserIDOfCurrentUser()
    );

    if ($insert) {
        echo "SUCCESS|Certification uploaded successfully!";
    } else {
        echo "ERROR|Failed to insert certification record.";
    }
} catch (Exception $e) {
    error_log("Error uploading certificate: " . $e->getMessage());
    echo "ERROR|Failed to upload certification. Error: " . $e->getMessage();
}
?>
