<?php
$PAGE_ID = "CONSULTATION_TEAM_LIST";
include_once __DIR__ . "/../../includes/baseIncludes.php";
include_once __DIR__ . "/../../includes/auth_check.php";

$filter = filter_input(INPUT_GET, 'filter', FILTER_SANITIZE_SPECIAL_CHARS);
?>
<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
<head>
    <?php include_once __DIR__ . "/../../includes/dashboard/dashboard_head_section.php"; ?>
    <title>GrandMaster ERP | TEAM Members</title>
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
            <!-- Content wrapper -->
            <div class="content-wrapper">
                <!-- Content -->
                <div class="container-xxl flex-grow-1 container-p-y">
                    <div class="card mb-6">
                        <div class="card-widget-separator-wrapper">
                            <div class="card-body card-widget-separator py-2">
                                <div class="row gy-1 gy-sm-1">
                                    <h4 class="my-0">Members List</h4>
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
                                                <h4 class="mb-0">24</h4>
                                                <p class="mb-0">Active</p>
                                            </div>
                                            <div class="avatar me-sm-6">
                            <span class="avatar-initial rounded bg-label-secondary text-heading">
                              <i class="ti ti-user ti-26px"></i>
                            </span>
                                            </div>
                                        </div>
                                        <hr class="d-none d-sm-block d-lg-none me-6"/>
                                    </div>
                                    <div class="col-sm-6 col-lg-4">
                                        <div class="d-flex justify-content-between align-items-center card-widget-2 border-end pb-4 pb-sm-0">
                                            <div>
                                                <h4 class="mb-0">165</h4>
                                                <p class="mb-0">Confirmed</p>
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
                                                <h4 class="mb-0">SAR 2.46k</h4>
                                                <p class="mb-0">Total Value</p>
                                            </div>
                                            <div class="avatar me-sm-6">
                            <span class="avatar-initial rounded bg-label-secondary text-heading">
                              <i class="ti ti-checks ti-26px"></i>
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
                        <!--                        <div class="card-header">-->
                        <!--                            <h5 class="card-title">Filter</h5>-->
                        <!--                            <div class="d-flex justify-content-between align-items-center row pt-4 gap-6 gap-md-0">-->
                        <!--                                <div class="col-md-4 product_status">-->
                        <!--                                    <select id="ProductStatus" class="form-select text-capitalize">-->
                        <!--                                        <option value="">Status</option>-->
                        <!--                                        <option value="Scheduled">Scheduled</option>-->
                        <!--                                        <option value="Publish">Publish</option>-->
                        <!--                                        <option value="Inactive">Inactive</option>-->
                        <!--                                    </select>-->
                        <!--                                </div>-->
                        <!--                                <div class="col-md-4 product_category">-->
                        <!--                                    <select id="ProductCategory" class="form-select text-capitalize">-->
                        <!--                                        <option value="">Category</option>-->
                        <!--                                        <option value="Household">Household</option>-->
                        <!--                                        <option value="Office">Office</option>-->
                        <!--                                        <option value="Electronics">Electronics</option>-->
                        <!--                                        <option value="Shoes">Shoes</option>-->
                        <!--                                        <option value="Accessories">Accessories</option>-->
                        <!--                                        <option value="Game">Game</option>-->
                        <!--                                    </select>-->
                        <!--                                </div>-->
                        <!--                                <div class="col-md-4 product_stock">-->
                        <!--                                    <select id="ProductStock" class="form-select text-capitalize">-->
                        <!--                                        <option value=""> Stock</option>-->
                        <!--                                        <option value="Out_of_Stock">Out of Stock</option>-->
                        <!--                                        <option value="In_Stock">In Stock</option>-->
                        <!--                                    </select>-->
                        <!--                                </div>-->
                        <!--                                <div class="col-md-6 mt-10">-->
                        <!--                                    <button type="button" class="btn btn-label-primary">All</button>-->
                        <!--                                    <button type="button" class="btn btn-label-info">Active</button>-->
                        <!--                                    <button type="button" class="btn btn-label-success">Confirmed</button>-->
                        <!--                                    <button type="button" class="btn btn-label-warning">Created</button>-->
                        <!--                                    <button type="button" class="btn btn-label-danger">Cancelled</button>-->
                        <!---->
                        <!--                                </div>-->
                        <!--                            </div>-->
                        <!--                        </div>-->
                        <div class="card-datatable table-responsive">
                            <table class="invoice-list-table table border-top">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>User Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Last Seen</th>
                                    <th>Active</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
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
<!-- / Layout wrapper -->
<?php /*?>
<!-- Core JS -->
<!-- build:js assets/vendor/js/core.js -->
<script src="/assets/vendor/libs/jquery/jquery.js"></script>
<script src="/assets/vendor/libs/popper/popper.js"></script>
<script src="/assets/vendor/js/bootstrap.js"></script>
<script src="/assets/vendor/libs/node-waves/node-waves.js"></script>
<script src="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="/assets/vendor/libs/hammer/hammer.js"></script>
<script src="/assets/vendor/libs/i18n/i18n.js"></script>
<script src="/assets/vendor/libs/typeahead-js/typeahead.js"></script>
<script src="/assets/vendor/js/menu.js"></script>
<!-- endbuild -->
<!-- Vendors JS -->
<script src="/assets/vendor/libs/moment/moment.js"></script>
<script src="/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
<!-- Main JS -->
<script src="/assets/js/main.js"></script>
<?php */

