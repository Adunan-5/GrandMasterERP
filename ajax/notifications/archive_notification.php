<?php
include_once __DIR__ . "/../../includes/baseIncludes.php"; 

try {
    $notification_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    
    if (!empty($notification_id)) {
        $user_id = getUserIDOfCurrentUser();
        if ($user_id > 0) { // Ensure logged in
            $res = $db->query("UPDATE `notifications` SET `is_archived` = 1 WHERE `notificationId` = ?s AND `recipient_userId` = ?s", $notification_id, $user_id);
            if (!$res) {
                error_log("archive_notification.php: Failed to archive $notification_id for user $user_id - " . $db->getLastError());
            }
        } else {
            error_log("archive_notification.php: No valid user session for $notification_id");
        }
    } else {
        error_log("archive_notification.php: Invalid id ($notification_id)");
    }
} catch (Exception $e) {
    error_log("archive_notification.php Exception: " . $e->getMessage());
}

// Redirect back to referrer (reloads dropdown seamlessly)
$redirect_url = $_SERVER['HTTP_REFERER'] ?? '/dashboard';
header("Location: " . $redirect_url);
exit;
?>