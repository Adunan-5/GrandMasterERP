<?php
include_once __DIR__ . "/../../includes/baseIncludes.php"; 

try {
    $user_id = getUserIDOfCurrentUser();
    if ($user_id > 0) { // Ensure logged in
        $res = $db->query("UPDATE `notifications` SET `is_read` = 1 WHERE `recipient_userId` = ?s AND `is_read` = 0 AND `is_archived` = 0 AND `is_active` = 1", $user_id);
        if (!$res) {
            error_log("mark_all_read.php: Failed to update for user $user_id - " . $db->getLastError());
        }
    } else {
        error_log("mark_all_read.php: No valid user session");
    }
} catch (Exception $e) {
    error_log("mark_all_read.php Exception: " . $e->getMessage());
}

// Redirect back to referrer (e.g., dashboard) or fallback
$redirect_url = $_SERVER['HTTP_REFERER'] ?? '/dashboard';
header("Location: " . $redirect_url);
exit;
?>