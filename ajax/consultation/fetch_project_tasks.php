<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

try {
    // Get projectId from request, e.g., as a GET parameter
    $projectID = filter_input(INPUT_GET, 'projectID', FILTER_VALIDATE_INT);

    // Base query for tasks
    $query = "SELECT
        taskId,
        projectId,
        itemId,
        taskTitle,
        taskDescription,
        taskNumberPrefix,
        taskNumber,
        dueDate,
        taskStatus,
        createdBy,
        createdAt,
        updatedAt
    FROM
        consultation_project_tasks";

    // Add WHERE clause if projectId is specified
    if ($projectID !== null) {
        $query .= " WHERE projectId = " . $projectID;
    }

    // Execute query
    $res = $db->query($query);

    // Fetch data
    $data = [];
    while ($row = mysqli_fetch_assoc($res)) {
        // Fetch assignee IDs for this task
        $assigneeRes = $db->query(
            "SELECT a.userId, u.firstName, u.lastName
     FROM consultation_project_task_assignees a
     JOIN users u ON a.userId = u.userID
     WHERE a.taskId = ?s",
            $row['taskId']
        );
        $assignees = mysqli_fetch_all($assigneeRes, MYSQLI_ASSOC);

        $data[] = [
            'taskId' => $row['taskId'],
            'projectId' => $row['projectId'],
            'projectName' => getProjectNameFromProjectID($row['projectId']),
            'itemName' => getItemNameForItemID($row['itemId']),
            'taskTitle' => $row['taskTitle'],
            'taskDescription' => $row['taskDescription'],
            'taskNumber' => getTaskNumberFromTaskID($row['taskId']),
            'dueDate' => $row['dueDate'],
            'taskStatus' => $row['taskStatus'],
            'createdBy' => $row['createdBy'],
            'createdAt' => $row['createdAt'],
            'updatedAt' => $row['updatedAt'],
            'assigneeIds' => array_column($assignees, 'userId'), // Add assignee IDs
            'assignees' => $assignees
        ];
    }

    // Response format
    $response = [
        "data" => $data
    ];

    // Output JSON response
    echo json_encode($response);
} catch (Exception $e) {
    // Handle error
    echo json_encode([
        "error" => "An error occurred: " . $e->getMessage()
    ]);
}
?>