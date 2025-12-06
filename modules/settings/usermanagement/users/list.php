<?php
$PAGE_ID = "USER_LIST";
include_once __DIR__ . "/../../../../includes/baseIncludes.php";
include_once __DIR__ . "/../../../../includes/auth_check.php";
?>
<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
<head>
    <?php include_once __DIR__ . "/../../../../includes/dashboard/dashboard_head_section.php"; ?>
    <title>GrandMaster ERP | Users</title>
    <link rel="stylesheet" href="/assets/vendor/libs/bootstrap-select/bootstrap-select.css"/>
    <link rel="stylesheet" href="/assets/vendor/libs/animate-css/animate.css"/>
    <link rel="stylesheet" href="/assets/vendor/libs/sweetalert2/sweetalert2.css"/>
    <link rel="stylesheet" href="/assets/vendor/libs/flatpickr/flatpickr.css"/>
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
                <!-- Content -->

                <div class="container-xxl flex-grow-1 container-p-y">
                    <!-- Product List Widget -->
                    <div class="card mb-6">
                        <div class="card-widget-separator-wrapper">
                            <div class="card-body card-widget-separator py-2">
                                <div class="row gy-1 gy-sm-1">
                                    <h4 class="my-0">Employees List</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product List Table -->
                    <div class="card">
                        <!-- <div class="card-header"> -->
                            <!-- <h5 class="card-title">Filter</h5> -->
                            <!-- <div class="d-flex justify-content-between align-items-center row pt-4 gap-6 gap-md-0">
                              <div class="col-md-4 product_status"></div>
                              <div class="col-md-4 product_category"></div>
                              <div class="col-md-4 product_stock"></div>
                            </div> -->
                        <!-- </div> -->
                        <div class="card-datatable table-responsive">
                            <table class="datatables-products table">
                                <thead class="border-top">
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>ID</th>
                                    <th>User Name</th>
                                    <th>Email</th>
                                    <th>Company</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <!-- <th>sku</th>
                                    <th>price</th>
                                    <th>status</th>
                                    <th>actions</th> -->
                                </tr>
                                </thead>
                            </table>
                        </div>
                        <!-- Offcanvas to add new user -->
                        <div class="offcanvas offcanvas-end form-block" tabindex="-1" id="offcanvasUserAdd" aria-labelledby="offcanvasEcommerceUserAddLabel">
                            <div class="offcanvas-header">
                                <h5 id="offcanvasEcommerceUserAddLabel" class="offcanvas-title">Add User</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body border-top mx-0 flex-grow-0">
                              <form class="ecommerce-user-add pt-0" id="userAddForm" method="POST" name="userAddForm"
                                    action="/users/list" enctype="multipart/form-data" onsubmit="return false;">
                                <div class="ecommerce-user-add-basic mb-4">
                                  <div class="mb-6">
                                    <label class="form-label" for="firstName">First Name*</label>
                                    <input type="text" class="form-control" id="firstName"
                                           placeholder="Enter First Name" name="firstName"
                                           aria-label="Mohammed Faisal" />
                                  </div>
                                  <div class="mb-6">
                                    <label class="form-label" for="lastName">Last Name</label>
                                    <input type="text" class="form-control" id="lastName" placeholder="Enter Last Name"
                                           name="lastName" aria-label="Mohammed Faisal" />
                                  </div>
                                  <div class="mb-6">
                                    <label class="form-label" for="userName">User Name*</label>
                                    <input type="text" class="form-control" id="userName" placeholder="Enter User Name"
                                           name="userName" aria-label="Mohammed Faisal" />
                                  </div>
                                  <div class="mb-6">
                                    <label class="form-label" for="userImage">User Image</label>
                                    <input type="file" class="form-control" id="userImage" name="userImage" accept="image/png, image/jpeg" required />
                                  </div>
                                  <div class="mb-6">
                                    <label class="form-label" for="email">Nationality*</label>
                                    <select id="countryId" name="countryId" class="selectpicker form-select w-100" data-style="btn-default" data-live-search="true" data-saudi-id="194">
                                        <option value="">Select a Country</option>
                                        <?php
                                        $res = $db->query("SELECT * FROM countries");
                                        while ($row = mysqli_fetch_assoc($res)) {
                                            ?>
                                            <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                  </div>
                                  <div class="mb-6">
                                    <label class="form-label" for="email">Email*</label>
                                    <input type="text" class="form-control" id="email" placeholder="Enter User Email"
                                           name="email" aria-label="Mohammed Faisal" />
                                  </div>
                                  <div class="mb-6 form-password-toggle">
                                    <label class="form-label" for="password">Password*</label>
                                    <div class="input-group input-group-merge">
                                    <input type="password" class="form-control" id="password"
                                           placeholder="Enter Password" name="password" aria-label="Password" aria-describedby="password"
                                           required />
                                    <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                    </div>
                                  </div>
                                  <div class="mb-6">
                                    <label class="form-label" for="roleId">Role*</label>
                                    <select class="form-select selectpicker w-100" id="roleId" name="roleId"
                                            data-style="btn-default" data-live-search="true" required>
                                      <option value="">Select Role</option>
                                        <?php
                                        $companyId = intval($_SESSION['SES_SELECTED_COMPANY']);
                                        $res = $db->query("SELECT * FROM user_roles");
                                        // $res = $db->query("SELECT * FROM user_roles WHERE companyId = ?s", $companyId);
                                        while ($row = mysqli_fetch_assoc($res)) {
                                            ?>
                                          <option value="<?= $row['roleId'] ?>"><?= $row['roleName'] ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                  </div>
<!--                                  <div class="mb-6">-->
<!--                                    <label class="form-label" for="companyId">Company*</label>-->
<!--                                    <select class="form-select selectpicker w-100" id="companyId" name="companyId"-->
<!--                                            data-style="btn-default" data-live-search="true" required>-->
<!--                                      <option value="">Select Company</option>-->
<!--                                        --><?php
//                                        $res = $db->query("SELECT * FROM companies");
//                                        while ($row = mysqli_fetch_assoc($res)) {
//                                            ?>
<!--                                          <option value="--><?php //= $row['companyId'] ?><!--">--><?php //= $row['companyLabel'] ?><!--</option>-->
<!--                                            --><?php
//                                        }
//                                        ?>
<!--                                    </select>-->
<!--                                  </div>-->
                                  <div class="mb-6">
                                    <label class="form-label">Companies*</label>
                                    <div>
                                        <?php
                                        $res = $db->query("SELECT * FROM companies");
                                        while ($row = mysqli_fetch_assoc($res)) {
                                            ?>
                                          <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="<?= $row['companyId'] ?>" id="company_<?= $row['companyId'] ?>" name="companyIds[]" />
                                            <label class="form-check-label" for="company_<?= $row['companyId'] ?>">
                                                <?= $row['companyLabel'] ?>
                                            </label>
                                          </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                  </div>

                                  <div class="mb-6" id="idNumberContainer">
                                    <label class="form-label" for="idNumber">IQAMA Number</label>
                                    <input type="text" class="form-control" id="idNumber"
                                           placeholder="Enter IQAMA Number" name="idNumber"
                                           aria-label="ID Number" />
                                  </div>
                                  <div class="mb-6" id="idExpiryContainer">
                                    <label class="form-label" for="idExpiry">IQAMA Expiry</label>
                                    <input type="text" class="form-control gmm-date-format" id="idExpiry" placeholder="Enter IQAMA Expiry"
                                           name="idExpiry" aria-label="ID Expiry" />
                                  </div>
                                  <div class="mb-6" id="passportNumberContainer">
                                    <label class="form-label" for="passportNumber">Passport Number</label>
                                    <input type="text" class="form-control" id="passportNumber"
                                           placeholder="Enter Passport Number" name="passportNumber"
                                           aria-label="Passport Number" />
                                  </div>
                                  <div class="mb-6" id="passportExpiryContainer">
                                    <label class="form-label" for="passportExpiry">Passport Expiry</label>
                                    <input type="text" class="form-control gmm-date-format" id="passportExpiry" placeholder="Enter Passport Expiry"
                                           name="passportExpiry" aria-label="Passport Expiry" />
                                  </div>
                                  <div class="mb-6">
                                    <label class="form-label" for="medicalInsuranceNumber">Medical Insurance Number</label>
                                    <input type="text" class="form-control" id="medicalInsuranceNumber"
                                           placeholder="Enter Medical Insurance Number" name="medicalInsuranceNumber"
                                           aria-label="Medical Insurance Number" />
                                  </div>
                                  <div class="mb-6">
                                    <label class="form-label" for="medicalInsuranceExpiry">Medical Insurance Expiry</label>
                                    <input type="text" class="form-control gmm-date-format" id="medicalInsuranceExpiry" placeholder="Enter Medical Insurance Expiry"
                                           name="medicalInsuranceExpiry" aria-label="Medical Insurance Expiry" />
                                  </div>
                                  <div class="mb-6">
                                    <label class="form-label" for="medicalInsuranceCompany">Medical Insurance Company</label>
                                    <input type="text" class="form-control" id="medicalInsuranceCompany"
                                           placeholder="Enter Medical Insurance Company" name="medicalInsuranceCompany"
                                           aria-label="Medical Insurance Company" />
                                  </div>
                                  <div class="text-center mb-6">
                                    <h6 class="mb-2">Payroll related Information</h4>
                                  </div>
                                  <div class="mb-6">
                                    <label class="form-label" for="userContractFile">Contract File</label>
                                    <input type="file" class="form-control" id="userContractFile" name="userContractFile" accept=".jpg,.jpeg,.png,.pdf" required />
                                  </div>
                                  <div class="mb-6">
                                    <label class="form-label" for="contractStartDate">Contract Start Date</label>
                                    <input type="text" class="form-control gmm-date-format" id="contractStartDate" placeholder="Enter Contract Start Date"
                                           name="contractStartDate" aria-label="Contract Start Date" />
                                  </div>
                                  <div class="mb-6">
                                    <label class="form-label" for="contractEndDate">Contract End Date</label>
                                    <input type="text" class="form-control gmm-date-format" id="contractEndDate" placeholder="Enter Contract End Date"
                                           name="contractEndDate" aria-label="Contract End Date" />
                                  </div>
                                  <div class="mb-6">
                                    <label class="form-label" for="workDaysPerMonth">Workdays per month</label>
                                    <input type="text" class="form-control" id="workDaysPerMonth"
                                           placeholder="30" name="workDaysPerMonth"
                                           aria-label="Workdays per month" />
                                  </div>
                                  <div class="mb-6">
                                    <label class="form-label" for="hoursPerDay">Hours per day</label>
                                    <input type="text" class="form-control" id="hoursPerDay"
                                           placeholder="8" name="hoursPerDay"
                                           aria-label="Hours per day" />
                                  </div>
                                  <div class="mb-6">
                                    <label class="form-label" for="basicSalary">Basic Salary</label>
                                    <input type="text" class="form-control" id="basicSalary"
                                           placeholder="10000" name="basicSalary"
                                           aria-label="Basic Salary" />
                                  </div>
                                  <div class="mb-6">
                                    <label class="form-label" for="otMultiplier">OT Multiplier</label>
                                    <input type="text" class="form-control" id="otMultiplier"
                                           placeholder="1.5" name="otMultiplier"
                                           aria-label="OT Multiplier" />
                                  </div>
                                  <div class="mb-6">
                                    <label class="form-label" for="gosiCap">GOSI cap</label>
                                    <input type="text" class="form-control" id="gosiCap"
                                           placeholder="40000" name="gosiCap"
                                           aria-label="GOSI cap" />
                                  </div>
                                  <!-- <div class="mb-6">
                                    <label class="form-label" for="saudiEmployerGOSI">Employer GOSI %</label>
                                    <input type="text" class="form-control" id="saudiEmployerGOSI"
                                           placeholder="12" name="saudiEmployerGOSI"
                                           aria-label="Saudi Employer GOSI %" />
                                  </div>
                                  <div class="mb-6">
                                    <label class="form-label" for="saudiEmployeeGOSI">Saudi Employee GOSI %</label>
                                    <input type="text" class="form-control" id="saudiEmployeeGOSI"
                                           placeholder="10" name="saudiEmployeeGOSI"
                                           aria-label="Saudi Employee GOSI %" />
                                  </div> -->
                                  <div class="mb-6" id="employerGOSIContainer">
                                    <label class="form-label" for="employerGOSI">Employer GOSI %</label>
                                    <input type="text" class="form-control" id="employerGOSI"
                                           placeholder="12" name="employerGOSI"
                                           aria-label="Employer GOSI %" />
                                  </div>
                                  <div class="mb-6" id="employeeGOSIContainer">
                                    <label class="form-label" for="employeeGOSI">Non-Saudi Employee GOSI %</label>
                                    <input type="text" class="form-control" id="employeeGOSI"
                                           placeholder="10" name="employeeGOSI"
                                           aria-label="Employee GOSI %" />
                                  </div>

                                  <!--                                        <div class="mb-6">-->
                                  <!--                                            <label class="form-label" for="userImage">User Image</label>-->
                                  <!--                                            <input type="file" class="form-control" id="userImage" name="userImage" accept="image/png, image/jpeg" required />-->
                                  <!--                                        </div>-->
                                </div>

                                <div>
                                  <button class="btn btn-primary me-sm-4 data-submit">Add</button>
                                  <button type="reset" class="btn btn-label-danger" data-bs-dismiss="offcanvas">
                                    Discard
                                  </button>
                                </div>
                              </form>
                            </div>
                        </div>
                    </div>
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
<?php
include_once __DIR__ . "/../../../../includes/dashboard/dashboard_footer_scripts.php";
?>

<script>

  'use strict';

  // Datatable (jquery)
  $(function () {
    let borderColor, bodyBg, headingColor;

    if (isDarkStyle) {
      borderColor = config.colors_dark.borderColor;
      bodyBg = config.colors_dark.bodyBg;
      headingColor = config.colors_dark.headingColor;
    } else {
      borderColor = config.colors.borderColor;
      bodyBg = config.colors.bodyBg;
      headingColor = config.colors.headingColor;
    }

    // Variable declaration for table
    var dt_product_table = $('.datatables-products'),
      userView = '/users/edit/',
      productAdd = 'app-ecommerce-product-add.html',
      statusObj = {
        1: { title: 'Scheduled', class: 'bg-label-warning' },
        2: { title: 'Publish', class: 'bg-label-success' },
        3: { title: 'Inactive', class: 'bg-label-danger' }
      },
      stockObj = {
        0: { title: 'Out_of_Stock' },
        1: { title: 'In_Stock' }
      },
      stockFilterValObj = {
        0: { title: 'Out of Stock' },
        1: { title: 'In Stock' }
      };

    // E-commerce Products datatable

    if (dt_product_table.length) {
      var dt_products = dt_product_table.DataTable({
        ajax: '/ajax/settings/usermanagement/users/fetch_users.php',
        // dataSrc: function (json) {
        //   console.log(json); // Check the structure of the JSON response
        //   return json.data; // Adjust if your data is nested under a different key
        // },
        columns: [
          // columns according to JSON
          { data: null },
          { data: null },
          { data: 'userId' },
          { data: 'userName' },
          { data: 'email' },
          { data: 'companyLabel' },
          { data: 'active' },
          { data: 'roleName' }
        ],
        columnDefs: [
          {
            // For Responsive
            className: 'control',
            searchable: false,
            orderable: false,
            responsivePriority: 2,
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
            targets: -1,
            title: 'Edit',
            searchable: false,
            orderable: false,
            render: function (data, type, full, meta) {
              var $userId = full['userId'];

              return (
                '<div class="d-flex align-items-center">' +
                '<a href="/users/edit/' + $userId + '" data-bs-toggle="tooltip" class="btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill" data-bs-placement="top" title="Edit"><i class="ti ti-edit mx-2 ti-md"></i></a>' +
                '</div>'
              );
            }
          },
          {
            // ID
            targets: 2,
            render: function (data, type, full, meta) {
              var $userId = full['userId'];

              return '<span>' + $userId + '</span>';
            }
          },
          {
            // User Image and Name
            targets: 3,
            responsivePriority: 1,
            render: function (data, type, full, meta) {
              var $userName = full['userName'];
              var userId = full['userId'];
              var $image = full['profilePic'];

              if ($image) {
                var $output =
                  '<img src="' +
                  $image  +
                  '" class="rounded-2" style="height: 40px; width: 40px;">';
              } else {
                // Fallback to an avatar badge if no image is available
                var stateNum = Math.floor(Math.random() * 6);
                var states = ['success', 'danger', 'warning', 'info', 'dark', 'primary', 'secondary'];
                var $state = states[stateNum],
                  $initials = $userName.match(/\b\w/g) || [];
                $initials = (($initials.shift() || '') + ($initials.pop() || '')).toUpperCase();
                $output = '<span class="avatar-initial rounded-2 bg-label-' + $state + '">' + $initials + '</span>';
              }

              var $row_output =
                '<div class="d-flex justify-content-start align-items-center product-name">' +
                '<div class="avatar-wrapper">' +
                '<div class="avatar avatar me-4 rounded-2 bg-label-secondary">' +
                $output +
                '</div>' +
                '</div>' +
                '<div class="d-flex flex-column">' +
                '<h6 class="text-nowrap mb-0">' +
                $userName +
                '</h6>' +
                '</div>' +
                '</div>';

              return '<span style="white-space: nowrap;">' +
                '<a href="' + userView + full['userId'] + '" class="text-heading" ><span class="fw-medium">' +
                $row_output +
                '</a>' +
                '</span>';

              // return (
              //   '<span style="white-space: nowrap;">' +
              //   '<a href="' + userView + userId + '" class="text-heading" ><span class="fw-medium">' +
              //   $userName +
              //   '</a>' +
              //   '</span>'
              // );
            }
          },
          {
            // Email
            targets: 4,
            render: function (data, type, full, meta) {
              var $email = full['email'];

              return '<span>' + $email + '</span>';
            }
          },
          {
            // Company
            targets: 5,
            render: function (data, type, full, meta) {
              var $company = full['companyLabel'];

              return '<span>' + $company + '</span>';
            }
          },
          {
            // Role
            targets: 6,
            render: function (data, type, full, meta) {
              var $roleName = full['roleName'];

              return '<span>' + $roleName + '</span>';
            }
          },
          {
            // Status
            targets: 7,
            render: function (data, type, full, meta) {
              var $active = full['active'];

              var badgeClass = $active === 'Active'
                ? 'badge bg-label-success text-capitalized'
                : 'badge bg-label-danger text-capitalized';

              return '<span class="' + badgeClass + '">' + $active + '</span>';
            }
          },
        ],
        order: [[2, 'asc']], //set any columns order asc/desc
        dom:
          '<"card-header d-flex border-top rounded-0 flex-wrap py-0 flex-column flex-md-row align-items-start"' +
          '<"me-5 ms-n4 pe-5 mb-n6 mb-md-0"f>' +
          '<"d-flex justify-content-start justify-content-md-end align-items-baseline"<"dt-action-buttons d-flex flex-column align-items-start align-items-sm-center justify-content-sm-center pt-0 gap-sm-4 gap-sm-0 flex-sm-row"lB>>' +
          '>t' +
          '<"row"' +
          '<"col-sm-12 col-md-6"i>' +
          '<"col-sm-12 col-md-6"p>' +
          '>',
        // lengthMenu: [7, 10, 20, 50, 70, 100], //for length of menu
        language: {
          sLengthMenu: '_MENU_',
          search: '',
          searchPlaceholder: 'Search User',
          info: 'Displaying _START_ to _END_ of _TOTAL_ entries',
          paginate: {
            next: '<i class="ti ti-chevron-right ti-sm"></i>',
            previous: '<i class="ti ti-chevron-left ti-sm"></i>'
          }
        },
        // Buttons with Dropdown
        buttons: [
          {
            extend: 'collection',
            className: 'btn btn-label-secondary dropdown-toggle me-4 waves-effect waves-light',
            text: '<i class="ti ti-upload me-1 ti-xs"></i>Export',
            buttons: [
              {
                extend: 'print',
                text: '<i class="ti ti-printer me-2" ></i>Print',
                className: 'dropdown-item',
                exportOptions: {
                  columns: [2, 3, 4, 5, 6, 7],
                  format: {
                    body: function (inner, coldex, rowdex) {
                      if (inner.length <= 0) return inner;
                      var el = $.parseHTML(inner);
                      var result = '';
                      $.each(el, function (index, item) {
                        if (item.classList !== undefined && item.classList.contains('product-name')) {
                          result = result + item.lastChild.firstChild.textContent;
                        } else if (item.innerText === undefined) {
                          result = result + item.textContent;
                        } else result = result + item.innerText;
                      });
                      return result;
                    }
                  }
                },
                customize: function (win) {
                  // Customize print view for dark
                  $(win.document.body)
                    .css('color', headingColor)
                    .css('border-color', borderColor)
                    .css('background-color', bodyBg);
                  $(win.document.body)
                    .find('table')
                    .addClass('compact')
                    .css('color', 'inherit')
                    .css('border-color', 'inherit')
                    .css('background-color', 'inherit');
                }
              },
              {
                extend: 'csv',
                text: '<i class="ti ti-file me-2" ></i>Csv',
                className: 'dropdown-item',
                exportOptions: {
                  columns: [2, 3, 4, 5, 6, 7],
                  format: {
                    body: function (inner, coldex, rowdex) {
                      if (inner.length <= 0) return inner;
                      var el = $.parseHTML(inner);
                      var result = '';
                      $.each(el, function (index, item) {
                        if (item.classList !== undefined && item.classList.contains('product-name')) {
                          result = result + item.lastChild.firstChild.textContent;
                        } else if (item.innerText === undefined) {
                          result = result + item.textContent;
                        } else result = result + item.innerText;
                      });
                      return result;
                    }
                  }
                }
              },
              {
                extend: 'excel',
                text: '<i class="ti ti-file-export me-2"></i>Excel',
                className: 'dropdown-item',
                exportOptions: {
                  columns: [2, 3, 4, 5, 6, 7],
                  format: {
                    body: function (inner, coldex, rowdex) {
                      if (inner.length <= 0) return inner;
                      var el = $.parseHTML(inner);
                      var result = '';
                      $.each(el, function (index, item) {
                        if (item.classList !== undefined && item.classList.contains('product-name')) {
                          result = result + item.lastChild.firstChild.textContent;
                        } else if (item.innerText === undefined) {
                          result = result + item.textContent;
                        } else result = result + item.innerText;
                      });
                      return result;
                    }
                  }
                }
              },
              {
                extend: 'pdf',
                text: '<i class="ti ti-file-text me-2"></i>Pdf',
                className: 'dropdown-item',
                exportOptions: {
                  columns: [2, 3, 4, 5, 6, 7],
                  format: {
                    body: function (inner, coldex, rowdex) {
                      if (inner.length <= 0) return inner;
                      var el = $.parseHTML(inner);
                      var result = '';
                      $.each(el, function (index, item) {
                        if (item.classList !== undefined && item.classList.contains('product-name')) {
                          result = result + item.lastChild.firstChild.textContent;
                        } else if (item.innerText === undefined) {
                          result = result + item.textContent;
                        } else result = result + item.innerText;
                      });
                      return result;
                    }
                  }
                }
              },
              {
                extend: 'copy',
                text: '<i class="ti ti-copy me-2"></i>Copy',
                className: 'dropdown-item',
                exportOptions: {
                  columns: [2, 3, 4, 5, 6, 7],
                  format: {
                    body: function (inner, coldex, rowdex) {
                      if (inner.length <= 0) return inner;
                      var el = $.parseHTML(inner);
                      var result = '';
                      $.each(el, function (index, item) {
                        if (item.classList !== undefined && item.classList.contains('product-name')) {
                          result = result + item.lastChild.firstChild.textContent;
                        } else if (item.innerText === undefined) {
                          result = result + item.textContent;
                        } else result = result + item.innerText;
                      });
                      return result;
                    }
                  }
                }
              }
            ]
          },
          {
            text: '<i class="ti ti-plus me-0 me-sm-1 mb-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add User</span>',
            className: 'add-new btn btn-primary waves-effect waves-light',
            attr: {
              'data-bs-toggle': 'offcanvas',
              'data-bs-target': '#offcanvasUserAdd'
            }
          }
        ],
        // For responsive popup
        responsive: {
          details: {
            display: $.fn.dataTable.Responsive.display.modal({
              header: function (row) {
                var data = row.data();
                // console.log(data); // Log the row data to the console
                return 'Details of ' + data['userName'];
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
          // Adding status filter once table initialized
          this.api()
            .columns(-2)
          ;
          // Adding category filter once table initialized
          this.api()
            .columns(3)
            .every(function () {
              var column = this;
              // var select = $(
              // '<select id="ProductCategory" class="form-select text-capitalize"><option value="">Category</option></select>'
              // )
              // .appendTo('.product_category')
              // .on('change', function () {
              //     var val = $.fn.dataTable.util.escapeRegex($(this).val());
              //     column.search(val ? '^' + val + '$' : '', true, false).draw();
              // });

              // column
              // .data()
              // .unique()
              // .sort()
              // .each(function (d, j) {
              //     select.append('<option value="' + categoryObj[d].title + '">' + categoryObj[d].title + '</option>');
              // });
            });
          // Adding stock filter once table initialized
          this.api()
            .columns(4)
            .every(function () {
              var column = this;
              // var select = $(
              // '<select id="ProductStock" class="form-select text-capitalize"><option value=""> Stock </option></select>'
              // )
              // .appendTo('.product_stock')
              // .on('change', function () {
              //     var val = $.fn.dataTable.util.escapeRegex($(this).val());
              //     column.search(val ? '^' + val + '$' : '', true, false).draw();
              // });

              // column
              // .data()
              // .unique()
              // .sort()
              // .each(function (d, j) {
              //     select.append('<option value="' + stockObj[d].title + '">' + stockFilterValObj[d].title + '</option>');
              // });
            });
        }
      });
      $('.dataTables_length').addClass('mx-n2');
      $('.dt-buttons').addClass('d-flex flex-wrap mb-6 mb-sm-0');
    }

    // Delete Record
    $('.datatables-products tbody').on('click', '.delete-record', function () {
      dt_products.row($(this).parents('tr')).remove().draw();
    });

    // Filter form control to default size
    // ? setTimeout used for multilingual table initialization
    setTimeout(() => {
      $('.dataTables_filter .form-control').removeClass('form-control-sm');
      $('.dataTables_length .form-select').removeClass('form-select-sm');
    }, 300);
  });

  document.addEventListener('DOMContentLoaded', function () {
    const params = new URLSearchParams(window.location.search);
    const currentPath = window.location.pathname;

    // Check if 'addNew' is present in the query string
    if (params.has('addnew') || currentPath.includes('/new')) {
      const offcanvasElement = document.getElementById('offcanvasUserAdd');
      if (offcanvasElement) {
        const offcanvasInstance = new bootstrap.Offcanvas(offcanvasElement);
        offcanvasInstance.show();
      } else {
        console.error("Offcanvas element not found.");
      }
    }

    // Handle nationality change
    const countrySelect = document.getElementById('countryId');
    if (countrySelect) {
        countrySelect.addEventListener('change', function () {
            const saudiId = countrySelect.getAttribute('data-saudi-id');
            const isSaudi = countrySelect.value === saudiId;

            // Update ID fields
            const idNumberLabel = document.querySelector('#idNumberContainer .form-label');
            const idExpiryLabel = document.querySelector('#idExpiryContainer .form-label');
            const idNumberInput = document.getElementById('idNumber');
            const idExpiryInput = document.getElementById('idExpiry');

            // Clear ID fields
            idNumberInput.value = '';
            idExpiryInput.value = '';

            if (isSaudi) {
                idNumberLabel.textContent = 'National ID Number';
                idNumberInput.placeholder = 'Enter National ID Number';
                idExpiryLabel.textContent = 'National ID Expiry';
                idExpiryInput.setAttribute('placeholder', 'Enter National ID Expiry');
            } else {
                idNumberLabel.textContent = 'IQAMA Number';
                idNumberInput.placeholder = 'Enter IQAMA Number';
                idExpiryLabel.textContent = 'IQAMA Expiry';
                idExpiryInput.setAttribute('placeholder', 'Enter IQAMA Expiry');
            }

            // Reinitialize datepicker if present to ensure placeholder update
            if (idExpiryInput.classList.contains('gmm-date-format')) {
                // Destroy and reinitialize datepicker (example for Flatpickr)
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
            const passportNumberContainer = document.getElementById('passportNumberContainer');
            const passportExpiryContainer = document.getElementById('passportExpiryContainer');
            const passportNumberInput = document.getElementById('passportNumber');
            const passportExpiryInput = document.getElementById('passportExpiry');
            passportNumberInput.value = '';
            passportExpiryInput.value = '';
            if (isSaudi) {
                passportNumberContainer.style.display = 'none';
                passportExpiryContainer.style.display = 'none';
                // Remove validation errors when hidden
                fv.revalidateField('passportNumber');
                fv.revalidateField('passportExpiry');
            } else {
                passportNumberContainer.style.display = 'block';
                passportExpiryContainer.style.display = 'block';
            }

            // Update GOSI fields
            const employeeGOSILabel = document.querySelector('#employeeGOSIContainer .form-label');
            const employerGOSIInput = document.getElementById('employerGOSI');
            const employeeGOSIInput = document.getElementById('employeeGOSI');
            employerGOSIInput.value = '';
            employeeGOSIInput.value = '';
            if (isSaudi) {
                employeeGOSILabel.textContent = 'Saudi Employee GOSI %';
                document.getElementById('employeeGOSI').placeholder = '10.00';
            } else {
                employeeGOSILabel.textContent = 'Non-Saudi Employee GOSI %';
                document.getElementById('employeeGOSI').placeholder = '10.00';
            }
        });

        // Trigger change event on page load to set initial state
        countrySelect.dispatchEvent(new Event('change'));
    }
  });

  $(document).ready(function () {
    // Form Validation
    const userAddForm = document.getElementById('userAddForm');

    // Initialize Form Validation
    const fv = FormValidation.formValidation(userAddForm, {
      fields: {
        firstName: {
          validators: {
            notEmpty: {
              message: 'Please enter the first name'
            }
          }
        },
        userName: {
          validators: {
            notEmpty: {
              message: 'Please enter the user name'
            }
          }
        },
        email: {
          validators: {
            notEmpty: {
              message: 'Please enter the email'
            }
          }
        },
        password: {
          validators: {
            notEmpty: {
              message: 'Please enter the password'
            }
          }
        },
        roleId: {
          validators: {
            notEmpty: {
              message: 'Please choose the role'
            }
          }
        },
        'companyIds[]': {  // Changed from companyId to companyIds[]
          validators: {
            choice: {
              min: 1,
              message: 'Please choose at least one company'
            }
          }
        },
        countryId: {
          validators: {
            notEmpty: {
              message: 'Please choose the country'
            }
          }
        },
        basicSalary: {
          validators: {
            notEmpty: {
              message: 'Please enter the Basic Salary'
            }
          }
        },
        employerGOSI: {
          validators: {
            notEmpty: {
              message: 'Please enter the Employer GOSI %'
            }
          }
        },
        employeeGOSI: {
          validators: {
            notEmpty: {
              message: 'Please enter the Employee GOSI %'
            }
          }
        },
        idNumber: {
          validators: {
            notEmpty: {
              message: 'Please enter the National ID or IQAMA Number'
            }
          }
        },
        idExpiry: {
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
        passportNumber: {
          validators: {
            callback: {
              message: 'Please enter the Passport Number',
              callback: function(input) {
                const countrySelect = document.getElementById('countryId');
                const saudiId = countrySelect.getAttribute('data-saudi-id');
                const isSaudi = countrySelect.value === saudiId;
                return isSaudi ? true : !!input.value;
              }
            }
          }
        },
        passportExpiry: {
          validators: {
            callback: {
              message: 'Please enter the Passport Expiry',
              callback: function(input) {
                const countrySelect = document.getElementById('countryId');
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
        contractStartDate: {
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
        contractEndDate: {
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
        
        // userImage: {
        //     validators: {
        //         notEmpty: {
        //             message: 'Please upload a user image'
        //         },
        //         file: {
        //             extension: 'jpeg,jpg,png',
        //             type: 'image/jpeg,image/png',
        //             message: 'Please upload a valid image file (JPEG or PNG)'
        //         }
        //     }
        // }
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

    // Handle Form Submission
    $("#userAddForm").submit(function (e) {
      e.preventDefault(); // Prevent default form submission

      // Validate the form
      fv.validate().then(function (status) {
        if (status === 'Valid') {
          blockArea($('.form-block')); // Show loading state

          var form = $('form')[0]; // You need to use standard javascript object here
          var formData = new FormData(form);

          // Submit the form data via AJAX
          $.ajax({
            url: '/ajax/settings/usermanagement/users/add_user.php', // Your server-side script
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (data, status) {
              console.log(data);
              unBlockArea($('.form-block')); // Remove loading state

              var statusMessage = data.trim().split("|")[0];
              var message = data.trim().split("|")[1];

              if (statusMessage === "SUCCESS") {
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
                }).then(function (result) {
                  dismissOffcanvas('offcanvasUserAdd');
                  blockArea($('body'));
                  location.reload(); // Reload the page
                });
              } else if (statusMessage === "ERROR") {
                Swal.fire({
                  title: 'Oops!',
                  icon: 'error',
                  text: message,
                  customClass: {
                    confirmButton: 'btn btn-primary'
                  },
                  buttonsStyling: false
                });
              }
            },
            error: function (error) {
              console.error(error);
              unBlockArea($('.form-block')); // Remove loading state
            }
          });
        }
      });

      return false; // Prevent default form submission
    });
  });

</script>

<style>
    #offcanvasUserAdd {
        width: 850px !important;
    }
</style>
</body>
</html>
 