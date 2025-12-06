<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";

try {
    $notification_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $url = isset($_GET['url']) ? urldecode($_GET['url']) : '/dashboard'; // Decode %2F to / safely
    
    if (!empty($notification_id) && $url !== '') {
        $user_id = getUserIDOfCurrentUser();
        if ($user_id > 0) { // Ensure logged in
            $res = $db->query("UPDATE `notifications` SET `is_read` = 1 WHERE `notificationId` = ?s AND `recipient_userId` = ?s", $notification_id, $user_id);
            if (!$res) {
                error_log("Failed to update notification $notification_id for user $user_id - " . $db->getLastError());
            }
        } else {
            error_log("No valid user session for notification $notification_id");
        }
    } else {
        error_log("Invalid id ($notification_id) or url ($url)");
        $url = '/dashboard'; // Force fallback
    }
} catch (Exception $e) {
    error_log("mark_read.php Exception: " . $e->getMessage());
    $url = '/dashboard'; // Safe fallback
}

// Critical: No output/echo before this – redirect immediately
header("Location: " . $url);
exit;
?>