<?php
$PAGE_ID = "INVENTORY_LIST";
include_once __DIR__ . "/../../includes/baseIncludes.php";
include_once __DIR__ . "/../../includes/auth_check.php";

$selectedWarehouse = null;

if(isset($_POST['warehouseLocation']) && !empty($_POST['warehouseLocation']))
{
    $selectedWarehouse = filter_var($_POST['warehouseLocation'], FILTER_SANITIZE_SPECIAL_CHARS);
}


?>
<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
<head>
    <?php include_once __DIR__ . "/../../includes/dashboard/dashboard_head_section.php"; ?>
    <title>GrandMaster ERP | Inventory</title>
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
                        <!-- <h5 class="card-title">Inventory</h5> -->
                        <!-- <div class="d-flex justify-content-between align-items-center row pt-4 gap-6 gap-md-0">
                            <div class="col-md-4 product_status"></div>
                            <div class="col-md-4 product_category"></div>
                            <div class="col-md-4 product_stock"></div>
                        </div> -->
                        <!-- </div> -->
                        <!-- <div class="card-datatable table-responsive"> -->
                        <div class="card">
                            <div class="card-header">
                                <div class="text-center">
                                    <h4><strong>Inventory</strong><h4>
                                </div>

                                <form  name="warehouseFilterForm" id="warehouseFilterForm" method="post">
                                    <div class="d-flex justify-content-between align-items-center row pt-4 gap-6 gap-md-0">
                                        <!-- Select Warehouse Location Dropdown -->
                                        <div class="col-md-4 product_status">

                                            <label for="warehouseLocation">Select Warehouse Location</label>
                                            <select id="warehouseLocation" name="warehouseLocation" class="form-select selectpicker form-select w-auto ms-3" data-style="btn-default" data-live-search="true">
                                                <option value="0">All</option>
                                                <?php
                                                $res = $db->query("SELECT * FROM warehouses");
                                                while ($row = mysqli_fetch_assoc($res)) {
                                                    ?>
                                                    <option <?php if($selectedWarehouse == $row['warehouseId']) echo 'selected'; ?> value="<?= $row['warehouseId'] ?>"><?= $row['warehouseName'] ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <!-- Add Warehouse Button -->
                                        <div class="col-md-4 text-md-end">
                                            <button class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvasWarehouseAdd" onclick="return false;">
                                                <i class="ti ti-plus me-0 me-sm-1 mb-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add Warehouse</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <!--  <div class="table-responsive text-nowrap">-->
                            <div class="">
                                <table class="datatables-products table">
                                    <thead class="table-light">
                                    <tr>
                                        <!-- <th></th>
                                        <th></th> -->
                                        <th>Item</th>
                                        <th>Intern#</th>
                                        <th>Description</th>
                                        <th>Unavailable</th>
                                        <th>Available</th>
                                        <th>On Hand</th>
                                        <th>Incoming</th>
                                        <th>Forecasted</th>
                                        <th>Bin</th>
                                        <th>Aisle</th>
                                        <th>Lot/Serial#</th>
                                        <th>Warehouse Location</th>
                                    </tr>
                                    </thead>
                                    <?php

                                    $filterQuery = '';


                                    if($selectedWarehouse != null)
                                    {
                                        $filterQuery .= "WHERE warehouseId = " . $selectedWarehouse ;
                                    }

                                    $res = $db->query("select * from inventory_stock $filterQuery");

                                    while ($row = mysqli_fetch_assoc($res)) {
                                        ?>
                                        <tr>
                                            <td class="text-nowrap">
                                                <?= getPartNumberForSparepartID($row['sparepartId']) ?>
                                            </td>
                                            <td>
                                                <?= getInternalReferenceNumberForSparepartID($row['sparepartId']) ?>
                                            </td>
                                            <td>
                                                <?= getItemDescriptionForSparepartID($row['sparepartId']) ?>
                                            </td>
                                            <td>
                                                0
                                            </td>
                                            <td>
                                                <?= getAvailableQtyForSparePart(sparepartID: $row['sparepartId'], warehouseID: $row['warehouseId']) ?>
                                            </td>
                                            <td>
                                                <?= getStockOnHandQtyForSparePart(sparepartID: $row['sparepartId'], warehouseID: $row['warehouseId']) ?>
                                            </td>
                                            <td>
                                                <?= getIncomingSpareparts($row['sparepartId'], $row['warehouseId'])['incomingQty'] ? : '-' ?>
                                            </td>
                                            <td>
                                                -
                                            </td>
                                            <td>
                                                <?=getSparepartLocationDetails($row['sparepartId'], $row['warehouseId'])['bin'] ? : '-' ?>
                                            </td>
                                            <td><?=getSparepartLocationDetails($row['sparepartId'], $row['warehouseId'])['aisle'] ? : '-' ?></td>
                                            <td><?=getSparepartLocationDetails($row['sparepartId'], $row['warehouseId'])['lotSerial'] ? : '-' ?></td>
                                            <td><?= getWarehouseInfoFromID($row['warehouseId'])['warehouseCode']; ?></td>
                                        </tr>
                                        <?php
                                    }


                                    ?>
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
                                            <label class="form-label" for="internalReference">Warehouse Name*
                                            </label>
                                            <input type="text" class="form-control" id="internalReference" placeholder="Enter Warehouse Name" name="warehouseName" aria-label="Warehouse Name"/>
                                        </div>
                                        <div class="mb-6">
                                            <label class="form-label" for="name">Warehouse Code</label>
                                            <input type="text" class="form-control" id="name" placeholder="Enter Warehouse Code" name="warehouseCode" aria-label="Warehouse Code" required/>
                                        </div>
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
        <!-- <script>
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
                                // warehouse id
                                targets: 2,
                                width:70,
                                visible:false,
                                render: function (data, type, full, meta) {
                                    var $warehouseId = full['warehouseId'];

                                    return '<span>' + $warehouseId + '</span>';
                                }
                            },
                            {
                                // warehouse name
                                targets: 3,
                                width:70,
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
                                width:70,
                                render: function (data, type, full, meta) {
                                    var $warehousecode = full['warehouseCode'];

                                    return '<span>' + $warehousecode + '</span>';
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
                                        return 'Details of ' + data['partNumber'];
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
        </script> -->
        <script>


            $("#warehouseLocation").change(function(e){
                e.stopPropagation();

                $("#warehouseFilterForm").submit();




            });

            $(".selectpicker").selectpicker();

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
                    select2 = $('.select2');
                if (select2.length) {
                    var $this = select2;
                    $this.wrap('<div class="position-relative"></div>').select2({
                        placeholder: 'United States ',
                        dropdownParent: $this.parent()
                    });
                }

                if (dt_warehouse_table.length) {
                    var dt_warehouses = dt_warehouse_table.DataTable({
                        "paging": false,               // Keep pagination
                        "searching": false,          // Disable search input
                        "lengthChange": false,       // Disable entries options (e.g. "10 entries")
                        "info": false,                // Keep the "Showing X of Y" info
                        "ordering": true             // Enable sorting if needed
                    });
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