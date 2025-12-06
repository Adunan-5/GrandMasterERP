<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";

// Sanitize input
$taskId = filter_var($_POST['taskId'] ?? null, FILTER_SANITIZE_NUMBER_INT) ?: null;

try {
    if (!$taskId) {
        throw new Exception('Task ID is required.');
    }

    // Verify task exists and get projectId for history logging
    $res = $db->query("SELECT projectId, taskStatus FROM `consultation_project_tasks` WHERE `taskId` = ?s", $taskId);
    $task = mysqli_fetch_assoc($res);
    if (!$task) {
        throw new Exception('Task not found.');
    }
    $projectId = $task['projectId'];

    // Check if task is already completed
    if ($task['taskStatus'] === 'COMPLETED') {
        throw new Exception('Task is already marked as completed.');
    }

    // Update task status to COMPLETED
    $res = $db->query(
        "UPDATE `consultation_project_tasks` 
         SET `taskStatus` = 'COMPLETED', `updatedAt` = CURRENT_TIMESTAMP 
         WHERE `taskId` = ?s",
        $taskId
    );

    if ($res === false) {
        throw new Exception('Failed to mark task as completed.');
    }

    // Log the activity
    recordProjectHistory($projectId, OT_MARK_TASK_DONE, logActivity(OT_MARK_TASK_DONE), "Task Marked as Done - " . getTaskNumberFromTaskID($taskId));

    echo json_encode([
        'status' => 'SUCCESS',
        'message' => 'Task marked as completed successfully'
    ]);
    exit();
} catch (Exception $e) {
    error_log("Error marking task as done: " . $e->getMessage());
    echo json_encode(['status' => 'ERROR', 'message' => $e->getMessage()]);
    exit;
}
?>