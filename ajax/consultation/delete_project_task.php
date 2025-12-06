<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";

// Sanitize input
$taskId = filter_var($_POST['taskId'] ?? null, FILTER_SANITIZE_NUMBER_INT) ?: null;

try {
    if (!$taskId) {
        throw new Exception('Task ID is required.');
    }

    // Verify task exists and get projectId for history logging
    $res = $db->query("SELECT projectId FROM `consultation_project_tasks` WHERE `taskId` = ?s", $taskId);
    $task = mysqli_fetch_assoc($res);
    if (!$task) {
        throw new Exception('Task not found.');
    }
    $projectId = $task['projectId'];

    $taskNumber = getTaskNumberFromTaskID($taskId);

    // Delete task assignees
    $db->query("DELETE FROM `consultation_project_task_assignees` WHERE `taskId` = ?s", $taskId);

    // Delete task
    $res = $db->query("DELETE FROM `consultation_project_tasks` WHERE `taskId` = ?s", $taskId);

    if ($res === false) {
        throw new Exception('Failed to delete task.');
    }

    // Log the activity
    recordProjectHistory($projectId, OT_DELETE_TASK, logActivity(OT_DELETE_TASK), "Task Deleted - " . $taskNumber);

    echo json_encode([
        'status' => 'SUCCESS',
        'message' => 'Task deleted successfully'
    ]);
    exit();
} catch (Exception $e) {
    error_log("Error deleting task: " . $e->getMessage());
    echo json_encode(['status' => 'ERROR', 'message' => $e->getMessage()]);
    exit;
}
?>