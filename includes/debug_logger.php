<?php
function logActivity(string $customLabel = "AUTO"): int
{
    global $db;

    try {


        $sessionId      = session_id();
        $requestMethod  = $_SERVER['REQUEST_METHOD'];
        $requestData    = [];

        // Handle JSON input
        if ($requestMethod === 'POST' && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
            $rawInput = file_get_contents('php://input');
            $requestData = json_decode($rawInput, true);
        } elseif ($requestMethod === 'POST') {
            $requestData = $_POST; // Fallback for form-encoded data
        } elseif ($requestMethod === 'GET') {
            $requestData = $_GET;
        }

        $scriptName = basename($_SERVER['PHP_SELF']);
//        $requestDataSerialized = serialize($requestData);
        $requestDataSerialized = json_encode($requestData, JSON_UNESCAPED_UNICODE);
        $userId = 0;



//        $sessionId             = session_id();
//        $requestMethod         = $_SERVER['REQUEST_METHOD'];
//        $requestData           = $requestMethod === 'POST' ? $_POST : $_GET;
//        $scriptName            = basename($_SERVER['PHP_SELF']);
////
//        $requestDataSerialized = serialize($requestData);
//        $userId                = 0;

        if (isset($_SESSION[IsLoggedIn]) && $_SESSION[IsLoggedIn] === true) {
            $userId = getAuthenticatedUser()->userID;
        }

        $data = [
            'userId'        => $userId,
            'sessionId'     => $sessionId,
            'requestMethod' => $requestMethod,
            'requestData'   => $requestDataSerialized,
            'scriptName'    => $scriptName,
            'actionLabel'   => $customLabel,
        ];

        // Insert data into the `activity_logs` table
        $db->query(
            "INSERT INTO activity_logs_raw (userId, sessionId, requestMethod, requestData, scriptName, actionLabel) 
             VALUES (?i, ?s, ?s, ?s, ?s, ?s)",
            $data['userId'],
            $data['sessionId'],
            $data['requestMethod'],
            $data['requestData'],
            $data['scriptName'],
            $data['actionLabel']
        );

        return $db->insertId();
    } catch (Exception $e) {
        error_log('Failed to log activity: ' . $e->getMessage());
        return 0;
    }
}
logActivity();