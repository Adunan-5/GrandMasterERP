<?php
$PAGE_ID = "WAREHOUSE_LIST";
include_once __DIR__ . "/../../includes/baseIncludes.php";
include_once __DIR__ . "/../../includes/auth_check.php";
?>
<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
<head>
    <?php include_once __DIR__ . "/../../includes/dashboard/dashboard_head_section.php"; ?>
    <title>GrandMaster ERP | Warehouses</title>
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
                    <!-- Product List Widget -->
                    <div class="card mb-6">
                        <div class="card-widget-separator-wrapper">
                            <div class="card-body card-widget-separator">
                                <div class="row gy-4 gy-sm-1">
                                    <div class="col-sm-6 col-lg-3">
                                        <div class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-4 pb-sm-0">
                                            <div>
                                                <p class="mb-1">In-store Sales</p>
                                                <h4 class="mb-1">$5,345.43</h4>
                                                <p class="mb-0">
                                                    <span class="me-2">5k orders</span><span class="badge bg-label-success">+5.7%</span>
                                                </p>
                                            </div>
                                            <span class="avatar me-sm-6">
                            <span class="avatar-initial rounded"><i class="ti-28px ti ti-smart-home text-heading"></i></span>
                          </span>
                                        </div>
                                        <hr class="d-none d-sm-block d-lg-none me-6"/>
                                    </div>
                                    <div class="col-sm-6 col-lg-3">
                                        <div class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-4 pb-sm-0">
                                            <div>
                                                <p class="mb-1">Website Sales</p>
                                                <h4 class="mb-1">$674,347.12</h4>
                                                <p class="mb-0">
                                                    <span class="me-2">21k orders</span><span class="badge bg-label-success">+12.4%</span>
                                                </p>
                                            </div>
                                            <span class="avatar p-2 me-lg-6">
                            <span class="avatar-initial rounded"><i class="ti-28px ti ti-device-laptop text-heading"></i></span>
                          </span>
                                        </div>
                                        <hr class="d-none d-sm-block d-lg-none"/>
                                    </div>
                                    <div class="col-sm-6 col-lg-3">
                                        <div class="d-flex justify-content-between align-items-start border-end pb-4 pb-sm-0 card-widget-3">
                                            <div>
                                                <p class="mb-1">Discount</p>
                                                <h4 class="mb-1">$14,235.12</h4>
                                                <p class="mb-0">6k orders</p>
                                            </div>
                                            <span class="avatar p-2 me-sm-6">
                            <span class="avatar-initial rounded"><i class="ti-28px ti ti-gift text-heading"></i></span>
                          </span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-lg-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <p class="mb-1">Affiliate</p>
                                                <h4 class="mb-1">$8,345.23</h4>
                                                <p class="mb-0">
                                                    <span class="me-2">150 orders</span><span class="badge bg-label-danger">-3.5%</span>
                                                </p>
                                            </div>
                                            <span class="avatar p-2">
                            <span class="avatar-initial rounded"><i class="ti-28px ti ti-wallet text-heading"></i></span>
                          </span>
                                        </div>
                                    </div>
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
                        <!-- <div class="card-datatable table-responsive"> -->
                        <div class="card">
                            <h5 class="card-header">Warehouse Lists</h5>
                            <div class="table-responsive text-nowrap">
                                <table class="datatables-products table">
                                    <thead class="table-light">
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>ID</th>
                                        <th style="white-space: nowrap;">Warehouse Name</th>
                                        <th>Warehouse Code</th>
                                        <th>Address</th>
                                        <th>City</th>
                                        <th>State</th>
                                        <th>Country</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <!-- Offcanvas to add new Warehouse -->
                        <div class="offcanvas offcanvas-end form-block" tabindex="-1" id="offcanvasWarehouseAdd" aria-labelledby="offcanvasEcommerceWarehouseAddLabel">
                            <div class="offcanvas-header">
                                <h5 id="offcanvasEcommerceWarehouseAddLabel" class="offcanvas-title">Add Warehouse</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body border-top mx-0 flex-grow-0">
                                <form class="ecommerce-warehouse-add pt-0" id="warehouseAddForm" method="POST" name="warehouseAddForm" action="/warehouses/list" onsubmit="return false;">
                                    <div class="ecommerce-warehouse-add-basic mb-4">
                                        <div class="mb-6">
                                            <label class="form-label" for="warehouseName">Warehouse Name*
                                            </label>
                                            <input type="text" class="form-control" id="warehouseName" placeholder="Enter Warehouse Name" name="warehouseName" aria-label="Warehouse Name"/>
                                        </div>
                                        <div class="mb-6">
                                            <label class="form-label" for="warehouseCode">Warehouse Code</label>
                                            <input type="text" class="form-control" id="warehouseCode" placeholder="Enter Warehouse Code" name="warehouseCode" aria-label="Warehouse Code" required/>
                                        </div>
