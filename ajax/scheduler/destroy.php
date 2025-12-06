<?php
header('Content-Type: application/json');
include_once __DIR__ . '/../../includes/baseIncludes.php';

try {
    $rawData = file_get_contents('php://input');
    $data = json_decode($rawData, true);

    // Check if models array exists
    if (!isset($data['models']) || !is_array($data['models']) || empty($data['models'])) {
        throw new Exception('Invalid or empty models data');
    }

    // Take the first event from models
    $event = $data['models'][0];

    // Validate required field
    if (!isset($event['id'])) {
        throw new Exception('Missing required field: id');
    }

    // Delete the event from the database
    $res = $db->query("DELETE FROM scheduler_events WHERE id = ?i", (int)$event['id']);

    if (!$res) {
        throw new Exception('Database delete failed');
    }

    // Kendo expects an empty array or the deleted event
    $response = [
        'status' => 'success',
        'message' => 'Event deleted successfully.',
        'eventId' => (int)$event['id'],
        'data' => [[]] // Kendo expects an empty array for destroy
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