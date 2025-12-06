<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../../../includes/baseIncludes.php";

try {
    $companyId = intval($_SESSION['SES_SELECTED_COMPANY']);
    // Base query to fetch active payment terms
    $baseQuery = "SELECT
        termId,
        termName,
        termValue
    FROM
        payment_terms
    WHERE
        active = '1' and companyId = $companyId";

    // Execute query
    $res = $db->query($baseQuery);

    // Fetch data
    $data = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $data[] = [
            'termId' => $row['termId'],
            'termName' => $row['termName'],
            'termValue' => $row['termValue']
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
