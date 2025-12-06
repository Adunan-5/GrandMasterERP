<?php
header('Content-Type: application/json');
include_once __DIR__ . '/../../includes/baseIncludes.php';

date_default_timezone_set('Asia/Riyadh');

try {
    $date = isset($_GET['date']) ? $_GET['date'] : null;
    $companyId = intval($_SESSION['SES_SELECTED_COMPANY']);

    if (!$date || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        throw new Exception('Valid date is required (YYYY-MM-DD).');
    }

    // Fetch employees and their attendance for the date
    $query = "SELECT u.userID, u.employeeId, u.firstName, u.lastName, u.countryId,
                    a.clock_in, a.clock_out, a.break_start, a.break_end, a.type, 
                    a.total_hours, a.overtime_hours, a.early_leaving
            FROM users u
            LEFT JOIN hr_attendance a 
                ON u.userID = a.userId AND a.date = ?s
            WHERE u.active = 1 
            AND u.userID != 1
            ORDER BY u.firstName ASC";
    $res = $db->query($query, $date);

    $employees = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $employees[] = [
            'id' => (int)$row['userID'],
            'employeeId' => $row['employeeId'],
            'name' => $row['firstName'] . ' ' . $row['lastName'],
            'countryId' => (int)$row['countryId'] ?? 0,
            'clock_in' => to12Hour($row['clock_in']),
            'clock_out' => to12Hour($row['clock_out']),
            'break_start' => to12Hour($row['break_start']),
            'break_end' => to12Hour($row['break_end']),
            'type' => $row['type'] ?? '',
            'total_hours' => $row['total_hours'] !== null ? (string)$row['total_hours'] : '',
            'overtime_hours' => $row['overtime_hours'] !== null ? (string)$row['overtime_hours'] : '',
            'early_leaving' => $row['early_leaving'] !== null ? (string)$row['early_leaving'] : ''
        ];
    }

    echo json_encode([
        'employees' => $employees
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>