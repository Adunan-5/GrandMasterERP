<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";

$projectId = filter_var($_POST['projectId'] ?? null, FILTER_SANITIZE_NUMBER_INT) ?: null;
$documentId = filter_var($_POST['documentId'] ?? null, FILTER_SANITIZE_NUMBER_INT) ?: null;
$customerId = filter_var($_POST['customerId'] ?? null, FILTER_SANITIZE_NUMBER_INT) ?: null;
$projectTitle = filter_var($_POST['projectTitle'] ?? null, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$projectHeadId = filter_var($_POST['projectHeadId'] ?? null, FILTER_SANITIZE_NUMBER_INT) ?: null;
$projectDescription = filter_var($_POST['projectDescription'] ?? null, FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';
$projectStartDate = $_POST['startDate'] ?? null;
$projectEndDate = $_POST['endDate'] ?? null;
$lineItemIds = isset($_POST['lineItemIds']) && !empty($_POST['lineItemIds']) ? json_decode($_POST['lineItemIds'], true) : [];
$assigneeIds = isset($_POST['assigneeIds']) && !empty($_POST['assigneeIds']) ? json_decode($_POST['assigneeIds'], true) : [];

try {
    if ($projectId) {
        if (!$projectId || !$projectTitle) {
            echo json_encode(['status' => 'ERROR', 'message' => 'Missing or invalid required fields for project update']);
            exit;
        }

        // Update project
        $res = $db->query(
            "UPDATE `consultation_projects` 
             SET `projectTitle` = ?s, `projectHeadId` = ?s, `description` = ?s, `startDate` = ?s, `endDate` = ?s, `customerId` = ?s 
             WHERE `projectId` = ?s",
            $projectTitle, $projectHeadId, $projectDescription, $projectStartDate, $projectEndDate, $customerId, $projectId
        );
        recordProjectHistory($projectId, OT_EDIT_PROJECT,logActivity(OT_EDIT_PROJECT));

        // Get existing eventId from consultation_projects
        $project = $db->getRow("SELECT eventId FROM consultation_projects WHERE projectId = ?i", $projectId);
        if (!$project || !$project['eventId']) {
            throw new Exception('No eventId found for the project');
        }
        $eventId = $project['eventId'];

        $db->query("DELETE FROM `consultation_project_assignees` WHERE `projectId` = ?s", $projectId);
        foreach ($assigneeIds as $userId) {
            $db->query(
                "INSERT INTO `consultation_project_assignees` (`projectId`, `userId`) 
                 VALUES (?s, ?s)",
                $projectId, $userId
            );
        }

        // Convert new assigneeIds to string for scheduler_events
        $assigneeIdsString = implode(',', $assigneeIds);

        // Update scheduler_events with new assigneeIds
        $res = $db->query(
            "UPDATE scheduler_events 
             SET title = ?s, start = ?s, end = ?s, assigneeIds = ?s, projectHeadId = ?s, description = ?s, customerId = ?i 
             WHERE id = ?i",
            $projectTitle,
            $projectStartDate,
            $projectEndDate,
            $assigneeIdsString,
            $projectHeadId,
            $projectDescription,
            $customerId,
            $eventId
        );

        if (!$res) {
            throw new Exception('Database update of scheduler_events failed');
        }

        echo json_encode([
            'status' => 'SUCCESS',
            'message' => 'Project updated successfully'
        ]);
    } else {
        if (!$documentId || !$projectTitle || !$projectHeadId || empty($lineItemIds) || !is_array($lineItemIds)) {
            echo json_encode(['status' => 'ERROR', 'message' => 'Missing or invalid required fields for project creation']);
            exit;
        }

        // Validate line items
        $existingLineItems = $db->getAll(
            "SELECT cpli.itemId 
             FROM consultation_project_line_items cpli 
             INNER JOIN consultation_projects cp ON cpli.projectId = cp.projectId 
             WHERE cp.documentId = ?s AND cpli.itemId IN (?a)",
            $documentId, $lineItemIds
        );

        if (!empty($existingLineItems)) {
            $usedItemNames = [];
            foreach ($existingLineItems as $item) {
                $usedItemNames[] = getItemNameForItemID($item['itemId']);
            }
            echo json_encode([
                'status' => 'ERROR',
                'message' => 'The following line items are already assigned to a project: ' . implode(', ', $usedItemNames)
            ]);
            exit;
        }

        // Generate project number
        $projectNumberPrefix = PREFIX_CONSULTATION_PROJECT;
        $projectNumber = getNextNewConsultationProjectNumber();

        // Insert project
        $res = $db->query(
            "INSERT INTO `consultation_projects` (
                `documentId`, `customerId`, `projectTitle`, `projectNumberPrefix`, `projectNumber`, `projectHeadId`, `startDate`, `endDate`, `createdBy`
            ) VALUES (?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s)",
            $documentId, $customerId, $projectTitle, $projectNumberPrefix, $projectNumber, $projectHeadId, $projectStartDate, $projectEndDate, getUserIDOfCurrentUser()
        );

        $projectId = $db->insertId();
        recordProjectHistory($projectId, OT_NEW_PROJECT,logActivity(OT_NEW_PROJECT), "Project Number Generated - " . getProjectNumberFromProjectID($projectId) );

        // Insert line items
        foreach ($lineItemIds as $lineItemId) {
            $itemName = getItemNameForItemID($lineItemId);
            $res = $db->query(
                "INSERT INTO `consultation_project_line_items` (`projectId`, `itemId`, `itemName`) 
                 VALUES (?s, ?s, ?s)",
                $projectId, $lineItemId, $itemName
            );
        }

        // Create new scheduler_events entry
        $assigneeIdsString = implode(',', $assigneeIds);
        $res = $db->query(
            "INSERT INTO scheduler_events (title, start, end, assigneeIds, projectHeadId, description, customerId) 
             VALUES (?s, ?s, ?s, ?s, ?s, ?s, ?i)",
            $projectTitle,
            $projectStartDate,
            $projectEndDate,
            $assigneeIdsString,
            $projectHeadId,
            $projectDescription,
            $customerId
        );

        if (!$res) {
            throw new Exception('Database insert into scheduler_events failed');
        }

        $eventId = $db->insertId();

        // Update consultation_projects with the new eventId
        $db->query(
            "UPDATE consultation_projects SET eventId = ?i WHERE projectId = ?i",
            $eventId,
            $projectId
        );

        echo json_encode([
            'status' => 'SUCCESS',
            'message' => 'Project created successfully'
        ]);
    }
    exit();
} catch (Exception $e) {
    error_log("Error saving project: " . $e->getMessage());
    echo json_encode(['status' => 'ERROR', 'message' => $e->getMessage()]);
    exit();
}
?>