<?php
$PAGE_ID = "CONSULTATION_RFP_LIST";
include_once __DIR__ . "/../../includes/baseIncludes.php";
include_once __DIR__ . "/../../includes/auth_check.php";

$filter = filter_input(INPUT_GET, 'filter', FILTER_SANITIZE_SPECIAL_CHARS);
?>
<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
<head>
    <?php include_once __DIR__ . "/../../includes/dashboard/dashboard_head_section.php"; ?>
  <title>GrandMaster ERP | Consultation RFPs</title>
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
                  <h4 class="my-0">All Consultation RFPs</h4>
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
          <!-- Quotation List Table -->
          <div class="card">

            <div class="card-datatable table-responsive">
              <table class="invoice-list-table table border-top">
                <thead>
                <tr>
                  <th></th>
                  <th></th>
                  <th class="text-truncate">RFP #</th>
                  <th class="cell-fit">Customer</th>
                  <th>Payment Term</th>
                  <th>Sales Order</th>
                  <th>Reason</th>
                  <th>Status</th>
                  <th class="text-truncate">Date</th>
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

    // RFP datatable
    if (dt_invoice_table.length) {

      let ajaxURL = '/ajax/consultation/fetch_rfps.php';

      let filter = '<?=$filter ?>';
      if(filter !== '')
      {
        ajaxURL += '?filter=' + filter;
      }

      var dt_invoice = dt_invoice_table.DataTable({
        ajax: ajaxURL,
        columns: [
          // columns according to JSON
          {data: 'rfpId'},
          {data: 'rfpId'},
          {data: 'rfpNumber'},
          {data: 'companyName'},
          {data: 'termName'},
          {data: 'saleOrderId'},
          {data: 'rfpReason'},
          {data: 'rfpStatus'},
          {data: 'rfpDateCreated'},
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
            render: function (data, type, full, meta) {
              var $rfpId = full['rfpId'];
              // return (
              //     '<div class="d-flex align-items-center">' +
              //     // '<a href="javascript:;" data-bs-toggle="tooltip" class="btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill" data-bs-placement="top" title="Delete"><i class="ti ti-trash mx-2 ti-md"></i></a>' +
              //     '<a href="/quotation/preview/' + $documentId + '" data-bs-toggle="tooltip" class="btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill" data-bs-placement="top" title="Preview Quotation"><i class="ti ti-eye mx-2 ti-md"></i></a>' +
              //     '<div class="dropdown">' +
              //     '<a href="javascript:;" class="btn dropdown-toggle hide-arrow btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill p-0" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-md"></i></a>' +
              //     '<div class="dropdown-menu dropdown-menu-end">' +
              //     '<a href="/ajax/documents/generate_pdf.php?isdownload=1&documentID=' + $documentId + '" class="dropdown-item">Download</a>' +
              //     '<a href="/quotation/edit/' + $documentId + '" class="dropdown-item">Edit</a>' +
              //     '</div>' +
              //     '</div>'
              // );

              return (
                '<div class="d-flex align-items-center">' +
                // '<a href="javascript:;" data-bs-toggle="tooltip" class="btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill" data-bs-placement="top" title="Delete"><i class="ti ti-trash mx-2 ti-md"></i></a>' +
                // '<a href="/rfp/edit/' + $rfpId + '" data-bs-toggle="tooltip" class="btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill" data-bs-placement="top" title=""><i class="ti ti-eye mx-2 ti-md"></i></a>' +
                '<div class="dropdown">' +
                '<a href="javascript:;" class="btn dropdown-toggle hide-arrow btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill p-0" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-md"></i></a>' +
                '<div class="dropdown-menu dropdown-menu-end">' +
                '<a href="/ajax/rfpdocument/generate_pdf.php?isdownload=1&rfpID=' + $rfpId + '" class="dropdown-item" target="_blank">Download</a>' +
                '<a href="/consultation/rfp/edit/' + $rfpId + '" class="dropdown-item">Edit</a>' +
                '</div>' +
                '</div>'
              );
            }
          },

          {
            // RFP Number
            targets: 2,
            render: function (data, type, full, meta) {
              var $rfpNumber = full['rfpNumber'];
              var $rfpId = full['rfpId'];
              var $rfpDateCreated = full['rfpDateCreated'];

              // Creates full output for row
              // if ($quotationNumber === null) $quotationNumber = "Draft";
              var $row_output = '<a class="text-truncate" href="/consultation/rfp/edit/' + $rfpId + '"># ' + $rfpNumber + '</a>';
              // '<br><small class="text-truncate">' + moment($quotationDate).format('DD MMM YYYY') + '</small>';
              return $row_output;
            }
          },
          {
            // Supplier
            targets: 3,
            responsivePriority: 4,

            render: function (data, type, full, meta) {
              var $name = full['companyName'];
              var $supplierId = full['supplierId'];
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
                '<a href="/suppliers/view/' + $supplierId + '" class="text-heading text-truncate"><span class="fw-medium">' +
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
            // Sales Order
            targets: 5,
            responsivePriority: 2,
            render: function (data, type, full, meta) {
              var $salesOrder = full['saleOrderId'];

              return '<span class=text-truncate>' + $salesOrder + '</span>';
            }
          },
          {
            // Status
            targets: 7,
            render: function (data, type, full, meta) {
              var $status = full['rfpStatus'];
              let $badge_class = '';

              //'NEW','AWAITING APPROVAL','APPROVED','SENT TO CUSTOMER','ACCEPTED','REJECTED','CONFIRMED','CANCELLED'

              if ($status === "NEW") {
                $badge_class = 'bg-label-linkedin';
                return ('<span class="badge ' + $badge_class + '" text-capitalized> DRAFT </span>');
              } else if ($status === "AWAITING PROCUREMENT MANAGER APPROVAL") {
                $badge_class = 'bg-label-warning';
                return ('<span class="badge ' + $badge_class + '" text-capitalized>' + $status + '</span>');
              } else if ($status === "PROCUREMENT MANAGER APPROVED") {
                $badge_class = 'bg-label-success';
                return ('<span class="badge ' + $badge_class + '" text-capitalized>' + $status + '</span>');
              } else if ($status === "PROCUREMENT MANAGER REJECTED") {
                $badge_class = 'bg-label-danger';
                return ('<span class="badge ' + $badge_class + '" text-capitalized>' + $status + '</span>');
              } else if ($status === "RFP SENT TO SUPPLIER") {
                $badge_class = 'bg-label-info';
                return ('<span class="badge ' + $badge_class + '" text-capitalized>' + $status + '</span>');
              }

              return ('<span text-capitalized>' + $status + '</span>');
            }
          },
          {
            // RFP Date
            targets: 8,
            render: function (data, type, full, meta) {
              var $rfpDate = new Date(full['rfpDateCreated']);

              let $row_output;
              if (full['rfpDateCreated'] != null) {
                $row_output = '<span class="d-none">' + moment($rfpDate).format('YYYYMMDD') + '</span><span class="text-truncate">' + moment($rfpDate).format('DD MMM YYYY') + '</span>';
              } else {
                $row_output = "-";
              }

              return $row_output;
            }
          },
          {
            // Reason
            targets: 6,
            responsivePriority: 2,
            render: function (data, type, full, meta) {
              var $reason = full['rfpReason'];

              return '<span>' + $reason + '</span>';
            }
          },
          {
            // Sold By
            targets: 10,
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
          searchPlaceholder: 'Search RFP',
          paginate: {
            next: '<i class="ti ti-chevron-right ti-sm"></i>',
            previous: '<i class="ti ti-chevron-left ti-sm"></i>'
          }
        },
        // Buttons with Dropdown
        buttons: [
          {
            text: '<i class="ti ti-plus ti-xs me-md-2"></i><span class="d-md-inline-block d-none">New RFP</span>',
            className: 'btn btn-primary waves-effect waves-light',
            action: function (e, dt, button, config) {

              window.open('/consultation/rfp/new', '_blank');

            }
          }
        ],
        // For responsive popup
        responsive: {
          details: {
            display: $.fn.dataTable.Responsive.display.modal({
              header: function (row) {
                var data = row.data();
                return 'Details of ' + data['rfpNumber'];
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