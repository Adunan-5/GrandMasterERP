<?php
if (isset($_FILES['supplierQuotationAttachmentFile'])) {
    $file = $_FILES['supplierQuotationAttachmentFile'];

    // File details
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];
    $fileType = $file['type'];

    // Specify the upload directory
    $uploadDir =  __DIR__ .'/../../uploads/';
    $uploadedFileName = microtime() ."_" . basename($fileName);
    $uploadFile = $uploadDir . $uploadedFileName;


    // Check for errors
    if ($fileError === 0) {
        // Move the uploaded file to the desired directory
        if (move_uploaded_file($fileTmpName, $uploadFile)) {
            echo json_encode(['status' => 'success', 'message' => $uploadedFileName]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded file.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'File upload error: ' . $fileError]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No file uploaded.']);
}