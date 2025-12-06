<?php
header('Content-Type: application/json');
include_once __DIR__ . '/../../includes/baseIncludes.php';

date_default_timezone_set('Asia/Riyadh');

try {
    // Query to fetch all events
    $res = $db->query("SELECT se.*, cp.projectId 
        FROM scheduler_events se
        LEFT JOIN consultation_projects cp ON se.id = cp.eventId");

    // Format data for Kendo Scheduler
    $events = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $assigneeIds = explode(',', $row['assigneeIds']);
        $events[] = [
            'id' => (int)$row['id'],
            'title' => $row['title'], // Keep title clean
            'projectNumber' => getProjectNumberFromProjectID($row['projectId']),
            'start' => (new DateTime($row['start'], new DateTimeZone('Asia/Riyadh')))->format('c'),
            'end' => (new DateTime($row['end'], new DateTimeZone('Asia/Riyadh')))->format('c'),
            'assigneeIds' => array_map('intval', $assigneeIds),
            'projectHeadId' => (int)$row['projectHeadId'],
            'clientId' => (int)$row['customerId'],
            'description' => $row['description']
        ];
    }

    echo json_encode($events);

} catch (Exception $e) {
    http_response_code(500);
    $error = ['error' => $e->getMessage()];
    echo json_encode($error);
}
?>