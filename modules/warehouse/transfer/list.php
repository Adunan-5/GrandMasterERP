<?php
$PAGE_ID = "TRANSFER_LIST";
include_once __DIR__ . "/../../../includes/baseIncludes.php";
include_once __DIR__ . "/../../../includes/auth_check.php";

$filter = filter_input(INPUT_GET, 'filter', FILTER_SANITIZE_SPECIAL_CHARS);
?>
<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
<head>
    <?php include_once __DIR__ . "/../../../includes/dashboard/dashboard_head_section.php"; ?>
  <title>GrandMaster ERP | Warehouse Transfer</title>
  <link rel="stylesheet" href="/assets/vendor/libs/bootstrap-select/bootstrap-select.css"/>
  <link rel="stylesheet" href="/assets/vendor/libs/animate-css/animate.css"/>
  <link rel="stylesheet" href="/assets/vendor/libs/sweetalert2/sweetalert2.css"/>
    <?php include_once __DIR__ . "/../../../includes/dashboard/dashboard_head_section_post_vendor.php"; ?>
</head>
<body>
<!-- Layout wrapper -->
<div class="layout-wrapper layout-content-navbar">
  <div class="layout-container">
    <!-- Menu -->
    <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
        <?php include_once __DIR__ . "/../../../includes/dashboard/menu_ceo.php" ?>
    </aside>
    <!-- / Menu -->
    <!-- Layout container -->
    <div class="layout-page">
      <!-- Navbar -->
      <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
          <?php include_once __DIR__ . "/../../../includes/dashboard/top_navbar.php"; ?>
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
                  <h4 class="my-0">All Transfers</h4>
                </div>
              </div>
            </div>
          </div>
          <!-- Invoice List Widget -->
<!--          <div class="card mb-6">-->
<!--            <div class="card-widget-separator-wrapper">-->
<!--              <div class="card-body card-widget-separator">-->
<!--                <div class="row gy-4 gy-sm-1">-->
<!--                  <div class="col-sm-6 col-lg-4">-->
<!--                    <div class="d-flex justify-content-between align-items-center card-widget-1 border-end pb-4 pb-sm-0">-->
<!--                      <div>-->
<!--                        <h4 class="mb-0">24</h4>-->
<!--                        <p class="mb-0">Active</p>-->
<!--                      </div>-->
<!--                      <div class="avatar me-sm-6">-->
<!--                            <span class="avatar-initial rounded bg-label-secondary text-heading">-->
<!--                              <i class="ti ti-user ti-26px"></i>-->
<!--                            </span>-->
<!--                      </div>-->
<!--                    </div>-->
<!--                    <hr class="d-none d-sm-block d-lg-none me-6"/>-->
<!--                  </div>-->
<!--                  <div class="col-sm-6 col-lg-4">-->
<!--                    <div class="d-flex justify-content-between align-items-center card-widget-2 border-end pb-4 pb-sm-0">-->
<!--                      <div>-->
<!--                        <h4 class="mb-0">165</h4>-->
<!--                        <p class="mb-0">Confirmed</p>-->
<!--                      </div>-->
<!--                      <div class="avatar me-lg-6">-->
<!--                            <span class="avatar-initial rounded bg-label-secondary text-heading">-->
<!--                              <i class="ti ti-file-invoice ti-26px"></i>-->
<!--                            </span>-->
<!--                      </div>-->
<!--                    </div>-->
<!--                    <hr class="d-none d-sm-block d-lg-none"/>-->
<!--                  </div>-->
<!--                  <div class="col-sm-6 col-lg-4">-->
<!--                    <div class="d-flex justify-content-between align-items-center border-end pb-4 pb-sm-0 card-widget-3">-->
<!--                      <div>-->
<!--                        <h4 class="mb-0">SAR 2.46k</h4>-->
<!--                        <p class="mb-0">Total Value</p>-->
<!--                      </div>-->
<!--                      <div class="avatar me-sm-6">-->
<!--                            <span class="avatar-initial rounded bg-label-secondary text-heading">-->
<!--                              <i class="ti ti-checks ti-26px"></i>-->
<!--                            </span>-->
<!--                      </div>-->
<!--                    </div>-->
<!--                  </div>-->
<!--                </div>-->
<!--              </div>-->
<!--            </div>-->
<!--          </div>-->
          <!-- Quotation List Table -->
          <div class="card">

            <div class="card-datatable table-responsive">
              <table class="invoice-list-table table border-top">
                <thead>
                <tr>
                  <th></th>
                  <th></th>
                  <th>Transfer#</th>
                  <th>Origin</th>
                  <th>Destination</th>
                  <th>Reason for Transfer</th>
                  <th>Status</th>
<!--                  <th>Received</th>-->
                  <th>Date</th>
                  <th>Action</th>
