<?php
include_once __DIR__ . "/../../../../includes/baseIncludes.php";

header('Content-Type: text/plain');

try {
    $userId = filter_var($_POST['userId'] ?? null, FILTER_VALIDATE_INT);
    $roleId = filter_var($_POST['roleId'] ?? null, FILTER_VALIDATE_INT);
    $permissions = json_decode($_POST['permissions'] ?? '[]', true);

    if (!$userId || !$roleId) {
        throw new Exception("Invalid user or role ID.");
    }

    if (!is_array($permissions)) {
        throw new Exception("Invalid permissions format.");
    }

    // 1️⃣ Ensure role mapping exists (insert or update)
    $mappingExists = $db->getRow("SELECT mappingId FROM user_role_mapping WHERE userId = ?i", $userId);

    if ($mappingExists) {
        $db->query("UPDATE user_role_mapping SET roleId = ?i WHERE userId = ?i", $roleId, $userId);
    } else {
        $db->query("INSERT INTO user_role_mapping (userId, roleId, createdBy) VALUES (?i, ?i, ?i)", $userId, $roleId, getLoggedInUserId());
    }

    // 2️⃣ Fetch base role permissions
    $role = $db->getRow("SELECT permissions FROM user_roles WHERE roleId = ?i", $roleId);
    $rolePermissions = json_decode($role['permissions'] ?? '{}', true) ?: [];

    // 3️⃣ Calculate diffs and save per module
    foreach ($permissions as $module => $userCaps) {
        if (!is_array($userCaps)) continue;

        $roleCaps = $rolePermissions[$module] ?? [];

        $added = array_values(array_diff($userCaps, $roleCaps));
        $revoked = array_values(array_diff($roleCaps, $userCaps));

        $json = json_encode(['added' => $added, 'revoked' => $revoked], JSON_UNESCAPED_UNICODE);

        $exists = $db->getRow("SELECT id FROM user_permissions WHERE userId = ?i AND module = ?s", $userId, $module);

        if ($exists) {
            $db->query(
                "UPDATE user_permissions SET capabilities = ?s WHERE userId = ?i AND module = ?s",
                $json, $userId, $module
            );
        } else {
            $db->query(
                "INSERT INTO user_permissions (userId, module, capabilities) VALUES (?i, ?s, ?s)",
                $userId, $module, $json
            );
        }
    }

    echo "SUCCESS|User permissions updated successfully";

} catch (Exception $e) {
    echo "ERROR|" . $e->getMessage();
}
