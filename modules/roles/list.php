<?php
$PAGE_ID = "ROLES_LIST";
include_once __DIR__ . "/../../includes/baseIncludes.php";
include_once __DIR__ . "/../../includes/auth_check.php";
?>
<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
<head>
    <?php include_once __DIR__ . "/../../includes/dashboard/dashboard_head_section.php"; ?>
    <title>GrandMaster ERP | Roles</title>
    <link rel="stylesheet" href="/assets/vendor/libs/bootstrap-select/bootstrap-select.css"/>
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
            <div class="content-wrapper">
            <!-- Content -->

            <div class="container-xxl flex-grow-1 container-p-y">
              <h4 class="mb-1">Roles List</h4>

              <p class="mb-6">
                A role provided access to predefined menus and features so that depending on <br />
                assigned role an administrator can have access to what user needs.
              </p>
              <!-- Role cards -->
              <div class="row g-6">
                <?php if (is_ceo() || has_permission('rolesAndPermissions', 'create')) { ?>
                <div class="col-xl-4 col-lg-6 col-md-6">
                  <div class="card h-100">
                    <div class="row h-100">
                      <div class="col-sm-5">
                        <div class="d-flex align-items-end h-100 justify-content-center mt-sm-0 mt-4">
                          <img
                            src="../../assets/img/illustrations/add-new-roles.png"
                            class="img-fluid mt-sm-4 mt-md-0"
                            alt="add-new-roles"
                            width="83" />
                        </div>
                      </div>
                      <div class="col-sm-7">
                        <div class="card-body text-sm-end text-center ps-sm-0">
                          <button
                            data-bs-target="#addRoleModal"
                            data-bs-toggle="modal"
                            class="btn btn-sm btn-primary mb-4 text-nowrap add-new-role">
                            Add New Role
                          </button>
                          <p class="mb-0">
                            Add new role, <br />
                            if it doesn't exist.
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <?php } ?>
                <?php
                $res = $db->query("SELECT * FROM user_roles WHERE roleId !=1 AND companyId = ?i AND active=1 ORDER BY roleId ASC", $_SESSION['SES_SELECTED_COMPANY']);
                while($row=mysqli_fetch_assoc($res))
                {
                  // Fetch users mapped to this role
                  $users = $db->getAll("
                      SELECT u.userID, u.firstName, u.lastName 
                      FROM users u
                      INNER JOIN user_role_mapping m ON u.userID = m.userId
                      WHERE m.roleId = ?i
                  ", $row['roleId']);

                  $userCount = count($users);
                ?>
                <div class="col-xl-4 col-lg-6 col-md-6">
                  <div class="card">
                    <div class="card-body">
                      <div class="d-flex justify-content-between align-items-center mb-4">
                        <!-- <h6 class="fw-normal mb-0 text-body">Total 4 users</h6>
                          -->
                         <h6 class="fw-normal mb-0 text-body">
                          <?= $userCount > 0 ? "Total {$userCount} user" . ($userCount > 1 ? "s" : "") : "No users" ?>
                        </h6>
                        <ul class="list-unstyled d-flex align-items-center avatar-group mb-0">
                          <!-- <li
                            data-bs-toggle="tooltip"
                            data-popup="tooltip-custom"
                            data-bs-placement="top"
                            title="Kaith D'souza"
                            class="avatar pull-up">
                            <img class="rounded-circle" src="../../assets/img/avatars/3.png" alt="Avatar" />
                          </li> -->
                          <?php
                          if ($userCount > 0) {
                              $visibleUsers = array_slice($users, 0, 4); // show only 4 users max
                              $totalUsers = $userCount;

                              echo '<ul class="list-unstyled d-flex align-items-center avatar-group mb-0 z-2">';

                              foreach ($visibleUsers as $u) {
                                  $firstName = $u['firstName'] ?? '';
                                  $lastName  = $u['lastName'] ?? '';
                                  $fullName  = trim("$firstName $lastName");

                                  // Build initials (A or MA)
                                  $initials = (!empty($firstName) ? strtoupper(substr($firstName, 0, 1)) : '') .
                                              (!empty($lastName) ? strtoupper(substr($lastName, 0, 1)) : '');

                                  // Random color for each avatar
                                  $colorClasses = ['success', 'danger', 'warning', 'info', 'primary', 'secondary'];
                                  $randomColor  = $colorClasses[array_rand($colorClasses)];

                                  echo '
                                  <li data-bs-toggle="tooltip"
                                      data-popup="tooltip-custom"
                                      data-bs-placement="top"
                                      title="' . htmlspecialchars($fullName) . '"
                                      class="avatar avatar-sm pull-up">
                                      <span class="avatar-initial rounded-circle bg-label-' . $randomColor . '">
                                          ' . $initials . '
                                      </span>
                                  </li>';
                              }

                              // If there are more than 4 users, show +X
                              if ($totalUsers > 4) {
                                  $remaining = $totalUsers - 4;
                                  echo '
                                  <li class="avatar avatar-sm">
                                      <span class="avatar-initial rounded-circle pull-up bg-label-dark"
                                          data-bs-toggle="tooltip"
                                          data-bs-placement="top"
                                          title="' . $remaining . ' more">
                                          +' . $remaining . '
                                      </span>
                                  </li>';
                              }

                              echo '</ul>';

                          } else {
                              echo '<ul class="list-unstyled d-flex align-items-center avatar-group mb-0 z-2">
                                      <li><small class="text-muted">No users</small></li>
                                    </ul>';
                          }
                          ?>

                        </ul>
                      </div>
                      <div class="d-flex justify-content-between align-items-end">
                        <div class="role-heading">
                          <!-- <h5 class="mb-1">Administrator</h5> -->
                           <h5 class="mb-1"><?= htmlspecialchars($row['roleName']) ?></h5>
                          <a href="javascript:;" 
                            class="role-edit-modal"
                            data-role-id="<?= $row['roleId'] ?>"
                            data-role-name="<?= htmlspecialchars($row['roleName']) ?>"
                          ><span>Edit Role</span></a>

                        </div>
                        <!-- <a href="javascript:void(0);"><i class="ti ti-copy ti-md text-heading"></i></a> -->
                      </div>
                    </div>
                  </div>
                </div>
                <?php
                }
                ?>
              </div>
              <!--/ Role cards -->

              <!-- Add Role Modal -->
              <!-- Add Role Modal -->
              <div class="modal fade" id="addRoleModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-simple modal-dialog-centered modal-add-new-role">
                  <div class="modal-content">
                    <div class="modal-body">
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      <div class="text-center mb-6">
                        <h4 class="role-title mb-2">Add New Role</h4>
                        <p>Set role permissions</p>
                      </div>

                      <!-- Add Role Form -->
                      <form id="addRoleForm" class="row g-6" onsubmit="return false">
                        <div class="col-12">
                          <label class="form-label" for="modalRoleName">Role Name</label>
                          <input type="text" id="modalRoleName" name="modalRoleName" class="form-control" placeholder="Enter a role name" />
                        </div>

                        <div class="col-12">
                          <h5 class="mb-6">Role Permissions</h5>

                          <div class="table-responsive">
                            <table class="table table-bordered roles no-margin align-middle">
                              <thead class="bg-light">
                                <tr>
                                  <th style="width: 35%;">Features</th>
                                  <th style="width: 65%;">Capabilities</th>
                                </tr>
                              </thead>
                              <tbody>
                                <!-- Administrator Access -->
                                <tr>
                                  <td><b>Administrator Access</b></td>
                                  <td>
                                    <div class="ms-3">
                                      <div class="form-check mb-0">
                                        <input class="form-check-input" type="checkbox" id="selectAll" />
                                        <label class="form-check-label fw-medium" for="selectAll">Select All</label>
                                        <i class="ti ti-info-circle ms-2 text-muted" data-bs-toggle="tooltip" title="Allows full access to the system"></i>
                                      </div>
                                    </div>
                                  </td>
                                </tr>

                                <!-- Quotations -->
                                <tr>
                                  <td><b>Quotations</b></td>
                                  <td>
                                    <div class="ms-3">
                                      <div class="form-check mb-1">
                                        <input type="checkbox" class="form-check-input" id="quotationsView" />
                                        <label class="form-check-label" for="quotationsView">View</label>
                                      </div>
                                      <div class="form-check mb-1">
                                        <input type="checkbox" class="form-check-input" id="quotationsCreate" />
                                        <label class="form-check-label" for="quotationsCreate">Create</label>
                                      </div>
                                      <div class="form-check mb-1">
                                        <input type="checkbox" class="form-check-input" id="quotationsEdit" />
                                        <label class="form-check-label" for="quotationsEdit">Edit</label>
                                      </div>
                                      <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="quotationsCancel" />
                                        <label class="form-check-label" for="quotationsCancel">Cancel</label>
                                      </div>
                                    </div>
                                  </td>
                                </tr>

                                <!-- Customers -->
                                <tr>
                                  <td><b>Customers</b></td>
                                  <td>
                                    <div class="ms-3">
                                      <div class="form-check mb-1">
                                        <input type="checkbox" class="form-check-input" id="customersView" />
                                        <label class="form-check-label" for="customersView">View</label>
                                      </div>
                                      <div class="form-check mb-1">
                                        <input type="checkbox" class="form-check-input" id="customersCreate" />
                                        <label class="form-check-label" for="customersCreate">Create</label>
                                      </div>
                                      <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="customersEdit" />
                                        <label class="form-check-label" for="customersEdit">Edit</label>
                                      </div>
                                    </div>
                                  </td>
                                </tr>

                                <!-- Items / Products -->
                                <tr>
                                  <td><b>Items / Products</b></td>
                                  <td>
                                    <div class="ms-3">
                                      <div class="form-check mb-1">
                                        <input type="checkbox" class="form-check-input" id="itemsOrProductsView" />
                                        <label class="form-check-label" for="itemsOrProductsView">View</label>
                                      </div>
                                      <div class="form-check mb-1">
                                        <input type="checkbox" class="form-check-input" id="itemsOrProductsCreate" />
                                        <label class="form-check-label" for="itemsOrProductsCreate">Create</label>
                                      </div>
                                      <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="itemsOrProductsEdit" />
                                        <label class="form-check-label" for="itemsOrProductsEdit">Edit</label>
                                      </div>
                                    </div>
                                  </td>
                                </tr>

                                <!-- Roles & Permissions -->
                                <tr>
                                  <td><b>Roles & Permissions</b></td>
                                  <td>
                                    <div class="ms-3">
                                      <div class="form-check mb-1">
                                        <input type="checkbox" class="form-check-input" id="rolesAndPermissionsView" />
                                        <label class="form-check-label" for="rolesAndPermissionsView">View</label>
                                      </div>
                                      <div class="form-check mb-1">
                                        <input type="checkbox" class="form-check-input" id="rolesAndPermissionsCreate" />
                                        <label class="form-check-label" for="rolesAndPermissionsCreate">Create</label>
                                      </div>
                                      <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="rolesAndPermissionsEdit" />
                                        <label class="form-check-label" for="rolesAndPermissionsEdit">Edit</label>
                                      </div>
                                    </div>
                                  </td>
                                </tr>

                              </tbody>
                            </table>
                          </div>
                        </div>

                        <div class="col-12 text-center mt-4">
                          <button type="submit" class="btn btn-primary me-3">Submit</button>
                          <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>

              <!--/ Add Role Modal -->

              <!-- / Add Role Modal -->
              <!-- Edit Role Modal -->
              <div class="modal fade" id="editRoleModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-simple modal-dialog-centered modal-edit-role">
                  <div class="modal-content">
                    <div class="modal-body">
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      <div class="text-center mb-6">
                        <h4 class="role-title mb-2">Edit Role</h4>
                        <p>Update role permissions</p>
                      </div>

                      <!-- Edit Role Form -->
                      <form id="editRoleForm" class="row g-6" onsubmit="return false">
                        <input type="hidden" id="editRoleId" name="editRoleId" />

                        <div class="col-12">
                          <label class="form-label" for="editRoleName">Role Name</label>
                          <input type="text" id="editRoleName" name="editRoleName" class="form-control" placeholder="Enter a role name" />
                        </div>

                        <div class="col-12">
                          <h5 class="mb-6">Role Permissions</h5>

                          <div class="table-responsive">
                            <table class="table table-bordered roles no-margin align-middle">
                              <thead class="bg-light">
                                <tr>
                                  <th style="width: 35%;">Features</th>
                                  <th style="width: 65%;">Capabilities</th>
                                </tr>
                              </thead>
                              <tbody>
                                <!-- Administrator Access -->
                                <tr>
                                  <td><b>Administrator Access</b></td>
                                  <td>
                                    <div class="ms-3">
                                      <div class="form-check mb-0">
                                        <input class="form-check-input" type="checkbox" id="editSelectAll" />
                                        <label class="form-check-label fw-medium" for="editSelectAll">Select All</label>
                                        <i class="ti ti-info-circle ms-2 text-muted" data-bs-toggle="tooltip" title="Allows full access to the system"></i>
                                      </div>
                                    </div>
                                  </td>
                                </tr>

                                <!-- Quotations -->
                                <tr>
                                  <td><b>Quotations</b></td>
                                  <td>
                                    <div class="ms-3">
                                      <div class="form-check mb-1">
                                        <input type="checkbox" class="form-check-input permission-checkbox" id="edit_quotations_view" />
                                        <label class="form-check-label" for="edit_quotations_view">View</label>
                                      </div>
                                      <div class="form-check mb-1">
                                        <input type="checkbox" class="form-check-input permission-checkbox" id="edit_quotations_create" />
                                        <label class="form-check-label" for="edit_quotations_create">Create</label>
                                      </div>
                                      <div class="form-check mb-1">
                                        <input type="checkbox" class="form-check-input permission-checkbox" id="edit_quotations_edit" />
                                        <label class="form-check-label" for="edit_quotations_edit">Edit</label>
                                      </div>
                                      <div class="form-check">
                                        <input type="checkbox" class="form-check-input permission-checkbox" id="edit_quotations_cancel" />
                                        <label class="form-check-label" for="edit_quotations_cancel">Cancel</label>
                                      </div>
                                    </div>
                                  </td>
                                </tr>

                                <!-- Customers -->
                                <tr>
                                  <td><b>Customers</b></td>
                                  <td>
                                    <div class="ms-3">
                                      <div class="form-check mb-1">
                                        <input type="checkbox" class="form-check-input permission-checkbox" id="edit_customers_view" />
                                        <label class="form-check-label" for="edit_customers_view">View</label>
                                      </div>
                                      <div class="form-check mb-1">
                                        <input type="checkbox" class="form-check-input permission-checkbox" id="edit_customers_create" />
                                        <label class="form-check-label" for="edit_customers_create">Create</label>
                                      </div>
                                      <div class="form-check">
                                        <input type="checkbox" class="form-check-input permission-checkbox" id="edit_customers_edit" />
                                        <label class="form-check-label" for="edit_customers_edit">Edit</label>
                                      </div>
                                    </div>
                                  </td>
                                </tr>

                                <!-- Items / Products -->
                                <tr>
                                  <td><b>Items / Products</b></td>
                                  <td>
                                    <div class="ms-3">
                                      <div class="form-check mb-1">
                                        <input type="checkbox" class="form-check-input permission-checkbox" id="edit_itemsOrProducts_view" />
                                        <label class="form-check-label" for="edit_itemsOrProducts_view">View</label>
                                      </div>
                                      <div class="form-check mb-1">
                                        <input type="checkbox" class="form-check-input permission-checkbox" id="edit_itemsOrProducts_create" />
                                        <label class="form-check-label" for="edit_itemsOrProducts_create">Create</label>
                                      </div>
                                      <div class="form-check">
                                        <input type="checkbox" class="form-check-input permission-checkbox" id="edit_itemsOrProducts_edit" />
                                        <label class="form-check-label" for="edit_itemsOrProducts_edit">Edit</label>
                                      </div>
                                    </div>
                                  </td>
                                </tr>

                                <!-- Roles & Permissions -->
                                <tr>
                                  <td><b>Roles & Permissions</b></td>
                                  <td>
                                    <div class="ms-3">
                                      <div class="form-check mb-1">
                                        <input type="checkbox" class="form-check-input permission-checkbox" id="edit_rolesAndPermissions_view" />
                                        <label class="form-check-label" for="edit_rolesAndPermissions_view">View</label>
                                      </div>
                                      <div class="form-check mb-1">
                                        <input type="checkbox" class="form-check-input permission-checkbox" id="edit_rolesAndPermissions_create" />
                                        <label class="form-check-label" for="edit_rolesAndPermissions_create">Create</label>
                                      </div>
                                      <div class="form-check">
                                        <input type="checkbox" class="form-check-input permission-checkbox" id="edit_rolesAndPermissions_edit" />
                                        <label class="form-check-label" for="edit_rolesAndPermissions_edit">Edit</label>
                                      </div>
                                    </div>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>

                        <div class="col-12 text-center mt-4">
                          <?php if (is_ceo() || has_permission('rolesAndPermissions', 'edit')) { ?>
                            <button type="submit" class="btn btn-primary me-3">Update</button>
                          <?php } ?>
                          <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Edit Role Modal Ends -->

            </div>
            <!-- / Content -->

            <!-- Footer -->
            <footer class="content-footer footer bg-footer-theme">
                <?php include_once __DIR__ . "/../../includes/dashboard/dashboard_footer_section.php"; ?>
            </footer>
            <!-- / Footer -->

            <div class="content-backdrop fade"></div>
          </div>
            <!-- Overlay -->
            <div class="layout-overlay layout-menu-toggle"></div>
            <!-- Drag Target Area To SlideIn Menu On Small Screens -->
            <div class="drag-target"></div>
        </div>
        <!-- / Layout wrapper -->
        <?php
        include_once __DIR__ . "/../../includes/dashboard/dashboard_footer_scripts.php";
        ?>
        <script>
            $(".selectpicker").selectpicker();

            'use strict';

           $(document).ready(function() {

            $('#selectAll').on('change', function() {
              $('input.form-check-input[type=checkbox]').not(this).prop('checked', this.checked);
            });
            // Role Add Form Handler
            $('#addRoleForm').on('submit', function(e) {
              e.preventDefault();

              var formData = new FormData(this);

              // Collect permissions manually from checked boxes
              var permissions = {};

              // ✅ Updated regex to handle multi-word modules like salesOrdersView
              $('input.form-check-input[type=checkbox]').each(function() {
                if ($(this).is(':checked') && $(this).attr('id') !== 'selectAll') {
                let id = $(this).attr('id');
                let match = id.match(/^([a-zA-Z]+?)(View|Create|Edit|Cancel)$/);
                if (match) {
                  let module = match[1]; // ✅ preserve original case
                  let action = match[2].toLowerCase();
                  if (!permissions[module]) permissions[module] = [];
                  permissions[module].push(action);
                }
              }
              });

              formData.append('permissions', JSON.stringify(permissions));

              $.ajax({
                url: '/ajax/roles/add_role.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                  blockArea($('.modal-content')); // optional spinner area like your customer form
                },
                success: function(data) {
                  unBlockArea($('.modal-content'));
                  console.log(data);
                  var statusmessage = data.trim().split('|')[0];
                  var message = data.trim().split('|')[1];

                  if (statusmessage === 'SUCCESS') {
                    Swal.fire({
                      title: 'Success!',
                      icon: 'success',
                      text: message,
                      customClass: {
                        confirmButton: 'btn btn-primary'
                      },
                      buttonsStyling: false
                    }).then(function() {
                      $('#addRoleModal').modal('hide');
                      location.reload();
                    });
                  } else if (statusmessage === 'ERROR') {
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
                error: function(xhr, status, error) {
                  unBlockArea($('.modal-content'));
                  console.error("AJAX Error:", error);
                  Swal.fire('Error!', 'An error occurred while adding the role.', 'error');
                }
              });

              return false;
            });
          });

          $(document).ready(function() {

            // ✅ "Select All" toggle handler for Edit Modal
            $(document).on('change', '#editSelectAll', function() {
              const isChecked = $(this).is(':checked');
              $('#editRoleModal .permission-checkbox').prop('checked', isChecked);
            });

            // ✅ Load Role Data
            $(document).on('click', '.role-edit-modal', function() {
              const roleId = $(this).data('role-id');
              const roleName = $(this).data('role-name');

              $('#editRoleId').val(roleId);
              $('#editRoleName').val(roleName);
              $('#editRoleModal input[type=checkbox]').prop('checked', false);

              $.ajax({
                url: '/ajax/roles/get_role.php',
                type: 'POST',
                data: { roleId: roleId },
                dataType: 'json',
                success: function(response) {
                  if (response.status === 'SUCCESS') {
                    const perms = response.permissions || {};
                    for (let mod in perms) {
                      perms[mod].forEach(cap => {
                        $(`#edit_${mod}_${cap}`).prop('checked', true);
                      });
                    }
                    $('#editRoleModal').modal('show');
                  } else {
                    Swal.fire('Error', response.message, 'error');
                  }
                },
                error: function() {
                  Swal.fire('Error', 'Failed to load role data', 'error');
                }
              });
            });

            // ✅ Update Role Submit
            $('#editRoleForm').submit(function(e) {
              e.preventDefault();

              const formData = new FormData(this);
              const permissions = {};

              $('#editRoleModal input.permission-checkbox').each(function() {
                const id = $(this).attr('id');
                if ($(this).is(':checked')) {
                  const [, module, action] = id.split('_');
                  if (!permissions[module]) permissions[module] = [];
                  permissions[module].push(action);
                }
              });

              formData.append('permissions', JSON.stringify(permissions));

              $.ajax({
                url: '/ajax/roles/update_role.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                  blockArea($('.modal-content'));
                },
                success: function(data) {
                  unBlockArea($('.modal-content'));
                  console.log(data);
                  const [status, msg] = data.trim().split('|');

                  if (status === 'SUCCESS') {
                    Swal.fire({
                      title: 'Success!',
                      icon: 'success',
                      text: msg,
                      customClass: { confirmButton: 'btn btn-primary' },
                      showClass: { popup: 'animate__animated animate__bounce' },
                      buttonsStyling: false
                    }).then(() => {
                      $('#editRoleModal').modal('hide');
                      location.reload();
                    });
                  } else {
                    Swal.fire({
                      title: 'Oops!',
                      icon: 'error',
                      text: msg,
                      customClass: { confirmButton: 'btn btn-primary' },
                      showClass: { popup: 'animate__animated animate__shakeX' },
                      buttonsStyling: false
                    });
                  }
                },
                error: function() {
                  unBlockArea($('.modal-content'));
                  Swal.fire('Error!', 'Something went wrong while updating the role.', 'error');
                }
              });
            });
          });

        </script>
        <style>
            #offcanvasSparePartAdd {
                width: 850px !important;
            }

            #offcanvasSparePartEdit {
                width: 850px !important;
            }

            .table.roles td, .table.roles th {
              vertical-align: top !important;
              padding: 12px 16px;
            }
            .table.roles .form-check {
              margin-bottom: 4px;
            }
            .table.roles .form-check:last-child {
              margin-bottom: 0;
            }
            .table.roles label {
              font-weight: 400;
              color: #212529;
            }
            .table.roles b {
              color: #344054;
              font-weight: 600;
            }
            .table.roles th {
              background-color: #f9fafb;
              font-weight: 600;
              text-transform: uppercase;
              font-size: 13px;
            }
        </style>
</body>
</html>