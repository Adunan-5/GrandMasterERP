<?php
header('Content-Type: application/json');
include_once __DIR__ . '/../../includes/baseIncludes.php';

date_default_timezone_set('Asia/Riyadh');

try {
    $month = isset($_GET['month']) ? date('Y-m-01', strtotime($_GET['month'])) : null;
    $companyId = intval($_SESSION['SES_SELECTED_COMPANY']);

    if (!$month) {
        throw new Exception('Month is required.');
    }

    // Fetch employees and their payroll data, prioritizing hr_payroll basicSalary
    $query = "SELECT u.userID, u.firstName, u.lastName, 
                     COALESCE(p.basicSalary, u.basicSalary) AS basicSalary, 
                     p.housingAllowance, p.travelAllowance, p.otherAllowance, 
                     p.totalBeforeGosi, p.totalSalary, 
                     p.deductionEmployee10, p.deductionCompany12, 
                     p.totalGosi, p.totalPaid
              FROM users u
              LEFT JOIN hr_payroll p 
                ON u.userID = p.userId 
               AND DATE_FORMAT(p.month, '%Y-%m') = ?s
              WHERE u.active = 1 
              AND u.userID != 1
              ORDER BY u.firstName ASC";
    $res = $db->query($query, date('Y-m', strtotime($month)));

    $employees = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $employees[] = [
            'id' => (int)$row['userID'],
            'name' => $row['firstName'] . ' ' . $row['lastName'],
            'basicSalary' => $row['basicSalary'] ? (float)$row['basicSalary'] : null,
            'ha' => $row['housingAllowance'] ? (float)$row['housingAllowance'] : null,
            'ta' => $row['travelAllowance'] ? (float)$row['travelAllowance'] : null,
            'oa' => $row['otherAllowance'] ? (float)$row['otherAllowance'] : null,
            'totalBeforeGosi' => $row['totalBeforeGosi'] ? (float)$row['totalBeforeGosi'] : null,
            'totalSalary' => $row['totalSalary'] ? (float)$row['totalSalary'] : null,
            'deductionEmployee10' => $row['deductionEmployee10'] ? (float)$row['deductionEmployee10'] : null,
            'deductionCompany12' => $row['deductionCompany12'] ? (float)$row['deductionCompany12'] : null,
            'totalGosi' => $row['totalGosi'] ? (float)$row['totalGosi'] : null,
            'totalPaid' => $row['totalPaid'] ? (float)$row['totalPaid'] : null
        ];
    }

    echo json_encode($employees);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>