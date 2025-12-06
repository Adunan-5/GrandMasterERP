<?php
$PAGE_ID = "ACCOUNTS_RECEIVABLE_LIST";
include_once __DIR__ . "/../../includes/baseIncludes.php";
include_once __DIR__ . "/../../includes/auth_check.php";
?>
<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
<head>
    <?php include_once __DIR__ . "/../../includes/dashboard/dashboard_head_section.php"; ?>
    <title>GrandMaster ERP | Accounts Receivable</title>
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
                            <div class="card-body card-widget-separator py-2">
                                <div class="row gy-1 gy-sm-1">
                                    <h4 class="my-0">Accounts Receivable</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Product List Table -->
                    <div class="card">
<!--                        <div class="card-header">-->
<!--                            <div class="d-flex justify-content-between align-items-center row pt-4 gap-6 gap-md-0">-->
<!--                                <div class="col-md-4 product_status"></div>-->
<!--                                <div class="col-md-4 product_category"></div>-->
<!--                                <div class="col-md-4 product_stock"></div>-->
<!--                            </div>-->
<!--                        </div>-->
                        <div class="card-datatable table-responsive">
                            <table class="datatables-products table">
                                <thead class="border-top">
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>Customer</th>
                                    <th>Invoice#</th>
                                    <th>Total Amount</th>
                                    <th>Received Amount</th>
                                    <th>Pending Amount</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                            </table>
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
                // Variable declaration for table
                var dt_sparePart_table = $('.datatables-products'),
                    select2 = $('.select2');
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
                        ajax: '/ajax/accounting/fetch_accounts_receivable.php',
                        columns: [
                            // columns according to JSON
                            {data: null},
                            {data: null},
                            {data: 'customerName'},
                            {data: 'invoiceNumber'},
                            {data: 'totalAmount'},
                            {data: 'receivedAmount'},
                            {data: 'pendingAmount'},
                            {data: 'status'},
                            // {data: 'cost'},
                            // {data: 'margin'},
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
                          visible: false,
                          checkboxes: {
                            selectAllRender: '<input type="checkbox" class="form-check-input">'
                          },
                          render: function () {
                            return '<input type="checkbox" class="dt-checkboxes form-check-input" >';
                          },
                          searchable: false
                        },
                        {
                          // Supplier Name
                          targets: 2,
                          render: function (data, type, full, meta) {
                            var $customerName = full['customerName'];
                            var $customerId = full['customerId'];
                            let $output;

                            var stateNum = Math.floor(Math.random() * 6),
                              states = ['success', 'danger', 'warning', 'info', 'primary', 'secondary'],
                              $state = states[stateNum],
                              $initials = $customerName.match(/\b\w/g) || [];

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
                              $customerName +
                              '</span></a>' +
                              '</div>' +
                              '</div>';

                            return $row_output;
                          }
                        },
                        {
                          // SO Number
                          targets: 3,
                          responsivePriority: 1,
                          render: function (data, type, full, meta) {
                            var $invoiceNumber = full['invoiceNumber'];
                            var $soID = full['soId'];

                            return '<span><a href="/salesorders/edit/' + $soID + '" class="text-heading text-truncate"><span class="fw-medium">' + $invoiceNumber + '</a></span>';
                          }

                        },
                        {
                          // Total Amount
                          targets: 4,
                          render: function (data, type, full, meta) {
                            var $totalAmount = full['totalAmount'];

                            return '<span>' + $totalAmount + '</span>';
                          }
                        },
                        {
                          // Paid Amount
                          targets: 5,
                          render: function (data, type, full, meta) {
                            var $receivedAmount = full['receivedAmount'];
                            return '<span>' + $receivedAmount + '</span>';
                          }
                        },
                        {
                          // Pending Amount
                          targets: 6,
                          // responsivePriority: 1,
                          render: function (data, type, full, meta) {
                            var $pendingAmount = full['pendingAmount'];
                            return '<span>' + $pendingAmount + '</span>';
                          }
                        },
                        {
                          // Status
                          targets: 7,
                          responsivePriority: 2,
                          render: function (data, type, full, meta) {
                            var $status = full['status'];
                            let $badge_class = '';

                            //'NEW','AWAITING APPROVAL','APPROVED','SENT TO CUSTOMER','ACCEPTED','REJECTED','CONFIRMED','CANCELLED'

                            if ($status === "RECEIVED") {
                              $badge_class = 'bg-label-success';
                              return ('<span class="badge ' + $badge_class + '" text-capitalized>' + $status + '</span>');
                            } else if ($status === "PENDING") {
                              $badge_class = 'bg-label-secondary';
                              return ('<span class="badge ' + $badge_class + '" text-capitalized>' + $status + '</span>');
                            } else if ($status === "PARTIAL") {
                              $badge_class = 'bg-label-info';
                              return ('<span class="badge ' + $badge_class + '" text-capitalized>' + $status + '</span>');
                            }
                            return ('<span text-capitalized>' + $status + '</span>');
                          }
                        },
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
                            searchPlaceholder: 'Search',
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
                                            columns: [2, 3, 4, 5, 6, 7],
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
                                            columns: [2, 3, 4, 5, 6, 7],
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
                                            columns: [2, 3, 4, 5, 6, 7],
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
                                            columns: [2, 3, 4, 5, 6, 7],
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
                            // {
                            //     text: '<i class="ti ti-plus me-0 me-sm-1 mb-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add Spare Part</span>',
                            //     className: 'add-new btn btn-primary waves-effect waves-light',
                            //     attr: {
                            //         'data-bs-toggle': 'offcanvas',
                            //         'data-bs-target': '#offcanvasSparePartAdd'
                            //     }
                            // }
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


        </script>
        <style>
        </style>
</body>
</html>