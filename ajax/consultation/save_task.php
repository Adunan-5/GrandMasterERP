<?php
include_once  __DIR__ . "/../../includes/baseIncludes.php";

$projectId = filter_var($_POST['projectId'] ?? null, FILTER_SANITIZE_NUMBER_INT) ?: null;
$taskId = filter_var($_POST['taskId'] ?? null, FILTER_SANITIZE_NUMBER_INT) ?: null;
//$itemId = filter_var($_POST['itemId'], FILTER_SANITIZE_NUMBER_INT) ?: NULL;
$taskTitle = filter_var($_POST['taskTitle'] ?? null, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$taskDescription = filter_var($_POST['taskDescription'] ?? null, FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: null;
$dueDate = $_POST['dueDate'] ?? null;
$assigneeIds = isset($_POST['assigneeIds']) && !empty($_POST['assigneeIds']) ? json_decode($_POST['assigneeIds'], true) : [];

try {

    if ($taskId) {
        $res = $db->query(
            "UPDATE `consultation_project_tasks` 
             SET `taskTitle` = ?s, `taskDescription` = ?s, `dueDate` = ?s
             WHERE `taskId` = ?s",
            $taskTitle, $taskDescription, $dueDate, $taskId
        );

        recordProjectHistory($projectId, OT_EDIT_TASK,logActivity(OT_EDIT_TASK), "Task Edited for - " . getTaskNumberFromTaskID($taskId) );

        // Replace assignees
        $db->query("DELETE FROM `consultation_project_task_assignees` WHERE `taskId` = ?s", $taskId);
        foreach ($assigneeIds as $userId) {
            $db->query(
                "INSERT INTO `consultation_project_task_assignees` (`taskId`, `userId`)
                 VALUES (?s, ?s)",
                $taskId, $userId
            );
        }

        echo json_encode([
            'status' => 'SUCCESS',
            'message' => 'Task updated successfully'
        ]);
    } else {

        $taskNumberPrefix = PREFIX_CONSULTATION_PROJECT_TASK;
        $taskNumber = getNextNewConsultationProjectTaskNumber();

        $res = $db->query(
            "INSERT INTO `consultation_project_tasks` (
                `projectId`, `taskTitle`, `taskDescription`, `taskNumberPrefix`, `taskNumber`, `dueDate`, `taskStatus`, `createdBy`
            ) VALUES (?s, ?s, ?s, ?s, ?s, ?s, 'NEW', ?s)",
            $projectId, $taskTitle, $taskDescription, $taskNumberPrefix, $taskNumber, $dueDate, getUserIDOfCurrentUser()
        );
        $taskId = $db->insertId();
        recordProjectHistory($projectId, OT_NEW_TASK,logActivity(OT_NEW_TASK), "Task Number Generated - " . getTaskNumberFromTaskID($taskId) );

        // Insert assignees
        foreach ($assigneeIds as $userId) {
            $res = $db->query(
                "INSERT INTO `consultation_project_task_assignees` (`taskId`, `userId`)
                 VALUES (?s, ?s)",
                $taskId, $userId
            );
        }

        echo json_encode([
            'status' => 'SUCCESS',
            'message' => 'Task created successfully'
        ]);
    }
    exit();
} catch (Exception $e) {
    error_log("Error saving task: " . $e->getMessage());
    echo json_encode(['status' => 'ERROR', 'message' => $e->getMessage()]);
    exit;
}
?>