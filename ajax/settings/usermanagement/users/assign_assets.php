<?php
include_once __DIR__ . "/../../../../includes/baseIncludes.php";

try {
    // Validate and sanitize input data
    $userID = filter_var($_POST['userId'], FILTER_VALIDATE_INT);
    $assetIds   = isset($_POST['userAssets']) ? $_POST['userAssets'] : [];

    if (!$userID) {
        throw new Exception("Invalid or missing user ID.");
    }
    
    $db->query("DELETE FROM `user_asset_mapping` WHERE `userId` = ?s", $userID);
    if (!empty($assetIds)) {
        foreach ($assetIds as $assetId) {
            $res = $db->query(
                "INSERT INTO `user_asset_mapping` (`userId`, `assetId`, `assigneeId`) VALUES (?s, ?s, ?s)",
                $userID,
                $assetId,
                getUserIDOfCurrentUser()
            );
        }
    }

    if ($res) {
        echo "SUCCESS|Asset Assigned successfully!";
    } else {
        echo "ERROR|Failed to assign asset.";
    }
} catch (Exception $e) {
    error_log("Error assigning assets: " . $e->getMessage());
    echo "ERROR|Failed to assign assets. Error: " . $e->getMessage();
}
?>
