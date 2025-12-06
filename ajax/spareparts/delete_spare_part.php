<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";

// Sanitize input
$sparepartId = filter_var($_POST['sparepartId'] ?? null, FILTER_SANITIZE_NUMBER_INT) ?: null;

try {
    if (!$sparepartId) {
        throw new Exception('Spare Part ID is required.');
    }

    // Verify spare part exists and is not already deleted
    $res = $db->query("SELECT * FROM `spareparts` WHERE `sparepartId` = ?s", $sparepartId);
    $sparepart = mysqli_fetch_assoc($res);
    if (!$sparepart) {
        throw new Exception('Spare part not found.');
    }
    if ($sparepart['isDeleted'] == 1) {
        throw new Exception('Spare part is already deleted.');
    }

    // Soft delete spare part by setting isDeleted to 1
    $res = $db->query("UPDATE `spareparts` SET `isDeleted` = 1, `deletedBy` = ?s WHERE `sparepartId` = ?s", getUserIDOfCurrentUser(), $sparepartId);

    if ($res === false) {
        throw new Exception('Failed to mark spare part as deleted.');
    }

    // Determine if it's a machine or sparepart
    $itemType = strtoupper(trim($sparepart['itemType'] ?? 'SPAREPART'));
    $successMessage = ($itemType === 'MACHINE')
        ? 'Machine deleted successfully!'
        : 'Spare part deleted successfully!';

    echo json_encode([
        'status' => 'SUCCESS',
        // 'message' => 'Spare part deleted successfully'
        'message' => $successMessage
    ]);
    exit();
} catch (Exception $e) {
    error_log("Error deleting spare part: " . $e->getMessage());
    echo json_encode(['status' => 'ERROR', 'message' => $e->getMessage()]);
    exit;
}
?>