include_once __DIR__ . "/../../includes/dashboard/dashboard_footer_scripts.php";
?>

<script>
    $(".selectpicker").selectpicker();

    'use strict';
    $(function () {
        // Variable declaration for table
        var dt_product_table = $('.invoice-list-table');

        // Quotation datatable
      if (dt_product_table.length) {
        var dt_products = dt_product_table.DataTable({
          ajax: '/ajax/settings/usermanagement/users/fetch_team_members.php',
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
              // User Image and Name
              targets: 2,
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
                  $row_output +
                  '</span>';
              }
            },

            {
              // Email
              targets: 3,
              render: function (data, type, full, meta) {
                var $email = full['email'];

                return '<span>' + $email + '</span>';
              }
            },
            {
              // Role
              targets: 4,
              render: function (data, type, full, meta) {
                var $roleName = full['roleName'];

                return '<span>' + $roleName + '</span>';
              }
            },
            {
              // Company
              targets: 5,
              render: function (data, type, full, meta) {
                // var $company = full['companyLabel'];

                return '<span>' + '-' + '</span>';
              }
            },

            {
              // Status
              targets: 6,
              render: function (data, type, full, meta) {
                var $active = full['active'];

                var badgeClass = $active === 'Active'
                  ? 'badge bg-label-success text-capitalized'
                  : 'badge bg-label-danger text-capitalized';

                return '<span class="' + badgeClass + '">' + $active + '</span>';
              }
            },

            {
              // Actions
              targets: -1,
              title: 'Actions',
              searchable: false,
              orderable: false,
              render: function (data, type, full, meta) {
                return (
                  '<div class="d-flex align-items-center">' +
                  '<a href="javascript:;" class="btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill delete-record"><i class="ti ti-trash ti-md"></i></a>' +
                  '<a href="' +
                  '" class="btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill"><i class="ti ti-eye ti-md"></i></a>' +
                  '<a href="javascript:;" class="btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-md"></i></a>' +
                  '<div class="dropdown-menu dropdown-menu-end m-0">' +
                  '<a href="javascript:;"" class="dropdown-item">Edit</a>' +
                  '<a href="javascript:;" class="dropdown-item">Suspend</a>' +
                  '</div>' +
                  '</div>'
                );
              }
            }
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

        // On each datatable draw, initialize tooltip
        invoice-list-table.on('draw.dt', function () {
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
</body>
</html>