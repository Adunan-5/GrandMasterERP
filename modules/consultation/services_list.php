<?php
$PAGE_ID = "CONSULTATION_SERVICES";
include_once __DIR__ . "/../../includes/baseIncludes.php";
include_once __DIR__ . "/../../includes/auth_check.php";

$filter = filter_input(INPUT_GET, 'filter', FILTER_SANITIZE_SPECIAL_CHARS);
?>
<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
<head>
    <?php include_once __DIR__ . "/../../includes/dashboard/dashboard_head_section.php"; ?>
    <title>GrandMaster ERP | Services</title>
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
                                    <h4 class="my-0">Services</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Top Summary Widget -->
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
                    <!-- Categories List Table -->
                    <div class="card">
                        <!--                                                <div class="card-header">-->
                        <!--                                                    <h5 class="card-title">Filter</h5>-->
                        <!--                                                    <div class="d-flex justify-content-between align-items-center row pt-4 gap-6 gap-md-0">-->
                        <!--                                                        <div class="col-md-4 product_status">-->
                        <!--                                                            <select id="ProductStatus" class="form-select text-capitalize">-->
                        <!--                                                                <option value="">Status</option>-->
                        <!--                                                                <option value="Scheduled">Scheduled</option>-->
                        <!--                                                                <option value="Publish">Publish</option>-->
                        <!--                                                                <option value="Inactive">Inactive</option>-->
                        <!--                                                            </select>-->
                        <!--                                                        </div>-->
                        <!--                                                        <div class="col-md-4 product_service">-->
                        <!--                                                            <select id="ProductService" class="form-select text-capitalize">-->
                        <!--                                                                <option value="">Service</option>-->
                        <!--                                                                <option value="Household">Household</option>-->
                        <!--                                                                <option value="Office">Office</option>-->
                        <!--                                                                <option value="Electronics">Electronics</option>-->
                        <!--                                                                <option value="Shoes">Shoes</option>-->
                        <!--                                                                <option value="Accessories">Accessories</option>-->
                        <!--                                                                <option value="Game">Game</option>-->
                        <!--                                                            </select>-->
                        <!--                                                        </div>-->
                        <!--                                                        <div class="col-md-4 product_stock">-->
                        <!--                                                            <select id="ProductStock" class="form-select text-capitalize">-->
                        <!--                                                                <option value=""> Stock</option>-->
                        <!--                                                                <option value="Out_of_Stock">Out of Stock</option>-->
                        <!--                                                                <option value="In_Stock">In Stock</option>-->
                        <!--                                                            </select>-->
                        <!--                                                        </div>-->
                        <!--                                                        <div class="col-md-6 mt-10">-->
                        <!--                                                            <button type="button" class="btn btn-label-primary">All</button>-->
                        <!--                                                            <button type="button" class="btn btn-label-info">Active</button>-->
                        <!--                                                            <button type="button" class="btn btn-label-success">Confirmed</button>-->
                        <!--                                                            <button type="button" class="btn btn-label-warning">Created</button>-->
                        <!--                                                            <button type="button" class="btn btn-label-danger">Cancelled</button>-->
                        <!---->
                        <!--                                                        </div>-->
                        <!--                                                    </div>-->
                        <!--                                                </div>-->
                        <div class="card-datatable table-responsive">
                            <table class="invoice-list-table table border-top">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>ID</th>
                                    <th>Item Name</th>
                                    <th>Description</th>
                                    <th>UOM</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Cost</th>
                                    <th>Status</th>
                                    <th class="cell-fit">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $res = $db->query("SELECT i.*, c.categoryName 
                                                    FROM consultation_services_items i
                                                    LEFT JOIN consultation_services_category c 
                                                    ON i.categoryId = c.categoryId");

                                while ($row = mysqli_fetch_assoc($res)) {
                                    ?>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td><?= $row['itemId'] ?></td>
                                        <td><?= $row['itemName'] ?></td>
                                        <!-- <td><?= $row['description'] ?></td> -->
                                        <td><?= nl2br(htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8', false)) ?></td>
                                        <td><?= $row['uom'] ?></td>
                                        <td><?= $row['categoryName'] ?></td>
                                        <td><?= $row['price'] ?></td>
                                        <td><?= $row['cost'] ?></td>
                                        <td><?php
                                            if ($row['active'] == 1) {
                                                ?>
                                                <span class="badge bg-label-success text-capitalized"> Active </span>
                                                <?php
                                            } else {
                                                ?>
                                                <span class="badge bg-label-danger text-capitalized"> Inactive </span>
                                                <?php
                                            }

                                            ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <a href="javascript:;" data-servicename="<?= $row['itemName'] ?>" data-servicedescription="<?= $row['description'] ?>" data-serviceid="<?= $row['itemId'] ?>" data-serviceuom="<?= $row['uom'] ?>" data-servicecategoryid="<?= $row['categoryId'] ?>" data-serviceprice="<?= $row['price'] ?>" data-servicecost="<?= $row['cost'] ?>" data-servicestatus="<?= $row['active'] ?>" data-bs-toggle="offcanvas" data-bs-target="#offcanvasServiceEdit" class="btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill serviceEditButton" data-bs-placement="top" title="Edit"><i class="ti ti-pencil mx-2 ti-md"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!--Add Form-->
                <div class="offcanvas offcanvas-end form-block" tabindex="-1" id="offcanvasServiceAdd">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title">Add Service</h5>
                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body border-top mx-0 flex-grow-0">
                        <form class="ecommerce-customer-add pt-0" id="serviceAddForm" method="POST" name="serviceAddForm" onsubmit="return false;">
                            <div class="ecommerce-customer-add-basic mb-4">
                                <div class="mb-6">
                                    <label class="form-label" for="serviceName">Service Name*</label>
                                    <input type="text" class="form-control" id="serviceName" placeholder="Service Name" name="serviceName" aria-label="Service Name"/>
                                </div>
                                <div class="mb-6">
                                    <label class="form-label" for="serviceDescription">Description</label>
                                    <!-- <input type="text" id="serviceDescription" class="form-control" placeholder="Description" aria-label="serviceDescription" name="serviceDescription"/> -->

                                    <textarea id="serviceDescription" class="form-control" placeholder="Description" aria-label="serviceDescription" name="serviceDescription" rows="8"></textarea>
                                </div>
                                <div class="mb-6">
                                    <label class="form-label" for="serviceUOM">UOM</label>
                                    <input type="text" id="serviceUOM" class="form-control" placeholder="UOM" aria-label="serviceUOM" name="serviceUOM"/>
                                </div>
                                <div class="mb-6">
                                    <label class="form-label" for="serviceCategory">Category</label>
                                    <select class="form-select selectpicker w-100" id="serviceCategory" name="serviceCategory" data-style="btn-default" data-live-search="true" required>
                                        <option value="">Select Category</option>
                                        <?php
                                        $res = $db->query("SELECT * FROM consultation_services_category");
                                        while ($row = mysqli_fetch_assoc($res)) {
                                            ?>
                                            <option value="<?= $row['categoryId'] ?>"><?= $row['categoryName'] ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-6">
                                    <label class="form-label" for="servicePrice">Price</label>
                                    <input type="number" id="servicePrice" class="form-control" placeholder="Price" aria-label="servicePrice" name="servicePrice"/>
                                </div>
                                <div class="mb-6">
                                    <label class="form-label" for="serviceCost">Cost</label>
                                    <input type="number" id="serviceCost" class="form-control" placeholder="Cost" aria-label="serviceCost" name="serviceCost"/>
                                </div>
                                <div class="mb-6">
                                    <label class="form-label" for="serviceStatus">Status</label>
                                    <select id="serviceStatus" name="serviceStatus" class="selectpicker w-100" data-style="btn-default" data-live-search="true">
                                        <option data-tokens="active" value="1">Active</option>
                                        <option data-tokens="inactive" value="0">Inactive</option>
                                    </select>
                                </div>
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
                <!--Edit Form-->
                <div class="offcanvas offcanvas-end form-block" tabindex="-1" id="offcanvasServiceEdit">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title">Edit Service</h5>
                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body border-top mx-0 flex-grow-0">
                        <form class="ecommerce-customer-add pt-0" id="serviceEditForm" method="POST" name="serviceEditForm" onsubmit="return false;">
                            <div class="ecommerce-customer-add-basic mb-4">
                                <div class="mb-6">
                                    <label class="form-label" for="editServiceID">Service ID</label>
                                    <input type="text" class="form-control" id="editServiceID" name="serviceID" readonly value=""/>
                                </div>
                                <div class="mb-6">
                                    <label class="form-label" for="serviceName">Service Name*</label>
                                    <input type="text" class="form-control" id="editServiceName" placeholder="App Development" name="serviceName" aria-label="App Development"/>
                                </div>
                                <div class="mb-6">
                                    <label class="form-label" for="serviceDescription">Description</label>
                                    <!-- <input type="text" id="editServiceDescription" class="form-control" placeholder="Description" aria-label="Description" name="serviceDescription"/> -->

                                    <textarea id="editServiceDescription" class="form-control" placeholder="Description" aria-label="Description" name="serviceDescription" rows="8"></textarea>
                                </div>
                                <div class="mb-6">
                                    <label class="form-label" for="serviceUOM">UOM</label>
                                    <input type="text" id="editServiceUOM" class="form-control" placeholder="UOM" aria-label="serviceUOM" name="serviceUOM"/>
                                </div>
                                <div class="mb-6">
                                    <label class="form-label" for="serviceCategory">Category</label>
                                    <select class="form-select w-100" id="editServiceCategory" name="serviceCategory" data-style="btn-default" required>
                                        <option value="">Select Category</option>
                                        <?php
                                        $res = $db->query("SELECT * FROM consultation_services_category WHERE active = 1");
                                        while ($row = mysqli_fetch_assoc($res)) {
                                            ?>
                                            <option value="<?=$row['categoryId'] ?>"><?= $row['categoryName'] ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-6">
                                    <label class="form-label" for="servicePrice">Price</label>
                                    <input type="number" id="editServicePrice" class="form-control" placeholder="Price" aria-label="servicePrice" name="servicePrice"/>
                                </div>
                                <div class="mb-6">
                                    <label class="form-label" for="serviceCost">Cost</label>
                                    <input type="number" id="editServiceCost" class="form-control" placeholder="Cost" aria-label="serviceCost" name="serviceCost"/>
                                </div>
                                <div class="mb-6">
                                    <label class="form-label" for="serviceStatus">Status</label>
                                    <select id="editServiceStatus" name="serviceStatus" class="form-select w-100" data-style="btn-default" data-live-search="true">
                                        <option data-tokens="active" value="1">Active</option>
                                        <option data-tokens="inactive" value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <button class="btn btn-primary me-sm-4 data-submit">Update</button>
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
    'use strict';
    $(function () {
        // Variable declaration for table
        var dt_invoice_table = $('.invoice-list-table');

        if (dt_invoice_table.length) {

            let ajaxURL = '/ajax/documents/fetch_quotations.php';

            let filter = '<?=$filter ?>';
            if (filter !== '') {
                ajaxURL += '?filter=' + filter;
            }

            var dt_invoice = dt_invoice_table.DataTable({

                columnDefs: [
                    {
                        // For Responsive
                        targets: 0,
                        visible: false,
                        className: 'control',
                        responsivePriority: 2,
                        searchable: false,
                        render: function (data, type, full, meta) {
                            return '';
                        }
                    },
                    {
                        // For Checkboxes
                        targets: 1,
                        visible: false,
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
                        title: 'Actions',
                        searchable: false,
                        orderable: false
                    }
                ],
                order: [[2, 'asc']],
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
                    searchPlaceholder: 'Search Service',
                    paginate: {
                        next: '<i class="ti ti-chevron-right ti-sm"></i>',
                        previous: '<i class="ti ti-chevron-left ti-sm"></i>'
                    }
                },
                // Buttons with Dropdown
                buttons: [
                    {
                        text: '<i class="ti ti-plus ti-xs me-md-2"></i><span class="d-md-inline-block d-none">New Service</span>',
                        className: 'btn btn-primary waves-effect waves-light',
                        attr: {
                            'data-bs-toggle': 'offcanvas',
                            'data-bs-target': '#offcanvasServiceAdd'
                        }
                    }
                ],
                // For responsive popup
                responsive: {
                    details: {
                        display: $.fn.dataTable.Responsive.display.modal({
                            header: function (row) {
                                var data = row.data();
                                return 'Details of ' + data['full_name'];
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
                    // Adding role filter once table initialized
                    // this.api()
                    //     .columns(8)
                    //     .every(function () {
                    //         var column = this;
                    //         var select = $(
                    //             '<select id="UserRole" class="form-select"><option value=""> Quotation Status </option></select>'
                    //         )
                    //             .appendTo('.invoice_status')
                    //             .on('change', function () {
                    //                 var val = $.fn.dataTable.util.escapeRegex($(this).val());
                    //                 column.search(val ? '^' + val + '$' : '', true, false).draw();
                    //             });
                    //
                    //         column
                    //             .data()
                    //             .unique()
                    //             .sort()
                    //             .each(function (d, j) {
                    //                 select.append('<option value="' + d + '" class="text-capitalize">' + d + '</option>');
                    //             });
                    //     });
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

    });

    $(document).ready(function () {
        //Form Validation
        const ServiceAddForm = document.getElementById('serviceAddForm');
        const ServiceEditForm = document.getElementById('serviceEditForm');
        const fv = FormValidation.formValidation(ServiceAddForm, {
            fields: {
                serviceName: {
                    validators: {
                        notEmpty: {
                            message: 'Please enter a service name'
                        }
                    }
                }
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap5: new FormValidation.plugins.Bootstrap5({
                    eleValidClass: '',
                    rowSelector: function (field, ele) {
                        // field is the field name & ele is the field element
                        return '.mb-6';
                    }
                }),
                submitButton: new FormValidation.plugins.SubmitButton(),
                // Submit the form when all fields are valid
                // defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                autoFocus: new FormValidation.plugins.AutoFocus()
            }
        });

        const editFormValidation = FormValidation.formValidation(serviceEditForm, {
            fields: {
                editServiceName: {
                    validators: {
                        notEmpty: {
                            message: 'Please enter a service name'
                        }
                    }
                }
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap5: new FormValidation.plugins.Bootstrap5({
                    eleValidClass: '',
                    rowSelector: function (field, ele) {
                        // field is the field name & ele is the field element
                        return '.mb-6';
                    }
                }),
                submitButton: new FormValidation.plugins.SubmitButton(),
                // Submit the form when all fields are valid
                // defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                autoFocus: new FormValidation.plugins.AutoFocus()
            }
        });

        $("#serviceAddForm").submit(function (e) {
            e.preventDefault();

            fv.validate().then(function (status) {
                if (status === 'Valid') {

                    blockArea($('.form-block'));

                    var form = $("#serviceAddForm")[0];
                    var formData = new FormData(form);

                    $.ajax({
                        url: '/ajax/consultation/add_service.php',
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
                                        dismissOffcanvas('offcanvasServiceAdd');
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


        $("#serviceEditForm").submit(function (e) {

            e.preventDefault();

            editFormValidation.validate().then(function (status) {
                if (status === 'Valid') {
                    blockArea($('.form-block'));

                    var form = $("#serviceEditForm")[0]; // You need to use standard javascript object here
                    var formData = new FormData(form);


                    var jsonData = formDataToJson(formData);

                    $.ajax({
                        url: '/ajax/consultation/edit_service.php',
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
                                        dismissOffcanvas('offcanvasServiceEdit');
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

    $(document).on('click', '.serviceEditButton', function (e) {

        let serviceName = $(this).data("servicename");
        let serviceDescription = $(this).data("servicedescription");
        let serviceId = $(this).data("serviceid");
        let serviceUOM = $(this).data("serviceuom");
        let serviceCategory = $(this).data("servicecategoryid");
        let servicePrice = $(this).data("serviceprice");
        let serviceCost = $(this).data("servicecost");
        let serviceStatus = $(this).data("servicestatus");
        let categoryId = $(this).data("servicecategoryid");


        $("#editServiceName").val(serviceName);
        $("#editServiceDescription").val(serviceDescription);
        $("#editServiceID").val(serviceId);
        $("#editServiceUOM").val(serviceUOM);
        $("#editServicePrice").val(servicePrice);
        $("#editServiceCost").val(serviceCost);

        $('#editServiceCategory').val(categoryId);
        $('#editServiceStatus').val(serviceStatus);
    });
</script>
<style>
    #offcanvasServiceAdd {
        width: 600px !important;
    }

    #offcanvasServiceEdit {
        width: 600px !important;
    }
</style>
</body>
</html>