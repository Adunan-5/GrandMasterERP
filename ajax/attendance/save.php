<?php
header('Content-Type: application/json');
include_once __DIR__ . '/../../includes/baseIncludes.php';

date_default_timezone_set('Asia/Riyadh');

try {
    $rawData = file_get_contents('php://input');
    $data = json_decode($rawData, true);

    if (!isset($data['date']) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['date'])) {
        throw new Exception('Valid date is required (YYYY-MM-DD).');
    }
    if (!isset($data['attendanceData']) || !is_array($data['attendanceData']) || empty($data['attendanceData'])) {
        throw new Exception('Invalid or empty attendance data.');
    }

    $date = $data['date'];
    $companyId = intval($_SESSION['SES_SELECTED_COMPANY'] ?? 0);
    if ($companyId === 0) {
        throw new Exception('Invalid company ID in session.');
    }

    $db->query("START TRANSACTION");

    $validRowsProcessed = 0;
    $skippedRows = [];
    $currentUserId = getUserIDOfCurrentUser();

    foreach ($data['attendanceData'] as $index => $emp) {
        if (!isset($emp['id'])) {
            $skippedRows[] = "Row $index: Missing user ID.";
            continue;
        }

        $clock_in = $emp['clock_in'] ?? null;
        $clock_out = $emp['clock_out'] ?? null;
        $break_start = $emp['break_start'] ?? null;
        $break_end = $emp['break_end'] ?? null;
        $type = $emp['type'] ?? null;
        $total_hours = isset($emp['total_hours']) ? (float)$emp['total_hours'] : null;
        $overtime_hours = isset($emp['overtime_hours']) ? (float)$emp['overtime_hours'] : null;
        $early_leaving = isset($emp['early_leaving']) ? (float)$emp['early_leaving'] : null;

        // Skip if all fields are null/empty
        if (!$clock_in && !$clock_out && !$break_start && !$break_end && !$type && !$total_hours && !$overtime_hours && !$early_leaving) {
            continue;
        }

        // Validate times (if set)
        $timePattern = '/^([01]\d|2[0-3]):[0-5]\d$/';
        if ($clock_in && !preg_match($timePattern, $clock_in)) throw new Exception("Invalid clock_in time for user ID: {$emp['id']}");
        if ($clock_out && !preg_match($timePattern, $clock_out)) throw new Exception("Invalid clock_out time for user ID: {$emp['id']}");
        if ($break_start && !preg_match($timePattern, $break_start)) throw new Exception("Invalid break_start time for user ID: {$emp['id']}");
        if ($break_end && !preg_match($timePattern, $break_end)) throw new Exception("Invalid break_end time for user ID: {$emp['id']}");

        // Validate type
        $allowedTypes = ['Business Trip', 'Holiday', 'Leave', 'Week Off'];
        if ($type && !in_array($type, $allowedTypes)) throw new Exception("Invalid type '$type' for user ID: {$emp['id']}");

        // Validate early_leaving
        if ($early_leaving !== null && (!is_numeric($early_leaving) || $early_leaving < 0)) {
            throw new Exception("Invalid early_leaving value for user ID: {$emp['id']}");
        }

        // Append :00 for TIME format
        $clock_in = $clock_in ? $clock_in . ':00' : null;
        $clock_out = $clock_out ? $clock_out . ':00' : null;
        $break_start = $break_start ? $break_start . ':00' : null;
        $break_end = $break_end ? $break_end . ':00' : null;
        $type = $type ?: null;

        // UPSERT query
        $query = "INSERT INTO hr_attendance 
                  (userId, date, clock_in, clock_out, break_start, break_end, type, total_hours, overtime_hours, early_leaving, createdBy) 
                  VALUES (?i, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?i) 
                  ON DUPLICATE KEY UPDATE 
                  clock_in = VALUES(clock_in), 
                  clock_out = VALUES(clock_out), 
                  break_start = VALUES(break_start), 
                  break_end = VALUES(break_end), 
                  type = VALUES(type),
                  total_hours = VALUES(total_hours),
                  overtime_hours = VALUES(overtime_hours),
                  early_leaving = VALUES(early_leaving),
                  updatedAt = NOW()"; 

        $res = $db->query($query, (int)$emp['id'], $date, $clock_in, $clock_out, $break_start, $break_end, $type, $total_hours, $overtime_hours, $early_leaving, $currentUserId);

        if ($res) {
            $validRowsProcessed++;
        } else {
            throw new Exception("Failed to save attendance for user ID: {$emp['id']} - " . $db->error);
        }
    }

    if ($validRowsProcessed === 0) {
        $db->query("ROLLBACK");
        throw new Exception('No valid attendance data was processed.');
    }

    $db->query("COMMIT");

    echo json_encode([
        'status' => 'success',
        'message' => 'Attendance data saved successfully'
    ]);

} catch (Exception $e) {
    $db->query("ROLLBACK");
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>