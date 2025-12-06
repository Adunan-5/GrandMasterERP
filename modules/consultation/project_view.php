<?php
$PAGE_ID = "PROJECT_VIEW";
include_once __DIR__ . "/../../includes/baseIncludes.php";
include_once __DIR__ . "/../../includes/auth_check.php";

$projectID = "";

$projectID = filter_input(INPUT_GET, 'projectID', FILTER_VALIDATE_INT);

if ($projectID === null || $projectID === false || filter_var($projectID, FILTER_VALIDATE_INT) === false) {
    header("location:/projects");
    exit();
}
?>
<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
<head>
    <?php include_once __DIR__ . "/../../includes/dashboard/dashboard_head_section.php"; ?>
    <title>GrandMaster ERP | Projects</title>
    <link rel="stylesheet" href="/assets/vendor/libs/bootstrap-select/bootstrap-select.css"/>
    <link rel="stylesheet" href="/assets/vendor/libs/flatpickr/flatpickr.css" />
    <link rel="stylesheet" href="/assets/vendor/libs/animate-css/animate.css"/>
    <link rel="stylesheet" href="/assets/vendor/libs/sweetalert2/sweetalert2.css"/>
    <?php include_once __DIR__ . "/../../includes/dashboard/dashboard_head_section_post_vendor.php"; ?>
</head>

