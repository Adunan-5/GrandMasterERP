<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";
header('Content-Type: application/json');

try {
    $roleId = filter_var($_POST['roleId'] ?? null, FILTER_VALIDATE_INT);
    if (!$roleId) throw new Exception("Invalid role ID");

    $role = $db->getRow("SELECT permissions FROM user_roles WHERE roleId = ?i", $roleId);
    $permissions = json_decode($role['permissions'] ?? '{}', true) ?: [];

    // Transform structure for frontend compatibility
    $formatted = [];
    foreach ($permissions as $module => $caps) {
        $formatted[$module] = [];
        foreach (['view', 'create', 'edit', 'delete', 'cancel'] as $cap) {
            $formatted[$module][] = [
                'name' => $cap,
                'label' => ucfirst($cap),
                'checked' => in_array($cap, $caps)
            ];
        }
    }

    echo json_encode(['status' => 'SUCCESS', 'modules' => $formatted]);
} catch (Exception $e) {
    echo json_encode(['status' => 'ERROR', 'message' => $e->getMessage()]);
}
