<?php
$PAGE_ID = "";
include_once __DIR__ . "/../../includes/baseIncludes.php";
include_once __DIR__ . "/../../includes/auth_check.php";

$filter = filter_input(INPUT_GET, 'filter', FILTER_SANITIZE_SPECIAL_CHARS);

$orderType = '';
if($filter == 'SALES_ORDER') {
    $orderType = 'Sales Order';
    $PAGE_ID = "ORDER_MANAGEMENT_LIST_SALES_ORDER";
}
if($filter == 'TRANSFER_WH') {
  $orderType = 'WH Transfers';
    $PAGE_ID = "ORDER_MANAGEMENT_LIST_WH_TRANSFERS";
}
if($filter == 'ALL') {
    $orderType = 'Pick & Pack';
    $PAGE_ID = "ORDER_MANAGEMENT_LIST_PICK_AND_PACK";
}

?>
<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
<head>
    <?php include_once __DIR__ . "/../../includes/dashboard/dashboard_head_section.php"; ?>
  <title>GrandMaster ERP | Order Management</title>
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
      <nav class="layout-navbar container-fluid navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
          <?php include_once __DIR__ . "/../../includes/dashboard/top_navbar.php"; ?>
      </nav>
      <!-- / Navbar -->
      <!-- Content wrapper -->
      <div class="content-wrapper">
        <!-- Content -->
        <div class="container-fluid flex-grow-1 container-p-y">
          <div class="card mb-6">
            <div class="card-widget-separator-wrapper">
              <div class="card-body card-widget-separator py-2">
                <div class="row gy-1 gy-sm-1">
                  <h4 class="my-0">Order Management | <?= $orderType ?></h4>
                </div>
              </div>
            </div>
          </div>
          <div class="card">

            <div class="card-datatable table-responsive">
              <table class="invoice-list-table table border-top">
                <thead>
                <tr>
                  <th></th>
                  <th></th>
                  <th>Orders</th>
                  <?php if ($filter == 'ALL') { ?>
                  <th>Internal Ref.</th>
                  <th>Qty</th>
                  <?php
                  }
                  ?>
                  <th>Status</th>
                  <th>Origin</th>
                  <th class="text-wrap">Destination</th>
                  <th>Customer ACC</th>
                  <th>Transaction Type</th>
                  <th>Shipment#</th>
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
<?php
include_once __DIR__ . "/../../includes/dashboard/dashboard_footer_scripts.php";
?>

