<?php
header('Content-Type: application/json');
include_once __DIR__ . '/../../includes/baseIncludes.php';

date_default_timezone_set('Asia/Riyadh');

try {
    $rawData = file_get_contents('php://input');
    $data = json_decode($rawData, true);

    // Validate input
    if (!isset($data['month']) || empty($data['month'])) {
        throw new Exception('Month is required.');
    }
    if (!isset($data['payrollData']) || !is_array($data['payrollData']) || empty($data['payrollData'])) {
        throw new Exception('Invalid or empty payroll data.');
    }

    $month = date('Y-m-01', strtotime($data['month']));
    $companyId = intval($_SESSION['SES_SELECTED_COMPANY'] ?? 0);
    if ($companyId === 0) {
        throw new Exception('Invalid company ID in session.');
    }

    $db->query("START TRANSACTION");

    $validRowsProcessed = 0;
    $skippedRows = [];
    error_log('Received payroll data: ' . print_r($data['payrollData'], true)); // Debug: Log received data

    // Process each payroll record
    foreach ($data['payrollData'] as $index => $row) {
        // Check for required fields
        $requiredFields = ['id', 'basicSalary', 'ha', 'ta', 'totalBeforeGosi', 'totalSalary', 'deductionEmployee10', 'deductionCompany12', 'totalGosi', 'totalPaid'];
        $missingFields = [];
        foreach ($requiredFields as $field) {
            if (!isset($row[$field]) || $row[$field] === null || $row[$field] === '') {
                $missingFields[] = $field;
            }
        }
        // Allow oa to be null
        if (!isset($row['oa'])) {
            $row['oa'] = null;
        }

        if (!empty($missingFields)) {
            $skippedRows[] = "Row $index (userId: " . ($row['id'] ?? 'unknown') . "): Missing or null fields: " . implode(', ', $missingFields);
            error_log($skippedRows[count($skippedRows) - 1]);
            continue;
        }

        // Validate basicSalary
        $basicSalary = is_numeric($row['basicSalary']) ? (float)$row['basicSalary'] : null;
        if ($basicSalary === null || is_nan($basicSalary) || !is_finite($basicSalary) || $basicSalary < 0) {
            $skippedRows[] = "Row $index (userId: " . ($row['id'] ?? 'unknown') . "): Invalid basicSalary '" . ($row['basicSalary'] ?? 'null') . "'";
            error_log($skippedRows[count($skippedRows) - 1]);
            continue;
        }

        // Validate other numeric fields
        $numericFields = ['ha', 'ta', 'totalBeforeGosi', 'totalSalary', 'deductionEmployee10', 'deductionCompany12', 'totalGosi', 'totalPaid'];
        foreach ($numericFields as $field) {
            if (!is_numeric($row[$field]) || is_nan((float)$row[$field]) || !is_finite((float)$row[$field])) {
                $skippedRows[] = "Row $index (userId: " . ($row['id'] ?? 'unknown') . "): Invalid $field '" . ($row[$field] ?? 'null') . "'";
                error_log($skippedRows[count($skippedRows) - 1]);
                continue 2; // Skip to next row
            }
        }

        // Check if a record exists for this user and month
        $existingRecord = $db->getRow(
            "SELECT id FROM hr_payroll WHERE userId = ?i AND DATE_FORMAT(month, '%Y-%m') = ?s",
            (int)$row['id'],
            date('Y-m', strtotime($month))
        );

        if ($existingRecord) {
            // Update existing record
            $res = $db->query(
                "UPDATE hr_payroll 
                 SET basicSalary = ?s, housingAllowance = ?s, travelAllowance = ?s, otherAllowance = ?s, 
                     totalBeforeGosi = ?s, totalSalary = ?s, deductionEmployee10 = ?s, 
                     deductionCompany12 = ?s, totalGosi = ?s, totalPaid = ?s, createdBy = ?s 
                 WHERE id = ?i",
                (float)$row['basicSalary'],
                (float)$row['ha'],
                (float)$row['ta'],
                $row['oa'] === null ? null : (float)$row['oa'],
                (float)$row['totalBeforeGosi'],
                (float)$row['totalSalary'],
                (float)$row['deductionEmployee10'],
                (float)$row['deductionCompany12'],
                (float)$row['totalGosi'],
                (float)$row['totalPaid'],
                getUserIDOfCurrentUser(),
                (int)$existingRecord['id']
            );
        } else {
            // Insert new record
            $res = $db->query(
                "INSERT INTO hr_payroll 
                 (userId, month, basicSalary, housingAllowance, travelAllowance, otherAllowance, 
                  totalBeforeGosi, totalSalary, deductionEmployee10, deductionCompany12, totalGosi, totalPaid, createdBy) 
                 VALUES (?i, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s)",
                (int)$row['id'],
                $month,
                (float)$row['basicSalary'],
                (float)$row['ha'],
                (float)$row['ta'],
                $row['oa'] === null ? null : (float)$row['oa'],
                (float)$row['totalBeforeGosi'],
                (float)$row['totalSalary'],
                (float)$row['deductionEmployee10'],
                (float)$row['deductionCompany12'],
                (float)$row['totalGosi'],
                (float)$row['totalPaid'],
                getUserIDOfCurrentUser()
            );
        }

        if ($res) {
            $validRowsProcessed++;
            error_log('Successfully processed row for userId ' . $row['id']);
        } else {
            $error = $db->error ?: 'Unknown database error';
            error_log('Failed to process row for userId ' . $row['id'] . ': ' . $error);
            throw new Exception('Failed to save payroll data for user ID: ' . $row['id'] . ' - ' . $error);
        }
    }

    if ($validRowsProcessed === 0) {
        $db->query("ROLLBACK");
        $errorMessage = 'No valid payroll data was processed.';
        if (!empty($skippedRows)) {
            $errorMessage .= ' Reasons: ' . implode('; ', $skippedRows);
        }
        throw new Exception($errorMessage);
    }

    $db->query("COMMIT");

    echo json_encode([
        'status' => 'success',
        'message' => 'Payroll data saved successfully'
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