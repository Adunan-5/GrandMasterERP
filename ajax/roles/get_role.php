<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";

try {
    $roleId = filter_var($_POST['roleId'] ?? null, FILTER_VALIDATE_INT);
    if (!$roleId) {
        throw new Exception("Invalid role ID.");
    }

    $role = $db->getRow("SELECT roleName, permissions FROM user_roles WHERE roleId = ?i", $roleId);
    if (!$role) {
        throw new Exception("Role not found.");
    }

    // Handle missing or empty permissions gracefully
    $permissionsRaw = $role['permissions'] ?? '{}';
    if (empty($permissionsRaw) || $permissionsRaw === 'null') {
        $permissionsRaw = '{}';
    }

    $permissions = json_decode($permissionsRaw, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $permissions = [];
    }

    echo json_encode([
        'status' => 'SUCCESS',
        'roleName' => $role['roleName'],
        'permissions' => $permissions
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'ERROR',
        'message' => $e->getMessage()
    ]);
}