<script>
  $(".selectpicker").selectpicker();

  'use strict';
  $(function () {
    // Variable declaration for table
    var dt_invoice_table = $('.invoice-list-table');

    //Transfer datatable
    if (dt_invoice_table.length) {

      let ajaxURL = '/ajax/ordermanagement/fetch_filter_orders.php';

      let filter = '<?=$filter ?>';
      if (filter !== '') {
        ajaxURL += '?filter=' + filter;
      }

      if (filter === 'ALL') {
        var dt_invoice = dt_invoice_table.DataTable({
          ajax: ajaxURL,
          columns: [
            // columns according to JSON
            { data: 'documentId' },
            { data: 'documentId' },
            { data: 'orderNumber' },
            { data: 'internalRef' },
            { data: 'quantity' },
            { data: 'orderWHStatus' },
            { data: 'originWHName' },
            { data: 'destinationWHName' },
            { data: 'customerAccount' },
            { data: 'transactionType' },
            { data: 'shipmentNumber' },
            { data: 'action' }
          ],
          columnDefs: [
            {
              // For Responsive
              className: 'control',
              responsivePriority: 2,
              searchable: false,
              targets: 0,
              render: function(data, type, full, meta) {
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
              render: function() {
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
              render: function(data, type, full, meta) {
                var $orderManagementId = full['orderManagementId'];
                // return (
                //   '<div class="d-flex align-items-center">' +
                //   '<div class="dropdown">' +
                //   '<a href="javascript:;" class="btn dropdown-toggle hide-arrow btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill p-0" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-md"></i></a>' +
                //   '<div class="dropdown-menu dropdown-menu-end">' +
                //   // '<a href="/ajax/rfpdocument/generate_pdf.php?isdownload=1&rfpID=' + $rfpId + '" class="dropdown-item" target="_blank">Download</a>' +
                //   '<a href="/warehouse/transfer/edit/' + $transferId + '" class="dropdown-item">Edit</a>' +
                //   '</div>' +
                //   '</div>'
                // );
                return (
                  '<div class="d-flex align-items-center">' +
                  '<a href="/order-management/picknpack/new?refOrderID=' + $orderManagementId + '" data-bs-toggle="tooltip" class="btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill" data-bs-placement="top" title="Edit"><i class="ti ti-edit mx-2 ti-md"></i></a>' +
                  '</div>'
                );
              }
            },

            {
              // Order
              targets: 2,
              responsivePriority: 2,
              render: function(data, type, full, meta) {
                var $orderNumber = full['orderNumber'];
                var $orderManagementId = full['orderManagementId'];

                // Creates full output for row
                // if ($quotationNumber === null) $quotationNumber = "Draft";
                var $row_output = '<a class="text-truncate" href="/order-management/picknpack/new?refOrderID=' + $orderManagementId + '"># ' + $orderNumber + '</a>';
                return $row_output;
              }
            },

            {
              // Internal Ref.
              targets: 3,
              responsivePriority: 4,

              render: function(data, type, full, meta) {
                var $internalRef = full['internalRef'];

                return '<span class=text-truncate>' + $internalRef + '</span>';
              }
            },

            {
              // Internal Ref.
              targets: 4,
              responsivePriority: 4,

              render: function(data, type, full, meta) {
                var $quantity = full['quantity'];

                return '<span class=text-truncate>' + $quantity + '</span>';
              }
            },

            {
              // Order Status
              targets: 5,
              responsivePriority: 4,

              render: function(data, type, full, meta) {
                let $badge_class = '';
                var $orderStatus = full['orderWHStatus'];

                if ($orderStatus == 'ALLOCATED') {
                  $badge_class = 'bg-label-secondary';
                  return ('<span class="badge ' + $badge_class + '" text-capitalized>' + $orderStatus + ' </span>');
                } else if ($orderStatus == 'STAGED') {
                  $badge_class = 'bg-label-linkedin';
                  return ('<span class="badge ' + $badge_class + '" text-capitalized>' + $orderStatus + ' </span>');
                } else if ($orderStatus == 'RESERVED') {
                  $badge_class = 'bg-label-info';
                  return ('<span class="badge ' + $badge_class + '" text-capitalized>' + $orderStatus + ' </span>');
                } else if ($orderStatus == 'READY') {
                  $badge_class = 'bg-label-warning';
                  return ('<span class="badge ' + $badge_class + '" text-capitalized>' + $orderStatus + ' </span>');
                } else if ($orderStatus == 'SHIPPED') {
                  $badge_class = 'bg-label-success';
                  return ('<span class="badge ' + $badge_class + '" text-capitalized>' + $orderStatus + ' </span>');
                }

                return '<span class=text-capitalized>' + $orderStatus + '</span>';
              }
            },
            {
              // Origin WH
              targets: 6,
              responsivePriority: 4,

              render: function(data, type, full, meta) {
                var $originWHName = full['originWHName'];
                var $originWHCode = full['originWHCode'];

                var $row_output =
                  '<div class="d-flex flex-column">' +
                  '<span class=text-truncate><strong>' + $originWHName + '</strong></span>' +
                  '<small class="text-center">' +
                  $originWHCode +
                  '</small>' +
                  '</div>' +
                  '</div>';

                return $row_output;
              }
            },

            {
              // Destination WH
              targets: 7,
              responsivePriority: 2,
              render: function(data, type, full, meta) {
                var $destinationWHName = full['destinationWHName'];

                return '<span>' + $destinationWHName + '</span>';
              }
            },

            {
              // Customer Account
              targets: 8,
              responsivePriority: 2,
              render: function(data, type, full, meta) {
                var $customerAccount = full['customerAccount'];

                return '<span class=text-truncate>' + $customerAccount + '</span>';
              }
            },

            {
              // Transaction Type
              targets: 9,
              responsivePriority: 2,
              render: function(data, type, full, meta) {
                var $transactionType = full['transactionType'];

                return '<span class=text-truncate>' + $transactionType + '</span>';
              }
            },

            {
              // Shipment #
              targets: 10,
              responsivePriority: 2,
              render: function(data, type, full, meta) {
                var $shipmentNumber = full['shipmentNumber'];

                return '<span class=text-truncate>' + $shipmentNumber + '</span>';
              }
            },

          ],
          order: [[10, 'desc']],
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
            searchPlaceholder: 'Search Order',
            paginate: {
              next: '<i class="ti ti-chevron-right ti-sm"></i>',
              previous: '<i class="ti ti-chevron-left ti-sm"></i>'
            }
          },
          // Buttons with Dropdown
          // buttons: [
            // {
            //   text: '<i class="ti ti-report-analytics ti-xs me-md-2"></i><span class="d-md-inline-block d-none">Sales Order</span>',
            //   className: 'btn btn-info waves-effect waves-light me-2', // Added me-2 for margin
            //   action: function (e, dt, button, config) {
            //     // window.location = '/warehouse/transfer/new';
            //   }
            // },
            // {
            //   text: '<i class="ti ti-transfer ti-xs me-md-2"></i><span class="d-md-inline-block d-none">Transfer WH</span>',
            //   className: 'btn btn-info waves-effect waves-light me-2', // Added me-2 for margin
            //   action: function (e, dt, button, config) {
            //     // Add your export functionality here
            //     console.log('Export button clicked');
            //   }
            // },
            // {
            //   text: '<i class="ti ti-packages ti-xs me-md-2"></i><span class="d-md-inline-block d-none">Pick & Pack</span>',
            //   className: 'btn btn-info waves-effect waves-light',
            //   action: function (e, dt, button, config) {
            //     // Add your filter functionality here
            //     console.log('Filter button clicked');
            //   }
            // }
          // ],
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
                    columns: [2, 3, 4, 5, 6, 7, 8, 9, 10],
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
                    columns: [2, 3, 4, 5, 6, 7, 8, 9, 10],
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
                    columns: [2, 3, 4, 5, 6, 7, 8, 9, 10],
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
                    columns: [2, 3, 4, 5, 6, 7, 8, 9, 10],
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
          ],
          // For responsive popup
          responsive: {
            details: {
              display: $.fn.dataTable.Responsive.display.modal({
                header: function(row) {
                  var data = row.data();
                  return 'Details of ' + data['orderNumber'];
                }
              }),
              type: 'column',
              renderer: function(api, rowIdx, columns) {
                var data = $.map(columns, function(col, i) {
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
          initComplete: function() {
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
      } else {

        var dt_invoice = dt_invoice_table.DataTable({
          ajax: ajaxURL,
          columns: [
            // columns according to JSON
            { data: 'documentId' },
            { data: 'documentId' },
            { data: 'orderNumber' },
            { data: 'orderWHStatus' },
            { data: 'originWHName' },
            { data: 'destinationWHName' },
            { data: 'customerAccount' },
            { data: 'transactionType' },
            { data: 'shipmentNumber' },
            { data: 'action' }
          ],
          columnDefs: [
            {
              // For Responsive
              className: 'control',
              responsivePriority: 2,
              searchable: false,
              targets: 0,
              render: function(data, type, full, meta) {
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
              render: function() {
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
              render: function(data, type, full, meta) {
                var $orderManagementId = full['orderManagementId'];
                // return (
                //   '<div class="d-flex align-items-center">' +
                //   '<div class="dropdown">' +
                //   '<a href="javascript:;" class="btn dropdown-toggle hide-arrow btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill p-0" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-md"></i></a>' +
                //   '<div class="dropdown-menu dropdown-menu-end">' +
                //   // '<a href="/ajax/rfpdocument/generate_pdf.php?isdownload=1&rfpID=' + $rfpId + '" class="dropdown-item" target="_blank">Download</a>' +
                //   '<a href="/warehouse/transfer/edit/' + $transferId + '" class="dropdown-item">Edit</a>' +
                //   '</div>' +
                //   '</div>'
                // );
                return (
                  '<div class="d-flex align-items-center">' +
                  '<a href="/order-management/order/edit/' + $orderManagementId + '" data-bs-toggle="tooltip" class="btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill" data-bs-placement="top" title="Edit"><i class="ti ti-edit mx-2 ti-md"></i></a>' +
                  '</div>'
                );
              }
            },

            {
              // Order
              targets: 2,
              responsivePriority: 2,
              render: function(data, type, full, meta) {
                var $orderNumber = full['orderNumber'];
                var $orderManagementId = full['orderManagementId'];

                // Creates full output for row
                // if ($quotationNumber === null) $quotationNumber = "Draft";
                var $row_output = '<a class="text-truncate" href="/order-management/order/edit/' + $orderManagementId + '"># ' + $orderNumber + '</a>';
                return $row_output;
              }
            },
            {
              // Order Status
              targets: 3,
              responsivePriority: 4,

              render: function(data, type, full, meta) {
                let $badge_class = '';
                var $orderStatus = full['orderWHStatus'];

                if ($orderStatus == 'ALLOCATED') {
                  $badge_class = 'bg-label-secondary';
                  return ('<span class="badge ' + $badge_class + '" text-capitalized>' + $orderStatus + ' </span>');
                } else if ($orderStatus == 'STAGED') {
                  $badge_class = 'bg-label-linkedin';
                  return ('<span class="badge ' + $badge_class + '" text-capitalized>' + $orderStatus + ' </span>');
                } else if ($orderStatus == 'RESERVED') {
                  $badge_class = 'bg-label-info';
                  return ('<span class="badge ' + $badge_class + '" text-capitalized>' + $orderStatus + ' </span>');
                } else if ($orderStatus == 'READY') {
                  $badge_class = 'bg-label-warning';
                  return ('<span class="badge ' + $badge_class + '" text-capitalized>' + $orderStatus + ' </span>');
                } else if ($orderStatus == 'SHIPPED') {
                  $badge_class = 'bg-label-success';
                  return ('<span class="badge ' + $badge_class + '" text-capitalized>' + $orderStatus + ' </span>');
                }

                return '<span class="text-capitalized badge bg-label-dark">' + $orderStatus + '</span>';
              }
            },
            {
              // Origin WH
              targets: 4,
              responsivePriority: 4,

              render: function(data, type, full, meta) {
                var $originWHName = full['originWHName'];

                return '<span class=text-truncate>' + $originWHName + '</span>';
              }
            },

            {
              // Destination WH
              targets: 5,
              responsivePriority: 2,
              render: function(data, type, full, meta) {
                var $destinationWHName = full['destinationWHName'];

                return '<span>' + $destinationWHName + '</span>';
              }
            },

            {
              // Customer Account
              targets: 6,
              responsivePriority: 2,
              render: function(data, type, full, meta) {
                var $customerAccount = full['customerAccount'];

                return '<span class=text-truncate>' + $customerAccount + '</span>';
              }
            },

            {
              // Transaction Type
              targets: 7,
              responsivePriority: 2,
              render: function(data, type, full, meta) {
                var $transactionType = full['transactionType'];

                return '<span class=text-truncate>' + $transactionType + '</span>';
              }
            },

            {
              // Shipment #
              targets: 8,
              responsivePriority: 2,
              render: function(data, type, full, meta) {
                var $shipmentNumber = full['shipmentNumber'];

                return '<span class=text-truncate>' + $shipmentNumber + '</span>';
              }
            },

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
            searchPlaceholder: 'Search Order',
            paginate: {
              next: '<i class="ti ti-chevron-right ti-sm"></i>',
              previous: '<i class="ti ti-chevron-left ti-sm"></i>'
            }
          },
          // Buttons with Dropdown
          buttons: [
            // {
            //   text: '<i class="ti ti-report-analytics ti-xs me-md-2"></i><span class="d-md-inline-block d-none">Sales Order</span>',
            //   className: 'btn btn-info waves-effect waves-light me-2', // Added me-2 for margin
            //   action: function (e, dt, button, config) {
            //     // window.location = '/warehouse/transfer/new';
            //   }
            // },
            // {
            //   text: '<i class="ti ti-transfer ti-xs me-md-2"></i><span class="d-md-inline-block d-none">Transfer WH</span>',
            //   className: 'btn btn-info waves-effect waves-light me-2', // Added me-2 for margin
            //   action: function (e, dt, button, config) {
            //     // Add your export functionality here
            //     console.log('Export button clicked');
            //   }
            // },
            // {
            //   text: '<i class="ti ti-packages ti-xs me-md-2"></i><span class="d-md-inline-block d-none">Pick & Pack</span>',
            //   className: 'btn btn-info waves-effect waves-light',
            //   action: function (e, dt, button, config) {
            //     // Add your filter functionality here
            //     console.log('Filter button clicked');
            //   }
            // }
          ],
          // For responsive popup
          responsive: {
            details: {
              display: $.fn.dataTable.Responsive.display.modal({
                header: function(row) {
                  var data = row.data();
                  return 'Details of ' + data['orderNumber'];
                }
              }),
              type: 'column',
              renderer: function(api, rowIdx, columns) {
                var data = $.map(columns, function(col, i) {
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
          initComplete: function() {
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