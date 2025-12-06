<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";

try {
    // Fetch companyId from session
    $companyId = $_SESSION['SES_SELECTED_COMPANY'] ?? null;
    if (!$companyId) {
        throw new Exception("Company not selected or missing from session.");
    }

    // Sanitize inputs
    $roleId = filter_var($_POST['editRoleId'] ?? null, FILTER_VALIDATE_INT);
    $roleName = filter_var($_POST['editRoleName'] ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $description = filter_var($_POST['roleDescription'] ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if (!$roleId) {
        throw new Exception("Invalid or missing Role ID.");
    }

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

    // Check for duplicate role name (exclude current role)
    $exists = $db->getRow(
        "SELECT roleId FROM user_roles WHERE roleName = ?s AND companyId = ?i AND roleId != ?i",
        $roleName,
        $companyId,
        $roleId
    );
    if ($exists) {
        throw new Exception("Another role with this name already exists for this company.");
    }

    // Update role
    $res = $db->query(
        "UPDATE user_roles 
         SET roleName = ?s, description = ?s, permissions = ?s, modifiedAt = NOW()
         WHERE roleId = ?i AND companyId = ?i",
        $roleName,
        $description,
        $permissionsJson,
        $roleId,
        $companyId
    );

    if ($res) {
        echo "SUCCESS|Role updated successfully!";
    } else {
        echo "ERROR|Failed to update the role.";
    }

} catch (Exception $e) {
    error_log("Error updating role: " . $e->getMessage());
    echo "ERROR|Failed to update the role. Error: " . $e->getMessage();
}
