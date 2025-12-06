<?php
$PAGE_ID = "BRAND_LIST";
include_once __DIR__ . "/../../includes/baseIncludes.php";
include_once __DIR__ . "/../../includes/auth_check.php";
?>
<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
<head>
    <?php include_once __DIR__ . "/../../includes/dashboard/dashboard_head_section.php"; ?>
    <title>GrandMaster ERP | Brands</title>
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
                        <div
                          class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-4 pb-sm-0">
                          <div>
                            <p class="mb-1">In-store Sales</p>
                            <h4 class="mb-1">$5,345.43</h4>
                            <p class="mb-0">
                              <span class="me-2">5k orders</span><span class="badge bg-label-success">+5.7%</span>
                            </p>
                          </div>
                          <span class="avatar me-sm-6">
                            <span class="avatar-initial rounded"
                              ><i class="ti-28px ti ti-smart-home text-heading"></i
                            ></span>
                          </span>
                        </div>
                        <hr class="d-none d-sm-block d-lg-none me-6" />
                      </div>
                      <div class="col-sm-6 col-lg-3">
                        <div
                          class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-4 pb-sm-0">
                          <div>
                            <p class="mb-1">Website Sales</p>
                            <h4 class="mb-1">$674,347.12</h4>
                            <p class="mb-0">
                              <span class="me-2">21k orders</span><span class="badge bg-label-success">+12.4%</span>
                            </p>
                          </div>
                          <span class="avatar p-2 me-lg-6">
                            <span class="avatar-initial rounded"
                              ><i class="ti-28px ti ti-device-laptop text-heading"></i
                            ></span>
                          </span>
                        </div>
                        <hr class="d-none d-sm-block d-lg-none" />
                      </div>
                      <div class="col-sm-6 col-lg-3">
                        <div
                          class="d-flex justify-content-between align-items-start border-end pb-4 pb-sm-0 card-widget-3">
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
                            <span class="avatar-initial rounded"
                              ><i class="ti-28px ti ti-wallet text-heading"></i
                            ></span>
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
                  <!-- <h5 class="card-title">Filter</h5> -->
                  <!-- <div class="d-flex justify-content-between align-items-center row pt-4 gap-6 gap-md-0">
                    <div class="col-md-4 product_status"></div>
                    <div class="col-md-4 product_category"></div>
                    <div class="col-md-4 product_stock"></div>
                  </div> -->
                </div>
                <div class="card-datatable table-responsive">
                  <table class="datatables-products table">
                    <thead class="border-top">
                      <tr>
                        <th></th>
                        <th></th>
                        <th>ID</th>
                        <th>Brand Name</th>
                        <th>Product Count</th>
                        <!-- <th>sku</th>
                        <th>price</th>
                        <th>status</th>
                        <th>actions</th> -->
                      </tr>
                    </thead>
                  </table>
                </div>
                <!-- Offcanvas to add new brand -->
                <div class="offcanvas offcanvas-end form-block" tabindex="-1" id="offcanvasBrandAdd" aria-labelledby="offcanvasEcommerceBrandAddLabel">
                            <div class="offcanvas-header">
                                <h5 id="offcanvasEcommerceBrandAddLabel" class="offcanvas-title">Add Brand</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body border-top mx-0 flex-grow-0">
                                <form class="ecommerce-brand-add pt-0" id="brandAddForm" method="POST" name="brandAddForm" action="/brands/list" enctype="multipart/form-data" onsubmit="return false;">
                                    <div class="ecommerce-brand-add-basic mb-4">
                                        <div class="mb-6">
                                            <label class="form-label" for="brandName">Brand Name*</label>
                                            <input type="text" class="form-control" id="brandName" placeholder="Enter Brand Name" name="brandName" aria-label="Mohammed Faisal"/>
                                        </div>
                                        <div class="mb-6">
                                            <label class="form-label" for="brandImage">Brand Image</label>
                                            <input type="file" class="form-control" id="brandImage" name="brandImage" accept="image/png, image/jpeg" required />
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
        brandView = '/brands/edit/',
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
                ajax: '/ajax/brands/fetch_brands.php',
            columns: [
                // columns according to JSON
                { data: null },
                { data: null },
                { data: 'brandId' },
                { data: 'brandName' },
                { data: 'productCount' }
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
                // ID
                targets: 2,
                render: function (data, type, full, meta) {
                    var $brandId = full['brandId'];

                    return '<span>' + $brandId + '</span>';
                }
                },
                {
                // Product name and product_brand
                targets: 3,
                responsivePriority: 1,
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
                    '<div class="d-flex justify-content-start align-items-center product-name">' +
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
                    
                    return '<span style="white-space: nowrap;">' +
                '<a href="' + brandView + full['brandId'] + '" class="text-heading" ><span class="fw-medium">' +
                $row_output +
                '</a>' +
                '</span>';
                }
                },
                {
                // qty
                targets: 4,
                render: function (data, type, full, meta) {
                    var $qty = full['productCount'];

                    return '<span>' + $qty + '</span>';
                }
                },
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
                searchPlaceholder: 'Search Brand',
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
                        columns: [2, 3, 4],
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
                        columns: [2, 3, 4],
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
                        columns: [2, 3, 4],
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
                        columns: [2, 3, 4],
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
                        columns: [2, 3, 4],
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
                        text: '<i class="ti ti-plus me-0 me-sm-1 mb-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add Brand</span>',
                        className: 'add-new btn btn-primary waves-effect waves-light',
                        attr: {
                            'data-bs-toggle': 'offcanvas',
                            'data-bs-target': '#offcanvasBrandAdd'
                        }
                    }
            ],
            // For responsive popup
            responsive: {
                details: {
                display: $.fn.dataTable.Responsive.display.modal({
                    header: function (row) {
                    var data = row.data();
                    return 'Details of ' + data['brandName'];
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
            dt_products.row($(this).parents('tr')).remove().draw();
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
        const offcanvasElement = document.getElementById('offcanvasBrandAdd');
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
    const brandAddForm = document.getElementById('brandAddForm');

    // Initialize Form Validation
    const fv = FormValidation.formValidation(brandAddForm, {
        fields: {
            brandName: {
                validators: {
                    notEmpty: {
                        message: 'Please enter the brand name'
                    }
                }
            },
            // brandImage: {
            //     validators: {
            //         notEmpty: {
            //             message: 'Please upload a brand image'
            //         },
            //         file: {
            //             extension: 'jpeg,jpg,png',
            //             type: 'image/jpeg,image/png',
            //             message: 'Please upload a valid image file (JPEG or PNG)'
            //         }
            //     }
            // }
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

    // Handle Form Submission
    $("#brandAddForm").submit(function (e) {
        e.preventDefault(); // Prevent default form submission

        // Validate the form
        fv.validate().then(function (status) {
            if (status === 'Valid') {
                blockArea($('.form-block')); // Show loading state

                var form = $('form')[0]; // You need to use standard javascript object here
                var formData = new FormData(form);

                // Submit the form data via AJAX
                $.ajax({
                    url: '/ajax/brands/add_brand.php', // Your server-side script
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (data, status) {

                        unBlockArea($('.form-block')); // Remove loading state
                      console.log(data);
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
                                dismissOffcanvas('offcanvasBrandAdd');
                                blockArea($('body'));
                                location.reload(); // Reload the page
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
                        unBlockArea($('.form-block')); // Remove loading state
                    }
                });
            }
        });

        return false; // Prevent default form submission
    });
});

    </script>

    <style>
    #offcanvasBrandAdd {
        width: 850px !important;
    }
</style>
  </body>
</html>
 