<?php
$PAGE_ID = "CATEGORIES_LIST";
include_once __DIR__ . "/../../includes/baseIncludes.php";
include_once __DIR__ . "/../../includes/auth_check.php";

$filter = filter_input(INPUT_GET, 'filter', FILTER_SANITIZE_SPECIAL_CHARS);
?>
<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
<head>
    <?php include_once __DIR__ . "/../../includes/dashboard/dashboard_head_section.php"; ?>
    <title>GrandMaster ERP | Categories</title>
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
                                    <h4 class="my-0">Categories</h4>
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
                        <div class="card-datatable table-responsive">
                            <table class="invoice-list-table table border-top">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>Category ID</th>
                                    <th>Name</th>
                                    <th>Total Items</th>
                                    <th>Status</th>
                                    <th class="cell-fit">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $res = $db->query("SELECT sc.*, 
                                                            (SELECT COUNT(*) 
                                                            FROM spareparts sp 
                                                            WHERE sp.categoryId = sc.categoryId 
                                                                AND sp.itemType = 'SERVICE' 
                                                                AND sp.active = 1) AS totalItems
                                                    FROM services_category sc");

                                while ($row = mysqli_fetch_assoc($res)) {
                                    ?>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td><?= $row['categoryId'] ?></td>
                                        <td>
                                            <span class="text-heading text-wrap fw-medium"><?= htmlspecialchars($row['categoryName'], ENT_QUOTES, 'UTF-8') ?></span>
                                            <br>
                                            <span class="text-truncate mb-0 d-none d-sm-block"><small><?= htmlspecialchars($row['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></small></span>

                                        </td>
                                        <td><?= $row['totalItems'] ?? 0 ?></td>
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
                                                <a href="javascript:;"
                                                data-categoryname="<?= htmlspecialchars($row['categoryName'], ENT_QUOTES, 'UTF-8') ?>"
                                                data-categorydescription="<?= htmlspecialchars($row['description'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                                data-categoryid="<?= $row['categoryId'] ?>"
                                                data-categorystatus="<?= $row['active'] ?>" 
                                                data-bs-toggle="offcanvas" 
                                                data-bs-target="#offcanvasCategoryEdit" 
                                                class="btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill categoryEditButton" 
                                                data-bs-placement="top" 
                                                title="Edit">
                                                    <i class="ti ti-pencil mx-2 ti-md"></i>
                                                </a>
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
                <div class="offcanvas offcanvas-end form-block" tabindex="-1" id="offcanvasCategoryAdd">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title">Add Category</h5>
                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body border-top mx-0 flex-grow-0">
                        <form class="ecommerce-customer-add pt-0" id="categoryAddForm" method="POST" name="categoryAddForm" onsubmit="return false;">
                            <div class="ecommerce-customer-add-basic mb-4">
                                <div class="mb-6">
                                    <label class="form-label" for="categoryName">Category Name*</label>
                                    <input type="text" class="form-control" id="categoryName" placeholder="Category Name" name="categoryName" aria-label="Category Name"/>
                                </div>
                                <div class="mb-6">
                                    <label class="form-label" for="categoryDescription">Description</label>
                                    <input type="text" id="categoryDescription" class="form-control" placeholder="Description" aria-label="Description" name="categoryDescription"/>
                                </div>
                                <div class="mb-6">
                                    <label class="form-label" for="categoryStatus">Status</label>
                                    <select id="categoryStatus" name="categoryStatus" class="selectpicker w-100" data-style="btn-default" data-live-search="true">
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
                <div class="offcanvas offcanvas-end form-block" tabindex="-1" id="offcanvasCategoryEdit">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title">Edit Category</h5>
                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body border-top mx-0 flex-grow-0">
                        <form class="ecommerce-customer-add pt-0" id="categoryEditForm" method="POST" name="categoryEditForm" onsubmit="return false;">
                            <div class="ecommerce-customer-add-basic mb-4">
                                <div class="mb-6">
                                    <label class="form-label" for="editCategoryID">Category ID</label>
                                    <input type="text" class="form-control" id="editCategoryID" name="categoryID" readonly value=""/>
                                </div>
                                <div class="mb-6">
                                    <label class="form-label" for="categoryName">Category Name*</label>
                                    <input type="text" class="form-control" id="editCategoryName" placeholder="Category Name" name="categoryName" aria-label="Category Name"/>
                                </div>
                                <div class="mb-6">
                                    <label class="form-label" for="categoryDescription">Description</label>
                                    <input type="text" id="editCategoryDescription" class="form-control" placeholder="Description" aria-label="Description" name="categoryDescription"/>
                                </div>
                                <div class="mb-6">
                                    <label class="form-label" for="categoryStatus">Status</label>
                                    <select id="editCategoryStatus" name="categoryStatus" class="selectpicker w-100" data-style="btn-default" data-live-search="true">
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
<?php

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
                    searchPlaceholder: 'Search Category',
                    paginate: {
                        next: '<i class="ti ti-chevron-right ti-sm"></i>',
                        previous: '<i class="ti ti-chevron-left ti-sm"></i>'
                    }
                },
                // Buttons with Dropdown
                buttons: [
                    {
                        text: '<i class="ti ti-plus ti-xs me-md-2"></i><span class="d-md-inline-block d-none">New Category</span>',
                        className: 'btn btn-primary waves-effect waves-light',
                        attr: {
                            'data-bs-toggle': 'offcanvas',
                            'data-bs-target': '#offcanvasCategoryAdd'
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
        const CategoryAddForm = document.getElementById('categoryAddForm');
        const CategoryEditForm = document.getElementById('categoryEditForm');
        const fv = FormValidation.formValidation(CategoryAddForm, {
            fields: {
                categoryName: {
                    validators: {
                        notEmpty: {
                            message: 'Please enter a category name'
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

        const editFormValidation = FormValidation.formValidation(categoryEditForm, {
            fields: {
                editCategoryName: {
                    validators: {
                        notEmpty: {
                            message: 'Please enter a category name'
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

        $("#categoryAddForm").submit(function (e) {
            e.preventDefault();

            fv.validate().then(function (status) {
                if (status === 'Valid') {

                    blockArea($('.form-block'));

                    var form = $("#categoryAddForm")[0];
                    var formData = new FormData(form);

                    $.ajax({
                        url: '/ajax/categories/add_category.php',
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
                                        dismissOffcanvas('offcanvasCategoryAdd');
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


        $("#categoryEditForm").submit(function (e) {

            e.preventDefault();

            editFormValidation.validate().then(function (status) {
                if (status === 'Valid') {
                    // alert("Edit form submit");
                    blockArea($('.form-block'));

                    var form = $("#categoryEditForm")[0]; // You need to use standard javascript object here
                    var formData = new FormData(form);


                    var jsonData = formDataToJson(formData);
                    // alert(jsonData);

                    $.ajax({
                        url: '/ajax/categories/edit_category.php',
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
                                        dismissOffcanvas('offcanvasCategoryEdit');
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

    $(document).on('click', '.categoryEditButton', function (e) {

        let categoryName = $(this).data("categoryname");
        let categoryDescription = $(this).data("categorydescription");
        let categoryId = $(this).data("categoryid");
        let categoryStatus = $(this).data("categorystatus");


        $("#editCategoryName").val(categoryName);
        $("#editCategoryDescription").val(categoryDescription);
        $("#editCategoryID").val(categoryId);

        $('#editCategoryStatus').val(categoryStatus).trigger('change');
    });
</script>
<style>
    #offcanvasCategoryAdd {
        width: 600px !important;
    }
    #offcanvasCategoryEdit {
        width: 600px !important;
    }
</style>
</body>
</html>