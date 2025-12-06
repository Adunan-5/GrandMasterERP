<?php
$PAGE_ID = "CAPABILITY_USER_LIST";
include_once __DIR__ . "/../../../includes/baseIncludes.php";
include_once __DIR__ . "/../../../includes/auth_check.php";
?>
<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
<head>
    <?php include_once __DIR__ . "/../../../includes/dashboard/dashboard_head_section.php"; ?>
    <title>GrandMaster ERP | Users</title>
    <link rel="stylesheet" href="/assets/vendor/libs/bootstrap-select/bootstrap-select.css"/>
    <link rel="stylesheet" href="/assets/vendor/libs/animate-css/animate.css"/>
    <link rel="stylesheet" href="/assets/vendor/libs/sweetalert2/sweetalert2.css"/>
    <link rel="stylesheet" href="/assets/vendor/libs/flatpickr/flatpickr.css"/>
    <?php include_once __DIR__ . "/../../../includes/dashboard/dashboard_head_section_post_vendor.php"; ?>
</head>

<body>
<!-- Layout wrapper -->
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <!-- Menu -->

        <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
            <?php include_once __DIR__ . "/../../../includes/dashboard/menu_ceo.php" ?>
        </aside>
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
            <!-- Navbar -->

            <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
                <?php include_once __DIR__ . "/../../../includes/dashboard/top_navbar.php"; ?>
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
                                    <h4 class="my-0">User Permissions Management</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product List Table -->
                    <div class="card">
                        <div class="card-datatable table-responsive">
                            <table class="datatables-products table">
                                <thead class="border-top">
                                    <tr>
                                        <th>ID</th>
                                        <th>User Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

                    <!-- Edit User Permission Modal -->
                    <div class="modal fade" id="editUserPermissionModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-simple modal-dialog-centered modal-edit-user-permission">
                            <div class="modal-content">
                            <div class="modal-body">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                                <div class="text-center mb-6">
                                <h4 class="role-title mb-2">Edit User Permissions</h4>
                                <p>Modify user’s capabilities based on their assigned role</p>
                                </div>

                                <form id="editUserPermissionForm" class="row g-6" onsubmit="return false">
                                <input type="hidden" id="permUserId" name="userId" />

                                <div class="col-md-6">
                                    <label class="form-label fw-medium">User Name</label>
                                    <input type="text" id="permUserName" class="form-control" readonly />
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Assigned Role</label>
                                    <select id="modalEditUserRole" name="modalEditUserRole" class="form-select">
                                    <option value="">Select a Role</option>
                                    <?php
                                    $companyId = intval($_SESSION['SES_SELECTED_COMPANY']);
                                    $res = $db->query("SELECT * FROM user_roles WHERE roleId !=1");
                                    while ($row = mysqli_fetch_assoc($res)) { ?>
                                        <option value="<?= $row['roleId'] ?>"><?= htmlspecialchars($row['roleName']) ?></option>
                                    <?php } ?>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <h5 class="mb-6 mt-4">User Capabilities</h5>

                                    <div class="table-responsive">
                                    <table class="table table-bordered roles no-margin align-middle" id="userPermissionTable">
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
                                                <input class="form-check-input" type="checkbox" id="selectAllUserPerms" />
                                                <label class="form-check-label fw-medium" for="selectAllUserPerms">Select All</label>
                                                <i class="ti ti-info-circle ms-2 text-muted" data-bs-toggle="tooltip"
                                                    title="Grants full access for this user (overrides role-level permissions)"></i>
                                                </div>
                                            </div>
                                            </td>
                                        </tr>

                                        <!-- Quotations -->
                                        <tr data-module="quotations">
                                            <td><b>Quotations</b></td>
                                            <td>
                                            <div class="ms-3">
                                                <div class="form-check mb-1">
                                                <input class="form-check-input user-cap" type="checkbox" data-module="quotations" value="view" id="qView">
                                                <label class="form-check-label" for="qView">View</label>
                                                </div>
                                                <div class="form-check mb-1">
                                                <input class="form-check-input user-cap" type="checkbox" data-module="quotations" value="create" id="qCreate">
                                                <label class="form-check-label" for="qCreate">Create</label>
                                                </div>
                                                <div class="form-check mb-1">
                                                <input class="form-check-input user-cap" type="checkbox" data-module="quotations" value="edit" id="qEdit">
                                                <label class="form-check-label" for="qEdit">Edit</label>
                                                </div>
                                                <div class="form-check">
                                                <input class="form-check-input user-cap" type="checkbox" data-module="quotations" value="cancel" id="qCancel">
                                                <label class="form-check-label" for="qCancel">Cancel</label>
                                                </div>
                                            </div>
                                            </td>
                                        </tr>

                                        <!-- Customers -->
                                        <tr data-module="customers">
                                            <td><b>Customers</b></td>
                                            <td>
                                            <div class="ms-3">
                                                <div class="form-check mb-1">
                                                <input class="form-check-input user-cap" type="checkbox" data-module="customers" value="view" id="cView">
                                                <label class="form-check-label" for="cView">View</label>
                                                </div>
                                                <div class="form-check mb-1">
                                                <input class="form-check-input user-cap" type="checkbox" data-module="customers" value="create" id="cCreate">
                                                <label class="form-check-label" for="cCreate">Create</label>
                                                </div>
                                                <div class="form-check">
                                                <input class="form-check-input user-cap" type="checkbox" data-module="customers" value="edit" id="cEdit">
                                                <label class="form-check-label" for="cEdit">Edit</label>
                                                </div>
                                            </div>
                                            </td>
                                        </tr>

                                        <!-- Items / Products -->
                                        <tr data-module="itemsOrProducts">
                                            <td><b>Items / Products</b></td>
                                            <td>
                                            <div class="ms-3">
                                                <div class="form-check mb-1">
                                                <input class="form-check-input user-cap" type="checkbox" data-module="itemsOrProducts" value="view" id="iView">
                                                <label class="form-check-label" for="iView">View</label>
                                                </div>
                                                <div class="form-check mb-1">
                                                <input class="form-check-input user-cap" type="checkbox" data-module="itemsOrProducts" value="create" id="iCreate">
                                                <label class="form-check-label" for="iCreate">Create</label>
                                                </div>
                                                <div class="form-check">
                                                <input class="form-check-input user-cap" type="checkbox" data-module="itemsOrProducts" value="edit" id="iEdit">
                                                <label class="form-check-label" for="iEdit">Edit</label>
                                                </div>
                                            </div>
                                            </td>
                                        </tr>

                                        <!-- Roles & Permissions -->
                                        <tr data-module="rolesAndPermissions">
                                            <td><b>Roles & Permissions</b></td>
                                            <td>
                                            <div class="ms-3">
                                                <div class="form-check mb-1">
                                                <input class="form-check-input user-cap" type="checkbox" data-module="rolesAndPermissions" value="view" id="rView">
                                                <label class="form-check-label" for="rView">View</label>
                                                </div>
                                                <div class="form-check mb-1">
                                                <input class="form-check-input user-cap" type="checkbox" data-module="rolesAndPermissions" value="create" id="rCreate">
                                                <label class="form-check-label" for="rCreate">Create</label>
                                                </div>
                                                <div class="form-check">
                                                <input class="form-check-input user-cap" type="checkbox" data-module="rolesAndPermissions" value="edit" id="rEdit">
                                                <label class="form-check-label" for="rEdit">Edit</label>
                                                </div>
                                            </div>
                                            </td>
                                        </tr>

                                        </tbody>
                                    </table>
                                    </div>
                                </div>

                                <div class="col-12 text-center mt-4">
                                    <button type="submit" class="btn btn-primary me-3">Save Changes</button>
                                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                                </div>

                                </form>
                            </div>
                            </div>
                        </div>
                    </div>
                <!-- / Content -->

                <!-- Footer -->
                <footer class="content-footer footer bg-footer-theme">
                    <?php include_once __DIR__ . "/../../../includes/dashboard/dashboard_footer_section.php"; ?>
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
include_once __DIR__ . "/../../../includes/dashboard/dashboard_footer_scripts.php";
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
        columns: [
          // columns according to JSON
          { data: 'userId' },
          { data: 'userName' },
          { data: 'email' },
          { data: 'roleName' },
          { data: null}
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
            // Actions
            targets: -1,
            title: 'Edit',
            searchable: false,
            orderable: false,
            render: function (data, type, full, meta) {
              var $userId = full['userId'];

              return `
                <button class="btn btn-icon btn-sm btn-text-secondary waves-effect waves-light rounded-pill edit-user-permissions"
                data-user-id="${full.userId}"
                data-user-name="${full.userName}"
                data-user-role="${full.roleId}">
                <i class="ti ti-edit ti-md"></i>
                </button>`;
            }
          },
          
        ],
        order: [[0, 'asc']], //set any columns order asc/desc
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
        //   {
        //     text: '<i class="ti ti-plus me-0 me-sm-1 mb-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add User</span>',
        //     className: 'add-new btn btn-primary waves-effect waves-light',
        //     attr: {
        //       'data-bs-toggle': 'offcanvas',
        //       'data-bs-target': '#offcanvasUserAdd'
        //     }
        //   }
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
            });
          // Adding stock filter once table initialized
          this.api()
            .columns(4)
            .every(function () {
              var column = this;
            });
        }
      });
      $('.dataTables_length').addClass('mx-n2');
      $('.dt-buttons').addClass('d-flex flex-wrap mb-6 mb-sm-0');
    }

    function resetAllCapabilities() {
        $('#userPermissionTable input.user-cap').prop('checked', false);
    }

    // Fill checkboxes according to a permission object (from role or merged user)
    function applyCapabilities(permissionData) {
        resetAllCapabilities();

        for (let module in permissionData) {
            const caps = permissionData[module];
            caps.forEach(cap => {
                if (cap.checked) {
                    $(`#userPermissionTable input.user-cap[data-module='${module}'][value='${cap.name}']`).prop('checked', true);
                }
        });
        }
    }

    $(document).on('change', '#selectAllUserPerms', function () {
        const checked = $(this).is(':checked');
        // Toggle all capability checkboxes inside the table
        $('#userPermissionTable input.user-cap').prop('checked', checked);
    });

    $(document).on('click', '.edit-user-permissions', function() {
        const userId = $(this).data('user-id');
        const userName = $(this).data('user-name');

        $('#permUserId').val(userId);
        $('#permUserName').val(userName);

        $.ajax({
            url: '/ajax/settings/usermanagement/users/get_permissions.php',
            type: 'POST',
            data: { userId },
            dataType: 'json',
            success: function(response) {
            if (response.status === 'SUCCESS') {
                // Set role dropdown
                $('#modalEditUserRole').val(response.roleId);

                // Apply role + user overrides
                applyCapabilities(response.modules);

                // Show modal
                $('#editUserPermissionModal').modal('show');
            } else {
                Swal.fire('Error', response.message, 'error');
            }
            },
            error: function() {
            Swal.fire('Error', 'Could not load permissions', 'error');
            }
        });
    });

    // When role selection changes manually
    $('#modalEditUserRole').on('change', function() {
        const roleId = $(this).val();
        if (!roleId) return;

        $.ajax({
            url: '/ajax/roles/get_role_permissions.php',
            type: 'POST',
            data: { roleId },
            dataType: 'json',
            success: function(response) {
            if (response.status === 'SUCCESS') {
                applyCapabilities(response.modules);
            } else {
                Swal.fire('Error', response.message, 'error');
            }
            }
        });
    });

    // Save User Permissions
    $('#editUserPermissionForm').on('submit', function(e) {
        e.preventDefault();

        const userId = $('#permUserId').val();
        const roleId = $('#modalEditUserRole').val();
        const permissions = {};

        $('#userPermissionTable input.user-cap').each(function() {
            const module = $(this).data('module');
            const cap = $(this).val();
            if (!permissions[module]) permissions[module] = [];
            if ($(this).is(':checked')) permissions[module].push(cap);
        });

        $.ajax({
            url: '/ajax/settings/usermanagement/users/save_permissions.php',
            type: 'POST',
            data: { userId, roleId, permissions: JSON.stringify(permissions) },
            beforeSend: () => blockArea($('.modal-content')),
            success: function(data) {
            unBlockArea($('.modal-content'));
            const [status, msg] = data.trim().split('|');
            Swal.fire({
                title: status === 'SUCCESS' ? 'Saved!' : 'Error',
                text: msg,
                icon: status === 'SUCCESS' ? 'success' : 'error',
                customClass: { confirmButton: 'btn btn-primary' }
            }).then(() => {
                if (status === 'SUCCESS') {
                $('#editUserPermissionModal').modal('hide');
                $('.datatables-products').DataTable().ajax.reload();
                }
            });
            },
            error: function() {
            unBlockArea($('.modal-content'));
            Swal.fire('Error', 'Could not save permissions', 'error');
            }
        });
    });

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



</script>
<style>
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
 