<?php
include_once __DIR__ . "/../includes/baseIncludes.php";
header('Content-Type: application/json');

// Determine the request method
$method = $_SERVER['REQUEST_METHOD'];

$companyID = null;

// If it's a POST request, assume JSON data in the body
if ($method === 'POST') {
    // Read the request body
    $input = file_get_contents('php://input');
    // Decode as JSON
    $data = json_decode($input, true);

    // If there's a "companyID" key in the JSON, use it
    if (isset($data['companyID'])) {
        $companyID = $data['companyID'];
    }
}
// If it's a GET request, check query parameters
elseif ($method === 'GET') {
    if (isset($_GET['companyID'])) {
        $companyID = $_GET['companyID'];
    }
}

// Handle the result
if ($companyID !== null) {
    // Save to session
    $_SESSION[SES_SELECTED_COMPANY] = $companyID;

    echo json_encode([
        'success'   => true,
        'message'   => 'Session variable "companyID" set successfully.',
        'companyID' => $companyID,
    ]);
} else {
    // No companyID received or invalid request
    echo json_encode([
        'success' => false,
        'message' => 'No "companyID" provided or invalid request method.',
    ]);
}