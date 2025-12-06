<?php
$PAGE_ID = "SPARE_PART_LIST";
include_once __DIR__ . "/../../includes/baseIncludes.php";
include_once __DIR__ . "/../../includes/auth_check.php";
?>
<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
<head>
    <?php include_once __DIR__ . "/../../includes/dashboard/dashboard_head_section.php"; ?>
    <title>GrandMaster ERP | Spareparts</title>
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
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center row pt-4 gap-6 gap-md-0">
                                <div class="col-md-4 product_status">
                                    <button class="btn btn-primary" onclick="downloadTemplate()" type="button">
                                        <i class="ti ti-download me-1"></i> Spareparts Import Template
                                    </button>
                                </div>
                                <div class="col-md-4 product_category"></div>
                                <div class="col-md-4 product_stock">
                                    <label for="importFile" class="form-label fw-bold mb-2">Import Spare Parts Excel</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="file" class="form-control" id="importFile" name="importFile" accept=".xlsx,.xls" required />
                                        <button class="btn btn-primary" onclick="importSpareParts()" type="button">
                                            <i class="ti ti-upload me-1"></i> Upload
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-datatable table-responsive">
                            <table class="datatables-products table">
                                <thead class="border-top">
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>ID</th>
                                    <th style="white-space: nowrap;">Part Number</th>
                                    <th>Internal Reference</th>
                                    <th>Description</th>
                                    <th>Brand</th>
                                    <th>Sale Price</th>
                                    <th>Cost</th>
                                    <th>Margin</th>
                                    <th>Created By</th>
                                    <th>Updated By</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                        <!-- Offcanvas to add new Spare Part -->
                        <div class="offcanvas offcanvas-end form-block" tabindex="-1" id="offcanvasSparePartAdd" aria-labelledby="offcanvasEcommerceSparePartAddLabel">
                            <div class="offcanvas-header">
                                <h5 id="offcanvasEcommerceSparePartAddLabel" class="offcanvas-title">Add Spare Part</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body border-top mx-0 flex-grow-0">
                                <form class="ecommerce-sparepart-add pt-0" id="sparepartAddForm" method="POST" name="sparepartAddForm" action="/spareparts/list" enctype="multipart/form-data" onsubmit="return false;">
                                    <div class="ecommerce-sparepart-add-basic mb-4">
