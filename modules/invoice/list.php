<?php
$PAGE_ID = "INVOICE_LIST";
include_once __DIR__ . "/../../includes/baseIncludes.php";
include_once __DIR__ . "/../../includes/auth_check.php";

$filter = filter_input(INPUT_GET, 'filter', FILTER_SANITIZE_SPECIAL_CHARS);
?>
<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
<head>
    <?php include_once __DIR__ . "/../../includes/dashboard/dashboard_head_section.php"; ?>
    <title>GrandMaster ERP | Invoices</title>
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
                                    <h4 class="my-0">All Invoices</h4>
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
                                                <h4 class="mb-0"><?= getTotalApprovedInvoices() ?></h4>
                                                <p class="mb-0">Approved</p>
                                            </div>
                                            <div class="avatar me-sm-6">
                            <span class="avatar-initial rounded bg-label-secondary text-heading">
                              <i class="ti ti-file-invoice ti-26px"></i>
                            </span>
                                            </div>
                                        </div>
                                        <hr class="d-none d-sm-block d-lg-none me-6"/>
                                    </div>
                                    <div class="col-sm-6 col-lg-4">
                                        <div class="d-flex justify-content-between align-items-center card-widget-2 border-end pb-4 pb-sm-0">
                                            <div>
                                                <h4 class="mb-0"><?= getTotalInvoices() ?></h4>
                                                <p class="mb-0">Overall</p>
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
                                                <h4 class="mb-0">SAR <?=getTotalAmountForAllInvoices()['totalAmountAfterVAT']?></h4>
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
                                    <th class="text-truncate">Invoice #</th>
                                    <th class="cell-fit">Customer</th>
                                    <th>Payment Term</th>
                                    <th>Status</th>
                                    <!--                                    <th  class="text-truncate">Sale Order</th>-->
                                    <th class="text-truncate">Date</th>
                                    <th>Amount</th>
                                    <th>Company</th>
                                    <th>Sold By</th>
                                    <th class="cell-fit">Action</th>
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
<?php
include_once __DIR__ . "/../../includes/dashboard/dashboard_footer_scripts.php";
?>

