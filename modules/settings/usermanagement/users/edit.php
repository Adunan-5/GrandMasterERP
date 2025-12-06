<?php
global $domain, $profilePicFolder;
$PAGE_ID = "USER_VIEW";
include_once __DIR__ . "/../../../../includes/baseIncludes.php";
include_once __DIR__ . "/../../../../includes/auth_check.php";

$userID = "";

$userID = filter_input(INPUT_GET, 'userID', FILTER_VALIDATE_INT);

if ($userID === null || $userID === false || filter_var($userID, FILTER_VALIDATE_INT) === false) {
    header("location:/users/list");
    exit();
}


?>
<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="../../assets/" data-template="vertical-menu-template" data-style="light">
<head>
    <?php include_once __DIR__ . "/../../../../includes/dashboard/dashboard_head_section.php"; ?>
    <title>GrandMaster ERP | <?=getDisplayNameFromUserID($userID)?></title>
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="/assets/vendor/libs/node-waves/node-waves.css"/>
    <link rel="stylesheet" href="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css"/>
    <link rel="stylesheet" href="/assets/vendor/libs/typeahead-js/typeahead.css"/>
    <link rel="stylesheet" href="/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css"/>
    <link rel="stylesheet" href="/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css"/>
    <link rel="stylesheet" href="/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css"/>
    <link rel="stylesheet" href="/assets/vendor/libs/select2/select2.css"/>
    <link rel="stylesheet" href="/assets/vendor/libs/flatpickr/flatpickr.css"/>
    <link rel="stylesheet" href="/assets/vendor/libs/@form-validation/form-validation.css"/>
    <link rel="stylesheet" href="/assets/vendor/libs/bootstrap-select/bootstrap-select.css"/>
    <link rel="stylesheet" href="/assets/vendor/libs/animate-css/animate.css"/>
    <link rel="stylesheet" href="/assets/vendor/libs/sweetalert2/sweetalert2.css"/>
    <!-- Page CSS -->
    <?php include_once __DIR__ . "/../../../../includes/dashboard/dashboard_head_section_post_vendor.php"; ?>
</head>
<body>
<!-- Layout wrapper -->
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <!-- Menu -->
        <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
            <?php include_once __DIR__ . "/../../../../includes/dashboard/menu_ceo.php" ?>
        </aside>
        <!-- / Menu -->
        <!-- Layout container -->
        <div class="layout-page">
            <!-- Navbar -->
            <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
                <?php include_once __DIR__ . "/../../../../includes/dashboard/top_navbar.php"; ?>
            </nav>
            <!-- / Navbar -->
            <!-- Content wrapper -->
            <div class="content-wrapper">
                <?php
                $user = new User($db);
                $user->loadById($userID);
                ?>
                <!-- Content -->
                <div class="container-xxl flex-grow-1 container-p-y">
                    <div class="row">
                        <!-- User Sidebar -->
                        <div class="col-xl-4 col-lg-5 order-1 order-md-0">
                            <!-- User Card -->
                            <div class="card mb-6">
                                <div class="card-body pt-12">
                                    <div class="user-avatar-section">
                                        <div class="d-flex align-items-center flex-column">
                                          <?php
                                            $domain =  PROFILE_PIC_FOLDER;
                                            $defaultImage = DEFAULT_PROFILE_IMAGE;
                                            ?>
                                            <img
                                                class="img-fluid rounded mb-4"
                                                src="<?= $domain . (!empty($user->profilePic) ? $user->profilePic : $defaultImage); ?>"
                                                height="120"
                                                width="120"
                                                alt="User avatar" />
                                            <div class="user-info text-center">
                                                <h5><?= $user->firstName . ' ' . $user->lastName ?></h5>