<!--                  <th>ETA</th>-->
                </tr>
                </thead>
              </table>
            </div>
          </div>
        </div>
        <!-- / Content -->
        <!-- Footer -->
        <footer class="content-footer footer bg-footer-theme">
            <?php include_once __DIR__ . "/../../../includes/dashboard/dashboard_footer_section.php"; ?>
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
include_once __DIR__ . "/../../../includes/dashboard/dashboard_footer_scripts.php";
?>

<script>
  $(".selectpicker").selectpicker();

  'use strict';
  $(function () {
    // Variable declaration for table
    var dt_invoice_table = $('.invoice-list-table');

    //Transfer datatable
    if (dt_invoice_table.length) {

      let ajaxURL = '/ajax/transfers/fetch_transfers.php';

      //let filter = '<?php //=$filter ?>//';
      //if(filter !== '')
      //{
      //  ajaxURL += '?filter=' + filter;
      //}

      var dt_invoice = dt_invoice_table.DataTable({
        ajax: ajaxURL,
        columns: [
          // columns according to JSON
          {data: 'transferId'},
          {data: 'transferId'},
          {data: 'transferNumber'},
          {data: 'originWHName'},
          {data: 'destinationWHName'},
          {data: 'transferReason'},
          {data: 'transferStatus'},
          {data: 'transferDateCreated'},
          // {data: 'rfpStatus'},
          // {data: 'rfpDateCreated'},
          // {data: 'salesPersonName'},
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
              var $transferId = full['transferId'];

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
                '<a href="/warehouse/transfer/edit/' + $transferId + '" data-bs-toggle="tooltip" class="btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill" data-bs-placement="top" title="Edit"><i class="ti ti-edit mx-2 ti-md"></i></a>' +
                '</div>'
              );
            }
          },

          {
            // Transfer Number
            targets: 2,
            render: function (data, type, full, meta) {
              var $transferNumber = full['transferNumber'];
              var $transferId = full['transferId'];

              // Creates full output for row
              // if ($quotationNumber === null) $quotationNumber = "Draft";
              var $row_output = '<a class="text-truncate" href="/warehouse/transfer/edit/' + $transferId + '"># ' + $transferNumber + '</a>';
              return $row_output;
            }
          },
          {
            // Origin WH
            targets: 3,
            responsivePriority: 2,

            render: function (data, type, full, meta) {
              var $originWHName = full['originWHName'];

              return '<span class=text-truncate>' + $originWHName + '</span>';
            }
          },

          {
            // Destination WH
            targets: 4,
            responsivePriority: 2,
            render: function (data, type, full, meta) {
              var $destinationWHName = full['destinationWHName'];

              return '<span class=text-truncate>' + $destinationWHName + '</span>';
            }
          },

          {
            // Reason
            targets: 5,
            responsivePriority: 4,
            render: function (data, type, full, meta) {
              var $transferReason = full['transferReason'];

              return '<span class=text-truncate>' + $transferReason + '</span>';
            }
          },
          {
            // Status
            targets: 6,
            render: function (data, type, full, meta) {
              var $status = full['transferStatus'];
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
              } else if ($status === "SENT TO PICK AND PACK") {
                $badge_class = 'bg-label-success';
                return ('<span class="badge ' + $badge_class + '" text-capitalized>' + $status + '</span>');
              }

              return ('<span text-capitalized>' + $status + '</span>');
            }
          },
          {
            // Transfer Date
            targets: 7,
            render: function (data, type, full, meta) {
              var $transferDate = new Date(full['transferDateCreated']);

              let $row_output;
              if (full['transferDateCreated'] != null) {
                $row_output = '<span class="d-none">' + moment($transferDate).format('YYYYMMDD') + '</span><span class="text-truncate">' + moment($transferDate).format('DD MMM YYYY') + '</span>';
              } else {
                $row_output = "-";
              }

              return $row_output;
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
          searchPlaceholder: 'Search Transfer',
          paginate: {
            next: '<i class="ti ti-chevron-right ti-sm"></i>',
            previous: '<i class="ti ti-chevron-left ti-sm"></i>'
          }
        },
        // Buttons with Dropdown
        buttons: [
          {
            text: '<i class="ti ti-plus ti-xs me-md-2"></i><span class="d-md-inline-block d-none">Create Transfer</span>',
            className: 'btn btn-primary waves-effect waves-light',
            action: function (e, dt, button, config) {

              window.location = '/warehouse/transfer/new';

            }
          }
        ],
        // For responsive popup
        responsive: {
          details: {
            display: $.fn.dataTable.Responsive.display.modal({
              header: function (row) {
                var data = row.data();
                return 'Details of ' + data['transferNumber'];
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