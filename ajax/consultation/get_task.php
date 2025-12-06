<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";

if ($_POST['taskId']) {
    $taskId = filter_var($_POST['taskId'], FILTER_SANITIZE_NUMBER_INT);

    try {
        // Fetch task details
        $task = $db->getRow(
            "SELECT taskId, projectId, taskTitle, taskDescription, dueDate
             FROM consultation_project_tasks
             WHERE taskId = ?s",
            $taskId
        );

        if (!$task) {
            echo json_encode(['status' => 'ERROR', 'message' => 'Task not found']);
            exit;
        }

        // Fetch assignee IDs
        $assignees = $db->getAll(
            "SELECT userId
             FROM consultation_project_task_assignees
             WHERE taskId = ?s",
            $taskId
        );
        $assigneeIds = array_column($assignees, 'userId');
        $dueDate = $task['dueDate'] ? date('Y-m-d', strtotime($task['dueDate'])) : '';

        echo json_encode([
            'status' => 'SUCCESS',
            'data' => [
                'taskId' => $task['taskId'],
                'taskTitle' => $task['taskTitle'],
                'taskDescription' => $task['taskDescription'],
                'assigneeIds' => $assigneeIds,
                'dueDate' => $dueDate
            ]
        ]);
        exit;
    } catch (Exception $e) {
        error_log("Error fetching task: " . $e->getMessage());
        echo json_encode(['status' => 'ERROR', 'message' => $e->getMessage()]);
        exit;
    }
}

echo json_encode(['status' => 'ERROR', 'message' => 'Invalid task ID']);
exit;
?>