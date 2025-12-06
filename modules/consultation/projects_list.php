<?php
$PAGE_ID = "PROJECTS_LIST";
include_once __DIR__ . "/../../includes/baseIncludes.php";
include_once __DIR__ . "/../../includes/auth_check.php";
?>

<!doctype html>

<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
<head>
    <?php include_once __DIR__ . "/../../includes/dashboard/dashboard_head_section.php"; ?>
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="/assets/vendor/libs/node-waves/node-waves.css"/>
    <link rel="stylesheet" href="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css"/>
    <link rel="stylesheet" href="/assets/vendor/libs/typeahead-js/typeahead.css"/>
    <link rel="stylesheet" href="/assets/vendor/libs/flatpickr/flatpickr.css"/>
    <link rel="stylesheet" href="/assets/vendor/libs/bootstrap-select/bootstrap-select.css"/>
    <link rel="stylesheet" href="/assets/vendor/libs/animate-css/animate.css"/>
    <link rel="stylesheet" href="/assets/vendor/libs/sweetalert2/sweetalert2.css"/>
    <!-- Page CSS -->
    <link rel="stylesheet" href="/assets/vendor/css/pages/app-invoice.css"/>
    <link rel="stylesheet" href="/assets/vendor/css/jquery-ui.css"/>
    <link rel="stylesheet" href="/assets/vendor/libs/select2/select2.css "/>
    <?php include_once __DIR__ . "/../../includes/dashboard/dashboard_head_section_post_vendor.php"; ?>
    <title>GrandMaster ERP | Projects</title>
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
                <div class="container-xxl flex-grow-1 container-p-y">
                    <div class="card mb-6">
                        <div class="card-widget-separator-wrapper">
                            <div class="card-body card-widget-separator py-2">
                                <div class="row gy-1 gy-sm-1">
                                    <h4 class="my-0">Projects</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-datatable">
                        <div class="row g-6 mb-4">
                            <?php
                            $res = $db->query("SELECT * FROM `consultation_projects`");
                            while($row = mysqli_fetch_assoc($res)){
                                ?>
                                <div class="col-xl-4 col-lg-6 col-md-6 d-flex align-items-stretch">
                                    <div class="card h-100 w-100">
                                        <div class="card-header pb-4">
                                            <div class="d-flex align-items-start">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar me-4">
                                                        <?php
                                                        $projectTitle = $row['projectTitle'];
                                                        // Remove special characters and keep only letters and spaces
                                                        $cleanTitle = preg_replace('/[^A-Za-z\s]/', '', $projectTitle);
                                                        $words = preg_split('/\s+/', trim($cleanTitle));
                                                        $initials = '';
                                                        if (count($words) >= 1) {
                                                            $initials .= strtoupper(substr($words[0], 0, 1));
                                                            if (count($words) >= 2) {
                                                                $initials .= strtoupper(substr($words[1], 0, 1));
                                                            }
                                                        }
                                                        // If title was empty after cleanup, use 'NA'
                                                        $initials = !empty($initials) ? $initials : 'NA';

                                                        // Random color class
                                                        $colorClasses = ['bg-label-primary', 'bg-label-success', 'bg-label-info',
                                                            'bg-label-warning', 'bg-label-danger', 'bg-label-dark'];
                                                        $randomColor = $colorClasses[array_rand($colorClasses)];
                                                        ?>
                                                        <span class="avatar-initial rounded-circle <?= $randomColor ?>"><?= $initials ?></span>
                                                    </div>
                                                    <div class="me-2">
                                                        <h5 class="mb-0">
                                                            <a href="/project/view/<?= $row['projectId'] ?>" class="text-heading"><?=$row['projectTitle']?></a>
                                                        </h5>
                                                        <div class="client-info text-body">
                                                            <span class="fw-medium">Client: </span><span><?php
                                                            // if(!empty($row['documentId']))
                                                            // {
                                                            // echo getCustomerNameByProjectID($row['projectId']);
                                                            // } else {
                                                            //     echo getCustomerNameFromID($row['customerId']);
                                                            // }
                                                            if($row['customerId'] == 0) {
                                                                echo 'Internal';
                                                            } else {
                                                                echo getCustomerNameFromID($row['customerId']);
                                                            }
                                                            ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="ms-auto">
                                                    <a href="/project/view/<?= $row['projectId'] ?>" class="btn btn-icon btn-text-secondary rounded-pill p-0 edit-icon" style="z-index: 10;">
                                                        <i class="ti ti-edit ti-md text-muted"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="d-flex align-items-center flex-wrap">
                                                <div class="bg-lighter px-3 py-2 rounded me-auto mb-4">
                                                    <p class="mb-1"><span class="fw-medium text-heading">$24.8k</span> <span>/ $18.2k</span></p>
                                                    <span class="text-body">Total Budget</span>
                                                </div>
                                                <div class="text-start mb-4">
                                                    <p class="mb-1">
                                                        <span class="text-heading fw-medium">Start Date: </span> <span><?=formatDate($row['startDate'], false)?></span>
                                                    </p>
                                                    <p class="mb-1">
                                                        <span class="text-heading fw-medium">Deadline: </span> <span><?= formatDate($row['endDate'], false)?></span>
                                                    </p>
                                                </div>
                                            </div>
                                            <p class="mb-0"><span class="fw-medium">Project Head: </span><span><?=getDisplayNameFromUserID($row['projectHeadId'])?></span></p>
                                             <div class="mt-2">
                                                <span class="fw-medium">Description: </span>
                                                <?php
                                                $description = $row['description'] ?? '';
                                                $maxLength = 50; // Adjust this value as needed
                                                $truncated = mb_strlen($description) > $maxLength 
                                                    ? mb_substr($description, 0, $maxLength) . '...' 
                                                    : $description;
                                                ?>
                                                <span class="text-body" 
                                                    data-bs-toggle="tooltip" 
                                                    data-bs-placement="top" 
                                                    title="<?= htmlspecialchars($description) ?>"
                                                    style="cursor: help;">
                                                    <?= htmlspecialchars($truncated) ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-body border-top">
                                            <div class="d-flex align-items-center mb-4">
                                                <p class="mb-1"><span class="text-heading fw-medium">All Hours: </span> <span>380/244</span></p>
                                                <?php
                                                // Calculate days left (proper date-only comparison)
                                                $today = new DateTime('today'); // Gets today at midnight
                                                $endDate = new DateTime($row['endDate']);
                                                $endDate->setTime(0, 0, 0); // Set time to midnight for proper date comparison

                                                $interval = $today->diff($endDate);
                                                $daysLeft = $interval->days;

                                                // Handle positive/negative intervals correctly
                                                if ($interval->invert) {
                                                    // End date is in the past
                                                    $daysLeft = -$daysLeft;
                                                }

                                                // Format display text
                                                if ($daysLeft < 0) {
                                                    $badgeClass = 'bg-label-dark';
                                                    $daysLeftText = 'Expired';
                                                } else {
                                                    $daysLeftText = $daysLeft . ' ' . ($daysLeft == 1 ? 'Day' : 'Days') . ' left';

                                                    // Determine badge color
                                                    if ($daysLeft == 0) {
                                                        $badgeClass = 'bg-label-warning'; // Due today
                                                    } elseif ($daysLeft <= 3) {
                                                        $badgeClass = 'bg-label-danger'; // Less than 3 days
                                                    } elseif ($daysLeft <= 7) {
                                                        $badgeClass = 'bg-label-warning'; // Less than a week
                                                    } else {
                                                        $badgeClass = 'bg-label-success'; // More than a week
                                                    }
                                                }
                                                ?>
                                                <span class="badge <?= $badgeClass ?> ms-auto"><?= $daysLeftText ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <small class="text-body">Task: 290/344</small>
                                                <small class="text-body">95% Completed</small>
                                            </div>
                                            <div class="progress mb-4 rounded" style="height: 8px">
                                                <div
                                                        class="progress-bar rounded"
                                                        role="progressbar"
                                                        style="width: 95%"
                                                        aria-valuenow="95"
                                                        aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <?php
                                                    $assignees = getProjectAssignees($row['projectId']);
                                                    $visibleAssignees = array_slice($assignees, 0, 4);
                                                    $totalAssignees = count($assignees);
                                                    ?>
                                                    
                                                    <ul class="list-unstyled d-flex align-items-center avatar-group mb-0 z-2">
                                                        <?php foreach ($visibleAssignees as $assignee): 
                                                            $firstName = $assignee['firstName'] ?? '';
                                                            $lastName = $assignee['lastName'] ?? '';
                                                            $fullName = trim("$firstName $lastName");
                                                            $initials = (!empty($firstName) ? strtoupper(substr($firstName, 0, 1)) : '') . 
                                                                    (!empty($lastName) ? strtoupper(substr($lastName, 0, 1)) : '');
                                                            
                                                            // Random color class
                                                            $colorClasses = ['success', 'danger', 'warning', 'info', 'primary', 'secondary'];
                                                            $randomColor = $colorClasses[array_rand($colorClasses)];
                                                        ?>
                                                            <li data-bs-toggle="tooltip"
                                                                data-popup="tooltip-custom"
                                                                data-bs-placement="top"
                                                                title="<?= htmlspecialchars($fullName) ?>"
                                                                class="avatar avatar-sm pull-up">
                                                                <span class="avatar-initial rounded-circle bg-label-<?= $randomColor ?>">
                                                                    <?= $initials ?>
                                                                </span>
                                                            </li>
                                                        <?php endforeach; ?>
                                                        
                                                        <?php if ($totalAssignees > 4): ?>
                                                            <li class="avatar avatar-sm">
                                                                <span class="avatar-initial rounded-circle pull-up bg-label-dark"
                                                                    data-bs-toggle="tooltip"
                                                                    data-bs-placement="top"
                                                                    title="<?= ($totalAssignees - 4) ?> more">
                                                                    +<?= ($totalAssignees - 4) ?>
                                                                </span>
                                                            </li>
                                                        <?php endif; ?>
                                                        
                                                        <?php if ($totalAssignees === 0): ?>
                                                            <li><small class="text-muted">No members</small></li>
                                                        <?php endif; ?>
                                                    </ul>
                                                    
                                                    <?php if ($totalAssignees > 0): ?>
                                                        <small class="text-muted ms-2"><?= $totalAssignees ?> Member<?= $totalAssignees !== 1 ? 's' : '' ?></small>
                                                    <?php endif; ?>
                                                </div>
                                                <!--                                        <div class="ms-auto">-->
                                                <!--                                            <a href="javascript:void(0);" class="text-muted d-flex align-items-center"-->
                                                <!--                                            ><i class="ti ti-message-dots ti-lg me-1"></i> 15</a-->
                                                <!--                                            >-->
                                                <!--                                        </div>-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <table id="hidden-card-table" style="display: none;"></table>
                    </div>
                </div>
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
<div class="modal fade" id="editProjectModal" tabindex="-1" aria-hidden=true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProjectModalLabel">Edit Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="projectTitle" class="form-label"> Project Title</label>
                        <input type="text" class="form-control" id="projectTitle" placeholder="Project Title" required>
                    </div>
                    <div class="col-md-6">
                        <label for="projectHead" class="form-label">Project Head</label>
                        <select id="projectHeadId" class="form-select selectpicker w-100 mb-4" data-style="btn-default" data-live-search="true" tabindex="null" required>
                            <option value="">Select Project Head</option>
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
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="startDate" class="form-label"> Project Start Date</label>
                        <input id="startDate" type="text" class="form-control gmm-date-format" placeholder="DD - MMM - YYYY" value="<?php echo date('Y-m-d'); ?>"/>
                    </div>
                    <div class="col-md-6">
                        <label for="endDate" class="form-label"> Project End Date</label>
                        <input id="endDate" type="text" class="form-control gmm-date-format" placeholder="DD - MMM - YYYY" value="<?php echo date('Y-m-d'); ?>"/>
                    </div>
                </div>
                <div class="row mt-4">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Item #</th>
                            <th>Item Name</th>
                            <th>Description</th>
                            <th>Quantity</th>
                        </tr>
                        </thead>
                        <tbody id="selectedItemsTableBody">
                        <!-- Populated dynamically by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Discard</button>
                <button type="button" class="btn btn-primary" onclick="createProject()">Create</button>
            </div>
        </div>
    </div>
</div>
<!-- / Layout wrapper -->

<?php include_once __DIR__ . "/../../includes/dashboard/dashboard_footer_scripts.php"; ?>
<script>
    'use strict';
    
    $(function () {
        // Variable declaration for card container and hidden table
        var card_container = $('.card-datatable');
        var hidden_table = $('#hidden-card-table');

        if (card_container.length && hidden_table.length) {
            // Collect card data from existing cards
            var cardData = [];
            $('.card-datatable .row.g-6 > div').each(function () {
                var $card = $(this);
                cardData.push({
                    html: $card.html(), // Store the card's HTML
                    projectTitle: $card.find('.text-heading').text(), // For sorting/searching
                    clientName: $card.find('.client-info span').eq(1).text() // For sorting/searching
                });
            });

            // Initialize DataTable on hidden table
            var dt_cards = hidden_table.DataTable({
                data: cardData,
                columns: [
                    { data: 'html', visible: false }, // Hidden column for card HTML
                    { data: 'projectTitle', visible: false }, // Hidden column for sorting/searching
                    { data: 'clientName', visible: false } // Hidden column for sorting/searching
                ],
                paging: true,
                pageLength: 9, // Show 9 cards per page
                lengthChange: false, // Hide "Show X entries" dropdown
                searching: true, // Enable search
                ordering: false, // Disable sorting for simplicity
                info: true, // Show "Showing X to Y of Z entries"
                dom:
                    '<"row mx-1"' +
                    '<"col-12 col-md-6"f>' + // Search input
                    '<"col-12 col-md-6"p>' + // Pagination
                    '>t' + // Table (hidden, but needed for DataTables)
                    '<"row mx-1"' +
                    '<"col-12 col-md-6"i>' + // Info
                    '>',
                language: {
                    search: '',
                    searchPlaceholder: 'Search Projects',
                    paginate: {
                        next: '<i class="ti ti-chevron-right ti-sm"></i>',
                        previous: '<i class="ti ti-chevron-left ti-sm"></i>'
                    }
                },
                drawCallback: function () {
                    // Clear existing cards
                    $('.card-datatable .row.g-6').empty();

                    // Render cards for the current page
                    var api = this.api();
                    api.rows({ page: 'current' }).data().each(function (data, index) {
                        var cardHtml = '<div class="col-xl-4 col-lg-6 col-md-6 d-flex align-items-stretch">' + data.html + '</div>';
                        $('.card-datatable .row.g-6').append(cardHtml);
                    });

                    // Reinitialize tooltips for new cards
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
                        new bootstrap.Tooltip(tooltipTriggerEl, {
                            boundary: document.body
                        });
                    });
                }
            });

            // Move and style search box
            $('.dataTables_filter')
                .addClass('d-flex justify-content-end mb-3')
                .prependTo('.card-datatable');

            // Make input wider
            $('.dataTables_filter input').addClass('form-control').attr('placeholder', 'Search Projects').css({
                'width': '300px', // or any size you want
                'max-width': '100%',
                'height': '38px', // Match the height of other inputs
            });
        }
    });
</script>

<style>
    .tooltip-inner {
        max-width: 300px; /* Adjust as needed */
        text-align: left;
        padding: 0.5rem;
    }
</style>
</body>
</html>