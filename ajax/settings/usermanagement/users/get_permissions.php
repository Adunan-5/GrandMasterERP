<?php
include_once __DIR__ . "/../../../../includes/baseIncludes.php";

header('Content-Type: application/json');

try {
    $userId = filter_var($_POST['userId'] ?? null, FILTER_VALIDATE_INT);
    if (!$userId) {
        throw new Exception("Invalid user ID.");
    }

    // 1️⃣ Fetch role mapping
    $mapping = $db->getRow("
        SELECT ur.roleId, ur.roleName, ur.permissions
        FROM user_role_mapping urm
        INNER JOIN user_roles ur ON ur.roleId = urm.roleId
        WHERE urm.userId = ?i
        LIMIT 1
    ", $userId);

    if (!$mapping) {
        throw new Exception("No role assigned to this user.");
    }

    $roleId = (int) $mapping['roleId'];
    $permissionsRaw = $mapping['permissions'] ?? '{}';
    $rolePermissions = json_decode($permissionsRaw, true) ?: [];

    // 2️⃣ Fetch user overrides
    $userOverrides = $db->getAll("SELECT module, capabilities FROM user_permissions WHERE userId = ?i", $userId);
    $userOverridesMap = [];

    foreach ($userOverrides as $row) {
        $decoded = json_decode($row['capabilities'] ?? '{}', true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $userOverridesMap[$row['module']] = $decoded;
        }
    }

    // 3️⃣ Merge permissions
    $finalPermissions = [];

    $allModules = array_unique(array_merge(array_keys($rolePermissions), array_keys($userOverridesMap)));

    foreach ($allModules as $module) {
        $roleCaps = $rolePermissions[$module] ?? [];
        $added = $userOverridesMap[$module]['added'] ?? [];
        $revoked = $userOverridesMap[$module]['revoked'] ?? [];

        // Combine role + added - revoked
        $effectiveCaps = array_unique(array_merge($roleCaps, $added));
        $effectiveCaps = array_diff($effectiveCaps, $revoked);

        // Generate formatted capability list
        $capList = [];
        foreach (['view', 'create', 'edit', 'delete', 'cancel'] as $cap) {
            $capList[] = [
                'name' => $cap,
                'label' => ucfirst($cap),
                'checked' => in_array($cap, $effectiveCaps)
            ];
        }

        $finalPermissions[$module] = $capList;
    }

    echo json_encode([
        'status' => 'SUCCESS',
        'roleId' => $roleId,
        'roleName' => $mapping['roleName'],
        'modules' => $finalPermissions
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'ERROR',
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
