<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";
include_once __DIR__ . "/../../includes/auth_check.php";

// Read raw JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Access values
$itemId = $input['itemId'] ?? '';

try {

    $baseQuery = "SELECT * from consultation_services_items WHERE itemId = ?s";
    $res       = $db->query($baseQuery, $itemId);

    $row = mysqli_fetch_assoc($res);

    // Response format for DataTables
    $response = [
        "status"  => 'success',
        "message" => '',
        "data"    => $row
    ];

    // Output JSON response
    echo json_encode($response);
} catch (Exception $e) {
    // Handle error
    echo json_encode([
        "error" => "An error occurred: " . $e->getMessage()
    ]);
}