<body>
<!-- Layout wrapper -->
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <!-- Menu -->

        <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
            <?php include_once __DIR__ . "/../../includes/dashboard/menu_ceo.php" ?>
        </aside>
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
            <!-- Navbar -->

            <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
                <?php include_once __DIR__ . "/../../includes/dashboard/top_navbar.php"; ?>
            </nav>

            <!-- / Navbar -->

            <!-- Content wrapper -->
            <div class="content-wrapper">
                <!-- Content -->
                <?php
                $projectDATA = "";
                $res = $db->query("SELECT * FROM consultation_projects WHERE projectId = ?s", $projectID);
                while($row = mysqli_fetch_assoc($res)) {
                    $projectDATA = $row;
                }

                $projectHeadId = $projectDATA['projectHeadId'];
                $customerId = $projectDATA['customerId'];
                ?>

                <div class="container-xxl flex-grow-1 container-p-y">
                    <div class="card mb-6">
                        <div class="card-widget-separator-wrapper">
                            <div class="card-body card-widget-separator py-2">
                                <div class="row gy-1 gy-sm-1">
                                    <div class="col-6">
                                        <h4 class="my-0">Project No: <span><?=getProjectNumberFromProjectID($projectID)?></span></h4>
                                    </div>
                                    <div class="col-6 text-end">
                                        <button class="btn btn-primary text-nowrap d-inline-flex position-relative me-4 waves-effect waves-light"
                                           data-bs-toggle="modal" data-bs-target="#editProjectModal"
                                        > <i class="ti ti-edit ti-xs me-md-2"></i><span class="d-md-inline-block d-none">Edit Project</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-6">
                        <div class="card-widget-separator-wrapper">
                            <div class="card-body card-widget-separator py-2">
                                <div class="row gy-1 gy-sm-1">
                                    <div class="col-6">
                                    </div>
                                    <div class="col-6 text-end">
                                        <button class="btn btn-info text-nowrap d-inline-flex position-relative me-4 waves-effect waves-light"
                                           data-bs-toggle="offcanvas" data-bs-target="#showHistoryOffcanvas"
                                        > <i class="ti ti-clock ti-xs me-md-2"></i><span class="d-md-inline-block d-none">History</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Invoice List Widget -->
                    <div class="card mb-6">
                        <div class="card-widget-separator-wrapper">
                            <div class="card-body card-widget-separator">
                                <div class="row gy-4 gy-sm-1">
                                    <div class="col-sm-6 col-lg-4">
                                        <div class="d-flex justify-content-between align-items-center card-widget-1 border-end pb-4 pb-sm-0">
                                            <div>
                                                 <?php
                                                $description = $projectDATA['description'] ?? '';
                                                $maxLength = 30; // Shorter length for this compact display
                                                $truncated = mb_strlen($description) > $maxLength 
                                                    ? mb_substr($description, 0, $maxLength) . '...' 
                                                    : $description;
                                                ?>
                                                <h4 class="mb-0" 
                                                    data-bs-toggle="tooltip" 
                                                    data-bs-placement="top" 
                                                    title="<?= htmlspecialchars($description) ?>"
                                                    style="cursor: help;">
                                                    <?= htmlspecialchars($truncated) ?>
                                                </h4>
                                                <p class="mb-0">Description</p>
                                            </div>
                                            <div class="avatar me-sm-6">
                                                <span class="avatar-initial rounded bg-label-secondary text-heading">
                                                <i class="ti ti-file-description ti-26px"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <hr class="d-none d-sm-block d-lg-none me-6"/>
                                    </div>
                                    <div class="col-sm-6 col-lg-4">
                                        <div class="d-flex justify-content-between align-items-center card-widget-2 border-end pb-4 pb-sm-0">
                                            <div>
                                                <h4 class="mb-0"><?= $projectDATA['customerId'] == 0 ? 'Internal' : getCustomerNameFromID($projectDATA['customerId'])?></h4>
                                                <p class="mb-0">Client</p>
                                            </div>
                                            <div class="avatar me-lg-6">
                                                <span class="avatar-initial rounded bg-label-secondary text-heading">
                                                <i class="ti ti-file-invoice ti-26px"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <hr class="d-none d-sm-block d-lg-none"/>
                                    </div>
                                    <div class="col-sm-6 col-lg-4">
                                        <div class="d-flex justify-content-between align-items-center border-end pb-4 pb-sm-0 card-widget-3">
                                            <div>
                                                <h4 class="mb-0"><?=getDisplayNameFromUserID($projectHeadId)?></h4>
                                                <p class="mb-0">Project Head</p>
                                            </div>
                                            <div class="avatar me-sm-6">
                                                <span class="avatar-initial rounded bg-label-secondary text-heading">
                                                <i class="ti ti-user-star ti-26px"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- After the current card (Invoice List Widget) -->
                    <div class="card mb-6">
                        <div class="card-widget-separator-wrapper">
                            <div class="card-body card-widget-separator">
                                <div class="row gy-4 gy-sm-1">
                                    <!-- Project Start Date -->
                                    <div class="col-sm-6 col-lg-4">
                                        <div class="d-flex justify-content-between align-items-center card-widget-1 border-end pb-4 pb-sm-0">
                                            <div>
                                                <h4 class="mb-0">
                                                    <?= !empty($projectDATA['startDate']) ? date('d M Y', strtotime($projectDATA['startDate'])) : 'Not set' ?>
                                                </h4>
                                                <p class="mb-0">Start Date</p>
                                            </div>
                                            <div class="avatar me-sm-6">
                                                <span class="avatar-initial rounded bg-label-secondary text-heading">
                                                    <i class="ti ti-calendar-time ti-26px"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <hr class="d-none d-sm-block d-lg-none me-6"/>
                                    </div>
                                    
                                    <!-- Project End Date -->
                                    <div class="col-sm-6 col-lg-4">
                                        <div class="d-flex justify-content-between align-items-center card-widget-2 border-end pb-4 pb-sm-0">
                                            <div>
                                                <h4 class="mb-0">
                                                    <?= !empty($projectDATA['endDate']) ? date('d M Y', strtotime($projectDATA['endDate'])) : 'Not set' ?>
                                                </h4>
                                                <p class="mb-0">End Date</p>
                                            </div>
                                            <div class="avatar me-lg-6">
                                                <span class="avatar-initial rounded bg-label-secondary text-heading">
                                                    <i class="ti ti-calendar-due ti-26px"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <hr class="d-none d-sm-block d-lg-none"/>
                                    </div>
                                    
                                    <!-- Days Difference -->
                                    <div class="col-sm-6 col-lg-4">
                                        <div class="d-flex justify-content-between align-items-center border-end pb-4 pb-sm-0 card-widget-3">
                                            <div>
                                                <?php
                                                if (!empty($projectDATA['startDate']) && !empty($projectDATA['endDate'])) {
                                                    $today = new DateTime('today');
                                                    $end = new DateTime($projectDATA['endDate']);
                                                    $end->setTime(0, 0, 0);
                                                    $interval = $today->diff($end);
                                                    $daysLeft = $interval->days;
                                                    
                                                    // Handle positive/negative intervals
                                                    if ($interval->invert) {
                                                        $daysLeft = -$daysLeft;
                                                    }
                                                    
                                                    // Determine button class and text
                                                    if ($daysLeft < 0) {
                                                        $btnClass = 'btn-label-dark';
                                                        $daysText = 'Expired';
                                                    } else {
                                                        $daysText = $daysLeft . ' ' . ($daysLeft == 1 ? 'Day' : 'Days') . ' left';
                                                        
                                                        if ($daysLeft == 0) {
                                                            $btnClass = 'btn-label-warning'; // Due today
                                                        } elseif ($daysLeft <= 3) {
                                                            $btnClass = 'btn-label-danger'; // Less than 3 days
                                                        } elseif ($daysLeft <= 7) {
                                                            $btnClass = 'btn-label-warning'; // Less than a week
                                                        } else {
                                                            $btnClass = 'btn-label-success'; // More than a week
                                                        }
                                                    }
                                                } else {
                                                    $btnClass = 'btn-label-secondary';
                                                    $daysText = 'N/A';
                                                }
                                                ?>
                                                
                                                <h4 class="mb-0">
                                                    <span class="btn <?= $btnClass ?> text-nowrap d-inline-flex position-relative">
                                                        <?= $daysText ?>
                                                    </span>
                                                </h4>
                                                <p class="mb-0"></p>
                                            </div>
                                            <div class="avatar me-sm-6">
                                                <span class="avatar-initial rounded bg-label-secondary text-heading">
                                                    <i class="ti ti-clock ti-26px"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Quotation List Table -->
                    <div class="card">

                        <div class="card-datatable table-responsive">
                            <table class="invoice-list-table table border-top">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th class="text-truncate">Task #</th>
                                    <th class="cell-fit">Project</th>
                                    <th>Task Title</th>
                                    <th>Task Description</th>
                                    <th>Assignees</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th class="cell-fit">Action</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Offcanvas -->
                    <!--Project History Canvas starts-->
                    <div class="offcanvas offcanvas-end" id="showHistoryOffcanvas" aria-hidden="true">
                        <div class="offcanvas-header mb-6 border-bottom">
                            <h5 class="offcanvas-title">History</h5>
                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body pt-0 flex-grow-1">
                            <!-- Timeline Basic-->
                            <div class="mt-5">
                                <ul class="timeline mb-0">
                                    <?php

                                    $projectHistory = new ProjectHistory();
                                    $projectHistory->loadByProjectId($projectID);

                                    foreach ($projectHistory->timeline as $historyItem) {
                                        ?>
                                        <li class="timeline-item timeline-item-transparent">
                                          <span class="timeline-point timeline-point-<?= $historyItem->stickerPottu ?>"></span>
                                            <div class="timeline-event">
                                                <div class="timeline-header mb-3">
                                                    <h6 class="mb-0"><?= $historyItem->title ?></h6>
                                                    <small class="text-muted"><?= $historyItem->updatedAt ?></small>
                                                </div>
                                              <div class="d-flex justify-content-between flex-wrap gap-2 mb-2">
                                                <div class="d-flex flex-wrap align-items-center mb-50">
                                                  <div class="avatar avatar-sm me-2">
                                                      <?php if ($historyItem->updatedBy > 0) { ?>
                                                        <img src="<?= getProfilePicFromUserID($historyItem->updatedBy) ?>" alt="Avatar" class="rounded-circle">
                                                      <?php } ?>
                                                  </div>
                                                  <div>
                                                    <p class="mb-0 small fw-medium"><?= getDisplayNameFromUserID($historyItem->updatedBy) ?></p>
                                                  </div>
                                                </div>
                                              </div>
                                                <?php if (!empty($historyItem->remark)) {
                                                    // Check if the title contains 'rejected'
                                                    $remarkClass = 'bg-info'; // Default class
                                                    if (strpos(strtolower($historyItem->title), 'rejected') !== false) {
                                                        $remarkClass = 'bg-danger text-white'; // Apply danger class if title contains 'rejected'
                                                    }
                                                    ?>
                                                  <p class="badge <?= $remarkClass ?> rounded d-flex align-items-center">
                                                      <?= htmlspecialchars($historyItem->remark) ?>
                                                  </p>
                                                <?php } ?>
                                            </div>
                                        </li>
                                        <?php
                                    }

                                    ?>

                                    <?php

                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!--Project History Canvas ENDS-->
                    <!-- Offcanvas Ends -->
                <!-- / Content -->
                <!-- Footer -->
                <footer class="content-footer footer bg-footer-theme">
                    <?php include_once __DIR__ . "/../../includes/dashboard/dashboard_footer_section.php"; ?>
                </footer>
                <!-- / Footer -->
                <div class="content-backdrop fade"></div>
            </div>
            <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
    </div>
    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
    <!-- Drag Target Area To SlideIn Menu On Small Screens -->
    <div class="drag-target"></div>