<!--                                      <div class="ecommerce-supplier-add-shiping mb-6">-->
                                        <h6 class="mb-6">Address Information</h6>
                                        <div class="mb-6">
                                          <label class="form-label" for="warehouseAddress1">Address Line 1</label>
                                          <input type="text" id="warehouseAddress1" class="form-control" placeholder="45 Roker Terrace" aria-label="45 Roker Terrace" name="warehouseAddress1"/>
                                        </div>
                                        <div class="mb-6">
                                          <label class="form-label" for="warehouseAddress2">Address Line 2</label>
                                          <input type="text" id="warehouseAddress2" class="form-control" aria-label="address2" name="warehouseAddress2"/>
                                        </div>
                                        <div class="row">
                                          <div class=" mb-6">
                                            <label class="form-label" for="warehouseCountry">Country*
                                            </label>
                                            <select id="warehouseCountry" name="warehouseCountry" class="selectpicker form-select w-100" data-style="btn-default" data-live-search="true" onchange="populateStateForSelectedCountry(this.value, 'warehouseState', 'warehouseCity')">
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
                                          <div class="col-6 mb-6">
                                            <label class="form-label" for="warehouseState">State / Province*</label>
                                            <select id="warehouseState" name="warehouseState" class="selectpicker w-100" data-style="btn-default" data-live-search="true" onchange="populateCityForSelectedState(this.value, 'warehouseCity')">
                                              <option value="">Select a country first</option>
                                            </select>
                                          </div>
                                          <div class="mb-6 col-6">
                                            <label class="form-label" for="warehouseCity">City*</label>
                                            <select id="warehouseCity" name="warehouseCity" class="selectpicker w-100" data-style="btn-default" data-live-search="true">
                                              <option data-tokens="" value="">Select a state first</option>
                                            </select>
                                          </div>
                                          <div class="col-12 mb-6">
                                            <label class="form-label" for="warehousePostalCode">Postal Code</label>
                                            <input type="text" id="warehousePostalCode" class="form-control" placeholder="734990" aria-label="734990" name="warehousePostalCode" pattern="[0-9]{8}" maxlength="8"/>
                                          </div>
                                          <div class="col-12 mb-6">
                                            <label class="form-label" for="warehouseEmail">Email</label>
                                            <input type="text" id="warehouseEmail" class="form-control" placeholder="warehouse@ggm.com.co" aria-label="warehouse@ggm.com.co" name="warehouseEmail"/>
                                          </div>
                                          <div class="col-12 mb-6">
                                            <label class="form-label" for="ecommerce-warehouse-add-contact">Mobile
                                            </label>
                                            <input type="text" id="ecommerce-warehouse-add-contact" class="form-control phone-mask" placeholder="+(123) 456-7890" aria-label="+(123) 456-7890" name="warehouseContact"/>
                                          </div>
                                        </div>