<!--                                        <div class="mb-6">-->
<!--                                            <label class="form-label" for="partNumber">Part Number*</label>-->
<!--                                            <input type="text" class="form-control" id="partNumber" placeholder="Enter Part Number" name="partNumber" aria-label="Part Number" required/>-->
<!--                                        </div>-->
                                        <div class="mb-6">
                                            <label class="form-label" for="internalReference">Internal Reference*
                                            </label>
                                            <input type="text" class="form-control" id="internalReference" placeholder="Enter Internal Reference" name="internalReference" aria-label="Internal Reference"/>
                                        </div>
                                        <div class="mb-6">
                                            <label class="form-label" for="name">Name</label>
                                            <input type="text" class="form-control" id="name" placeholder="Enter Spare Part Name" name="name" aria-label="Name" required/>
                                        </div>
                                        <div class="mb-6">
                                            <label class="form-label" for="image">Image</label>
                                            <input type="file" class="form-control" id="image" name="image" accept="image/png, image/jpeg" required/>
                                        </div>
                                        <div class="mb-6">
                                            <label class="form-label" for="description">Description</label>
                                            <textarea class="form-control" id="description" placeholder="Enter Description" name="description" aria-label="Description"></textarea>
                                        </div>
                                        <div class="mb-6">
                                            <label class="form-label" for="brandId">Brand*</label>
                                            <select class="form-select selectpicker w-100" id="brandId" name="brandId" data-style="btn-default" data-live-search="true" required>
                                                <option value="">Select Brand</option>
                                                <?php
                                                $res = $db->query("SELECT * FROM brands");
                                                while ($row = mysqli_fetch_assoc($res)) {
                                                    ?>
                                                    <option value="<?= $row['brandId'] ?>"><?= $row['brandName'] ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="mb-6">
                                            <label class="form-label" for="leadTime">Lead Time</label>
                                            <input type="number" class="form-control" id="leadTime" placeholder="Enter Lead Time" name="leadTime" aria-label="Lead Time"/>
                                        </div>
                                        <div class="mb-6">
                                            <label class="form-label" for="origin">Origin</label>
                                            <input type="text" class="form-control" id="origin" placeholder="Enter Origin" name="origin" aria-label="Origin"/>
                                        </div>
                                        <div class="mb-6">
                                            <label class="form-label" for="salesPrice">Sales Price*</label>
                                            <input type="number" class="form-control" id="salesPrice" placeholder="Enter Sales Price" name="salesPrice" aria-label="Sales Price" step="0.01"/>
                                        </div>
                                        <div class="mb-6">
                                            <label class="form-label" for="uom">UOM*</label>
                                            <select class="form-select" id="uom" name="uom" required>
                                                <option value="">Select UOM</option>
                                                <<?php
                                                $res = $db->query("SELECT * FROM uoms");
                                                while ($row = mysqli_fetch_assoc($res)) {
                                                    ?>
                                                    <option value="<?= $row['uomId'] ?>"><?= $row['uomName'] ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="mb-6">
                                            <label class="form-label" for="cost">Cost*</label>
                                            <input type="number" class="form-control" id="cost" placeholder="Enter Cost" name="cost" aria-label="Cost" step="0.01"/>
                                        </div>
                                        <div class="mb-6">
                                            <label class="form-label" for="hsCode">HS Code</label>
                                            <input type="text" class="form-control" id="hsCode" placeholder="Enter HS Code" name="hsCode" aria-label="HS Code"/>
                                        </div>
                                        <div class="mb-6">
                                            <label class="form-label" for="hsPercentage">HS Percentage</label>
                                            <input type="number" class="form-control" id="hsPercentage" placeholder="Enter HS Percentage" name="hsPercentage" aria-label="HS Percentage" step="0.01"/>
                                        </div>
                                        <div class="mb-6">
                                            <label class="form-label" for="publishedOnEcommerce">Published On
                                                Ecommerce
                                            </label>
                                            <select class="form-select" id="publishedOnEcommerce" name="publishedOnEcommerce" required>
                                                <option value="0">No</option>
                                                <option value="1">Yes</option>
                                            </select>
                                        </div>
                                        <div class="mb-6">
                                            <label class="form-label" for="notes">Notes</label>
                                            <textarea class="form-control" id="notes" placeholder="Enter Notes" name="notes" aria-label="Notes"></textarea>
                                        </div>
                                    </div>
                                    <div>
                                        <button class="btn btn-primary me-sm-4 data-submit">Add Spare Part</button>
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

            function importSpareParts() {
                var fileInput = document.getElementById('importFile');
                if (fileInput.files.length === 0) {
                    Swal.fire({
                        title: 'No file selected!',
                        icon: 'error',
                        text: 'Please select an Excel file (.xlsx or .xls) to upload.',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        showClass: {
                            popup: 'animate__animated animate__shakeX'
                        },
                        buttonsStyling: false
                    });
                    return;
                }

                var file = fileInput.files[0];
                var formData = new FormData();
                formData.append('importFile', file);

                blockArea($('.card')); // Assuming blockArea is defined in your codebase
                $.ajax({
                    url: '/ajax/spareparts/import_spare_parts.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        unBlockArea($('.card')); // Assuming unBlockArea is defined
                        var statusMessage = response.trim().split("|")[0];
                        var message = response.trim().split("|")[1];
                        if (statusMessage === "SUCCESS") {
                            showSuccessMessage(message, gotoPage, "/spareparts/list");
                        } else if (statusMessage === "ERROR") {
                            showErrorMessage(message);
                        }
                    },
                    error: function (xhr, status, error) {
                        unBlockArea($('.card'));
                        showErrorMessage("An error occurred while processing the request. Please try again.");
                        console.error(error);
                    }
                });
            }

            function downloadTemplate() {
                // Get the base URL of the current page
                const baseUrl = window.location.origin;
                // Define the relative URL to the template file
                const relativeUrl = '/uploads/template/sparepart-template/Spareparts_Bulk_Import_Template.xlsx';
                // Combine the base URL and relative URL
                const fullUrl = baseUrl + relativeUrl;
                // Create a link element to trigger the download
                const link = document.createElement('a');
                link.href = fullUrl;
                link.download = 'Spareparts_Bulk_Import_Template.xlsx';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }

            $(document).on('click', '.sparepartDeleteButton', function () {
                var sparepartId = $(this).data('sparepart-id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, cancel',
                    customClass: {
                        confirmButton: 'btn btn-danger me-2',
                        cancelButton: 'btn btn-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteSparePart(sparepartId);
                    }
                });
            });

            function deleteSparePart(sparepartId) {
                $.ajax({
                    url: '/ajax/spareparts/delete_spare_part.php',
                    type: 'POST',
                    data: { sparepartId: sparepartId },
                    success: function(data) {
                        var result = JSON.parse(data);
                        if (result.status === 'SUCCESS') {
                            showSuccessMessage(result.message, gotoPage, "/spareparts/list");
                        } else {
                            showErrorMessage(result.message);
                        }
                    },
                    error: function(error) {
                        console.log(error);
                        showErrorMessage('An error occurred while deleting the spare part.');
                    }
                });
            }

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
                var dt_sparePart_table = $('.datatables-products'),
                    select2 = $('.select2'),
                    sparepartView = '/spareparts/edit/';
                if (select2.length) {
                    var $this = select2;
                    $this.wrap('<div class="position-relative"></div>').select2({
                        placeholder: 'United States ',
                        dropdownParent: $this.parent()
                    });
                }

                // E-commerce Products datatable

                if (dt_sparePart_table.length) {
                    var dt_spareParts = dt_sparePart_table.DataTable({
                        ajax: '/ajax/spareparts/fetch_spare_parts.php?type=SPAREPART',
                        columns: [
                            // columns according to JSON
                            {data: null},
                            {data: null},
                            {data: 'sparepartId'},
                            {data: 'partNumber'},
                            {data: 'internalReference'},
                            {data: 'description'},
                            {data: 'brandName'},
                            {data: 'salesPrice'},
                            {data: 'cost'},
                            {data: 'margin'},
                            {data: 'createdBy'},
                            {data: 'updatedBy'},
                            {data: null} // Actions column
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
                                // Sparepart ID
                                targets: 2,
                                width:70,
                                render: function (data, type, full, meta) {
                                    var $sparepartId = full['sparepartId'];

                                    return '<span>' + $sparepartId + '</span>';
                                }
                            },
                            {
                                // GMM Part Number
                                targets: 3,
                                width:110,
                                responsivePriority: 1,
                                render: function (data, type, full, meta) {
                                    var $partNumber = full['partNumber'];
                                    var sparepartId = full['sparepartId'];

                                    return (
                                        '<span style="white-space: nowrap;">' +
                                        '<a href="' + sparepartView + sparepartId + '" class="text-heading" ><span class="fw-medium">' +
                                        $partNumber +
                                        '</a>' +
                                        '</span>'
                                    );
                                }

                            },
                            {
                                // Internal Reference
                                targets: 4,
                                width:130,
                                render: function (data, type, full, meta) {
                                    var $internalReference = full['internalReference'];

                                    return '<span>' + $internalReference + '</span>';
                                }
                            },
                            {
                                // Description
                                targets: 5,
                                width: 450,
                                render: function (data, type, full, meta) {
                                    var $description = full['description'];
                                    return '<span>' + $description + '</span>';
                                }
                            },
                            {
                                // Brand Name and Icon
                                targets: 6,
                                width:100,
                                // responsivePriority: 1,
                                render: function (data, type, full, meta) {
                                    var $name = full['brandName']
                                    var $image = full['brandIcon']; // Brand icon URL

                                    // If a brand icon is provided
                                    if ($image) {
                                        // Use the brand icon as the image
                                        var $output =
                                            '<img src="' +
                                            $image +
                                            '" alt="Brand-' +
                                            $name +
                                            '" class="rounded-2" style="height: 40px; width: 40px;">';
                                    } else {
                                        // Fallback to an avatar badge if no image is available
                                        var stateNum = Math.floor(Math.random() * 6);
                                        var states = ['success', 'danger', 'warning', 'info', 'dark', 'primary', 'secondary'];
                                        var $state = states[stateNum],
                                            $initials = $name.match(/\b\w/g) || [];
                                        $initials = (($initials.shift() || '') + ($initials.pop() || '')).toUpperCase();
                                        $output = '<span class="avatar-initial rounded-2 bg-label-' + $state + '">' + $initials + '</span>';
                                    }

                                    // Creates the full output for Product name and product_brand
                                    var $row_output =
                                        '<div class="d-flex justify-content-start align-items-center spartpart-name">' +
                                        '<div class="avatar-wrapper">' +
                                        '<div class="avatar avatar me-4 rounded-2 bg-label-secondary">' +
                                        $output +
                                        '</div>' +
                                        '</div>' +
                                        '<div class="d-flex flex-column">' +
                                        '<h6 class="text-nowrap mb-0">' +
                                        $name +
                                        '</h6>' +
                                        '</div>' +
                                        '</div>';

                                    return $row_output;
                                }
                            },
                            {
                                // Sales Price
                                targets: 7,
                                responsivePriority: 2,
                                render: function (data, type, full, meta) {
                                    var $salesPrice = full['salesPrice'];

                                    return '<span>' + $salesPrice + '</span>';
                                }
                            },
                            {
                                // Cost
                                targets: 8,
                                responsivePriority: 2,
                                render: function (data, type, full, meta) {
                                    var $cost = full['cost'];

                                    return '<span>' + $cost + '</span>';
                                }
                            },
                            {
                                // Margin
                                targets: 9,
                                responsivePriority: 2,
                                render: function (data, type, full, meta) {
                                    var $margin = full['margin'];

                                    return '<span>' + $margin + '</span>';
                                }
                            },
                            {
                                // Created By
                                targets: 10,
                                responsivePriority: 2,
                                render: function (data, type, full, meta) {
                                    var $createdBy = full['createdBy'];

                                    return '<span>' + $createdBy + '</span>';
                                }
                            },
                            {
                                // Updated By
                                targets: 11,
                                responsivePriority: 2,
                                render: function (data, type, full, meta) {
                                    var $updatedBy = full['updatedBy'];

                                    return '<span>' + $updatedBy + '</span>';
                                }
                            },
                            {
                                // Actions
                                targets: 12,
                                searchable: false,
                                orderable: false,
                                responsivePriority: 1,
                                render: function (data, type, full, meta) {
                                    var $sparepartId = full['sparepartId'];
                                    return (
                                        '<div class="dropdown">' +
                                            '<button class="btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">' +
                                                '<i class="ti ti-dots-vertical ti-md"></i>' +
                                            '</button>' +
                                            '<ul class="dropdown-menu">' +
                                                '<li><a class="dropdown-item" href="' + sparepartView + $sparepartId + '">' +
                                                    '<i class="ti ti-edit me-2"></i>Edit' +
                                                '</a></li>' +
                                                '<li><a class="dropdown-item sparepartDeleteButton" href="javascript:void(0)" ' +
                                                    'data-sparepart-id="' + $sparepartId + '">' +
                                                    '<i class="ti ti-trash me-2"></i>Delete' +
                                                '</a></li>' +
                                            '</ul>' +
                                        '</div>'
                                    );
                                }
                            }
                        ],
                        order: [[2, 'asc']],
                        dom:
                          '<"card-header d-flex flex-wrap flex-md-row flex-column align-items-start align-items-sm-center py-0"' +
                          '<"d-flex align-items-center me-5"f>' +
                          '<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-md-end flex-wrap flex-sm-nowrap mb-6 mb-sm-0"<"me-3"l>B>' +
                          '>t' +
                          '<"row mx-1"' +
                          '<"col-sm-12 col-md-6"i>' +
                          '<"col-sm-12 col-md-6"p>' +
                          '>',
                        // lengthMenu: [7, 10, 20, 50, 70, 100], //for length of menu
                        language: {
                            sLengthMenu: '_MENU_',
                            search: '',
                            searchPlaceholder: 'Search Spare Part',
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
                                            columns: [2, 3, 4, 5, 6, 7, 8, 9],
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
                                            columns: [2, 3, 4, 5, 6, 7, 8, 9],
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
                                            columns: [2, 3, 4, 5, 6, 7, 8, 9],
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
                                            columns: [2, 3, 4, 5, 6, 7, 8, 9],
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
                                            columns: [2, 3, 4, 5, 6, 7, 8, 9],
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
                            // {
                            // text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add Spare Part</span>',
                            // className: 'add-new btn btn-primary ms-2 ms-sm-0 waves-effect waves-light',
                            // action: function () {
                            //     window.location.href = sparePartAdd;
                            // }
                            // }
                            <?php if (is_ceo() || has_permission('itemsOrProducts', 'create')) { ?>
                            {
                                text: '<i class="ti ti-plus me-0 me-sm-1 mb-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add Spare Part</span>',
                                className: 'add-new btn btn-primary waves-effect waves-light',
                                attr: {
                                    'data-bs-toggle': 'offcanvas',
                                    'data-bs-target': '#offcanvasSparePartAdd'
                                }
                            }
                            <?php } ?>
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
                    dt_spareParts.row($(this).parents('tr')).remove().draw();
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
                    const offcanvasElement = document.getElementById('offcanvasSparePartAdd');
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
                const SparepartAddForm = document.getElementById('sparepartAddForm');

                // Add New Spare Part Form Validation
                const fv = FormValidation.formValidation(SparepartAddForm, {
                    fields: {
                        // partNumber: {
                        //     validators: {
                        //         notEmpty: {
                        //             message: 'Please enter the Part Number'
                        //         }
                        //     }
                        // },
                        internalReference: {
                            validators: {
                                notEmpty: {
                                    message: 'Please enter Internal Reference'
                                }
                            }
                        },
                        // image: {
                        //     validators: {
                        //         notEmpty: {
                        //             message: 'Please upload an image'
                        //         },
                        //         file: {
                        //             extension: 'jpeg,jpg,png',
                        //             type: 'image/jpeg,image/png',
                        //             message: 'Please upload a valid image file (JPEG/PNG)'
                        //         }
                        //     }
                        // },
                        brandId: {
                            validators: {
                                notEmpty: {
                                    message: 'Please select a Brand'
                                }
                            }
                        },
                        salesPrice: {
                            validators: {
                                notEmpty: {
                                    message: 'Please enter Sales Price'
                                },
                                numeric: {
                                    message: 'Please enter a valid Sales Price',
                                    decimalSeparator: '.'
                                }
                            }
                        },
                        uom: {
                            validators: {
                                notEmpty: {
                                    message: 'Please select the Unit of Measure (UOM)'
                                }
                            }
                        },
                        cost: {
                            validators: {
                                notEmpty: {
                                    message: 'Please enter Cost Price'
                                },
                                numeric: {
                                    message: 'Please enter a valid Cost',
                                    decimalSeparator: '.'
                                }
                            }
                        },
                        // hsCode: {
                        //     validators: {
                        //         notEmpty: {
                        //             message: 'Please enter HS Code'
                        //         }
                        //     }
                        // },
                        // hsPercentage: {
                        //     validators: {
                        //         notEmpty: {
                        //             message: 'Please enter HS Percentage'
                        //         },
                        //         numeric: {
                        //             message: 'Please enter a valid HS Percentage',
                        //             decimalSeparator: '.'
                        //         }
                        //     }
                        // },
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
                $("#sparepartAddForm").submit(function (e) {
                    e.preventDefault();

                    fv.validate().then(function (status) {
                        if (status === 'Valid') {
                            blockArea($('.form-block'));

                            var form = $('#sparepartAddForm')[0]; // Standard JavaScript object
                            var formData = new FormData(form);

                            $.ajax({
                                url: '/ajax/spareparts/add_spare_part.php',
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
                                            dismissOffcanvas('offcanvasSparePartAdd');
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

            // document.addEventListener('click', function (event) {
            //     if (event.target.classList.contains('open-modal')) {
            //         event.preventDefault();

            //         var sparepartId = event.target.getAttribute('data-sparepart-id');

            //         // Fetch sparepart details dynamically
            //         loadSparepartDetails(sparepartId);
            //     }
            // });

            // function loadSparepartDetails(sparepartId) {
            //     var modalContent = document.getElementById('sparepartModalContent');

            //     // Show loading message
            //     modalContent.innerHTML = '<strong>Loading details for Sparepart ID: ' + sparepartId + '...</strong>';

            //     // Simulate an API call (replace this with your actual logic)
            //     setTimeout(function () {
            //         modalContent.innerHTML = `
            //             <p><strong>Sparepart ID:</strong> ${sparepartId}</p>
            //             <p><strong>Name:</strong> Example Sparepart Name</p>
            //             <p><strong>Description:</strong> Sample description for the sparepart.</p>
            //             <p><strong>Price:</strong> $123.45</p>
            //         `;
            //     }, 1000);
            // }
        </script>
        <style>
            #offcanvasSparePartAdd {
                width: 850px !important;
            }

            #offcanvasSparePartEdit {
                width: 850px !important;
            }
        </style>
</body>
</html>