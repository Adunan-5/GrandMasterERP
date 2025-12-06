<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";

try {
    // Fetch companyId from session
    $companyId = $_SESSION['SES_SELECTED_COMPANY'] ?? null;
    if (!$companyId) {
        throw new Exception("Company not selected or missing from session.");
    }

    // Sanitize inputs
    $roleName = filter_var($_POST['modalRoleName'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $description = filter_var($_POST['roleDescription'] ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if (empty($roleName)) {
        throw new Exception("Please enter a valid Role Name.");
    }

    // Permissions come in as JSON string from JS
    $permissionsJson = $_POST['permissions'] ?? '{}';

    // Validate JSON format
    json_decode($permissionsJson);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid permissions data format.");
    }

    // Check for duplicate role name
    $exists = $db->getRow("SELECT roleId FROM user_roles WHERE roleName = ?s AND companyId = ?i", $roleName, $companyId);
    if ($exists) {
        throw new Exception("A role with this name already exists for this company.");
    }

    // Insert role
    $res = $db->query("INSERT INTO user_roles 
        (roleName, description, permissions, companyId, active, createdAt, modifiedAt)
        VALUES (?s, ?s, ?s, ?i, 1, NOW(), NOW())",
        $roleName,
        $description,
        $permissionsJson,
        $companyId
    );

    if ($res) {
        echo "SUCCESS|Role created successfully!";
    } else {
        echo "ERROR|Failed to insert the role.";
    }

} catch (Exception $e) {
    error_log("Error adding role: " . $e->getMessage());
    echo "ERROR|Failed to add role. Error: " . $e->getMessage();
}