<!--                                      </div>-->
                                    </div>
                                    <div>
                                        <button class="btn btn-primary me-sm-4 data-submit">Add Warehouse</button>
                                        <button type="reset" class="btn btn-label-danger" data-bs-dismiss="offcanvas">
                                            Discard
                                        </button>
                                    </div>
                                </form>
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
        <?php
        include_once __DIR__ . "/../../includes/dashboard/dashboard_footer_scripts.php";
        ?>

        <script>
            $(".selectpicker").selectpicker();

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
                var dt_warehouse_table = $('.datatables-products'),
                    select2 = $('.select2'),
                    warehouseView = '/warehouse/edit/';
                if (select2.length) {
                    var $this = select2;
                    $this.wrap('<div class="position-relative"></div>').select2({
                        placeholder: 'United States ',
                        dropdownParent: $this.parent()
                    });
                }

                // E-commerce Products datatable

                if (dt_warehouse_table.length) {
                    var dt_warehouses = dt_warehouse_table.DataTable({
                        ajax: '/ajax/warehouses/fetch_warehouses.php',
                        columns: [
                            // columns according to JSON
                            {data: null},
                            {data: null},
                            {data: 'warehouseId'},
                            {data: 'warehouseName'},
                            {data: 'warehouseCode'},
                            {data: 'addressLine1'},
                            {data: 'city'},
                            {data: 'state'},
                            {data: 'country'},
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
                                responsivePriority: 3,
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
                                var $warehouseId = full['warehouseId'];

                                return (
                                  '<div class="d-flex align-items-center">' +
                                  '<a href="/warehouse/edit/' + $warehouseId + '" data-bs-toggle="tooltip" class="btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill" data-bs-placement="top" title="Edit"><i class="ti ti-edit mx-2 ti-md"></i></a>' +
                                  '</div>'
                                );
                              }
                            },
                            {
                                // warehouse id
                                targets: 2,
                                // width:70,
                                visible:false,
                                render: function (data, type, full, meta) {
                                    var $warehouseId = full['warehouseId'];

                                    return '<span>' + $warehouseId + '</span>';
                                }
                            },
                            {
                                // warehouse name
                                targets: 3,
                                // width:70,
                                render: function (data, type, full, meta) {
                                    var $warehousename = full['warehouseName'];
                                    var warehouseId = full['warehouseId'];

                                    return (
                                        '<span style="white-space: nowrap;">' +
                                        '<a href="' + warehouseView + warehouseId + '" class="text-heading">' +
                                        '<span class="fw-medium">' + $warehousename + '</span>' +
                                        '</a>' +
                                        '</span>'
                                    );
                                }
                            },
                            {
                                // warehouse code
                                targets: 4,
                                // width:70,
                                render: function (data, type, full, meta) {
                                    var $warehousecode = full['warehouseCode'];

                                    return '<span>' + $warehousecode + '</span>';
                                }
                            },

                          {
                            // warehouse Address
                            targets: 5,
                            // width:70,
                            render: function (data, type, full, meta) {
                              var $warehouseAddress = full['addressLine1'] + ' ' + full['addressLine2'];

                              return '<span>' + $warehouseAddress + '</span>';
                            }
                          },
                            {
                              // Warehouse City
                              targets: 6,
                              render: function (data, type, full, meta) {
                                var $city = full['city'];
                                if ($city == null) $city = "-";
                                return "<span class='text-heading text-nowrap'>" + $city + '</span>';

                              }

                            },
                            {
                              // Warehouse State
                              targets: 7,
                              render: function (data, type, full, meta) {
                                var $state = full['state'];
                                if ($state == null) {
                                  $state = "-";
                                }
                                return "<span class='text-heading text-nowrap'>" + $state + '</span>';

                              }

                            },
                            {
                              // Warehouse Country
                              targets: 8,
                              render: function (data, type, full, meta) {
                                var $country = full['country'];
                                if ($country == null) $country = "-";
                                // var $code = full['country_code'];

                                var $code = 'sa';

                                if ($code) {
                                  var $output_code = `<i class ="fis fi fi-${$code} rounded-circle me-2 fs-4"></i>`;
                                } else {
                                  // For Avatar badge
                                  var $output_code = `<i class ="fis fi fi-xx rounded-circle me-2 fs-4"></i>`;
                                }

                                var $row_output =
                                  '<div class="d-flex justify-content-start align-items-center supplier-country">' +
                                  '<div>' +
                                  $output_code +
                                  '</div>' +
                                  '<div>' +
                                  '<span>' +
                                  $country +
                                  '</span>' +
                                  '</div>' +
                                  '</div>';
                                return $row_output;

                              }

                            },

                            
                        ],
                        order: [[2, 'asc']],
                        dom:
                            '<"card-header d-flex flex-wrap flex-md-row flex-column align-items-start align-items-sm-center py-0"' +
                            '<"d-flex align-items-center me-5"f>' +
                            '<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-md-end flex-wrap flex-sm-nowrap mb-6 mb-sm-0"lB>' +
                            '>t' +
                            '<"row mx-1"' +
                            '<"col-sm-12 col-md-6"i>' +
                            '<"col-sm-12 col-md-6"p>' +
                            '>',
                        // lengthMenu: [7, 10, 20, 50, 70, 100], //for length of menu
                        language: {
                            sLengthMenu: '_MENU_',
                            search: '',
                            searchPlaceholder: 'Search Warehouse',
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
                                            columns: [2, 3],
                                            format: {
                                                body: function (inner, coldex, rowdex) {
                                                    if (inner.length <= 0) return inner;
                                                    var el = $.parseHTML(inner);
                                                    var result = '';
                                                    $.each(el, function (index, item) {
                                                        if (item.classList !== undefined && item.classList.contains('spartpart-name')) {
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
                                            columns: [2, 3],
                                            format: {
                                                body: function (inner, coldex, rowdex) {
                                                    if (inner.length <= 0) return inner;
                                                    var el = $.parseHTML(inner);
                                                    var result = '';
                                                    $.each(el, function (index, item) {
                                                        if (item.classList !== undefined && item.classList.contains('spartpart-name')) {
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
                                            columns: [2, 3],
                                            format: {
                                                body: function (inner, coldex, rowdex) {
                                                    if (inner.length <= 0) return inner;
                                                    var el = $.parseHTML(inner);
                                                    var result = '';
                                                    $.each(el, function (index, item) {
                                                        if (item.classList !== undefined && item.classList.contains('spartpart-name')) {
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
                                            columns: [2, 3],
                                            format: {
                                                body: function (inner, coldex, rowdex) {
                                                    if (inner.length <= 0) return inner;
                                                    var el = $.parseHTML(inner);
                                                    var result = '';
                                                    $.each(el, function (index, item) {
                                                        if (item.classList !== undefined && item.classList.contains('spartpart-name')) {
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
                                            columns: [2, 3],
                                            format: {
                                                body: function (inner, coldex, rowdex) {
                                                    if (inner.length <= 0) return inner;
                                                    var el = $.parseHTML(inner);
                                                    var result = '';
                                                    $.each(el, function (index, item) {
                                                        if (item.classList !== undefined && item.classList.contains('spartpart-name')) {
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
                                text: '<i class="ti ti-plus me-0 me-sm-1 mb-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add Warehouse</span>',
                                className: 'add-new btn btn-primary waves-effect waves-light',
                                attr: {
                                    'data-bs-toggle': 'offcanvas',
                                    'data-bs-target': '#offcanvasWarehouseAdd'
                                }
                            }
                        ],
                        // For responsive popup
                        responsive: {
                            details: {
                                display: $.fn.dataTable.Responsive.display.modal({
                                    header: function (row) {
                                        var data = row.data();
                                        return 'Details of ' + data['warehouseName'];
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

                // Delete Record
                $('.datatables-products tbody').on('click', '.delete-record', function () {
                    dt_warehouses.row($(this).parents('tr')).remove().draw();
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
                    const offcanvasElement = document.getElementById('offcanvasWarehouseAdd');
                    if (offcanvasElement) {
                        const offcanvasInstance = new bootstrap.Offcanvas(offcanvasElement);
                        offcanvasInstance.show();
                    } else {
                        console.error("Offcanvas element not found.");
                    }
                }
            });

            $(document).ready(function () {
                // Form Validation
                const WarehouseAddForm = document.getElementById('warehouseAddForm');

                // Add New Warehouse Form Validation
                const fv = FormValidation.formValidation(WarehouseAddForm, {
                    fields: {
                        warehouseName: {
                            validators: {
                                notEmpty: {
                                    message: 'Please enter Warehouse Name'
                                }
                            }
                        },
                      warehouseCountry: {
                        validators: {
                          notEmpty: {
                            message: 'Please select the Country'
                          }
                        }
                      },
                      warehouseState: {
                        validators: {
                          notEmpty: {
                            message: 'Please select the State'
                          }
                        }
                      },
                      warehouseCity: {
                        validators: {
                          notEmpty: {
                            message: 'Please select the City'
                          }
                        }
                      },
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

                // Submit form via AJAX
                $("#warehouseAddForm").submit(function (e) {
                    e.preventDefault();

                    fv.validate().then(function (status) {
                        if (status === 'Valid') {
                            blockArea($('.form-block'));

                            var form = $('#warehouseAddForm')[0]; // Standard JavaScript object
                            var formData = new FormData(form);

                            $.ajax({
                                url: '/ajax/warehouses/add_warehouse.php',
                                type: 'POST',
                                data: formData,
                                contentType: false,
                                processData: false,
                                success: function (data) {
                                    console.log(data);
                                    unBlockArea($('.form-block'));
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
                                            dismissOffcanvas('offcanvasWarehouseAdd');
                                            blockArea($('body'));
                                            location.reload();
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
                                }
                            });
                        }
                    });

                    return false;
                });
            });
        </script>
        <style>
            #offcanvasWarehouseAdd {
                width: 850px !important;
            }

            #offcanvasWarehouseEdit {
                width: 850px !important;
            }
        </style>
</body>
</html>