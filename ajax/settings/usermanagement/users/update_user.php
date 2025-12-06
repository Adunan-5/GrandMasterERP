<?php
include_once __DIR__ . "/../../../../includes/baseIncludes.php";

try {
    // Validate and sanitize input data
    $userID = filter_var($_POST['userId'], FILTER_VALIDATE_INT);
    $firstName = filter_var($_POST['modalEditUserFirstName'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $lastName = filter_var($_POST['modalEditUserLastName'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $userName = filter_var($_POST['modalEditUserName'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_var($_POST['modalEditUserEmail'], FILTER_VALIDATE_EMAIL);
    $status = filter_var($_POST['modalEditUserStatus'], FILTER_VALIDATE_INT);
    $roleId = filter_var($_POST['modalEditUserRole'], FILTER_VALIDATE_INT);
    $companyIds = isset($_POST['userCompanies']) ? $_POST['userCompanies'] : [];
    $profilePic = $_FILES['userImage'];
    $contractFile = $_FILES['userContract'];
    // $iqamaNumber = filter_var($_POST['modalEditUserIQAMANumber'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    // $iqamaExpiry = filter_var($_POST['modalEditUserIQAMAExpiry'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $idNumber = filter_var($_POST['modalEditUserIdNumber'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $idExpiry = filter_var($_POST['modalEditUserIdExpiry'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $passportNumber = filter_var($_POST['modalEditUserPassportNumber'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $passportExpiry = filter_var($_POST['modalEditUserPassportExpiry'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $medicalInsuranceNumber = filter_var($_POST['modalEditUserMedicalInsuranceNumber'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $medicalInsuranceExpiry = filter_var($_POST['modalEditUserMedicalInsuranceExpiry'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $medicalInsuranceCompany = filter_var($_POST['modalEditUserMedicalInsuranceCompany'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $contractStartDate = filter_var($_POST['modalEditUserContractStartDate'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $contractEndDate = filter_var($_POST['modalEditUserContractEndDate'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $userCountry = filter_var($_POST['modalEditUserCountry'], FILTER_VALIDATE_INT) ?: null;
    $workDaysPerMonth = filter_var($_POST['modalEditUserWorkDaysPerMonth'], FILTER_VALIDATE_INT) ?: null;
    $hoursPerDay = filter_var($_POST['modalEditUserHoursPerDay'], FILTER_VALIDATE_INT) ?: null;

    // Validate decimal fields
    $basicSalary = filter_var($_POST['modalEditUserBasicSalary'], FILTER_VALIDATE_FLOAT) ?: null;
    $otMultiplier = filter_var($_POST['modalEditUserOTMultiplier'], FILTER_VALIDATE_FLOAT) ?: null;
    // $saudiEmployerGOSI = filter_var($_POST['modalEditUserSaudiEmployerGOSI'], FILTER_VALIDATE_FLOAT) ?: null;
    // $saudiEmployeeGOSI = filter_var($_POST['modalEditUserSaudiEmployeeGOSI'], FILTER_VALIDATE_FLOAT) ?: null;
    // $nonSaudiEmployerGOSI = filter_var($_POST['modalEditUserNonSaudiEmployerGOSI'], FILTER_VALIDATE_FLOAT) ?: null;
    // $nonSaudiEmployeeGOSI = filter_var($_POST['modalEditUserNonSaudiEmployeeGOSI'], FILTER_VALIDATE_FLOAT) ?: null;
    $employerGOSI = filter_var($_POST['modalEditUserEmployerGOSI'], FILTER_VALIDATE_FLOAT) ?: null;
    $employeeGOSI = filter_var($_POST['modalEditUserEmployeeGOSI'], FILTER_VALIDATE_FLOAT) ?: null;

    $gosiCap = filter_var($_POST['modalEditUserGOSICap'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Convert empty date fields to NULL
    // $iqamaExpiry = empty($iqamaExpiry) ? null : $iqamaExpiry;
    $idExpiry = empty($idExpiry) ? null : $idExpiry;
    $passportExpiry = empty($passportExpiry) ? null : $passportExpiry;
    $medicalInsuranceExpiry = empty($medicalInsuranceExpiry) ? null : $medicalInsuranceExpiry;
    $contractStartDate = empty($contractStartDate) ? null : $contractStartDate;
    $contractEndDate = empty($contractEndDate) ? null : $contractEndDate;

    // Validate required fields
    if (!$userID || !$firstName || !$userName || !$email || !$roleId || empty($companyIds)) {
        throw new Exception("Missing required fields or no companies selected.");
    }

    // Check if email already exists for another user
    $emailCheck = $db->query("SELECT userID FROM `users` WHERE `email` = ?s AND `userID` != ?i", $email, $userID);
    if ($emailCheck && $db->numRows($emailCheck) > 0) {
        echo "ERROR|Email Already Exists";
        exit;
    }

    // Check if iqama number already exists for another user (only if provided)
    if (!empty($idNumber)) {
        $idCheck = $db->query("SELECT userID FROM `users` WHERE `iqamaNumber` = ?s AND `userID` != ?i", $idNumber, $userID);
        if ($idCheck && $db->numRows($idCheck) > 0) {
            echo "ERROR|Iqama Number Already Exists";
            exit;
        }
    }

    if($userCountry == 194) { 
        $passportNumber = null;
        $passportExpiry = null;
    }

    // Default image name if no image is uploaded or found
    $defaultImageName = DEFAULT_PROFILE_IMAGE; // Specify your default image name

    // Check if a new profile image is uploaded
    $newImageName = null;
    if (isset($profilePic) && $profilePic['error'] === UPLOAD_ERR_OK) {
        // Validate image type
        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $imageExtension = strtolower(pathinfo($profilePic['name'], PATHINFO_EXTENSION));

        if (!in_array($imageExtension, $allowedExtensions)) {
            throw new Exception("Invalid image type. Only JPG, JPEG, and PNG are allowed.");
        }

        // Generate a unique image name
        $newImageName = $userName . "_" . bin2hex(random_bytes(4)) . "." . $imageExtension;
        $uploadDir = __DIR__ . "/../../../../uploads/userprofile-images/";
        $uploadPath = $uploadDir . $newImageName;

        // Ensure the upload directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Move the uploaded file to the designated folder
        if (!move_uploaded_file($profilePic['tmp_name'], $uploadPath)) {
            throw new Exception("Failed to move the uploaded image.");
        }
    } else {
        // Fetch the existing image name if no new image is uploaded
        $existingImageQuery = $db->getOne("SELECT `profilePic` FROM `users` WHERE `userID` = ?s", $userID);
        if ($existingImageQuery) {
            $newImageName = $existingImageQuery; // Use the existing image name from the database
        } else {
            $newImageName = $defaultImageName; // If no image found, use the default image
        }
    }

    $newContractName = null;
    if (isset($contractFile) && $contractFile['error'] === UPLOAD_ERR_OK) {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
        $fileExtension = strtolower(pathinfo($contractFile['name'], PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $allowedExtensions)) {
            throw new Exception("Invalid contract file type. Only JPG, JPEG, PNG, and PDF are allowed.");
        }

        $newContractName = $userName . "_contract_" . bin2hex(random_bytes(4)) . "." . $fileExtension;
        $contractDir = __DIR__ . "/../../../../uploads/user-contracts/";
        $contractPath = $contractDir . $newContractName;

        if (!is_dir($contractDir)) {
            mkdir($contractDir, 0777, true);
        }

        if (!move_uploaded_file($contractFile['tmp_name'], $contractPath)) {
            throw new Exception("Failed to move the uploaded contract file.");
        }
    } else {
        $existingContractQuery = $db->getOne("SELECT `userContract` FROM `users` WHERE `userID` = ?s", $userID);
        $newContractName = $existingContractQuery ?: null; 
    }

    // Update user details
    $res = $db->query(
        "UPDATE `users` SET 
            `firstName` = ?s, 
            `lastName` = ?s, 
            `userName` = ?s, 
            `email` = ?s, 
            `profilePic` = ?s,
            `userContract` = ?s,
            `iqamaNumber` = ?s,
            `iqamaExpiry` = ?s,
            `passportNumber` = ?s,
            `passportExpiry` = ?s,
            `medicalInsuranceNumber` = ?s,
            `medicalInsuranceExpiry` = ?s,
            `medicalInsuranceCompany` = ?s,
            `contractStartDate` = ?s,
            `contractEndDate` = ?s,
            `countryId` = ?s,
            `workDaysPerMonth` = ?s,
            `hoursPerDay` = ?s,
            `basicSalary` = ?s,
            `otMultiplier` = ?s,
            `gosiCap` = ?s,
            `employerGOSI` = ?s,
            `employeeGOSI` = ?s,
            `active` = ?s
        WHERE `userID` = ?i",
        $firstName,
        $lastName,
        $userName,
        $email,
        $newImageName,
        $newContractName,
        $idNumber,
        $idExpiry,
        $passportNumber,
        $passportExpiry,
        $medicalInsuranceNumber,
        $medicalInsuranceExpiry,
        $medicalInsuranceCompany,
        $contractStartDate,
        $contractEndDate,
        $userCountry,
        $workDaysPerMonth,
        $hoursPerDay,
        $basicSalary,
        $otMultiplier,
        $gosiCap,
        $employerGOSI,
        $employeeGOSI,
        $status,
        $userID
    );

    // Update role mapping
    $roleUpdate = $db->query(
        "UPDATE `user_role_mapping` SET `roleId` = ?s WHERE `userId` = ?s",
        $roleId,
        $userID
    );

    // Clear existing company mappings for the user before updating
    $db->query("DELETE FROM `user_company_mapping` WHERE `userId` = ?s", $userID);

    // Insert new company mappings
    if (!empty($companyIds)) {
        foreach ($companyIds as $companyId) {
            // Insert each selected company into the mapping table
            $companyUpdate = $db->query(
                "INSERT INTO `user_company_mapping` (`userId`, `companyId`) VALUES (?s, ?s)",
                $userID,
                $companyId
            );
        }
    }

    // $db->query("DELETE FROM `user_asset_mapping` WHERE `userId` = ?s", $userID);
    // if (!empty($assetIds)) {
    //     foreach ($assetIds as $assetId) {
    //         $db->query(
    //             "INSERT INTO `user_asset_mapping` (`userId`, `assetId`, `assigneeId`) VALUES (?s, ?s, ?s)",
    //             $userID,
    //             $assetId,
    //             getUserIDOfCurrentUser()
    //         );
    //     }
    // }

    if ($res && $roleUpdate && $companyUpdate) {
        echo "SUCCESS|User updated successfully!";
    } else {
        echo "ERROR|Query execution returned false!";
    }
} catch (Exception $e) {
    error_log("Error updating user: " . $e->getMessage());
    echo "ERROR|Failed to update user. Error: " . $e->getMessage();
}
?>
