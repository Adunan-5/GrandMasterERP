<?php
$PAGE_ID = "SUPPLIER_LIST";
include_once __DIR__ . "/../../includes/baseIncludes.php";
include_once __DIR__ . "/../../includes/auth_check.php";
?>
<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
<head>
    <?php include_once __DIR__ . "/../../includes/dashboard/dashboard_head_section.php"; ?>
    <title>GrandMaster ERP | Suppliers</title>
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
                    <!-- customers List Table -->
                    <div class="card">
                        <div class="card-datatable table-responsive">
                            <table class="datatables-suppliers table border-top">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>Supplier</th>
                                    <th class="text-nowrap">Supplier ID</th>
                                    <th>City</th>
                                    <th>State</th>
                                    <th>Country</th>
                                    <th>Phone</th>
                                    <th>VAT Number</th>
                                </tr>
                                </thead>
                            </table>
                        </div>

                        <!-- Offcanvas to add new supplier -->
                      <div class="offcanvas offcanvas-end form-block" tabindex="-1" id="offcanvasSupplierAdd" aria-labelledby="offcanvasEcommerceSupplierAddLabel">
                        <div class="offcanvas-header">
                          <h5 id="offcanvasEcommerceSupplierAddLabel" class="offcanvas-title">Add Supplier</h5>
                          <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body border-top mx-0 flex-grow-0">
                          <form class="ecommerce-supplier-add pt-0" id="supplierAddForm" method="POST" name="supplierAddForm" action="/suppliers/list" onsubmit="return false;">
                            <div class="ecommerce-supplier-add-basic mb-4">
                              <h6 class="mb-6">Basic Information</h6>
                              <div class="mb-6">
                                <label class="form-label" for="supplierName">Name*</label>
                                <input type="text" class="form-control" id="supplierName" placeholder="Mohammed Faisal" name="supplierName" aria-label="Mohammed Faisal"/>
                              </div>
                              <div class="mb-6">
                                <label class="form-label" for="supplierNameAR">Name in Arabic*</label>
                                <input type="text" class="form-control" id="supplierNameAR" placeholder="محمد فيصل" name="supplierNameAR" aria-label="محمد فيصل"/>
                              </div>
                              <div class="row">


                                <div class="mb-6 col-6">
                                  <label class="form-label" for="supplierEmail">Email*
                                  </label>
                                  <input type="text" id="supplierEmail" class="form-control" placeholder="john.doe@example.com" aria-label="john.doe@example.com" name="supplierEmail"/>
                                </div>
                                <div class="mb-6 col-6">
                                  <label class="form-label" for="supplierContact">Mobile*
                                  </label>
                                  <input type="text" id="supplierContact" class="form-control phone-mask" placeholder="+(123) 456-7890" aria-label="+(123) 456-7890" name="supplierContact"/>
                                </div>

                                <div class="mb-6 col-6">
                                  <label class="form-label" for="supplierVATNumber">VAT Number</label>
                                  <input type="text" id="supplierVATNumber" class="form-control" placeholder="123456789123456" aria-label="123456789123456" name="supplierVATNumber"/>
                                </div>

                                <div class="mb-6 col-6">
                                  <label class="form-label" for="companyCRNumber">CR Number</label>
                                  <input type="text" id="companyCRNumber" class="form-control" placeholder="123456789123456" aria-label="123456789123456" name="companyCRNumber"/>
                                </div>
                              </div>
                            </div>
                            <div class="ecommerce-supplier-add-shiping mb-6">
                              <h6 class="mb-6">Address Information</h6>
                              <div class="mb-6">
                                <label class="form-label" for="supplierAddress1">Address Line 1</label>
                                <input type="text" id="supplierAddress1" class="form-control" placeholder="45 Roker Terrace" aria-label="45 Roker Terrace" name="supplierAddress1"/>
                              </div>
                              <div class="mb-6">
                                <label class="form-label" for="supplierAddress2">Address Line 2</label>
                                <input type="text" id="supplierAddress2" class="form-control" aria-label="address2" name="supplierAddress2"/>
                              </div>
                              <div class="row">
                                <div class=" mb-6">
                                  <label class="form-label" for="supplierCountry">Country*
                                  </label>
                                  <select id="supplierCountry" name="supplierCountry" class="selectpicker form-select w-100" data-style="btn-default" data-live-search="true" onchange="populateStateForSelectedCountry(this.value, 'supplierState', 'supplierCity')">
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
                                  <label class="form-label" for="supplierState">State / Province*</label>
                                  <select id="supplierState" name="supplierState" class="selectpicker w-100" data-style="btn-default" data-live-search="true" onchange="populateCityForSelectedState(this.value, 'supplierCity')">
                                    <option value="">Select a country first</option>
                                  </select>
                                </div>
                                <div class="mb-6 col-6">
                                  <label class="form-label" for="supplierCity">City*</label>
                                  <select id="supplierCity" name="supplierCity" class="selectpicker w-100" data-style="btn-default" data-live-search="true">
                                    <option data-tokens="" value="">Select a state first</option>
                                  </select>
                                </div>
                                <div class="col-12 mb-6">
                                  <label class="form-label" for="supplierPostalCode">Postal Code</label>
                                  <input type="text" id="supplierPostalCode" class="form-control" placeholder="734990" aria-label="734990" name="supplierPostalCode" pattern="[0-9]{8}" maxlength="8"/>
                                </div>
                                <div class="col-12 mb-6">
                                  <label class="form-label" for="supplierWebsite">Website</label>
                                  <input type="text" id="supplierWebsite" class="form-control" placeholder="https://example.com" aria-label="https://example.com" name="supplierWebsite"/>
                                </div>
                              </div>
                            </div>
                            <div class="ecommerce-supplier-add-shiping mb-6">
                              <h6 class="mb-6">Other Information</h6>

                              <div class="row">

                                <div class="col-6 mb-6">
                                  <label class="form-label" for="supplierSalesPaymentTerm">Sales Payment
                                    Term*
                                  </label>
                                  <select id="supplierSalesPaymentTerm" name="supplierSalesPaymentTerm" class="selectpicker w-100" data-style="btn-default" data-live-search="true">
                                    <option value="">Select</option>
                                      <?php
                                      $res = $db->query("SELECT * FROM payment_terms WHERE active = 1");

                                      while ($row = mysqli_fetch_assoc($res)) {
                                          echo '<option value="' . $row['termId'] . '">' . $row['termName'] . '</option>';
                                      }
                                      ?>
                                  </select>
                                </div>

                                <div class=" mb-6 col-6">
                                  <label class="form-label" for="supplierPurchasePaymentTerm">Purchase
                                    Payment Term*
                                  </label>
                                  <select id="supplierPurchasePaymentTerm" name="supplierPurchasePaymentTerm" class="selectpicker form-select w-100" data-style="btn-default" data-live-search="true">
                                    <option value="">Select</option>
                                      <?php
                                      $res = $db->query("SELECT * FROM payment_terms WHERE active = 1");

                                      while ($row = mysqli_fetch_assoc($res)) {
                                          echo '<option value="' . $row['termId'] . '">' . $row['termName'] . '</option>';
                                      }
                                      ?>
                                  </select>
                                </div>

                                <div class="mb-6 col-6">
                                  <label class="form-label" for="supplierCurrency">Currency*</label>
                                  <select id="supplierCurrency" name="supplierCurrency" class="selectpicker w-100" data-style="btn-default" data-live-search="true">
                                    <option value="">Select</option>
                                      <?php
                                      $res = $db->query("SELECT * FROM currencies WHERE active = 1");

                                      while ($row = mysqli_fetch_assoc($res)) {
                                          echo '<option value="' . $row['currencyId'] . '">' . $row['currency'] . " - " . $row['currencyName'] . '</option>';
                                      }
                                      ?>
                                  </select>
                                </div>


                                <div class="mb-6 col-6">
                                  <label class="form-label" for="supplierStatus">Status</label>
                                  <select id="supplierStatus" name="supplierStatus" class="selectpicker w-100" data-style="btn-default" data-live-search="true">
                                    <option data-tokens="active" value="Active">Active</option>
                                    <option data-tokens="inactive" value="Inactive">Inactive</option>
                                  </select>
                                </div>
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


  /**
   * App eCommerce supplier all
   */

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
    var dt_supplier_table = $('.datatables-suppliers'),
      select2 = $('.select2'),
      supplierView = '/suppliers/view/';
    if (select2.length) {
      var $this = select2;
      $this.wrap('<div class="position-relative"></div>').select2({
        placeholder: 'United States ',
        dropdownParent: $this.parent()
      });
    }

    // suppliers datatable
    if (dt_supplier_table.length) {
      var dt_supplier = dt_supplier_table.DataTable({
        // ajax: assetsPath + 'json/ecommerce-supplier-all.json',
        ajax: '/ajax/supplier/fetch_suppliers.php',
        columns: [
          // columns according to JSON
          {data: ''},
          {data: 'companyName'},
          {data: 'supplierId', orderData: 'supplierId'},
          {data: 'city'},
          {data: 'state'},
          {data: 'country'},
          {data: 'phone'},
          {data: 'email'},
          {data: 'vatNumber'}
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
            searchable: false,
            responsivePriority: 3,
            render: function () {
              return '<input type="checkbox" class="dt-checkboxes form-check-input">';
            },
            checkboxes: {
              selectAllRender: '<input type="checkbox" class="form-check-input">'
            }
          },
          {
            // supplier name / Company name
            targets: 2,
            responsivePriority: 1,
            render: function (data, type, full, meta)
            {
              var $name = full['companyName'],
                $email = full['email'],
                $image = '';

              if ($image) {
                // For Avatar image
                var $output =
                  '<img src="' + assetsPath + 'img/avatars/' + $image + '" alt="Avatar" class="rounded-circle">';
              } else {
                // For Avatar badge
                var stateNum = Math.floor(Math.random() * 6);
                var states = ['success', 'danger', 'warning', 'info', 'dark', 'primary', 'secondary'];
                var $state = states[stateNum],
                  $name = full['companyName'],
                  $initials = $name.match(/\b\w/g) || [];
                $initials = (($initials.shift() || '') + ($initials.pop() || '')).toUpperCase();
                $output = '<span class="avatar-initial rounded-circle bg-label-info">' + $initials + '</span>';
              }

              // Creates full output for row
              var $row_output =
                '<div class="d-flex justify-content-start align-items-center supplier-name">' +
                '<div class="avatar-wrapper">' +
                '<div class="avatar avatar-sm me-3">' +
                $output +
                '</div>' +
                '</div>' +
                '<div class="d-flex flex-column">' +
                '<a href="' +
                supplierView + full['supplierId'] +
                '" class="text-heading" ><span class="fw-medium">' +
                $name +
                '</span></a>' +
                '<small>' +
                $email +
                '</small>' +
                '</div>' +
                '</div>';
              return $row_output;
            }
          },
          {
            // supplier ID
            targets: 3,
            render: function (data, type, full, meta) {
              console.log(type);
              var $id = full['supplierId'];

              if (type === 'display') {
                // Display the Supplier ID with '#' prefix
                return "<span class='text-heading'># GMM" + $id + '</span>';
              }
              if (type === 'filter') {
                // Display the Supplier ID with '#' prefix
                return "#" + $id;
              }
              return $id;
            }
          },

          {
            // City
            targets: 4,
            render: function (data, type, full, meta) {
              var $city = full['city'];
              if ($city == null) $city = "-";
              return "<span class='text-heading text-nowrap'>" + $city + '</span>';
            }
          },
          {
            // State
            targets: 5,
            render: function (data, type, full, meta) {
              var $state = full['state'];
              if ($state == null) {
                $state = "-";
              }
              return "<span class='text-heading text-nowrap'>" + $state + '</span>';
            }
          },
          {
            // Country
            targets: 6,
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
          {
            // Phone
            targets: 7,
            render: function (data, type, full, meta) {
              var $phone = full['phone'];
              if ($phone == null) $phone = "-";
              return '<span>' + $phone + '</span>';
            }
          },
          {
            // VAT
            targets: 8,
            render: function (data, type, full, meta) {
              var $vatNumber = full['vatNumber'];
              if ($vatNumber == null) $vatNumber = "-";
              return '<span class="fw-medium text-heading">' + $vatNumber + '</span>';
            }
          }
        ],
        order: [[3, 'asc']],
        dom:
          '<"card-header d-flex flex-wrap flex-md-row flex-column align-items-start align-items-sm-center py-0"' +
          '<"d-flex align-items-center me-5"f>' +
          '<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-md-end flex-wrap flex-sm-nowrap mb-6 mb-sm-0"lB>' +
          '>t' +
          '<"row mx-1"' +
          '<"col-sm-12 col-md-6"i>' +
          '<"col-sm-12 col-md-6"p>' +
          '>',

        language: {
          sLengthMenu: '_MENU_',
          search: '',
          searchPlaceholder: 'Search',
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
            text: '<i class="ti ti-upload ti-xs me-2"></i>Export',
            buttons: [
              {
                extend: 'print',
                text: '<i class="ti ti-printer me-2" ></i>Print',
                className: 'dropdown-item',
                exportOptions: {
                  columns: [1, 2, 3, 4, 5, 6],
                  // prevent avatar to be print
                  format: {
                    body: function (inner, coldex, rowdex) {
                      if (inner.length <= 0) return inner;
                      var el = $.parseHTML(inner);
                      var result = '';
                      $.each(el, function (index, item) {
                        if (item.classList !== undefined && item.classList.contains('supplier-name')) {
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
                  //customize print view for dark
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
                  columns: [1, 2, 3, 4, 5, 6],
                  // prevent avatar to be display
                  format: {
                    body: function (inner, coldex, rowdex) {
                      if (inner.length <= 0) return inner;
                      var el = $.parseHTML(inner);
                      var result = '';
                      $.each(el, function (index, item) {
                        if (item.classList !== undefined && item.classList.contains('supplier-name')) {
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
                  columns: [1, 2, 3, 4, 5, 6],
                  // prevent avatar to be display
                  format: {
                    body: function (inner, coldex, rowdex) {
                      if (inner.length <= 0) return inner;
                      var el = $.parseHTML(inner);
                      var result = '';
                      $.each(el, function (index, item) {
                        if (item.classList !== undefined && item.classList.contains('supplier-name')) {
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
                  columns: [1, 2, 3, 4, 5, 6],
                  // prevent avatar to be display
                  format: {
                    body: function (inner, coldex, rowdex) {
                      if (inner.length <= 0) return inner;
                      var el = $.parseHTML(inner);
                      var result = '';
                      $.each(el, function (index, item) {
                        if (item.classList !== undefined && item.classList.contains('supplier-name')) {
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
                text: '<i class="ti ti-copy me-2" ></i>Copy',
                className: 'dropdown-item',
                exportOptions: {
                  columns: [1, 2, 3, 4, 5, 6],
                  // prevent avatar to be display
                  format: {
                    body: function (inner, coldex, rowdex) {
                      if (inner.length <= 0) return inner;
                      var el = $.parseHTML(inner);
                      var result = '';
                      $.each(el, function (index, item) {
                        if (item.classList !== undefined && item.classList.contains('supplier-name')) {
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
            text: '<i class="ti ti-plus me-0 me-sm-1 mb-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add Supplier</span>',
            className: 'add-new btn btn-primary waves-effect waves-light',
            attr: {
              'data-bs-toggle': 'offcanvas',
              'data-bs-target': '#offcanvasSupplierAdd'
            }
          }
        ],

        // For responsive popup
        responsive: {
          details: {
            display: $.fn.dataTable.Responsive.display.modal({
              header: function (row) {
                var data = row.data();
                return 'Details of ' + data['companyName'];
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
        }
      });
      $('.dataTables_length').addClass('ms-n2 me-2');
      $('.dt-action-buttons').addClass('pt-0');
      $('.dataTables_filter').addClass('ms-n3 mb-0 mb-md-6');
      $('.dt-buttons').addClass('d-flex flex-wrap');
    }

    // Filter form control to default size
    // ? setTimeout used for multilingual table initialization
    setTimeout(() => {
      $('.dataTables_filter .form-control').removeClass('form-control-sm');
      $('.dataTables_length .form-select').removeClass('form-select-sm');
    }, 300);
  });

  document.addEventListener('DOMContentLoaded', function ()
  {
    const params = new URLSearchParams(window.location.search);
    const currentPath = window.location.pathname;

    // Check if 'addNew' is present in the query string
    if (params.has('addnew') || currentPath.includes('/new')) {
      const offcanvasElement = document.getElementById('offcanvasSupplierAdd');
      if (offcanvasElement) {
        const offcanvasInstance = new bootstrap.Offcanvas(offcanvasElement);
        offcanvasInstance.show();
      } else {
        console.error("Offcanvas element not found.");
      }
    }
  });

  $(document).ready(function ()
  {
    //Form Validation
    const SupplierAddForm = document.getElementById('supplierAddForm');

    // Add New supplier Form Validation
    const fv = FormValidation.formValidation(SupplierAddForm, {
      fields: {
        supplierName: {
          validators: {
            notEmpty: {
              message: 'Please enter supplier or company name '
            }
          }
        }, supplierNameAR: {
          validators: {
            notEmpty: {
              message: 'Please enter supplier or company name in arabic '
            }
          }
        },
        supplierEmail: {
          validators: {
            notEmpty: {
              message: 'Please enter your email'
            },
            emailAddress: {
              message: 'The value is not a valid email address'
            }
          }
        },
        supplierCountry: {
          validators: {
            notEmpty: {
              message: 'Please select the Country'
            }
          }
        },
        supplierState: {
          validators: {
            notEmpty: {
              message: 'Please select the State'
            }
          }
        },
        supplierCity: {
          validators: {
            notEmpty: {
              message: 'Please select the City'
            }
          }
        },
        supplierSalesPaymentTerm: {
          validators: {
            notEmpty: {
              message: 'Please select the Sales Payment Terms'
            }
          }
        },
        supplierPurchasePaymentTerm: {
          validators: {
            notEmpty: {
              message: 'Please select the Purchase Payment Terms'
            }
          }
        },
        supplierCurrency: {
          validators: {
            notEmpty: {
              message: 'Please select the Currency'
            }
          }
        },
        supplierContact: {
          validators: {
            notEmpty: {
              message: 'Please enter phone number'
            },
            regexp: {
              regexp: /^\+?[0-9\s]+$/,
              message: 'The phone number can only contain numbers, spaces, and an optional "+"'
            }
          }
        }
      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({
          // Use this for enabling/changing valid/invalid class
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

    $("#supplierAddForm").submit(function (e) {
      e.preventDefault();

      fv.validate().then(function (status) {
        if (status === 'Valid') {

          blockArea($('.form-block'));

          var form = $('form')[0]; // You need to use standard javascript object here
          var formData = new FormData(form);

          $.ajax({
            url: '/ajax/supplier/add_supplier.php',
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
                    dismissOffcanvas('offcanvasSupplierAdd');
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


</script>
<style>
    #offcanvasSupplierAdd {
        width: 850px !important;
    }
</style>
</body>
</html>