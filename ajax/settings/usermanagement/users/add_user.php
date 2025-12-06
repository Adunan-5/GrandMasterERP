<?php
include_once __DIR__ . "/../../../../includes/baseIncludes.php";

try {
    // Validate and sanitize input data
    $firstName = filter_var($_POST['firstName'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $lastName = filter_var($_POST['lastName'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $userName = filter_var($_POST['userName'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $password = filter_var($_POST['password'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $roleId = filter_var($_POST['roleId'], FILTER_VALIDATE_INT);
    $countryId = filter_var($_POST['countryId'], FILTER_VALIDATE_INT);
    $companyIds = isset($_POST['companyIds']) ? $_POST['companyIds'] : [];
    $image = $_FILES['userImage'];
    $contractFile = $_FILES['userContractFile'];
    // $iqamaNumber = filter_var($_POST['iqamaNumber'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    // $iqamaExpiry = filter_var($_POST['iqamaExpiry'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $idNumber = filter_var($_POST['idNumber'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $idExpiry = filter_var($_POST['idExpiry'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $passportNumber = filter_var($_POST['passportNumber'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $passportExpiry = filter_var($_POST['passportExpiry'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $medicalInsuranceNumber = filter_var($_POST['medicalInsuranceNumber'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $medicalInsuranceExpiry = filter_var($_POST['medicalInsuranceExpiry'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $medicalInsuranceCompany = filter_var($_POST['medicalInsuranceCompany'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $workDaysPerMonth = filter_var($_POST['workDaysPerMonth'], FILTER_VALIDATE_INT) ?: null;
    $hoursPerDay = filter_var($_POST['hoursPerDay'], FILTER_VALIDATE_INT) ?: null;
    
    // Validate decimal fields
    $basicSalary = filter_var($_POST['basicSalary'], FILTER_VALIDATE_FLOAT) ?: null;
    $otMultiplier = filter_var($_POST['otMultiplier'], FILTER_VALIDATE_FLOAT) ?: null;
    // $saudiEmployerGOSI = filter_var($_POST['saudiEmployerGOSI'], FILTER_VALIDATE_FLOAT) ?: null;
    // $saudiEmployeeGOSI = filter_var($_POST['saudiEmployeeGOSI'], FILTER_VALIDATE_FLOAT) ?: null;
    // $nonSaudiEmployerGOSI = filter_var($_POST['nonSaudiEmployerGOSI'], FILTER_VALIDATE_FLOAT) ?: null;
    // $nonSaudiEmployeeGOSI = filter_var($_POST['nonSaudiEmployeeGOSI'], FILTER_VALIDATE_FLOAT) ?: null;

    $employerGOSI = filter_var($_POST['employerGOSI'], FILTER_VALIDATE_FLOAT) ?: null;
    $employeeGOSI = filter_var($_POST['employeeGOSI'], FILTER_VALIDATE_FLOAT) ?: null;
    
    $gosiCap = filter_var($_POST['gosiCap'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $contractStartDate = filter_var($_POST['contractStartDate'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $contractEndDate = filter_var($_POST['contractEndDate'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $status = 1; // Default status active

    // Convert empty date fields to NULL
    // $iqamaExpiry = empty($iqamaExpiry) ? null : $iqamaExpiry;
    $idExpiry = empty($idExpiry) ? null : $idExpiry;
    $passportExpiry = empty($passportExpiry) ? null : $passportExpiry;
    $medicalInsuranceExpiry = empty($medicalInsuranceExpiry) ? null : $medicalInsuranceExpiry;
    $contractStartDate = empty($contractStartDate) ? null : $contractStartDate;
    $contractEndDate = empty($contractEndDate) ? null : $contractEndDate;

    // Validate required fields
    if (!$firstName || !$userName || !$email || !$roleId || empty($companyIds)) {
        throw new Exception("Missing required fields or no companies selected.");
    }

    // Validate basicSalary if it’s required (uncomment if basicSalary is mandatory)
    // if ($basicSalary === null || $basicSalary === false) {
    //     throw new Exception("Invalid or missing basic salary.");
    // }

    // Check if email already exists
    $emailCheck = $db->query("SELECT userID FROM `users` WHERE `email` = ?s", $email);
    if ($emailCheck && $db->numRows($emailCheck) > 0) {
        // throw new Exception("Email Already Exists");

        echo "ERROR|Email Already Exists";
        exit;
    }

    // Check if iqama number already exists (only if provided)
    if (!empty($idNumber)) {
        $idCheck = $db->query("SELECT userID FROM `users` WHERE `iqamaNumber` = ?s", $idNumber);
        if ($idCheck && $db->numRows($idCheck) > 0) {
            // throw new Exception("Iqama Number Already Exists");

            echo "ERROR|Iqama Number Already Exists";
            exit;
        }
    }

    if($countryId == 194) {
        $passportNumber = null;
        $passportExpiry = null;
    }

    $defaultImageName = DEFAULT_PROFILE_IMAGE;
    $uploadDir = __DIR__ . "/../../../../uploads/userprofile-images/";
    $imageName = $defaultImageName;

    // Handle image upload
    if (isset($image) && $image['error'] === UPLOAD_ERR_OK) {
        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $imageExtension = pathinfo($image['name'], PATHINFO_EXTENSION);
        if (!in_array(strtolower($imageExtension), $allowedExtensions)) {
            throw new Exception("Invalid image type. Only jpg, jpeg, and png are allowed.");
        }

        $imageName = $userName . "_" . bin2hex(random_bytes(4)) . "." . $imageExtension;
        $uploadPath = $uploadDir . $imageName;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (!move_uploaded_file($image['tmp_name'], $uploadPath)) {
            throw new Exception("Failed to move the uploaded image.");
        }
    }

    $contractName = null;
    if (isset($contractFile) && $contractFile['error'] === UPLOAD_ERR_OK) {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
        $fileExtension = strtolower(pathinfo($contractFile['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedExtensions)) {
            throw new Exception("Invalid contract file type. Only jpg, jpeg, png, pdf are allowed.");
        }

        $contractName = $userName . "_contract_" . bin2hex(random_bytes(4)) . "." . $fileExtension;
        $contractDir = __DIR__ . "/../../../../uploads/user-contracts/";
        $contractPath = $contractDir . $contractName;

        if (!is_dir($contractDir)) {
            mkdir($contractDir, 0777, true);
        }

        if (!move_uploaded_file($contractFile['tmp_name'], $contractPath)) {
            throw new Exception("Failed to move the uploaded contract file.");
        }
    }

    // Generate hashed password
    $hashedPassword = generateSsha512Password($password);

    // Insert user details
    $userInsert = $db->query(
        "INSERT INTO `users` (
            `firstName`, `lastName`, `userName`, `profilePic`, `userContract`, `countryId`, 
            `email`, `password`, `iqamaNumber`, `iqamaExpiry`, `passportNumber`, `passportExpiry`, 
            `medicalInsuranceNumber`, `medicalInsuranceExpiry`, `medicalInsuranceCompany`, 
            `workDaysPerMonth`, `hoursPerDay`, `basicSalary`, `otMultiplier`, `gosiCap`, 
            `employerGOSI`, `employeeGOSI`, `contractStartDate`, `contractEndDate`, `active`
        ) VALUES (?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s)",
        $firstName,
        $lastName,
        $userName,
        $imageName,
        $contractName,
        $countryId,
        $email,
        $hashedPassword,
        $idNumber,
        $idExpiry,
        $passportNumber,
        $passportExpiry,
        $medicalInsuranceNumber,
        $medicalInsuranceExpiry,
        $medicalInsuranceCompany,
        $workDaysPerMonth,
        $hoursPerDay,
        $basicSalary,
        $otMultiplier,
        $gosiCap,
        $employerGOSI,
        $employeeGOSI,
        $contractStartDate,
        $contractEndDate,
        $status
    );

    // Get the last inserted user ID
    $userID = $db->insertId();

    if (!$userID) {
        throw new Exception("Failed to retrieve user ID.");
    }

    // Insert role mapping
    $roleInsert = $db->query("INSERT INTO `user_role_mapping` (`userId`, `roleId`) VALUES (?i, ?i)", $userID, $roleId);

    if (!$roleInsert) {
        throw new Exception("Failed to map role to user.");
    }

    // Insert company mappings
    foreach ($companyIds as $companyId) {
        $companyId = filter_var($companyId, FILTER_VALIDATE_INT);
        if ($companyId) {
            $db->query("INSERT INTO `user_company_mapping` (`userId`, `companyId`) VALUES (?i, ?i)", $userID, $companyId);
        }
    }

    echo "SUCCESS|User added successfully!";
} catch (Exception $e) {
    error_log("Error adding user: " . $e->getMessage());
    echo "ERROR|Failed to add user. Error: " . $e->getMessage();
}
?>