<script>
    $(".selectpicker").selectpicker();

    'use strict';
    $(function () {
        // Variable declaration for table
        var dt_invoice_table = $('.invoice-list-table');

        // Invoice datatable
        if (dt_invoice_table.length) {

            let ajaxURL = '/ajax/invoice/fetch_invoices.php';

            var dt_invoice = dt_invoice_table.DataTable({
                ajax: ajaxURL,
                columns: [
                    // columns according to JSON
                    {data: 'invoiceId'},
                    {data: 'invoiceId'},
                    {data: 'invoiceNumber'},
                    {data: 'companyName'},
                    {data: 'termName'},
                    {data: 'invoiceStatus'},
                    {data: 'invoiceDateIssued'},
                    {data: 'totalAmount'},
                    {data: 'subsidiaryName'},
                    {data: 'salesPersonName'},
                    {data: 'action'}
                ],
                columnDefs: [
                    {
                        // For Responsive
                        className: 'control',
                        responsivePriority: 2,
                        searchable: false,
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
                        title: 'Actions',
                        searchable: false,
                        orderable: false,
                        responsivePriority: 1,
                        render: function (data, type, full, meta) {
                            var $invoiceId = full['invoiceId'];
                            var companyId = full['companyId'];
                            var editPath = (companyId == 1) ? '/invoice/edit/' : '/consultation/invoice/edit/';
                            var downloadPath = (companyId == 1) ? '/ajax/invoice/generate_pdf.php' : '/ajax/consultation/generate_invoice_pdf.php';
                            return (
                                '<div class="d-flex align-items-center">' +
                                // '<a href="javascript:;" data-bs-toggle="tooltip" class="btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill" data-bs-placement="top" title="Delete"><i class="ti ti-trash mx-2 ti-md"></i></a>' +
                                // '<a href="/quotation/preview/' + $invoiceId + '" data-bs-toggle="tooltip" class="btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill" data-bs-placement="top" title="Preview Quotation"><i class="ti ti-eye mx-2 ti-md"></i></a>' +
                                '<div class="dropdown">' +
                                '<a href="javascript:;" class="btn dropdown-toggle hide-arrow btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill p-0" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-md"></i></a>' +
                                '<div class="dropdown-menu dropdown-menu-end">' +
                               '<a href="' + downloadPath + '?isdownload=1&invoiceID=' + $invoiceId + '" class="dropdown-item">Download</a>' +
                                '<a href="' + editPath + $invoiceId + '" class="dropdown-item">Edit</a>' +
                                '</div>' +
                                '</div>'
                            );
                        }
                    },

                    {
                        // Invoice Number
                        targets: 2,
                        render: function (data, type, full, meta) {
                            var $invoiceNumber = full['invoiceNumber'];
                            var $invoiceId = full['invoiceId'];
                            var $invoiceDate = full['invoiceDateIssued'];

                            var companyId = full['companyId'];
                            var editPath = (companyId == 1) ? '/invoice/edit/' : '/consultation/invoice/edit/';

                            // Creates full output for row
                            // if ($invoiceNumber === null) $invoiceNumber = "Draft";
                            // var $row_output = '<a class="text-truncate" href="/consultation/invoice/edit/' + $invoiceId + '"># ' + $invoiceNumber + '</a>';
                            var $row_output = '<a class="text-truncate" href="' + editPath + $invoiceId + '"># ' + $invoiceNumber + '</a>';
                            // '<br><small class="text-truncate">' + moment($invoiceDate).format('DD MMM YYYY') + '</small>';
                            return $row_output;
                        }
                    },
                    {
                        // Client name
                        targets: 3,
                        responsivePriority: 4,

                        render: function (data, type, full, meta) {
                            var $name = full['companyName'];
                            var $customerId = full['customerId'];
                            var $nameAR = full['companyNameAr'];
                            let $output;

                            // For Avatar badge
                            var stateNum = Math.floor(Math.random() * 6),
                                states = ['success', 'danger', 'warning', 'info', 'primary', 'secondary'],
                                $state = states[stateNum],
                                $initials = $name.match(/\b\w/g) || [];

                            $initials = (($initials.shift() || '') + ($initials.pop() || '')).toUpperCase();

                            $output = '<span class="avatar-initial rounded-circle bg-label-' + $state + '">' + $initials + '</span>';

                            // Creates full output for row
                            var $row_output =
                                '<div class="d-flex justify-content-start align-items-center">' +
                                '<div class="avatar-wrapper">' +
                                '<div class="avatar avatar-sm me-3">' +
                                $output +
                                '</div>' +
                                '</div>' +
                                '<div class="d-flex flex-column">' +
                                '<a href="/customers/view/' + $customerId + '" class="text-heading text-truncate"><span class="fw-medium">' +
                                $name +
                                '</span></a>' +
                                '<small class="text-truncate">' +
                                $nameAR +
                                '</small>' +
                                '</div>' +
                                '</div>';
                            return $row_output;
                        }
                    },
                    {
                        // Status
                        targets: 5,
                        render: function (data, type, full, meta) {
                            var $status = full['invoiceStatus'];
                            let $badge_class = '';

                            //'NEW','AWAITING APPROVAL','APPROVED','SENT TO CUSTOMER','ACCEPTED','REJECTED','CONFIRMED','CANCELLED'

                            if ($status === "NEW") {
                                $badge_class = 'bg-label-linkedin';
                                return ('<span class="badge ' + $badge_class + '" text-capitalized> DRAFT </span>');
                            } else if ($status === "AWAITING ACCOUNTANT APPROVAL") {
                                $badge_class = 'bg-label-dark';
                                return ('<span class="badge ' + $badge_class + '" text-capitalized>' + $status + '</span>');
                            } else if ($status === "ACCOUNTANT APPROVED") {
                                $badge_class = 'bg-label-success';
                                return ('<span class="badge ' + $badge_class + '" text-capitalized>' + $status + '</span>');
                            } else if ($status === "ACCOUNTANT REJECTED") {
                                $badge_class = 'bg-label-danger';
                                return ('<span class="badge ' + $badge_class + '" text-capitalized>' + $status + '</span>');
                            }

                            return ('<span text-capitalized>' + $status + '</span>');
                        }
                    },
                    {
                        // Invoice Date
                        targets: 6,
                        render: function (data, type, full, meta) {
                            var $invoiceDate = new Date(full['invoiceDateIssued']);

                            let $row_output;
                            if (full['invoiceDateIssued'] != null) {
                                $row_output = '<span class="d-none">' + moment($invoiceDate).format('YYYYMMDD') + '</span><span class="text-truncate">' + moment($invoiceDate).format('DD MMM YYYY') + '</span>';
                            } else {
                                $row_output = "-";
                            }

                            return $row_output;
                        }
                    },
                    {
                        // Sold By
                        targets: 9,
                        render: function (data, type, full, meta) {
                            var $soldBy = full['salesPersonName'];
                            let $row_output;
                            if (full['salesPersonName'] != null) {
                                $row_output = '<span class="text-truncate">' + $soldBy + '</span>';
                            } else {
                                $row_output = "-";
                            }
                            return $row_output;
                        }
                    }
                ],
                order: [[2, 'desc']],
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
                    searchPlaceholder: 'Search Invoice',
                    paginate: {
                        next: '<i class="ti ti-chevron-right ti-sm"></i>',
                        previous: '<i class="ti ti-chevron-left ti-sm"></i>'
                    }
                },
                // Buttons with Dropdown
                buttons: [
                    // {
                    //     text: '<i class="ti ti-plus ti-xs me-md-2"></i><span class="d-md-inline-block d-none">New Quotation</span>',
                    //     className: 'btn btn-primary waves-effect waves-light',
                    //     action: function (e, dt, button, config) {
                    //         window.location = '/quotation/new';
                    //     }
                    // }
                ],
                // For responsive popup
                responsive: {
                    details: {
                        display: $.fn.dataTable.Responsive.display.modal({
                            header: function (row) {
                                var data = row.data();
                                return 'Details of ' + data['invoiceNumber'];
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