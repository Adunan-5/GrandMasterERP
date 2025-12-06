<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../../../includes/baseIncludes.php";

try {
    $companyId = intval($_SESSION['SES_SELECTED_COMPANY']);
    $companyName = getCompanyNameByID($companyId);

    // Initialize the WHERE clause
    $whereClause = "";
    if ($companyName === 'GrandMaster Holding Co') {
        // No company filter for GrandMaster Holding Co (fetch all users except userId 1)
        $whereClause = "users.userId != 1";
    } else {
        // Filter by companyId for other companies
        $whereClause = "user_company_mapping.companyId = $companyId AND users.userId != 1";
    }

    // Query to fetch user details with role names
    $query = "SELECT DISTINCT
        users.userId,
        users.userName,
        users.firstName,
        users.lastName,
        users.profilePic,
        users.email,
        users.dateCreated,
        users.dateModified,
        users.active,
        user_roles.roleName,
        user_company_mapping.companyId,
        companies.companyLabel
    FROM
        users
    LEFT JOIN
        user_role_mapping ON users.userId = user_role_mapping.userId
    LEFT JOIN
        user_roles ON user_role_mapping.roleId = user_roles.roleId
    LEFT JOIN
        user_company_mapping ON users.userId = user_company_mapping.userId
    LEFT JOIN
        companies ON user_company_mapping.companyId = companies.companyId
    WHERE
        $whereClause";

    // Execute query
    $res = $db->query($query);
    $profilePicFolder = PROFILE_PIC_FOLDER;
    $defaultProfileImage = DEFAULT_PROFILE_IMAGE;

    // Default profile picture
    $defaultProfilePic = $profilePicFolder . $defaultProfileImage;

    $users = [];
    while ($row = mysqli_fetch_assoc($res)) {
        // Check if the user already exists in the users array
        if (!isset($users[$row['userId']])) {
            // If not, create a new user entry
            $users[$row['userId']] = [
                'userId' => $row['userId'],
                'userName' => $row['userName'],
                'firstName' => $row['firstName'],
                'lastName' => $row['lastName'],
                'profilePic' => !empty($row['profilePic']) ? $profilePicFolder . $row['profilePic'] : $defaultProfilePic,
                'email' => $row['email'],
                'dateCreated' => $row['dateCreated'],
                'dateModified' => $row['dateModified'],
                'active' => $row['active'] == '1' ? 'Active' : 'Inactive',
                'roleName' => !empty($row['roleName']) ? $row['roleName'] : '-',
                'companyLabels' => [] // Initialize the array for company labels
            ];
        }

        // Add the company label to the user's companyLabels array
        if (!empty($row['companyLabel'])) {
            $users[$row['userId']]['companyLabels'][] = $row['companyLabel'];
        }
    }

    // Format the users data with concatenated company labels
    $data = [];
    foreach ($users as $user) {
        $data[] = [
            'userId' => $user['userId'],
            'userName' => $user['userName'],
            'firstName' => $user['firstName'],
            'lastName' => $user['lastName'],
            'profilePic' => $user['profilePic'],
            'email' => $user['email'],
            'dateCreated' => $user['dateCreated'],
            'dateModified' => $user['dateModified'],
            'active' => $user['active'],
            'roleName' => $user['roleName'],
            'companyLabel' => implode(', ', $user['companyLabels']) // Concatenate company labels
        ];
    }

    // Response format
    $response = [
        "data" => $data
    ];

    // Output JSON response
    echo json_encode($response);
} catch (Exception $e) {
    // Handle error
    echo json_encode([
        "error" => "An error occurred: " . $e->getMessage()
    ]);
}