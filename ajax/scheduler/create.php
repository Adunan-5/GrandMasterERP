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
    if (!isset($event['title'], $event['start'], $event['end'], $event['assigneeIds'])) {
        throw new Exception('Missing required fields: title, start, end, or assigneeIds');
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

    // Insert into database
    $res = $db->query(
        "INSERT INTO scheduler_events (title, start, end, projectHeadId, assigneeIds, description, customerId) 
         VALUES (?s, ?s, ?s, ?s, ?s, ?s, ?s)",
        $event['title'],
        $start,
        $end,
        $projectHeadId,
        $assigneeIds,
        $description,
        $customerId
    );

    if (!$res) {
        throw new Exception('Database insert failed');
    }

    $eventId = $db->insertId();

    // Get the inserted record
    $eventId = $db->insertId();

    $projectNumberPrefix = PREFIX_CONSULTATION_PROJECT;
    $projectNumber = getNextNewConsultationProjectNumber();

    // Insert into consultation_projects
    $res = $db->query(
        "INSERT INTO `consultation_projects` (
            `projectTitle`, `projectNumberPrefix`, `projectNumber`, 
            `startDate`, `endDate`, `description`, `projectHeadId`, `eventId`, `customerId`, `createdBy`
        ) VALUES (?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s)",
        $event['title'],
        $projectNumberPrefix,
        $projectNumber,
        $start,
        $end,
        $description,
        $projectHeadId,
        $eventId,
        $customerId,
        getUserIDOfCurrentUser() 
    );
    
    if (!$res) {
        throw new Exception('Database insert into consultation_projects failed');
    }
    
    // Get the inserted project ID
    $projectId = $db->insertId();
    recordProjectHistory($projectId, OT_NEW_PROJECT,logActivity(OT_NEW_PROJECT), "Project Number Generated - " . getProjectNumberFromProjectID($projectId) );

    // Insert into consultation_project_assignees
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

    $newEvent = $db->getRow("SELECT * FROM scheduler_events WHERE id = ?i", $eventId);

    // Convert assigneeIds back to array
    $assigneeIdsArray = explode(',', $newEvent['assigneeIds']);

    // Format response for Kendo
    $response = [
        'status' => 'success',
        'message' => 'Event created successfully.',
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