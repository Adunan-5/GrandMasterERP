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
    <link rel="stylesheet" href="../../assets/vendor/libs/node-waves/node-waves.css"/>
    <link rel="stylesheet" href="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css"/>
    <link rel="stylesheet" href="../../assets/vendor/libs/typeahead-js/typeahead.css"/>
    <link rel="stylesheet" href="../../assets/vendor/libs/flatpickr/flatpickr.css"/>
    <link rel="stylesheet" href="/assets/vendor/libs/bootstrap-select/bootstrap-select.css"/>
    <link rel="stylesheet" href="/assets/vendor/libs/animate-css/animate.css"/>
    <link rel="stylesheet" href="/assets/vendor/libs/sweetalert2/sweetalert2.css"/>
    <!-- Page CSS -->
    <link rel="stylesheet" href="../../assets/vendor/css/pages/app-invoice.css"/>
    <link rel="stylesheet" href="../../assets/vendor/css/jquery-ui.css"/>
    <link rel="stylesheet" href="../../assets/vendor/libs/select2/select2.css "/>
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

                <div class="row g-6">
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="card">
                            <div class="card-header pb-4">
                                <div class="d-flex align-items-start">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-4">
                                            <img
                                                src="../../assets/img/icons/brands/social-label.png"
                                                alt="Avatar"
                                                class="rounded-circle" />
                                        </div>
                                        <div class="me-2">
                                            <h5 class="mb-0">
                                                <a href="javascript:;" class="stretched-link text-heading">Social Banners</a>
                                            </h5>
                                            <div class="client-info text-body">
                                                <span class="fw-medium">Client: </span><span>Christian Jimenez</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ms-auto">
                                        <div class="dropdown z-2">
                                            <button
                                                type="button"
                                                class="btn btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow p-0"
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="ti ti-dots-vertical ti-md text-muted"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="javascript:void(0);">Rename project</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);">View details</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);">Add to favorites</a></li>
                                                <li>
                                                    <hr class="dropdown-divider" />
                                                </li>
                                                <li><a class="dropdown-item text-danger" href="javascript:void(0);">Leave Project</a></li>
                                            </ul>
                                        </div>
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
                                            <span class="text-heading fw-medium">Start Date: </span> <span>14/2/21</span>
                                        </p>
                                        <p class="mb-1">
                                            <span class="text-heading fw-medium">Deadline: </span> <span>28/2/22</span>
                                        </p>
                                    </div>
                                </div>
                                <p class="mb-0">We are Consulting, Software Development and Web Development Services.</p>
                            </div>
                            <div class="card-body border-top">
                                <div class="d-flex align-items-center mb-4">
                                    <p class="mb-1"><span class="text-heading fw-medium">All Hours: </span> <span>380/244</span></p>
                                    <span class="badge bg-label-success ms-auto">28 Days left</span>
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
                                        <ul class="list-unstyled d-flex align-items-center avatar-group mb-0 z-2">
                                            <li
                                                data-bs-toggle="tooltip"
                                                data-popup="tooltip-custom"
                                                data-bs-placement="top"
                                                title="Vinnie Mostowy"
                                                class="avatar avatar-sm pull-up">
                                                <img class="rounded-circle" src="../../assets/img/avatars/5.png" alt="Avatar" />
                                            </li>
                                            <li
                                                data-bs-toggle="tooltip"
                                                data-popup="tooltip-custom"
                                                data-bs-placement="top"
                                                title="Allen Rieske"
                                                class="avatar avatar-sm pull-up">
                                                <img class="rounded-circle" src="../../assets/img/avatars/12.png" alt="Avatar" />
                                            </li>
                                            <li
                                                data-bs-toggle="tooltip"
                                                data-popup="tooltip-custom"
                                                data-bs-placement="top"
                                                title="Julee Rossignol"
                                                class="avatar avatar-sm pull-up me-3">
                                                <img class="rounded-circle" src="../../assets/img/avatars/6.png" alt="Avatar" />
                                            </li>
                                            <li><small class="text-muted">280 Members</small></li>
                                        </ul>
                                    </div>
                                    <div class="ms-auto">
                                        <a href="javascript:void(0);" class="text-muted d-flex align-items-center"
                                        ><i class="ti ti-message-dots ti-lg me-1"></i> 15</a
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="card">
                            <div class="card-header pb-4">
                                <div class="d-flex align-items-start">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-4">
                                            <img
                                                src="../../assets/img/icons/brands/react-label.png"
                                                alt="Avatar"
                                                class="rounded-circle" />
                                        </div>
                                        <div class="me-2">
                                            <h5 class="mb-0">
                                                <a href="javascript:;" class="stretched-link text-heading">Admin Template</a>
                                            </h5>
                                            <div class="client-info text-body">
                                                <span class="fw-medium">Client: </span><span>Jeffrey Phillips</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ms-auto">
                                        <div class="dropdown z-2">
                                            <button
                                                type="button"
                                                class="btn btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow p-0"
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="ti ti-dots-vertical ti-md text-muted"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="javascript:void(0);">Rename project</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);">View details</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);">Add to favorites</a></li>
                                                <li>
                                                    <hr class="dropdown-divider" />
                                                </li>
                                                <li><a class="dropdown-item text-danger" href="javascript:void(0);">Leave Project</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center flex-wrap">
                                    <div class="bg-lighter px-3 py-2 rounded me-auto mb-4">
                                        <p class="mb-1"><span class="fw-medium text-heading">$2.4k</span> <span>/ 1.8k</span></p>
                                        <span class="text-body">Total Budget</span>
                                    </div>
                                    <div class="text-start mb-4">
                                        <p class="mb-1">
                                            <span class="text-heading fw-medium">Start Date: </span> <span>18/8/21</span>
                                        </p>
                                        <p class="mb-1">
                                            <span class="text-heading fw-medium">Deadline: </span> <span>21/6/22</span>
                                        </p>
                                    </div>
                                </div>
                                <p class="mb-0">
                                    Time is our most valuable asset, that's why we want to help you save it by creating…
                                </p>
                            </div>
                            <div class="card-body border-top">
                                <div class="d-flex align-items-center mb-4">
                                    <p class="mb-1"><span class="text-heading fw-medium">All Hours: </span> <span>98/135</span></p>
                                    <span class="badge bg-label-warning ms-auto">15 Days left</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="text-body">Task: 12/90</small>
                                    <small class="text-body">42% Completed</small>
                                </div>
                                <div class="progress mb-4 rounded" style="height: 8px">
                                    <div
                                        class="progress-bar rounded"
                                        role="progressbar"
                                        style="width: 42%"
                                        aria-valuenow="42"
                                        aria-valuemin="0"
                                        aria-valuemax="100"></div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center">
                                        <ul class="list-unstyled d-flex align-items-center avatar-group mb-0 z-2">
                                            <li
                                                data-bs-toggle="tooltip"
                                                data-popup="tooltip-custom"
                                                data-bs-placement="top"
                                                title="Kaith D'souza"
                                                class="avatar avatar-sm pull-up">
                                                <img class="rounded-circle" src="../../assets/img/avatars/5.png" alt="Avatar" />
                                            </li>
                                            <li
                                                data-bs-toggle="tooltip"
                                                data-popup="tooltip-custom"
                                                data-bs-placement="top"
                                                title="John Doe"
                                                class="avatar avatar-sm pull-up">
                                                <img class="rounded-circle" src="../../assets/img/avatars/1.png" alt="Avatar" />
                                            </li>
                                            <li
                                                data-bs-toggle="tooltip"
                                                data-popup="tooltip-custom"
                                                data-bs-placement="top"
                                                title="Alan Walker"
                                                class="avatar avatar-sm pull-up me-3">
                                                <img class="rounded-circle" src="../../assets/img/avatars/6.png" alt="Avatar" />
                                            </li>
                                            <li><small class="text-muted">1.1k Members</small></li>
                                        </ul>
                                    </div>
                                    <div class="ms-auto">
                                        <a href="javascript:void(0);" class="text-muted d-flex align-items-center"
                                        ><i class="ti ti-message-dots ti-lg me-2"></i> 236</a
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="card">
                            <div class="card-header pb-4">
                                <div class="d-flex align-items-start">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-4">
                                            <img
                                                src="../../assets/img/icons/brands/vue-label.png"
                                                alt="Avatar"
                                                class="rounded-circle" />
                                        </div>
                                        <div class="me-2">
                                            <h5 class="mb-0">
                                                <a href="javascript:;" class="stretched-link text-heading">App Design</a>
                                            </h5>
                                            <div class="client-info text-body">
                                                <span class="fw-medium">Client: </span><span>Ricky McDonald</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ms-auto">
                                        <div class="dropdown z-2">
                                            <button
                                                type="button"
                                                class="btn btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow p-0"
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="ti ti-dots-vertical ti-md text-muted"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="javascript:void(0);">Rename project</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);">View details</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);">Add to favorites</a></li>
                                                <li>
                                                    <hr class="dropdown-divider" />
                                                </li>
                                                <li><a class="dropdown-item text-danger" href="javascript:void(0);">Leave Project</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center flex-wrap">
                                    <div class="bg-lighter px-3 py-2 rounded me-auto mb-4">
                                        <p class="mb-1"><span class="fw-medium text-heading">$980</span> <span>/ $420</span></p>
                                        <span class="text-body">Total Budget</span>
                                    </div>
                                    <div class="text-start mb-4">
                                        <p class="mb-1">
                                            <span class="text-heading fw-medium">Start Date: </span> <span>24/7/21</span>
                                        </p>
                                        <p class="mb-1">
                                            <span class="text-heading fw-medium">Deadline: </span> <span>8/10/21</span>
                                        </p>
                                    </div>
                                </div>
                                <p class="mb-0">App design combines the user interface (UI) and user experience (UX).</p>
                            </div>
                            <div class="card-body border-top">
                                <div class="d-flex align-items-center mb-4">
                                    <p class="mb-1"><span class="text-heading fw-medium">All Hours: </span> <span>880/421</span></p>
                                    <span class="badge bg-label-danger ms-auto">45 Days left</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="text-body">Task: 22/140</small>
                                    <small class="text-body">68% Completed</small>
                                </div>
                                <div class="progress mb-4 rounded" style="height: 8px">
                                    <div
                                        class="progress-bar rounded"
                                        role="progressbar"
                                        style="width: 68%"
                                        aria-valuenow="68"
                                        aria-valuemin="0"
                                        aria-valuemax="100"></div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center">
                                        <ul class="list-unstyled d-flex align-items-center avatar-group mb-0 z-2">
                                            <li
                                                data-bs-toggle="tooltip"
                                                data-popup="tooltip-custom"
                                                data-bs-placement="top"
                                                title="Jimmy Ressula"
                                                class="avatar avatar-sm pull-up">
                                                <img class="rounded-circle" src="../../assets/img/avatars/4.png" alt="Avatar" />
                                            </li>
                                            <li
                                                data-bs-toggle="tooltip"
                                                data-popup="tooltip-custom"
                                                data-bs-placement="top"
                                                title="Kristi Lawker"
                                                class="avatar avatar-sm pull-up">
                                                <img class="rounded-circle" src="../../assets/img/avatars/2.png" alt="Avatar" />
                                            </li>
                                            <li
                                                data-bs-toggle="tooltip"
                                                data-popup="tooltip-custom"
                                                data-bs-placement="top"
                                                title="Danny Paul"
                                                class="avatar avatar-sm pull-up me-3">
                                                <img class="rounded-circle" src="../../assets/img/avatars/7.png" alt="Avatar" />
                                            </li>
                                            <li><small class="text-muted">458 Members</small></li>
                                        </ul>
                                    </div>
                                    <div class="ms-auto">
                                        <a href="javascript:void(0);" class="text-muted d-flex align-items-center"
                                        ><i class="ti ti-message-dots ti-lg me-1"></i> 98</a
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="card">
                            <div class="card-header pb-4">
                                <div class="d-flex align-items-start">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-4">
                                            <img
                                                src="../../assets/img/icons/brands/html-label.png"
                                                alt="Avatar"
                                                class="rounded-circle" />
                                        </div>
                                        <div class="me-2">
                                            <h5 class="mb-0">
                                                <a href="javascript:;" class="stretched-link text-heading">Create Website</a>
                                            </h5>
                                            <div class="client-info text-body">
                                                <span class="fw-medium">Client: </span><span>Hulda Wright</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ms-auto">
                                        <div class="dropdown z-2">
                                            <button
                                                type="button"
                                                class="btn btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow p-0"
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="ti ti-dots-vertical ti-md text-muted"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="javascript:void(0);">Rename project</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);">View details</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);">Add to favorites</a></li>
                                                <li>
                                                    <hr class="dropdown-divider" />
                                                </li>
                                                <li><a class="dropdown-item text-danger" href="javascript:void(0);">Leave Project</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center flex-wrap">
                                    <div class="bg-lighter px-3 py-2 rounded me-auto mb-4">
                                        <p class="mb-1"><span class="fw-medium text-heading">$8.5k</span> <span>/ $2.43k</span></p>
                                        <span class="text-body">Total Budget</span>
                                    </div>
                                    <div class="text-start mb-4">
                                        <p class="mb-1">
                                            <span class="text-heading fw-medium">Start Date: </span> <span>10/2/19</span>
                                        </p>
                                        <p class="mb-1">
                                            <span class="text-heading fw-medium">Deadline: </span> <span>12/9/22</span>
                                        </p>
                                    </div>
                                </div>
                                <p class="mb-0">Your domain name should reflect your products or services so that your...</p>
                            </div>
                            <div class="card-body border-top">
                                <div class="d-flex align-items-center mb-4">
                                    <p class="mb-1">
                                        <span class="text-heading fw-medium">All Hours: </span> <span>1.2k/820</span>
                                    </p>
                                    <span class="badge bg-label-warning ms-auto">126 Days left</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="text-body">Task: 237/420</small>
                                    <small class="text-body">72% Completed</small>
                                </div>
                                <div class="progress mb-4 rounded" style="height: 8px">
                                    <div
                                        class="progress-bar rounded"
                                        role="progressbar"
                                        style="width: 72%"
                                        aria-valuenow="72"
                                        aria-valuemin="0"
                                        aria-valuemax="100"></div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center">
                                        <ul class="list-unstyled d-flex align-items-center avatar-group mb-0 z-2">
                                            <li
                                                data-bs-toggle="tooltip"
                                                data-popup="tooltip-custom"
                                                data-bs-placement="top"
                                                title="Andrew Tye"
                                                class="avatar avatar-sm pull-up">
                                                <img class="rounded-circle" src="../../assets/img/avatars/6.png" alt="Avatar" />
                                            </li>
                                            <li
                                                data-bs-toggle="tooltip"
                                                data-popup="tooltip-custom"
                                                data-bs-placement="top"
                                                title="Rishi Swaat"
                                                class="avatar avatar-sm pull-up">
                                                <img class="rounded-circle" src="../../assets/img/avatars/9.png" alt="Avatar" />
                                            </li>
                                            <li
                                                data-bs-toggle="tooltip"
                                                data-popup="tooltip-custom"
                                                data-bs-placement="top"
                                                title="Rossie Kim"
                                                class="avatar avatar-sm pull-up me-3">
                                                <img class="rounded-circle" src="../../assets/img/avatars/12.png" alt="Avatar" />
                                            </li>
                                            <li><small class="text-muted">137 Members</small></li>
                                        </ul>
                                    </div>
                                    <div class="ms-auto">
                                        <a href="javascript:void(0);" class="text-muted d-flex align-items-center"
                                        ><i class="ti ti-message-dots ti-lg me-1"></i> 120</a
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="card">
                            <div class="card-header pb-4">
                                <div class="d-flex align-items-start">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-4">
                                            <img
                                                src="../../assets/img/icons/brands/figma-label.png"
                                                alt="Avatar"
                                                class="rounded-circle" />
                                        </div>
                                        <div class="me-2">
                                            <h5 class="mb-0">
                                                <a href="javascript:;" class="stretched-link text-heading">Figma Dashboard</a>
                                            </h5>
                                            <div class="client-info text-body">
                                                <span class="fw-medium">Client: </span><span>Jerry Greene</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ms-auto">
                                        <div class="dropdown z-2">
                                            <button
                                                type="button"
                                                class="btn btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow p-0"
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="ti ti-dots-vertical ti-md text-muted"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="javascript:void(0);">Rename project</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);">View details</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);">Add to favorites</a></li>
                                                <li>
                                                    <hr class="dropdown-divider" />
                                                </li>
                                                <li><a class="dropdown-item text-danger" href="javascript:void(0);">Leave Project</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center flex-wrap">
                                    <div class="bg-lighter px-3 py-2 rounded me-auto mb-4">
                                        <p class="mb-1"><span class="fw-medium text-heading">$52.7k</span> <span>/ $28.4k</span></p>
                                        <span class="text-body">Total Budget</span>
                                    </div>
                                    <div class="text-start mb-4">
                                        <p class="mb-1">
                                            <span class="text-heading fw-medium">Start Date: </span> <span>12/12/20</span>
                                        </p>
                                        <p class="mb-1">
                                            <span class="text-heading fw-medium">Deadline: </span> <span>25/12/21</span>
                                        </p>
                                    </div>
                                </div>
                                <p class="mb-0">
                                    Use this template to organize your design project. Some of the key features are…
                                </p>
                            </div>
                            <div class="card-body border-top">
                                <div class="d-flex align-items-center mb-4">
                                    <p class="mb-1"><span class="text-heading fw-medium">All Hours: </span> <span>142/420</span></p>
                                    <span class="badge bg-label-danger ms-auto">5 Days left</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="text-body">Task: 29/285</small>
                                    <small class="text-body">35% Completed</small>
                                </div>
                                <div class="progress mb-4 rounded" style="height: 8px">
                                    <div
                                        class="progress-bar rounded"
                                        role="progressbar"
                                        style="width: 35%"
                                        aria-valuenow="35"
                                        aria-valuemin="0"
                                        aria-valuemax="100"></div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center">
                                        <ul class="list-unstyled d-flex align-items-center avatar-group mb-0 z-2">
                                            <li
                                                data-bs-toggle="tooltip"
                                                data-popup="tooltip-custom"
                                                data-bs-placement="top"
                                                title="Kim Merchent"
                                                class="avatar avatar-sm pull-up">
                                                <img class="rounded-circle" src="../../assets/img/avatars/10.png" alt="Avatar" />
                                            </li>
                                            <li
                                                data-bs-toggle="tooltip"
                                                data-popup="tooltip-custom"
                                                data-bs-placement="top"
                                                title="Sam D'souza"
                                                class="avatar avatar-sm pull-up">
                                                <img class="rounded-circle" src="../../assets/img/avatars/13.png" alt="Avatar" />
                                            </li>
                                            <li
                                                data-bs-toggle="tooltip"
                                                data-popup="tooltip-custom"
                                                data-bs-placement="top"
                                                title="Nurvi Karlos"
                                                class="avatar avatar-sm pull-up me-3">
                                                <img class="rounded-circle" src="../../assets/img/avatars/15.png" alt="Avatar" />
                                            </li>
                                            <li><small class="text-muted">82 Members</small></li>
                                        </ul>
                                    </div>
                                    <div class="ms-auto">
                                        <a href="javascript:void(0);" class="text-muted d-flex align-items-center"
                                        ><i class="ti ti-message-dots ti-lg me-1"></i> 20</a
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="card">
                            <div class="card-header pb-4">
                                <div class="d-flex align-items-start">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-4">
                                            <img src="../../assets/img/icons/brands/xd-label.png" alt="Avatar" class="rounded-circle" />
                                        </div>
                                        <div class="me-2">
                                            <h5 class="mb-0">
                                                <a href="javascript:;" class="stretched-link text-heading">Logo Design</a>
                                            </h5>
                                            <div class="client-info text-body">
                                                <span class="fw-medium">Client: </span><span>Olive Strickland</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ms-auto">
                                        <div class="dropdown z-2">
                                            <button
                                                type="button"
                                                class="btn btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow p-0"
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="ti ti-dots-vertical ti-md text-muted"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="javascript:void(0);">Rename project</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);">View details</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);">Add to favorites</a></li>
                                                <li>
                                                    <hr class="dropdown-divider" />
                                                </li>
                                                <li><a class="dropdown-item text-danger" href="javascript:void(0);">Leave Project</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center flex-wrap">
                                    <div class="bg-lighter px-3 py-2 rounded me-auto mb-4">
                                        <p class="mb-1"><span class="fw-medium text-heading">$1.3k</span> <span>/ $655</span></p>
                                        <span class="text-body">Total Budget</span>
                                    </div>
                                    <div class="text-start mb-4">
                                        <p class="mb-1">
                                            <span class="text-heading fw-medium">Start Date: </span> <span>17/8/21</span>
                                        </p>
                                        <p class="mb-1">
                                            <span class="text-heading fw-medium">Deadline: </span> <span>02/11/21</span>
                                        </p>
                                    </div>
                                </div>
                                <p class="mb-0">
                                    Premium logo designs created by top logo designers. Create the branding of business.
                                </p>
                            </div>
                            <div class="card-body border-top">
                                <div class="d-flex align-items-center mb-4">
                                    <p class="mb-1"><span class="text-heading fw-medium">All Hours: </span> <span>580/445</span></p>
                                    <span class="badge bg-label-success ms-auto">4 Days left</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="text-body">Task: 290/290</small>
                                    <small class="text-body">100% Completed</small>
                                </div>
                                <div class="progress mb-4 rounded" style="height: 8px">
                                    <div
                                        class="progress-bar rounded"
                                        role="progressbar"
                                        style="width: 100%"
                                        aria-valuenow="100"
                                        aria-valuemin="0"
                                        aria-valuemax="100"></div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center">
                                        <ul class="list-unstyled d-flex align-items-center avatar-group mb-0 z-2">
                                            <li
                                                data-bs-toggle="tooltip"
                                                data-popup="tooltip-custom"
                                                data-bs-placement="top"
                                                title="Kim Karlos"
                                                class="avatar avatar-sm pull-up">
                                                <img class="rounded-circle" src="../../assets/img/avatars/3.png" alt="Avatar" />
                                            </li>
                                            <li
                                                data-bs-toggle="tooltip"
                                                data-popup="tooltip-custom"
                                                data-bs-placement="top"
                                                title="Katy Turner"
                                                class="avatar avatar-sm pull-up">
                                                <img class="rounded-circle" src="../../assets/img/avatars/9.png" alt="Avatar" />
                                            </li>
                                            <li
                                                data-bs-toggle="tooltip"
                                                data-popup="tooltip-custom"
                                                data-bs-placement="top"
                                                title="Peter Adward"
                                                class="avatar avatar-sm pull-up me-3">
                                                <img class="rounded-circle" src="../../assets/img/avatars/15.png" alt="Avatar" />
                                            </li>
                                            <li><small class="text-muted">16 Members</small></li>
                                        </ul>
                                    </div>
                                    <div class="ms-auto">
                                        <a href="javascript:void(0);" class="text-muted d-flex align-items-center"
                                        ><i class="ti ti-message-dots ti-lg me-1"></i> 37</a
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
                <!-- / Content -->

                <!-- Footer -->
                <footer class="content-footer footer bg-footer-theme">
                    <div class="container-xxl">
                        <div
                            class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
                            <div class="text-body">
                                ©
                                <script>
                                  document.write(new Date().getFullYear());
                                </script>
                                , made with ❤️ by <a href="https://pixinvent.com" target="_blank" class="footer-link">Zeenara</a>
                            </div>
                            <div class="d-none d-lg-inline-block">
                                <a href="https://themeforest.net/licenses/standard" class="footer-link me-4" target="_blank"
                                >License</a
                                >
                                <a href="https://1.envato.market/pixinvent_portfolio" target="_blank" class="footer-link me-4"
                                >More Themes</a
                                >

                                <a
                                    href="https://demos.pixinvent.com/vuexy-html-admin-template/documentation/"
                                    target="_blank"
                                    class="footer-link me-4"
                                >Documentation</a
                                >

                                <a href="https://pixinvent.ticksy.com/" target="_blank" class="footer-link d-none d-sm-inline-block"
                                >Support</a
                                >
                            </div>
                        </div>
                    </div>
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
<!-- / Layout wrapper -->

<?php include_once __DIR__ . "/../../includes/dashboard/dashboard_footer_scripts.php"; ?>
</body>
</html>