</div>
<div class="modal fade" id="createTaskModal" tabindex="-1" aria-hidden=true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createTaskModalLabel">Create Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="taskTitle" class="form-label"> Task Title</label>
                        <input type="text" class="form-control" id="taskTitle" placeholder="Task Title" required>
                    </div>
                    <div class="col-md-6">
                        <label for="taskDescription" class="form-label"> Task Description</label>
                        <input type="text" class="form-control" id="taskDescription" placeholder="Description" required>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="assignees" class="form-label">Assignees</label>
                        <select id="assignees" class="form-select selectpicker w-100 mb-4" multiple data-style="btn-default" data-live-search="true" tabindex="null" required>
                            <option value="">Choose</option>
                            <?php
                            $companyId = $_SESSION['SES_SELECTED_COMPANY'];
                            $res = $db->query("SELECT u.userID, u.firstName, u.lastName 
                                                  FROM users u
                                                  INNER JOIN user_company_mapping ucm ON u.userID = ucm.userId
                                                  WHERE u.active = 1 AND ucm.companyId = ?i
                                                  ORDER BY u.firstName ASC", $companyId);
                            while ($row = mysqli_fetch_assoc($res)) {
                                ?>
                                <option value="<?= $row['userID'] ?>"><?= $row['firstName'] . ' ' . $row['lastName'] ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="dueDate" class="form-label"> Task Due Date</label>
                        <input id="dueDate" type="text" class="form-control gmm-date-format" placeholder="DD - MMM - YYYY" value="<?php echo date('Y-m-d'); ?>"/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Discard</button>
                <button type="button" class="btn btn-primary" onclick="createTask()">Create</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editProjectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProjectModalLabel">Edit Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="projectId" value="<?=$projectDATA['projectId']?>">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="projectTitle" class="form-label">Project Title</label>
                        <input type="text" class="form-control" id="projectTitle" placeholder="Project Title" required value="<?=$projectDATA['projectTitle']?>">
                    </div>
                    <div class="col-md-6">
                        <label for="projectHeadId" class="form-label">Project Head</label>
                        <select id="projectHeadId" class="form-select selectpicker w-100 mb-4" data-style="btn-default" data-live-search="true" required>
                            <option value="">Select Project Head</option>
                            <?php
                            $companyId = $_SESSION['SES_SELECTED_COMPANY'];
                            $res = $db->query("SELECT u.userID, u.firstName, u.lastName 
                                               FROM users u
                                               INNER JOIN user_company_mapping ucm ON u.userID = ucm.userId
                                               WHERE u.active = 1 AND ucm.companyId = ?i
                                               ORDER BY u.firstName ASC", $companyId);
                            while ($row = mysqli_fetch_assoc($res)) {
                                $selected = "";
                                if($projectHeadId == $row['userID']) $selected = "selected";
                                ?>
                                <option value="<?= $row['userID'] ?>" <?=$selected?>><?= $row['firstName'] . ' ' . $row['lastName'] ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="startDate" class="form-label">Project Start Date</label>
                        <input id="startDate" type="text" class="form-control gmm-date-format" placeholder="DD - MMM - YYYY" value="<?=$projectDATA['startDate']?>">
                    </div>
                    <div class="col-md-6">
                        <label for="endDate" class="form-label">Project End Date</label>
                        <input id="endDate" type="text" class="form-control gmm-date-format" placeholder="DD - MMM - YYYY" value="<?=$projectDATA['endDate']?>">
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-12">
                        <label for="projectDescription" class="form-label">Project Description</label>
                        <textarea class="form-control" id="projectDescription" rows="3" placeholder="Enter project description"><?= htmlspecialchars($projectDATA['description'] ?? '') ?></textarea>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-12">
                        <label for="projectAssignees" class="form-label">Project Assignees</label>
                        <select id="projectAssignees" class="form-select w-100 mb-4" multiple data-style="btn-default" data-live-search="true">
                            <?php
                            $companyId = $_SESSION['SES_SELECTED_COMPANY'];
                            
                            // Get current assignees for this project
                            $currentAssignees = [];
                            if (isset($projectDATA['projectId'])) {
                                $assigneeRes = $db->query("SELECT userId FROM consultation_project_assignees WHERE projectId = ?i", $projectDATA['projectId']);
                                while ($assignee = mysqli_fetch_assoc($assigneeRes)) {
                                    $currentAssignees[] = $assignee['userId'];
                                }
                            }
                            
                            // Get all available users
                            $res = $db->query("SELECT u.userID, u.firstName, u.lastName 
                                               FROM users u
                                               INNER JOIN user_company_mapping ucm ON u.userID = ucm.userId
                                               WHERE u.active = 1 AND ucm.companyId = ?i
                                               ORDER BY u.firstName ASC", $companyId);
                            while ($row = mysqli_fetch_assoc($res)) {
                                $selected = in_array($row['userID'], $currentAssignees) ? "selected" : "";
                                ?>
                                <option value="<?= $row['userID'] ?>" <?=$selected?>><?= $row['firstName'] . ' ' . $row['lastName'] ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Discard</button>
                <button type="button" class="btn btn-primary" onclick="editProject()">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTaskModalLabel">Edit Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="taskId" value="">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="editTaskTitle" class="form-label">Task Title</label>
                        <input type="text" class="form-control" id="editTaskTitle" placeholder="Task Title" required>
                    </div>
                    <div class="col-md-6">
                        <label for="editTaskDescription" class="form-label">Task Description</label>
                        <input type="text" class="form-control" id="editTaskDescription" placeholder="Description" required>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="editAssignees" class="form-label">Assignees</label>
                        <select id="editAssignees" class="form-select w-100 mb-4" multiple data-style="btn-default" data-live-search="true" required>
                            <option value="">Choose</option>
                            <?php
                            $companyId = $_SESSION['SES_SELECTED_COMPANY'];
                            $res = $db->query("SELECT u.userID, u.firstName, u.lastName 
                                               FROM users u
                                               INNER JOIN user_company_mapping ucm ON u.userID = ucm.userId
                                               WHERE u.active = 1 AND ucm.companyId = ?i
                                               ORDER BY u.firstName ASC", $companyId);
                            while ($row = mysqli_fetch_assoc($res)) {
                                ?>
                                <option value="<?= $row['userID'] ?>"><?= $row['firstName'] . ' ' . $row['lastName'] ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="editDueDate" class="form-label">Task Due Date</label>
                        <input id="editDueDate" type="text" class="form-control gmm-date-format" placeholder="DD - MMM - YYYY" value="">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Discard</button>
                <button type="button" class="btn btn-primary" onclick="editTask()">Save</button>
            </div>
        </div>
    </div>
</div>
<!-- / Layout wrapper -->
<?php
include_once __DIR__ . "/../../includes/dashboard/dashboard_footer_scripts.php";
?>

<script>
    $(document).ready(function() {

        $('#projectAssignees').selectpicker();
    
        // Handle modal show event to ensure proper initialization
        // $('#editProjectModal').on('shown.bs.modal', function() {
        //     $('#projectAssignees').selectpicker('refresh');
        // });

        $(".selectpicker").selectpicker();

        // Handle task edit button click
        $(document).off('click', '.taskEditButton').on('click', '.taskEditButton', function (e) {
            let taskId = $(this).data('task-id');
            let projectId = $(this).data('project-id');
            let taskTitle = $(this).data('task-title');
            let taskDescription = $(this).data('task-description');
            let taskDueDate = $(this).data('task-due-date');
            let assigneeIdsRaw = $(this).data('assignee-ids');
            let assigneeIds = [];

            // Debug raw data
            console.log('Raw assigneeIds:', assigneeIdsRaw);
            console.log('Task ID:', taskId);
            console.log('Project ID:', projectId);
            console.log('Task Title:', taskTitle);
            console.log('Task Description:', taskDescription);
            console.log('Task Due Date:', taskDueDate);

            // Handle assigneeIds
            if (Array.isArray(assigneeIdsRaw)) {
                assigneeIds = assigneeIdsRaw;
            } else if (assigneeIdsRaw && typeof assigneeIdsRaw === 'string' && assigneeIdsRaw.trim() !== '') {
                try {
                    let decodedAssigneeIds = decodeURIComponent(assigneeIdsRaw);
                    console.log('Decoded Assignee IDs:', decodedAssigneeIds);
                    assigneeIds = JSON.parse(decodedAssigneeIds);
                    assigneeIds = assigneeIds.map(String); // Ensure IDs are strings
                } catch (error) {
                    console.error('JSON Parse Error for assigneeIds:', error, 'Raw value:', assigneeIdsRaw);
                    assigneeIds = [];
                }
            }
            console.log('Parsed Assignee IDs:', assigneeIds);

            // Populate modal fields
            $('#taskId').val(taskId || '');
            $('#projectId').val(projectId || '');
            $('#editTaskTitle').val(taskTitle || '');
            $('#editTaskDescription').val(taskDescription || '');
            $('#editDueDate').val(taskDueDate || '');
            $('#editDueDate').flatpickr({
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd - M - Y',
                maxDate: '<?=date('Y-m-d', strtotime($projectDATA['endDate']))?>'
            }).setDate(taskDueDate || '');

            $('#editAssignees').val(assigneeIds).selectpicker();

            // Reset modal on close to prevent state persistence
            $(document).off('hidden.bs.modal', '#editTaskModal').on('hidden.bs.modal', '#editTaskModal', function () {
                // Clear form fields
                $('#taskId').val('');
                $('#projectId').val('');
                $('#editTaskTitle').val('');
                $('#editTaskDescription').val('');
                $('#editDueDate').val('');

                // Reset #editAssignees selectpicker
                if ($('#editAssignees').hasClass('selectpicker-initialized')) {
                    $('#editAssignees').selectpicker('destroy');
                    console.log('Destroyed selectpicker for editAssignees on modal close');
                }
                $('#editAssignees').find('option:selected').prop('selected', false); // Deselect all options
                $('#editAssignees').val([]); // Clear selected values
                $('#editAssignees').selectpicker(); // Reinitialize
                $('#editAssignees').selectpicker('refresh');
                $('#editAssignees').addClass('selectpicker-initialized');

                console.log('Modal reset - editAssignees options:', $('#editAssignees').html());
                console.log('Modal reset - selected assignees:', $('#editAssignees').val());
            });

            // Log selectpicker dropdown show for debugging
            $('.selectpicker').on('show.bs.select', function () {
                console.log('Selectpicker dropdown shown for:', $(this).attr('id'));
            });
        });

        $(document).on('click', '.taskDeleteButton', function () {
            var taskId = $(this).data('task-id');

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel',
                customClass: {
                    confirmButton: 'btn btn-danger me-2',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteTask(taskId);
                }
            });
        });

        $(document).on('click', '.taskMarkDoneButton', function () {
            var taskId = $(this).data('task-id');
            $.ajax({
                url: '/ajax/consultation/mark_task_done.php',
                type: 'POST',
                data: { taskId: taskId },
                success: function(data) {
                    var result = JSON.parse(data);
                    if (result.status === 'SUCCESS') {
                        $('.invoice-list-table').DataTable().ajax.reload();
                        showSuccessMessage(result.message);
                    } else {
                        shpwErrorMessage(result.message);
                    }
                },
                error: function(error) {
                    console.log(error);
                    showErrorMessage('An error occurred while marking the task as done.');
                }
            });
        });
        
    });


    // Function to handle task deletion
function deleteTask(taskId) {
    $.ajax({
        url: '/ajax/consultation/delete_project_task.php',
        type: 'POST',
        data: { taskId: taskId },
        success: function(data) {
            var result = JSON.parse(data);
            if (result.status === 'SUCCESS') {
                $('.invoice-list-table').DataTable().ajax.reload();
                showSuccessMessage(result.message);
            } else {
                showErrorMessage(result.message);
            }
        },
        error: function(error) {
            console.log(error);
            showErrorMessage('An error occurred while deleting the task.');
            }
        });
    }

    function createTask() {
        let projectId = '<?php echo $projectID; ?>'; // From PHP context
        // let itemId = $('#itemId').val();
        let taskTitle = $('#taskTitle').val();
        let taskDescription = $('#taskDescription').val();
        let dueDate = $('#dueDate').val();
        let assigneeIds = $('#assignees').val();
        console.log(dueDate);

        console.log(assigneeIds);

        // Validate inputs
        if (!taskTitle) {
            showErrorMessage('Please enter a task title.');
            return;
        }

        if (!assigneeIds || assigneeIds.length === 0) {
            showErrorMessage('Please select at least one assignee.');
            return;
        }

        if (!dueDate) {
            showErrorMessage('Please select a due date for the task.');
            return;
        }

        // // Prepare FormData for AJAX
        var formData = new FormData();
        formData.append('projectId', projectId);
        // formData.append('itemId', itemId);
        formData.append('taskTitle', taskTitle);
        formData.append('taskDescription', taskDescription);
        formData.append('dueDate', dueDate);
        formData.append('assigneeIds', JSON.stringify(assigneeIds));

        $.ajax({
            url: '/ajax/consultation/save_task.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(data) {
                console.log(data);
                var result = JSON.parse(data);
                if (result.status === 'SUCCESS') {
                    showSuccessMessage(result.message, gotoPage, "/project/view/" + projectId);
                    $('#createTaskModal').modal('hide');
                } else {
                    showErrorMessage(result.message);
                }
            },
            error: function(error) {
                console.log(error);
                showErrorMessage('An error occurred while creating the task.');
            }
        });
    }

    function editTask() {

        let projectId = '<?php echo $projectID; ?>';
        let taskId = $('#taskId').val();
        let taskTitle = $('#editTaskTitle').val();
        let taskDescription = $('#editTaskDescription').val();
        let assigneeIds = $('#editAssignees').val();
        let dueDate = $('#editDueDate').val();

        console.log(dueDate);

        if (!taskTitle) {
            showErrorMessage('Please enter a task title.');
            return;
        }

        if (!assigneeIds || assigneeIds.length === 0) {
            showErrorMessage('Please select at least one assignee.');
            return;
        }

        if (!dueDate) {
            showErrorMessage('Please select a due date for the task.');
            return;
        }

        var formData = new FormData();
        formData.append('projectId', projectId);
        formData.append('taskId', taskId);
        formData.append('taskTitle', taskTitle);
        formData.append('taskDescription', taskDescription);
        formData.append('assigneeIds', JSON.stringify(assigneeIds));
        formData.append('dueDate', dueDate);

        $.ajax({
            url: '/ajax/consultation/save_task.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(data) {
                var result = JSON.parse(data);
                if(result.status === 'SUCCESS') {
                    showSuccessMessage(result.message, gotoPage, "/project/view/" + projectId);
                    $('#editTaskModal').modal('hide');
                } else {
                    showErrorMessage(result.message);
                }
            },
            error: function(error) {
                console.log(error);
                showErrorMessage('An error occurred while updating the task.');
            }
        });
    }

function editProject() {
    let projectId = '<?=$projectID?>';
    let projectHeadId = $('#projectHeadId').val();
    let customerId = '<?=$customerId?>';
    let projectTitle = $('#projectTitle').val();
    let projectDescription = $('#projectDescription').val();
    let startDate = $('#startDate').val();
    let endDate = $('#endDate').val();
    let assigneeIds = $('#projectAssignees').val();
    console.log(projectDescription);

    if(!projectTitle) {
        showErrorMessage('Please Enter a Suitable Title for the project');
        return;
    }

    // if (!projectHeadId) {
    //     showErrorMessage('Please select a project head.');
    //     return;
    // }

    if (!assigneeIds || assigneeIds.length === 0) {
        showErrorMessage('Please select at least one assignee.');
        return;
    }

    if(!startDate || !endDate) {
        showErrorMessage('Please select start and end dates for the project.');
        return;
    }

    var formData = new FormData();
    formData.append('projectId', projectId);
    formData.append('projectTitle', projectTitle);
    formData.append('projectDescription', projectDescription);
    formData.append('projectHeadId', projectHeadId);
    formData.append('customerId', customerId);
    formData.append('assigneeIds', JSON.stringify(assigneeIds));
    formData.append('startDate', startDate);
    formData.append('endDate', endDate);

    $.ajax({
        url: '/ajax/consultation/save_project.php',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(data) {
            console.log(data);
            var result = JSON.parse(data);
            if (result.status === 'SUCCESS') {
                showSuccessMessage(result.message, gotoPage, "/project/view/" + projectId);
                $('#editProjectModal').modal('hide');
            } else {
                showErrorMessage(result.message);
            }
        },
        error: function(error) {
            console.log(error);
            showErrorMessage('An error occurred while creating the project.');
        }
    });

}

    'use strict';
    $(function () {
        // Variable declaration for table
        var dt_invoice_table = $('.invoice-list-table');

        // RFP datatable
        if (dt_invoice_table.length) {

            let ajaxURL = '/ajax/consultation/fetch_project_tasks.php';

            let projectID = '<?=$projectID?>';
            if(projectID !== '')
            {
                ajaxURL += '?projectID=' + projectID;
            }

            var dt_invoice = dt_invoice_table.DataTable({
                ajax: ajaxURL,
                columns: [
                    // columns according to JSON
                    {data: 'taskId'},
                    {data: 'taskId'},
                    {data: 'taskNumber'},
                    {data: 'projectName'},
                    {data: 'taskTitle'},
                    {data: 'taskDescription'},
                    {data: 'dueDate'},
                    {data: 'taskStatus'},
                    {data: 'action'}
                ],
                columnDefs: [
                    {
                        // For Responsive
                        className: 'control',
                        responsivePriority: 2,
                        searchable: false,
                        targets: 0,
                        render: function (data, type, full, meta) {
                            return '';
                        }
                    },
                    {
                        // For Checkboxes
                        targets: 1,
                        orderable: false,
                        checkboxes: {
                            selectAllRender: '<input type="checkbox" class="form-check-input">'
                        },
                        render: function () {
                            return '<input type="checkbox" class="dt-checkboxes form-check-input" >';
                        },
                        searchable: false
                    },
                    {
                        // Actions
                        targets: 9,
                        searchable: false,
                        orderable: false,
                        render: function (data, type, full, meta) {
                            var $taskId = full['taskId'] || '';
                            var $taskTitle = full['taskTitle'] || '';
                            var $taskDescription = full['taskDescription'] || '';
                            var $taskDueDate = full['dueDate'] ? full['dueDate'].split(' ')[0] : ''; // Extract Y-m-d
                            var $projectId = full['projectId'] || '';
                            var $assigneeIds = JSON.stringify(full['assigneeIds'] || []); // JSON-encoded array

                            // Escape HTML entities for attributes
                            $taskTitle = $('<div/>').text($taskTitle).html();
                            $taskDescription = $('<div/>').text($taskDescription).html();
                            // Encode JSON string for HTML attribute to prevent breaking quotes
                            $assigneeIds = encodeURIComponent($assigneeIds).replace(/'/g, "&#39;").replace(/"/g, "&quot;");

                            return (
                                '<div class="dropdown">' +
                                    '<button class="btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">' +
                                        '<i class="ti ti-dots-vertical ti-md"></i>' +
                                    '</button>' +
                                    '<ul class="dropdown-menu">' +
                                        '<li><a class="dropdown-item taskEditButton" href="javascript:void(0)" ' +
                                        'data-bs-toggle="modal" data-bs-target="#editTaskModal" ' +
                                        'data-task-id="' + $taskId + '" ' +
                                        'data-project-id="' + $projectId + '" ' +
                                        'data-task-title="' + $taskTitle + '" ' +
                                        'data-task-description="' + $taskDescription + '" ' +
                                        'data-task-due-date="' + $taskDueDate + '" ' +
                                        'data-assignee-ids="' + $assigneeIds + '">' +
                                        '<i class="ti ti-edit me-2"></i>Edit' +
                                        '</a></li>' +
                                        '<li><a class="dropdown-item taskDeleteButton" href="javascript:void(0)" ' +
                                        'data-task-id="' + $taskId + '">' +
                                        '<i class="ti ti-trash me-2"></i>Delete' +
                                        '</a></li>' +
                                        '<li><a class="dropdown-item taskMarkDoneButton" href="javascript:void(0)" ' +
                                        'data-task-id="' + $taskId + '">' +
                                        '<i class="ti ti-check me-2"></i>Mark as Done' +
                                        '</a></li>' +
                                    '</ul>' +
                                '</div>'
                            );
                        }
                    },

                    {
                        // Task Number
                        targets: 2,
                        render: function (data, type, full, meta) {
                            var $taskNumber = full['taskNumber'];
                            var $taskId = full['taskId'];

                            return '<span class=text-truncate>' + $taskNumber + '</span>';
                        }
                    },
                    {
                        // Project
                        targets: 3,
                        responsivePriority: 4,

                        render: function (data, type, full, meta) {
                            var $projectName = full['projectName'];

                            return '<span class=text-truncate>' + $projectName + '</span>';
                        }
                    },

                    {
                        // Task Title
                        targets: 4,
                        responsivePriority: 2,
                        render: function (data, type, full, meta) {
                            var $taskTitle = full['taskTitle'];

                            return '<span class=text-truncate>' + $taskTitle + '</span>';
                        }
                    },
                    {
                        // Task Description
                        targets: 5,
                        responsivePriority: 2,
                        render: function (data, type, full, meta) {
                            var $taskDescription = full['taskDescription'];

                            return '<span style="white-space: normal; display: block;">' + $taskDescription + '</span>';
                        }
                    },
                    {
                        // Status
                        targets: 8,
                        render: function (data, type, full, meta) {
                            var $status = full['taskStatus'];
                            let $badge_class = '';

                            if ($status === "NEW") {
                                $badge_class = 'bg-label-linkedin';
                                return ('<span class="badge ' + $badge_class + '" text-capitalized>' + $status + '</span>');
                            } else if ($status === "COMPLETED") {
                                $badge_class = 'bg-label-success';
                                return ('<span class="badge ' + $badge_class + '" text-capitalized>' + $status + '</span>');
                            } else if ($status === "CANCELLED") {
                                $badge_class = 'bg-label-danger';
                                return ('<span class="badge ' + $badge_class + '" text-capitalized>' + $status + '</span>');
                            } else if ($status === "IN PROGRESS") {
                                $badge_class = 'bg-label-info';
                                return ('<span class="badge ' + $badge_class + '" text-capitalized>' + $status + '</span>');
                            }

                            return ('<span text-capitalized>' + $status + '</span>');
                        }
                    },
                    {
                        // Assignees
                        targets: 6,
                        orderable: false,
                        searchable: false,
                        render: function (data, type, full, meta) {
                            var $assignees = full['assignees'] || [];
                            var $assignee_items = '';
                            var $assignee_count = 0;

                            // Limit to 4 visible avatars
                            var visibleAssignees = $assignees.slice(0, 4);

                            // Create avatar items
                            visibleAssignees.forEach(function(assignee) {
                                var firstName = assignee.firstName || '';
                                var lastName = assignee.lastName || '';
                                var fullName = firstName + ' ' + lastName;

                                // Get initials
                                var initials = (firstName.charAt(0) + lastName.charAt(0)).toUpperCase();

                                // Random color state
                                var states = ['success', 'danger', 'warning', 'info', 'primary', 'secondary'];
                                var state = states[Math.floor(Math.random() * states.length)];

                                $assignee_items +=
                                    '<li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" ' +
                                    'title="' + fullName + '" class="avatar avatar-sm pull-up">' +
                                    '<span class="avatar-initial rounded-circle bg-label-' + state + '">' + initials + '</span>' +
                                    '</li>';
                                $assignee_count++;
                            });

                            // Show "+X more" if there are more than 4 assignees
                            if ($assignees.length > 4) {
                                var remaining = $assignees.length - 4;
                                $assignee_items +=
                                    '<li class="avatar avatar-sm">' +
                                    '<span class="avatar-initial rounded-circle pull-up bg-label-dark" ' +
                                    'data-bs-toggle="tooltip" data-bs-placement="top" title="' + remaining + ' more">+' +
                                    remaining + '</span>' +
                                    '</li>';
                            }

                            return '<div class="d-flex align-items-center">' +
                                '<ul class="list-unstyled d-flex align-items-center avatar-group mb-0 z-2">' +
                                $assignee_items +
                                '</ul>' +
                                '</div>';
                        }
                    },
                    {
                        // Due Date
                        targets: 7,
                        render: function (data, type, full, meta) {
                            var $dueDate = new Date(full['dueDate']);

                            let $row_output;
                            if (full['dueDate'] != null) {
                                $row_output = '<span class="d-none">' + moment($dueDate).format('YYYYMMDD') + '</span><span class="text-truncate">' + moment($dueDate).format('DD MMM YYYY') + '</span>';
                            } else {
                                $row_output = "-";
                            }

                            return $row_output;
                        }
                    },
                    
                ],
                order: [[2, 'desc']],
                dom:
                    '<"row mx-1"' +
                    '<"col-12 col-md-6 d-flex align-items-center justify-content-center justify-content-md-start gap-2"l<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start"B>>' +
                    '<"col-12 col-md-6 d-flex align-items-center justify-content-end flex-column flex-md-row pe-5 gap-md-4 mt-n6 mt-md-0"f<"invoice_status mb-6 mb-md-0">>' +
                    '>t' +
                    '<"row mx-1"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                language: {
                    sLengthMenu: 'Show _MENU_',
                    search: '',
                    searchPlaceholder: 'Search Task',
                    paginate: {
                        next: '<i class="ti ti-chevron-right ti-sm"></i>',
                        previous: '<i class="ti ti-chevron-left ti-sm"></i>'
                    }
                },
                // Buttons with Dropdown
                buttons: [
                    {
                        text: '<i class="ti ti-plus ti-xs me-md-2"></i><span class="d-md-inline-block d-none">New Task</span>',
                        className: 'btn btn-primary waves-effect waves-light',
                        action: function (e, dt, button, config) {

                            $('#createTaskModal').modal('show');

                        }
                    }
                ],
                // For responsive popup
                responsive: {
                    details: {
                        display: $.fn.dataTable.Responsive.display.modal({
                            header: function (row) {
                                var data = row.data();
                                return 'Details of ' + data['taskNumber'];
                            }
                        }),
                        type: 'column',
                        renderer: function (api, rowIdx, columns) {
                            var data = $.map(columns, function (col, i) {
                                return col.title !== '' // ? Do not show row in modal popup if title is blank (for check box)
                                    ? '<tr data-dt-row="' +
                                    col.rowIndex +
                                    '" data-dt-column="' +
                                    col.columnIndex +
                                    '">' +
                                    '<td>' +
                                    col.title +
                                    ':' +
                                    '</td> ' +
                                    '<td>' +
                                    col.data +
                                    '</td>' +
                                    '</tr>'
                                    : '';
                            }).join('');

                            return data ? $('<table class="table"/><tbody />').append(data) : false;
                        }
                    }
                },
                initComplete: function () {
                }
            });
        }

        // On each datatable draw, initialize tooltip
        dt_invoice_table.on('draw.dt', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl, {
                    boundary: document.body
                });
            });
        });

        // Delete Record
        $('.invoice-list-table tbody').on('click', '.delete-record', function () {
            dt_invoice.row($(this).parents('tr')).remove().draw();
        });

        // Filter form control to default size
        // ? setTimeout used for multilingual table initialization
        setTimeout(() => {
            $('.dataTables_filter .form-control').removeClass('form-control-sm');
            $('.dataTables_length .form-select').removeClass('form-select-sm');
        }, 300);
    });
</script>
<style>
    .tooltip-inner {
        max-width: 300px; /* Adjust as needed */
        text-align: left;
        padding: 0.5rem;
    }

    #showHistoryOffcanvas {
        width: 650px !important;
    }
</style>
</body>
</html>