<!--                                                <span class="badge bg-label-secondary">--><?php //= $user->companyLabel ?><!--</span>-->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-around flex-wrap my-6 gap-0 gap-md-3 gap-lg-4">
                                        <div class="d-flex align-items-center me-5 gap-4">
                                            <div class="avatar">
                                                <div class="avatar-initial bg-label-primary rounded">
                                                    <i class="ti ti-checkbox ti-lg"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h5 class="mb-0">1.23k</h5>
                                                <span>Task Done</span>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-4">
                                            <div class="avatar">
                                                <div class="avatar-initial bg-label-primary rounded">
                                                    <i class="ti ti-briefcase ti-lg"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h5 class="mb-0">568</h5>
                                                <span>Project Done</span>
                                            </div>
                                        </div>
                                    </div>
                                    <h5 class="pb-4 border-bottom mb-4">Details of <?=$user->employeeId?></h5>
                                    <div class="info-container">
                                        <div class="mb-6">
                                            <!-- Fullname and Email -->
                                            <div class="row mb-2">
                                                <div class="col-md-6 mb-2 mb-md-0">
                                                    <span class="h6">Fullname:</span>
                                                    <span><?= $user->firstName . ' ' . $user->lastName ?></span>
                                                </div>
                                                <div class="col-md-6">
                                                    <span class="h6">Email:</span>
                                                    <span><?= $user->email ?></span>
                                                </div>
                                                </div>
                                                <!-- Role and Company -->
                                                <div class="row mb-2">
                                                <div class="col-md-6 mb-2 mb-md-0">
                                                    <span class="h6">Role:</span>
                                                    <span><?= $user->roleName ?></span>
                                                </div>
                                                <div class="col-md-6">
                                                    <span class="h6">Company:</span>
                                                    <span><?= implode(', ', $user->companyLabels) ?></span>
                                                </div>
                                                </div>
                                                <!-- IQAMA Number and Expiry -->
                                                <div class="row mb-2">
                                                <div class="col-md-6 mb-2 mb-md-0">
                                                    <span class="h6">IQAMA Number:</span>
                                                    <span><?= $user->iqamaNumber ?? 'N/A' ?></span>
                                                </div>
                                                <div class="col-md-6">
                                                    <span class="h6">IQAMA Expiry:</span>
                                                    <span><?= formatDate($user->iqamaExpiry, false) ?? '' ?></span>
                                                </div>
                                                </div>
                                                <!-- Passport Number and Expiry -->
                                                <div class="row mb-2">
                                                <div class="col-md-6 mb-2 mb-md-0">
                                                    <span class="h6">Passport Number:</span>
                                                    <span><?= $user->passportNumber ?? 'N/A' ?></span>
                                                </div>
                                                <div class="col-md-6">
                                                    <span class="h6">Passport Expiry:</span>
                                                    <span><?= formatDate($user->passportExpiry, false) ?? '' ?></span>
                                                </div>
                                                </div>
                                                <!-- Medical Insurance Number and Expiry -->
                                                <div class="row mb-2">
                                                <div class="col-md-6 mb-2 mb-md-0">
                                                    <span class="h6">Medical Insurance Number:</span>
                                                    <span><?= $user->medicalInsuranceNumber ?? 'N/A' ?></span>
                                                </div>
                                                <div class="col-md-6">
                                                    <span class="h6">Medical Insurance Expiry:</span>
                                                    <span><?= formatDate($user->medicalInsuranceExpiry, false) ?? '' ?></span>
                                                </div>
                                            </div>
                                            <!-- Status -->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <span class="h6">Nationality:</span>
                                                    <span>
                                                    <?= getCountryFromID($user->countryId) ?>
                                                    </span>
                                                </div>
                                                <div class="col-md-6">
                                                    <span class="h6">Status:</span>
                                                    <span class="badge <?= ($user->active == 1) ? 'bg-label-success' : 'bg-label-danger'; ?> text-capitalized">
                                                    <?= ($user->active == 1) ? 'Active' : 'Inactive'; ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                                            <!-- Edit Button -->
                                            <a href="javascript:;" 
                                            class="btn btn-primary" 
                                            data-bs-target="#editUserModal" 
                                            data-bs-toggle="modal">
                                            Edit
                                            </a>

                                            <!-- Training & Certifications Button -->
                                            <!-- <a href="javascript:;" 
                                            class="btn btn-info"
                                            data-bs-target="#uploadTrainingCertificationsModal" 
                                            data-bs-toggle="modal">
                                            Training & Certifications
                                            </a> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /User Card -->
                            <!-- Plan Card -->
                            <!-- <div class="card mb-6 border border-2 border-primary rounded primary-shadow">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <span class="badge bg-label-primary">Standard</span>
                                        <div class="d-flex justify-content-center">
                                            <sub class="h5 pricing-currency mb-auto mt-1 text-primary">$</sub>
                                            <h1 class="mb-0 text-primary">99</h1>
                                            <sub class="h6 pricing-duration mt-auto mb-3 fw-normal">month</sub>
                                        </div>
                                    </div>
                                    <ul class="list-unstyled g-2 my-6">
                                        <li class="mb-2 d-flex align-items-center">
                                            <i class="ti ti-circle-filled ti-10px text-secondary me-2"></i><span>10 Users</span>
                                        </li>
                                        <li class="mb-2 d-flex align-items-center">
                                            <i class="ti ti-circle-filled ti-10px text-secondary me-2"></i
                                            ><span>Up to 10 GB storage</span>
                                        </li>
                                        <li class="mb-2 d-flex align-items-center">
                                            <i class="ti ti-circle-filled ti-10px text-secondary me-2"></i><span>Basic Support</span>
                                        </li>
                                    </ul>
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="h6 mb-0">Days</span>
                                        <span class="h6 mb-0">26 of 30 Days</span>
                                    </div>
                                    <div class="progress mb-1 bg-label-primary" style="height: 6px">
                                        <div
                                            class="progress-bar"
                                            role="progressbar"
                                            style="width: 65%"
                                            aria-valuenow="65"
                                            aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                    <small>4 days remaining</small>
                                    <div class="d-grid w-100 mt-6">
                                        <button class="btn btn-primary" data-bs-target="#upgradePlanModal" data-bs-toggle="modal">
                                            Upgrade Plan
                                        </button>
                                    </div>
                                </div>
                            </div> -->
                            <!-- /Plan Card -->
                        </div>
                        <!--/ User Sidebar -->

                        <!-- User Content -->
                        <div class="col-xl-8 col-lg-7 order-0 order-md-1">
                            <!-- User Pills -->
                            <div class="nav-align-top">
                                <ul class="nav nav-pills flex-column flex-md-row flex-wrap mb-6 row-gap-2">
                                    <li class="nav-item">
                                        <a class="nav-link active" href="javascript:void(0);"
                                        ><i class="ti ti-user-check ti-sm me-1_5"></i>Account</a
                                        >
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="app-user-view-security.html"
                                        ><i class="ti ti-lock ti-sm me-1_5"></i>Security</a
                                        >
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="app-user-view-billing.html"
                                        ><i class="ti ti-bookmark ti-sm me-1_5"></i>Billing & Plans</a
                                        >
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="app-user-view-notifications.html"
                                        ><i class="ti ti-bell ti-sm me-1_5"></i>Notifications</a
                                        >
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="app-user-view-connections.html"
                                        ><i class="ti ti-link ti-sm me-1_5"></i>Connections</a
                                        >
                                    </li>
                                </ul>
                            </div>
                            <!--/ User Pills -->

                            <!-- Asset table -->
                            <div class="card mb-6">
                                <div class="d-flex justify-content-between align-items-center mb-3 mt-6 px-3">
                                    <h5 class="mb-2 mb-0">Assigned Assets</h5>
                                    <a href="javascript:;" 
                                    class="btn btn-info"
                                    data-bs-target="#assignAssetsModal" 
                                    data-bs-toggle="modal">
                                        Assign Assets
                                    </a>
                                </div>
                                <div class="card-datatable table-responsive">
                                    <table class="datatables-projects table border-top">
                                        <thead>
                                        <tr>
                                            <th></th>
                                            <th>#</th>
                                            <th>Asset name</th>
                                            <th>Assigned By</th>
                                            <th>Assigned At</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $counter = 0;
                                            $res     = $db->query("SELECT * FROM user_asset_mapping where userId = ?s", $userID);
                                            while ($row = mysqli_fetch_assoc($res)) {
                                            ?>
                                            <tr>
                                                <td> </td>
                                                <td> <?=++$counter?></td>
                                                <td> <?=getAssetNameByID($row['assetId'])?></td>
                                                <td> <?=getDisplayNameFromUserID($row['assigneeId'])?></td>
                                                <td> <?=formatDate($row['createdAt'], false)?></td>
                                            </tr>
                                            <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- /Asset table -->

                            <!-- Certificate table -->
                            <div class="card mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3 mt-6 px-3">
                                    <h5 class="mb-2 mb-0">Training & Certifications</h5>
                                    <a href="javascript:;" 
                                    class="btn btn-info"
                                    data-bs-target="#uploadTrainingCertificationsModal" 
                                    data-bs-toggle="modal">
                                        Add Training & Certifications
                                    </a>
                                </div>
                                <div class="card-datatable table-responsive">
                                    <table class="table datatable-invoice">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>#</th>
                                                <th>Certificate Name</th>
                                                <th>Certificate File</th>
                                                <th>Uploaded By</th>
                                                <th>Uploaded At</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $counter = 0;
                                            $res     = $db->query("SELECT * FROM user_certificate_mapping where userId = ?s", $userID);
                                            while ($row = mysqli_fetch_assoc($res)) {
                                            ?>
                                            <tr>
                                                <td> </td>
                                                <td> <?=++$counter?></td>
                                                <td> <?=$row['certificateName']?></td>
                                                <td> 
                                                    <?php if (!empty($row['certificateFile'])): ?>
                                                    <a href="/uploads/user-certifications/<?=$row['certificateFile'] ?>" target="_blank">
                                                        <?= htmlspecialchars($row['certificateFile']) ?>
                                                    </a>
                                                    <?php else: ?>
                                                    <span class="text-muted">No file</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td> <?=getDisplayNameFromUserID($row['uploadedBy'])?></td>
                                                <td> <?=formatDate($row['createdAt'], false)?></td>
                                            </tr>
                                            <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- /Certificate table -->

                            <!-- Activity Timeline -->
                            <!-- <div class="card mb-6">
                                <h5 class="card-header">User Activity Timeline</h5>
                                <div class="card-body pt-1">
                                    <ul class="timeline mb-0">
                                        <li class="timeline-item timeline-item-transparent">
                                            <span class="timeline-point timeline-point-primary"></span>
                                            <div class="timeline-event">
                                                <div class="timeline-header mb-3">
                                                    <h6 class="mb-0">12 Invoices have been paid</h6>
                                                    <small class="text-muted">12 min ago</small>
                                                </div>
                                                <p class="mb-2">Invoices have been paid to the company</p>
                                                <div class="d-flex align-items-center mb-2">
                                                    <div class="badge bg-lighter rounded d-flex align-items-center">
                                                        <img src="../../assets//img/icons/misc/pdf.png" alt="img" width="15" class="me-2" />
                                                        <span class="h6 mb-0 text-body">invoices.pdf</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="timeline-item timeline-item-transparent">
                                            <span class="timeline-point timeline-point-success"></span>
                                            <div class="timeline-event">
                                                <div class="timeline-header mb-3">
                                                    <h6 class="mb-0">Client Meeting</h6>
                                                    <small class="text-muted">45 min ago</small>
                                                </div>
                                                <p class="mb-2">Project meeting with john @10:15am</p>
                                                <div class="d-flex justify-content-between flex-wrap gap-2 mb-2">
                                                    <div class="d-flex flex-wrap align-items-center mb-50">
                                                        <div class="avatar avatar-sm me-2">
                                                            <img src="../../assets/img/avatars/1.png" alt="Avatar" class="rounded-circle" />
                                                        </div>
                                                        <div>
                                                            <p class="mb-0 small fw-medium">Lester McCarthy (Client)</p>
                                                            <small>CEO of Zeenara</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="timeline-item timeline-item-transparent">
                                            <span class="timeline-point timeline-point-info"></span>
                                            <div class="timeline-event">
                                                <div class="timeline-header mb-3">
                                                    <h6 class="mb-0">Create a new project for client</h6>
                                                    <small class="text-muted">2 Day Ago</small>
                                                </div>
                                                <p class="mb-2">6 team members in a project</p>
                                                <ul class="list-group list-group-flush">
                                                    <li
                                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap border-top-0 p-0">
                                                        <div class="d-flex flex-wrap align-items-center">
                                                            <ul class="list-unstyled users-list d-flex align-items-center avatar-group m-0 me-2">
                                                                <li
                                                                    data-bs-toggle="tooltip"
                                                                    data-popup="tooltip-custom"
                                                                    data-bs-placement="top"
                                                                    title="Vinnie Mostowy"
                                                                    class="avatar pull-up">
                                                                    <img class="rounded-circle" src="../../assets/img/avatars/5.png" alt="Avatar" />
                                                                </li>
                                                                <li
                                                                    data-bs-toggle="tooltip"
                                                                    data-popup="tooltip-custom"
                                                                    data-bs-placement="top"
                                                                    title="Allen Rieske"
                                                                    class="avatar pull-up">
                                                                    <img class="rounded-circle" src="../../assets/img/avatars/12.png" alt="Avatar" />
                                                                </li>
                                                                <li
                                                                    data-bs-toggle="tooltip"
                                                                    data-popup="tooltip-custom"
                                                                    data-bs-placement="top"
                                                                    title="Julee Rossignol"
                                                                    class="avatar pull-up">
                                                                    <img class="rounded-circle" src="../../assets/img/avatars/6.png" alt="Avatar" />
                                                                </li>
                                                                <li class="avatar">
                                      <span
                                          class="avatar-initial rounded-circle pull-up text-heading"
                                          data-bs-toggle="tooltip"
                                          data-bs-placement="bottom"
                                          title="3 more"
                                      >+3</span
                                      >
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div> -->
                            <!-- /Activity Timeline -->
                        </div>
                        <!--/ User Content -->
                    </div>

                    <!-- Modal -->
                    <!-- Edit User Modal -->
                    <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-simple modal-edit-user">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    <div class="text-center mb-6">
                                        <h4 class="mb-2">Modify User Information</h4>
                                        <!--                                        <p>Updating user details will receive a privacy audit.</p>-->
                                    </div>
                                    <form class="ecommerce-user-add pt-0" id="userEditForm" method="POST" name="userEditForm" enctype="multipart/form-data" action="#" onsubmit="return false;">
                                        <div class="ecommerce-user-add-basic mb-4">
                                            <div class="row">
                                                <div class="mb-6  col-6">
                                                    <label class="form-label" for="modalEditUserFirstName">First Name</label>
                                                    <input type="text" id="modalEditUserFirstName"name="modalEditUserFirstName"class="form-control"placeholder="John"value="<?= $user-> firstName ?>" />
                                                </div>
                                                <!--HIDDEN FIELDS-->
                                                <input type="hidden" id="userId" name="userId" value="<?= $user->userID ?>"/>
                                                <!--HIDDEN FIELDS END-->
                                                <div class="mb-6 col-6">
                                                    <label class="form-label" for="modalEditUserLastName">Last Name</label>
                                                    <input type="text" id="modalEditUserLastName" name="modalEditUserLastName" class="form-control" placeholder="Doe" value="<?= $user-> lastName ?>" />
                                                </div>
                                                <div class="mb-6 col-6">
                                                    <label class="form-label" for="modalEditUserName">Username</label>
                                                    <input type="text" id="modalEditUserName" name="modalEditUserName" class="form-control" placeholder="johndoe007" value="<?= $user-> userName ?>" />
                                                </div>
                                                <div class="mb-6 col-6">
                                                    <label class="form-label" for="modalEditUserEmail">Email</label>
                                                    <input type="text" id="modalEditUserEmail" name="modalEditUserEmail" class="form-control" placeholder="example@domain.com" value="<?= $user-> email ?>" />
                                                </div>
                                                <div class="mb-6 col-6">
                                                  <label class="form-label" for="userImage">Image</label>
                                                  <input type="file" id="userImage" class="form-control" name="userImage" accept="image/*" />
                                                  <!-- Display existing image URL as a clickable link if available, and not the default placeholder image -->
                                                    <?php if (!empty($user->profilePic) && $user->profilePic !== DEFAULT_PROFILE_IMAGE) : ?>
                                                      <div class="mb-2">
                                                        <a href="/uploads/userprofile-images/<?= htmlspecialchars($user->profilePic) ?>" target="_blank">
                                                          View User Image
                                                        </a>
                                                      </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="mb-6 col-6">
                                                  <label class="form-label" for="modalEditUserCountry">Nationality</label>
                                                  <select id="modalEditUserCountry" name="modalEditUserCountry" class="selectpicker form-select w-100" data-style="btn-default" data-live-search="true" data-saudi-id="194">
                                                      <option value="">Select a Country</option>
                                                      <?php
                                                      $res = $db->query("SELECT * FROM countries");
                                                      while ($row = mysqli_fetch_assoc($res)) {
                                                          ?>
                                                          <option value="<?= $row['id'] ?>" <?php if ($row['id'] == $user->countryId) echo "selected" ?> ><?= $row['name'] ?></option>
                                                          <?php
                                                      }
                                                      ?>
                                                  </select>
                                                </div>
                                                <div class="mb-6 col-6">
                                                    <label class="form-label" for="modalEditUserRole">Role</label>
                                                    <select id="modalEditUserRole" name="modalEditUserRole" class="selectpicker form-select w-100" data-style="btn-default" data-live-search="true">
                                                        <option value="">Select a Role</option>
                                                        <?php
                                                        $companyId = intval($_SESSION['SES_SELECTED_COMPANY']);
                                                        $res = $db->query("SELECT * FROM user_roles");
                                                        // $res = $db->query("SELECT * FROM user_roles WHERE companyId = ?s", $companyId);
                                                        while ($row = mysqli_fetch_assoc($res)) {
                                                            ?>
                                                            <option value="<?= $row['roleId'] ?>" <?php if ($row['roleId'] == $user->roleId) echo "selected" ?> ><?= $row['roleName'] ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
<!--                                              <div class="mb-6 col-6">-->
<!--                                                <label class="form-label" for="modalEditUserCompany">Company</label>-->
<!--                                                <select id="modalEditUserCompany" name="modalEditUserCompany" class="selectpicker form-select w-100" data-style="btn-default" data-live-search="true">-->
<!--                                                  <option value="">Select a Company</option>-->
<!--                                                    --><?php
//                                                    $res = $db->query("SELECT * FROM companies");
//                                                    while ($row = mysqli_fetch_assoc($res)) {
//                                                        ?>
<!--                                                      <option value="--><?php //= $row['companyId'] ?><!--" --><?php //if ($row['companyId'] == $user->companyId) echo "selected" ?><!-- >--><?php //= $row['companyLabel'] ?><!--</option>-->
<!--                                                        --><?php
//                                                    }
//                                                    ?>
<!--                                                </select>-->
<!--                                              </div>-->
                                              <div class="mb-6 col-6">
                                                <label class="form-label" for="modalEditUserCompany">Company</label>
                                                <div class="form-check">
                                                    <?php
                                                    // Fetch all companies
                                                    $res = $db->query("SELECT * FROM companies");
                                                    while ($row = mysqli_fetch_assoc($res)) {
                                                        // Check if the user is already associated with this company
                                                        $checked = '';
                                                        $userCompanyRes = $db->query("SELECT * FROM user_company_mapping WHERE userId = {$user->userID} AND companyId = {$row['companyId']}");
                                                        if (mysqli_num_rows($userCompanyRes) > 0) {
                                                            $checked = 'checked';
                                                        }
                                                        ?>
                                                      <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" value="<?= $row['companyId'] ?>" id="company_<?= $row['companyId'] ?>" name="userCompanies[]" <?= $checked ?>>
                                                        <label class="form-check-label" for="company_<?= $row['companyId'] ?>">
                                                            <?= $row['companyLabel'] ?>
                                                        </label>
                                                      </div>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                              </div>

                                              <div class="mb-6  col-6" id="idNumberContainer">
                                                <?php
                                                $label = 'IQAMA Number';
                                                $selectedCountry = $user->countryId;
                                                if($selectedCountry == 194) $label = 'National ID Number';
                                                ?>
                                                <label class="form-label" for="modalEditUserIdNumber"><?=$label?></label>
                                                <input type="text" id="modalEditUserIdNumber"name="modalEditUserIdNumber"class="form-control"placeholder="Enter IQAMA Number"value="<?= $user-> iqamaNumber ?>" />
                                              </div>
                                              <div class="mb-6  col-6" id="idExpiryContainer">
                                                <?php
                                                $label = 'IQAMA Expiry';
                                                $selectedCountry = $user->countryId;
                                                if($selectedCountry == 194) $label = 'National ID Expiry';
                                                ?>
                                                <label class="form-label" for="modalEditUserIdExpiry"><?=$label?></label>
                                                <input type="text" id="modalEditUserIdExpiry"name="modalEditUserIdExpiry"class="form-control gmm-date-format"placeholder="Enter IQAMA Expiry"value="<?= $user-> iqamaExpiry ?>" />
                                              </div>
                                              <div class="mb-6  col-6" id="passportNumberContainer">
                                                <label class="form-label" for="modalEditUserPassportNumber">Passport Number</label>
                                                <input type="text" id="modalEditUserPassportNumber"name="modalEditUserPassportNumber"class="form-control"placeholder="Enter Passport Number"value="<?= $user-> passportNumber ?>" />
                                              </div>
                                              <div class="mb-6  col-6" id="passportExpiryContainer">
                                                <label class="form-label" for="modalEditUserPassportExpiry">Passport Expiry</label>
                                                <input type="text" id="modalEditUserPassportExpiry"name="modalEditUserPassportExpiry"class="form-control gmm-date-format"placeholder="Enter Passport Expiry"value="<?= $user-> passportExpiry ?>" />
                                              </div>
                                              <div class="mb-6  col-6">
                                                <label class="form-label" for="modalEditUserMedicalInsuranceNumber">Medical Insurance Number</label>
                                                <input type="text" id="modalEditUserMedicalInsuranceNumber"name="modalEditUserMedicalInsuranceNumber"class="form-control"placeholder="Enter MedicalInsurance Number"value="<?= $user-> medicalInsuranceNumber ?>" />
                                              </div>
                                              <div class="mb-6  col-6">
                                                <label class="form-label" for="modalEditUserMedicalInsuranceExpiry">Medical Insurance Expiry</label>
                                                <input type="text" id="modalEditUserMedicalInsuranceExpiry"name="modalEditUserMedicalInsuranceExpiry"class="form-control gmm-date-format"placeholder="Enter MedicalInsurance Expiry"value="<?= $user-> medicalInsuranceExpiry ?>" />
                                              </div>
                                              <div class="mb-6  col-6">
                                                <label class="form-label" for="modalEditUserMedicalInsuranceCompany">Medical Insurance Company</label>
                                                <input type="text" id="modalEditUserMedicalInsuranceCompany"name="modalEditUserMedicalInsuranceCompany"class="form-control"placeholder="Enter MedicalInsurance Company"value="<?= $user-> medicalInsuranceCompany ?>" />
                                              </div>
                                              <!-- <div class="mb-6 col-6">
                                                    <label class="form-label" for="modalEditUserAssets">Company Assets</label>
                                                    <select id="modalEditUserAssets" 
                                                            name="userAssets[]" 
                                                            class="selectpicker form-select w-100" 
                                                            data-style="btn-default" 
                                                            data-live-search="true" 
                                                            multiple>
                                                        <?php
                                                        // Fetch all assets
                                                        $res = $db->query("SELECT * FROM company_assets");
                                                        while ($row = mysqli_fetch_assoc($res)) {
                                                            // Check if user already has this asset
                                                            $selected = '';
                                                            $userAssetRes = $db->query("SELECT * FROM user_asset_mapping 
                                                                                        WHERE userId = {$user->userID} 
                                                                                        AND assetId = {$row['assetId']}");
                                                            if (mysqli_num_rows($userAssetRes) > 0) {
                                                                $selected = 'selected';
                                                            }
                                                            ?>
                                                            <option value="<?= $row['assetId'] ?>" <?= $selected ?>>
                                                                <?= htmlspecialchars($row['assetName']) ?>
                                                            </option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div> -->

                                              <div class="text-center mb-3 mt-6">
                                                <h6 class="mb-2">Payroll related Information</h4>
                                              </div>

                                              <div class="mb-6 col-6">
                                                <label class="form-label" for="userContract">Contract File</label>
                                                <input type="file" id="userContract" class="form-control" name="userContract" accept=".jpg,.jpeg,.png,.pdf" />
                                                <!-- Display existing contract file if available -->
                                                <?php if (!empty($user->userContract)) : ?>
                                                    <div class="mt-2">
                                                    <a href="/uploads/user-contracts/<?= htmlspecialchars($user->userContract) ?>" target="_blank">
                                                        View Contract
                                                    </a>
                                                    </div>
                                                <?php endif; ?>
                                              </div>
                                              <div class="mb-6  col-6">
                                                <label class="form-label" for="modalEditUserContractStartDate">Contract Start Date</label>
                                                <input type="text" id="modalEditUserContractStartDate"name="modalEditUserContractStartDate"class="form-control gmm-date-format"placeholder="Enter Contract Start Date"value="<?= $user-> contractStartDate ?>" />
                                              </div>
                                              <div class="mb-6  col-6">
                                                <label class="form-label" for="modalEditUserContractEndDate">Contract End Date</label>
                                                <input type="text" id="modalEditUserContractEndDate"name="modalEditUserContractEndDate"class="form-control gmm-date-format"placeholder="Enter Contract End Date"value="<?= $user-> contractEndDate ?>" />
                                              </div>
                                              <div class="mb-6 col-6">
                                                <label class="form-label" for="modalEditUserWorkDaysPerMonth">Workdays per month</label>
                                                <input type="text" id="modalEditUserWorkDaysPerMonth" name="modalEditUserWorkDaysPerMonth" class="form-control" placeholder="30" value="<?= $user-> workDaysPerMonth ?>" />
                                              </div>
                                              <div class="mb-6 col-6">
                                                <label class="form-label" for="modalEditUserHoursPerDay">Hours per day</label>
                                                <input type="text" id="modalEditUserHoursPerDay" name="modalEditUserHoursPerDay" class="form-control" placeholder="8" value="<?= $user-> hoursPerDay ?>" />
                                              </div>
                                              <div class="mb-6 col-6">
                                                <label class="form-label" for="modalEditUserBasicSalary">Basic Salary</label>
                                                <input type="text" id="modalEditUserBasicSalary" name="modalEditUserBasicSalary" class="form-control" placeholder="5000" value="<?= $user-> basicSalary ?>" />
                                              </div>
                                              <div class="mb-6 col-6">
                                                <label class="form-label" for="modalEditUserOTMultiplier">OT Multiplier</label>
                                                <input type="text" id="modalEditUserOTMultiplier" name="modalEditUserOTMultiplier" class="form-control" placeholder="1.5" value="<?= $user-> otMultiplier ?>" />
                                              </div>
                                              <div class="mb-6 col-6">
                                                <label class="form-label" for="modalEditUserGOSICap">GOSI cap</label>
                                                <input type="text" id="modalEditUserGOSICap" name="modalEditUserGOSICap" class="form-control" placeholder="45000" value="<?= $user-> gosiCap ?>" />
                                              </div>
                                              <div class="mb-6 col-6" id="employerGOSIContainer">
                                                <label class="form-label" for="modalEditUserEmployerGOSI">Employer GOSI %</label>
                                                <input type="text" id="modalEditUserEmployerGOSI" name="modalEditUserEmployerGOSI" class="form-control" placeholder="12" value="<?= $user-> employerGOSI ?>" />
                                              </div>
                                              <div class="mb-6 col-6" id="employeeGOSIContainer">
                                                <label class="form-label" for="modalEditUserEmployeeGOSI">Non-Saudi Employee GOSI %</label>
                                                <input type="text" id="modalEditUserEmployeeGOSI" name="modalEditUserEmployeeGOSI" class="form-control" placeholder="10" value="<?= $user-> employeeGOSI ?>" />
                                              </div>
                                              <div class=" mb-6 col-6">
                                                    <label class="form-label" for="modalEditUserStatus">Status</label>
                                                    <select id="modalEditUserStatus" name="modalEditUserStatus" class="selectpicker form-select w-100" data-style="btn-default" data-live-search="true">
                                                        <option data-tokens="active" value="1" <?= ($user->active == 1) ? 'selected' : '' ?>>Active</option>
                                                        <option data-tokens="inactive" value="0" <?= ($user->active == 0) ? 'selected' : '' ?>>Inactive</option>
                                                    </select>
                                                </div>

                                            </div>
                                        </div>
                                        <div>
                                            <button class="btn btn-primary me-sm-4 data-submit">Update</button>
                                            <button type="reset" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal" aria-label="Close">
                                                Discard
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--/ Edit User Modal -->
                    <!-- Upload Training Certifications Modal -->
                    <div class="modal fade" id="uploadTrainingCertificationsModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-simple modal-edit-user">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    <div class="text-center mb-6">
                                        <h4 class="mb-2">Upload Training Certifications of the User</h4>
                                        <!--                                        <p>Updating user details will receive a privacy audit.</p>-->
                                    </div>
                                    <form class="ecommerce-user-add pt-0" id="userCertificationForm" method="POST" name="userCertificationForm" enctype="multipart/form-data" action="#" onsubmit="return false;">
                                        <div class="ecommerce-user-add-basic mb-4">
                                            <div class="row">
                                                <div class="mb-6 col-6">
                                                <label class="form-label" for="userCertificateName">Training Certification Name</label>
                                                <input type="text" id="userCertificateName" class="form-control" name="userCertificateName" accept=".jpg,.jpeg,.png,.pdf" />
                                              </div>
                                                <div class="mb-6 col-6">
                                                <label class="form-label" for="userCertificationFile">Contract File</label>
                                                <input type="file" id="userCertificationFile" class="form-control" name="userCertificationFile" accept=".jpg,.jpeg,.png,.pdf" />
                                              </div>
                                                <!--HIDDEN FIELDS-->
                                                <input type="hidden" id="userId" name="userId" value="<?= $user->userID ?>"/>
                                                <!--HIDDEN FIELDS END-->

                                            </div>
                                        </div>
                                        <div>
                                            <button class="btn btn-primary me-sm-4 data-submit">Update</button>
                                            <button type="reset" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal" aria-label="Close">
                                                Discard
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--/ Upload Training Certifications Modal -->

                    <!-- Assign Assets Modal -->
                    <div class="modal fade" id="assignAssetsModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-simple modal-edit-user">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    <div class="text-center mb-6">
                                        <h4 class="mb-2">Assign Assets to the User</h4>
                                    </div>
                                    <form class="ecommerce-user-add pt-0" id="assignAssetsForm" method="POST" name="assignAssetsForm" enctype="multipart/form-data" action="#" onsubmit="return false;">
                                        <div class="ecommerce-user-add-basic mb-4">
                                            <div class="row justify-content-center">
                                                <div class="mb-6 col-8">
                                                    <label class="form-label" for="modalEditUserAssets">Company Assets</label>
                                                    <select id="modalEditUserAssets" 
                                                            name="userAssets[]" 
                                                            class="selectpicker form-select w-100" 
                                                            data-style="btn-default" 
                                                            data-live-search="true" 
                                                            multiple>
                                                        <?php
                                                        // Fetch all assets
                                                        $res = $db->query("SELECT * FROM company_assets");
                                                        while ($row = mysqli_fetch_assoc($res)) {
                                                            // Check if user already has this asset
                                                            $selected = '';
                                                            $userAssetRes = $db->query("SELECT * FROM user_asset_mapping 
                                                                                        WHERE userId = {$user->userID} 
                                                                                        AND assetId = {$row['assetId']}");
                                                            if (mysqli_num_rows($userAssetRes) > 0) {
                                                                $selected = 'selected';
                                                            }
                                                            ?>
                                                            <option value="<?= $row['assetId'] ?>" <?= $selected ?>>
                                                                <?= htmlspecialchars($row['assetName']) ?>
                                                            </option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <!--HIDDEN FIELDS-->
                                                <input type="hidden" id="userId" name="userId" value="<?= $user->userID ?>"/>
                                                <!--HIDDEN FIELDS END-->
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <button class="btn btn-primary me-sm-4 data-submit">Update</button>
                                            <button type="reset" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal" aria-label="Close">
                                                Discard
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--/ Assign Assets Modal -->

                    <!-- Add New Credit Card Modal -->
                    <div class="modal fade" id="upgradePlanModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-simple modal-upgrade-plan">
                            <div class="modal-content">
                                <div class="modal-body p-4">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    <div class="text-center mb-6">
                                        <h4 class="mb-2">Upgrade Plan</h4>
                                        <p>Choose the best plan for user.</p>
                                    </div>
                                    <form id="upgradePlanForm" class="row g-4" onsubmit="return false">
                                        <div class="col-sm-9">
                                            <label class="form-label" for="choosePlan">Choose Plan</label>
                                            <select id="choosePlan" name="choosePlan" class="form-select" aria-label="Choose Plan">
                                                <option selected>Choose Plan</option>
                                                <option value="standard">Standard - $99/month</option>
                                                <option value="exclusive">Exclusive - $249/month</option>
                                                <option value="Enterprise">Enterprise - $499/month</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-3 d-flex align-items-end">
                                            <button type="submit" class="btn btn-primary">Upgrade</button>
                                        </div>
                                    </form>
                                </div>
                                <hr class="mx-4 my-2" />
                                <div class="modal-body p-4">
                                    <p class="mb-0">User current plan is standard plan</p>
                                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                                        <div class="d-flex justify-content-center me-2 mt-1">
                                            <sup class="h6 pricing-currency pt-1 mt-2 mb-0 me-1 text-primary">$</sup>
                                            <h1 class="mb-0 text-primary">99</h1>
                                            <sub class="pricing-duration mt-auto mb-5 pb-1 small text-body">/month</sub>
                                        </div>
                                        <button class="btn btn-label-danger cancel-subscription">Cancel Subscription</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--/ Add New Credit Card Modal -->

                    <!-- /Modal -->
                </div>
                <!-- / Content -->
                <!-- Footer -->
                <footer class="content-footer footer bg-footer-theme">
                    <?php include_once __DIR__ . "/../../../../includes/dashboard/dashboard_footer_section.php"; ?>
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
<?php include_once __DIR__ . "/../../../../includes/dashboard/dashboard_footer_scripts.php"; ?>
<script>
  $(document).ready(function () {

    // Handle edit form nationality change
    // const editCountrySelect = document.getElementById('modalEditUserCountry');
    // if (editCountrySelect) {
    //     editCountrySelect.addEventListener('change', function () {
    //         const saudiId = editCountrySelect.getAttribute('data-saudi-id');
    //         const isSaudi = editCountrySelect.value === saudiId;

    //         // Update ID fields
    //         const idNumberLabel = document.querySelector('#idNumberContainer .form-label');
    //         const idExpiryLabel = document.querySelector('#idExpiryContainer .form-label');
    //         const idNumberInput = document.getElementById('modalEditUserIdNumber');
    //         const idExpiryInput = document.getElementById('modalEditUserIdExpiry');

    //         // Clear ID fields
    //         idNumberInput.value = '';
    //         idExpiryInput.value = '';

    //         if (isSaudi) {
    //             idNumberLabel.textContent = 'National ID Number';
    //             idNumberInput.setAttribute('placeholder', 'Enter National ID Number');
    //             idExpiryLabel.textContent = 'National ID Expiry';
    //             idExpiryInput.setAttribute('placeholder', 'Enter National ID Expiry');
    //         } else {
    //             idNumberLabel.textContent = 'IQAMA Number';
    //             idNumberInput.setAttribute('placeholder', 'Enter IQAMA Number');
    //             idExpiryLabel.textContent = 'IQAMA Expiry';
    //             idExpiryInput.setAttribute('placeholder', 'Enter IQAMA Expiry');
    //         }

    //         // Reinitialize datepicker if present
    //         if (idExpiryInput.classList.contains('gmm-date-format')) {
    //             if (typeof flatpickr !== 'undefined' && idExpiryInput._flatpickr) {
    //                 idExpiryInput._flatpickr.destroy();
    //                 flatpickr(idExpiryInput, {
    //                     dateFormat: 'Y-m-d',
    //                     placeholder: isSaudi ? 'Enter National ID Expiry' : 'Enter IQAMA Expiry',
    //                     altInput: true,
    //                     altFormat: 'd - M - Y'
    //                 });
    //             }
    //         }

    //         // Toggle passport fields visibility
    //         const passportNumberContainer = document.getElementById('passportNumberContainer');
    //         const passportExpiryContainer = document.getElementById('passportExpiryContainer');
    //         const passportNumberInput = document.getElementById('modalEditUserPassportNumber');
    //         const passportExpiryInput = document.getElementById('modalEditUserPassportExpiry');
    //         passportNumberInput.value = '';
    //         passportExpiryInput.value = '';
    //         if (isSaudi) {
    //             passportNumberContainer.style.display = 'none';
    //             passportExpiryContainer.style.display = 'none';
    //         } else {
    //             passportNumberContainer.style.display = 'block';
    //             passportExpiryContainer.style.display = 'block';
    //         }

    //         // Update GOSI fields
    //         const employeeGOSILabel = document.querySelector('#employeeGOSIContainer .form-label');
    //         const employerGOSIInput = document.getElementById('modalEditUserEmployerGOSI');
    //         const employeeGOSIInput = document.getElementById('modalEditUserEmployeeGOSI');
    //         employerGOSIInput.value = '';
    //         employeeGOSIInput.value = '';
    //         if (isSaudi) {
    //             employeeGOSILabel.textContent = 'Saudi Employee GOSI %';
    //             document.getElementById('modalEditUserEmployeeGOSI').setAttribute('placeholder', '10.00');
    //         } else {
    //             employeeGOSILabel.textContent = 'Non-Saudi Employee GOSI %';
    //             document.getElementById('modalEditUserEmployeeGOSI').setAttribute('placeholder', '10.00');
    //         }
    //     });

    //     // Trigger change event on page load to set initial state
    //     editCountrySelect.dispatchEvent(new Event('change'));
    // }

    // Edit user Form Validation
    const userEditForm = document.getElementById('userEditForm');
    const fv = FormValidation.formValidation(userEditForm, {
      fields: {
        modalEditUserName: {
          validators: {
            notEmpty: {
              message: 'Please enter Username'
            }
          }
        },
        modalEditUserEmail: {
          validators: {
            notEmpty: {
              message: 'Please enter Email'
            }
          }
        },
        modalEditUserStatus: {
          validators: {
            notEmpty: {
              message: 'Please choose Status'
            }
          }
        },
        modalEditUserRole: {
          validators: {
            notEmpty: {
              message: 'Please choose Role'
            }
          }
        },
        'userCompanies[]': {  // Changed from companyId to userCompanies[]
            validators: {
                choice: {
                    min: 1,
                    message: 'Please choose at least one company'
                }
            }
        },
        modalEditUserCountry: {
            validators: {
                notEmpty: {
                    message: 'Please select a nationality'
                }
            }
        },
        modalEditUserBasicSalary: {
          validators: {
            notEmpty: {
              message: 'Please enter the Basic Salary'
            }
          }
        },
        modalEditUserEmployerGOSI: {
          validators: {
            notEmpty: {
              message: 'Please enter the Employer GOSI %'
            }
          }
        },
        modalEditUserEmployeeGOSI: {
          validators: {
            notEmpty: {
              message: 'Please enter the Employee GOSI %'
            }
          }
        },
        modalEditUserIdNumber: {
            validators: {
                notEmpty: {
                    message: 'Please enter the National ID or IQAMA Number'
                }
            }
        },
        modalEditUserIdExpiry: {
            validators: {
                notEmpty: {
                    message: 'Please enter the National ID or IQAMA Expiry'
                },
                date: {
                    format: 'YYYY-MM-DD',
                    message: 'Please enter a valid date format (YYYY-MM-DD)'
                }
            }
        },
        modalEditUserPassportNumber: {
            validators: {
                callback: {
                    message: 'Please enter the Passport Number',
                    callback: function(input) {
                        const countrySelect = document.getElementById('modalEditUserCountry');
                        const saudiId = countrySelect.getAttribute('data-saudi-id');
                        const isSaudi = countrySelect.value === saudiId;
                        return isSaudi ? true : !!input.value;
                    }
                }
            }
        },
        modalEditUserPassportExpiry: {
            validators: {
                callback: {
                    message: 'Please enter the Passport Expiry',
                    callback: function(input) {
                        const countrySelect = document.getElementById('modalEditUserCountry');
                        const saudiId = countrySelect.getAttribute('data-saudi-id');
                        const isSaudi = countrySelect.value === saudiId;
                        return isSaudi ? true : !!input.value;
                    }
                },
                date: {
                    format: 'YYYY-MM-DD',
                    message: 'Please enter a valid date format (YYYY-MM-DD)'
                }
            }
        },
        modalEditUserContractStartDate: {
            validators: {
                notEmpty: {
                    message: 'Please enter the Contract Start Date'
                },
                date: {
                    format: 'YYYY-MM-DD',
                    message: 'Please enter a valid date format (YYYY-MM-DD)'
                }
            }
        },
        modalEditUserContractEndDate: {
            validators: {
                notEmpty: {
                    message: 'Please enter the Contract End Date'
                },
                date: {
                    format: 'YYYY-MM-DD',
                    message: 'Please enter a valid date format (YYYY-MM-DD)'
                }
            }
        }
      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({
          eleValidClass: '',
          rowSelector: function (field, ele) {
            return '.mb-6';
          }
        }),
        submitButton: new FormValidation.plugins.SubmitButton(),
        autoFocus: new FormValidation.plugins.AutoFocus()
      }
    });

    // Function to update form fields based on country selection
    function updateCountryFields(isSaudi, clearFields = false) {
        const idNumberLabel = document.querySelector('#idNumberContainer .form-label');
        const idExpiryLabel = document.querySelector('#idExpiryContainer .form-label');
        const idNumberInput = document.getElementById('modalEditUserIdNumber');
        const idExpiryInput = document.getElementById('modalEditUserIdExpiry');
        const passportNumberContainer = document.getElementById('passportNumberContainer');
        const passportExpiryContainer = document.getElementById('passportExpiryContainer');
        const passportNumberInput = document.getElementById('modalEditUserPassportNumber');
        const passportExpiryInput = document.getElementById('modalEditUserPassportExpiry');
        const employeeGOSILabel = document.querySelector('#employeeGOSIContainer .form-label');
        const employerGOSIInput = document.getElementById('modalEditUserEmployerGOSI');
        const employeeGOSIInput = document.getElementById('modalEditUserEmployeeGOSI');

        // Clear fields if specified (only on user-initiated change)
        if (clearFields) {
            idNumberInput.value = '';
            idExpiryInput.value = '';
            passportNumberInput.value = '';
            passportExpiryInput.value = '';
            employerGOSIInput.value = '';
            employeeGOSIInput.value = '';
        }

        // Update ID fields
        if (isSaudi) {
            idNumberLabel.textContent = 'National ID Number';
            idNumberInput.setAttribute('placeholder', 'Enter National ID Number');
            idExpiryLabel.textContent = 'National ID Expiry';
            idExpiryInput.setAttribute('placeholder', 'Enter National ID Expiry');
        } else {
            idNumberLabel.textContent = 'IQAMA Number';
            idNumberInput.setAttribute('placeholder', 'Enter IQAMA Number');
            idExpiryLabel.textContent = 'IQAMA Expiry';
            idExpiryInput.setAttribute('placeholder', 'Enter IQAMA Expiry');
        }

        // Reinitialize datepicker if present
        if (idExpiryInput.classList.contains('gmm-date-format')) {
            if (typeof flatpickr !== 'undefined' && idExpiryInput._flatpickr) {
                idExpiryInput._flatpickr.destroy();
                flatpickr(idExpiryInput, {
                    dateFormat: 'Y-m-d',
                    placeholder: isSaudi ? 'Enter National ID Expiry' : 'Enter IQAMA Expiry',
                    altInput: true,
                    altFormat: 'd - M - Y'
                });
            }
        }

        // Toggle passport fields visibility
        if (isSaudi) {
            passportNumberContainer.style.display = 'none';
            passportExpiryContainer.style.display = 'none';
            // Remove validation errors when hidden
            fv.revalidateField('modalEditUserPassportNumber');
            fv.revalidateField('modalEditUserPassportExpiry');
        } else {
            passportNumberContainer.style.display = 'block';
            passportExpiryContainer.style.display = 'block';
        }

        // Update GOSI fields
        if (isSaudi) {
            employeeGOSILabel.textContent = 'Saudi Employee GOSI %';
            employeeGOSIInput.setAttribute('placeholder', '10.00');
        } else {
            employeeGOSILabel.textContent = 'Non-Saudi Employee GOSI %';
            employeeGOSIInput.setAttribute('placeholder', '10.00');
        }
        employerGOSIInput.setAttribute('placeholder', '12.00');
    }

    // Handle edit form nationality change
    const editCountrySelect = document.getElementById('modalEditUserCountry');
    if (editCountrySelect) {
        // Initial setup without clearing fields
        const saudiId = editCountrySelect.getAttribute('data-saudi-id');
        const isSaudi = editCountrySelect.value === saudiId;
        updateCountryFields(isSaudi, false);

        // Add change event listener to clear fields on user interaction
        editCountrySelect.addEventListener('change', function () {
            const isSaudi = editCountrySelect.value === saudiId;
            updateCountryFields(isSaudi, true);
            fv.revalidateField('modalEditUserPassportNumber');
            fv.revalidateField('modalEditUserPassportExpiry');
        });
    }
    // Form Validation
    const userCertificationForm = document.getElementById('userCertificationForm');
    const assignAssetsForm = document.getElementById('assignAssetsForm');

    // Certification Form Validation
    const fvCert = FormValidation.formValidation(userCertificationForm, {
    fields: {
        userCertificateName: {
        validators: {
            notEmpty: {
            message: 'Please enter certification name'
            }
        }
        },
        userCertificationFile: {
        validators: {
            notEmpty: {
            message: 'Please upload a certification file'
            },
            file: {
            extension: 'jpg,jpeg,png,pdf',
            type: 'image/jpeg,image/png,application/pdf',
            message: 'The file must be JPG, PNG, or PDF'
            }
        }
        }
    },
    plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({
        eleValidClass: '',
        rowSelector: function (field, ele) {
            return '.mb-6';
        }
        }),
        submitButton: new FormValidation.plugins.SubmitButton(),
        autoFocus: new FormValidation.plugins.AutoFocus()
    }
    });

    // Assign Assets Form Validation
    const fvAssets = FormValidation.formValidation(assignAssetsForm, {
    fields: {
        'userAssets[]': {
            validators: {
                notEmpty: {
                    message: 'Please select at least one asset'
                }
            }
        }
    },
    plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({
        eleValidClass: '',
        rowSelector: function (field, ele) {
            return '.mb-6';
        }
        }),
        submitButton: new FormValidation.plugins.SubmitButton(),
        autoFocus: new FormValidation.plugins.AutoFocus()
    }
    });


    $("#userEditForm").submit(function (e) {
      e.preventDefault();

      fv.validate().then(function (status) {
        if (status === 'Valid') {

          blockArea($('.form-block'));

          var form = $('form')[0]; // You need to use standard javascript object here
          var formData = new FormData(form);
          console.log(formData);

          $.ajax({
            url: '/ajax/settings/usermanagement/users/update_user.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (data, status) {
              console.log(data);
              console.log(status);
              unBlockArea($('.form-block'));
              var statusmessage = data.trim().split("|")[0];
              var message = data.trim().split("|")[1];

              if (statusmessage == "SUCCESS") {
                // window.location = "/dashboard";
                Swal.fire({
                  title: 'Success!',
                  icon: 'success',
                  text: message,
                  type: 'success',
                  customClass: {
                    confirmButton: 'btn btn-primary'
                  },
                  showClass: {
                    popup: 'animate__animated animate__bounce'
                  },
                  buttonsStyling: false
                }).then(
                  function (result) {
                    $('#editUserModal').modal('hide');
                    blockArea($('body'));
                    location.reload();
                  }
                );
              }

              if (statusmessage == "ERROR") {

                Swal.fire({
                  title: 'Oops!',
                  icon: 'error',
                  text: message,
                  type: 'error',
                  customClass: {
                    confirmButton: 'btn btn-primary'
                  },
                  showClass: {
                    popup: 'animate__animated animate__shakeX'
                  },

                  buttonsStyling: false
                });
              }
            },

            error: function (error) {

              console.log(error);
            }
          });


        }

      });


      return false;
    });

    $("#userCertificationForm").submit(function (e) {
      e.preventDefault();

      fvCert.validate().then(function (status) {
        if (status === 'Valid') {

          blockArea($('.form-block'));

          var form = $('#userCertificationForm')[0]; 
          var formData = new FormData(form);
          console.log(formData);

          $.ajax({
            url: '/ajax/settings/usermanagement/users/update_user_certification.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (data, status) {
              console.log(data);
              console.log(status);
              unBlockArea($('.form-block'));
              var statusmessage = data.trim().split("|")[0];
              var message = data.trim().split("|")[1];

              if (statusmessage == "SUCCESS") {
                // window.location = "/dashboard";
                Swal.fire({
                  title: 'Success!',
                  icon: 'success',
                  text: message,
                  type: 'success',
                  customClass: {
                    confirmButton: 'btn btn-primary'
                  },
                  showClass: {
                    popup: 'animate__animated animate__bounce'
                  },
                  buttonsStyling: false
                }).then(
                  function (result) {
                    $('#editUserModal').modal('hide');
                    blockArea($('body'));
                    location.reload();
                  }
                );
              }

              if (statusmessage == "ERROR") {

                Swal.fire({
                  title: 'Oops!',
                  icon: 'error',
                  text: message,
                  type: 'error',
                  customClass: {
                    confirmButton: 'btn btn-primary'
                  },
                  showClass: {
                    popup: 'animate__animated animate__shakeX'
                  },

                  buttonsStyling: false
                });
              }
            },

            error: function (error) {

              console.log(error);
            }
          });


        }

      });


      return false;
    });

    $("#assignAssetsForm").submit(function (e) {
      e.preventDefault();

      fvAssets.validate().then(function (status) {
        if (status === 'Valid') {

          blockArea($('.form-block'));

          var form = $('#assignAssetsForm')[0]; 
          var formData = new FormData(form);
          console.log(formData);

          $.ajax({
            url: '/ajax/settings/usermanagement/users/assign_assets.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (data, status) {
              console.log(data);
              console.log(status);
              unBlockArea($('.form-block'));
              var statusmessage = data.trim().split("|")[0];
              var message = data.trim().split("|")[1];

              if (statusmessage == "SUCCESS") {
                // window.location = "/dashboard";
                Swal.fire({
                  title: 'Success!',
                  icon: 'success',
                  text: message,
                  type: 'success',
                  customClass: {
                    confirmButton: 'btn btn-primary'
                  },
                  showClass: {
                    popup: 'animate__animated animate__bounce'
                  },
                  buttonsStyling: false
                }).then(
                  function (result) {
                    $('#editUserModal').modal('hide');
                    blockArea($('body'));
                    location.reload();
                  }
                );
              }

              if (statusmessage == "ERROR") {

                Swal.fire({
                  title: 'Oops!',
                  icon: 'error',
                  text: message,
                  type: 'error',
                  customClass: {
                    confirmButton: 'btn btn-primary'
                  },
                  showClass: {
                    popup: 'animate__animated animate__shakeX'
                  },

                  buttonsStyling: false
                });
              }
            },

            error: function (error) {

              console.log(error);
            }
          });


        }

      });


      return false;
    });
  });

</script>
<style>
    #editUserModal {
        /*width: 400px !important;*/
    }
</style>
</body>
</html>