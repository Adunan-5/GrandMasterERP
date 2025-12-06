<?php
header('Content-Type: application/json');
include_once __DIR__ . '/../../includes/baseIncludes.php';
date_default_timezone_set('Asia/Riyadh');

try {
    $rawData = file_get_contents('php://input');
    $data = json_decode($rawData, true);

    // Check if models array exists
    if (!isset($data['models']) || !is_array($data['models']) || empty($data['models'])) {
        throw new Exception('Invalid or empty models data');
    }

    // Take the first event from models
    $event = $data['models'][0];

    // Validate required fields
    if (!isset($event['id'], $event['title'], $event['start'], $event['end'], $event['assigneeIds'])) {
        throw new Exception('Missing required fields: id, title, start, end, or assigneeIds');
    }

    // Convert Kendo date strings to MySQL format
    $start = (new DateTime($event['start'], new DateTimeZone('UTC')))->setTimezone(new DateTimeZone('Asia/Riyadh'))->format('Y-m-d H:i:s');
    $end = (new DateTime($event['end'], new DateTimeZone('UTC')))->setTimezone(new DateTimeZone('Asia/Riyadh'))->format('Y-m-d H:i:s');

    // Convert assigneeIds array to comma-separated string
    $assigneeIdsArray = array_map('intval', $event['assigneeIds']);
    $assigneeIds = implode(',', array_map('intval', $event['assigneeIds']));
    $description = isset($event['description']) ? $event['description'] : '';
    $customerId = (int)$event['clientId'];
    $projectHeadId = (int)$event['projectHeadId'];

    // Update the event in the database
    $res = $db->query(
        "UPDATE scheduler_events SET title = ?s, start = ?s, end = ?s, projectHeadId = ?s, assigneeIds = ?s, description = ?s, customerId = ?s WHERE id = ?i",
        $event['title'],
        $start,
        $end,
        $projectHeadId,
        $assigneeIds,
        $description,
        $customerId,
        (int)$event['id']
    );

    if (!$res) {
        throw new Exception('Database update failed');
    }

    // Get the projectId using eventId from consultation_projects
    $project = $db->getRow("SELECT projectId FROM consultation_projects WHERE eventId = ?i LIMIT 1", (int)$event['id']);
    if (!$project) {
        throw new Exception('No matching project found for the event');
    }
    $projectId = $project['projectId'];

    // Update consultation_projects
    $res = $db->query(
        "UPDATE `consultation_projects` 
         SET `projectTitle` = ?s, `startDate` = ?s, `endDate` = ?s, `projectHeadId` = ?s, `description` = ?s, customerId = ?s
         WHERE `eventId` = ?i",
        $event['title'],
        $start,
        $end,
        $projectHeadId,
        $description,
        $customerId,
        (int)$event['id']
    );

    if (!$res) {
        throw new Exception('Database update of consultation_projects failed');
    }
    recordProjectHistory($projectId, OT_EDIT_PROJECT,logActivity(OT_EDIT_PROJECT), "Project Edited through Scheduler for - " . getProjectNumberFromProjectID($projectId) );

    // Delete existing assignees and insert new ones
    $db->query("DELETE FROM `consultation_project_assignees` WHERE `projectId` = ?s", $projectId);
    foreach ($assigneeIdsArray as $userId) {
        $res = $db->query(
            "INSERT INTO `consultation_project_assignees` (`projectId`, `userId`) 
             VALUES (?s, ?s)",
            $projectId,
            $userId
        );
        if (!$res) {
            throw new Exception('Database insert into consultation_project_assignees failed for userId: ' . $userId);
        }
    }

    // Get the updated record
    $newEvent = $db->getRow("SELECT * FROM scheduler_events WHERE id = ?i", (int)$event['id']);

    // Convert assigneeIds back to array
    $assigneeIdsArray = explode(',', $newEvent['assigneeIds']);

    // Format response for Kendo
    $response = [
        'status' => 'success',
        'message' => 'Event updated successfully.',
        'eventId' => (int)$newEvent['id'],
        'data' => [[
            'id' => (int)$newEvent['id'],
            'title' => $newEvent['title'], // Keep title clean
            'projectNumber' => getProjectNumberFromProjectID($projectId),
            'start' => (new DateTime($newEvent['start'], new DateTimeZone('Asia/Riyadh')))->format('c'),
            'end' => (new DateTime($newEvent['end'], new DateTimeZone('Asia/Riyadh')))->format('c'),
            'assigneeIds' => array_map('intval', $assigneeIdsArray),
            'projectHeadId' => (int)$newEvent['projectHeadId'], 
            'clientId' => (int)$newEvent['customerId'], 
            'description' => $newEvent['description']
        ]]
    ];

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(400);
    $error = [
        'status' => 'error',
        'message' => $e->getMessage()
    ];
    echo json_encode($error);
}
?>