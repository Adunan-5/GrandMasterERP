<?php
$PAGE_ID = "PO_EDIT";
include_once __DIR__ . "/../../includes/baseIncludes.php";
include_once __DIR__ . "/../../includes/auth_check.php";

$poID = "";
$poID = filter_input(INPUT_GET, 'poID', FILTER_VALIDATE_INT);
if ($poID === null || $poID === false || filter_var($poID, FILTER_VALIDATE_INT) === false) {
    header("location:/purchaseorder/list");
    exit();
}


$currentUserRole = getAuthenticatedUser()->getRoles()[0];

$poDATA = "";
$res = $db->query("SELECT * FROM consultation_purchase_orders WHERE active = 1 and poId = ?s", $poID);
while ($row = mysqli_fetch_assoc($res)) {
    $poDATA = $row;
}

$supplierID = $poDATA['supplierId'];
$currencyID = $poDATA['currencyId'];
$documentStatus = $poDATA['poStatus'];

?>
<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
      data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
<head>
    <?php include_once __DIR__ . "/../../includes/dashboard/dashboard_head_section.php"; ?>
  <!-- Vendors CSS -->
  <link rel="stylesheet" href="/assets/vendor/libs/node-waves/node-waves.css" />
  <link rel="stylesheet" href="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
  <link rel="stylesheet" href="/assets/vendor/libs/typeahead-js/typeahead.css" />
  <link rel="stylesheet" href="/assets/vendor/libs/flatpickr/flatpickr.css" />
  <link rel="stylesheet" href="/assets/vendor/libs/bootstrap-select/bootstrap-select.css" />
  <link rel="stylesheet" href="/assets/vendor/libs/animate-css/animate.css" />
  <link rel="stylesheet" href="/assets/vendor/libs/sweetalert2/sweetalert2.css" />
  <!-- Page CSS -->
  <link rel="stylesheet" href="/assets/vendor/css/pages/app-invoice.css" />
  <link rel="stylesheet" href="/assets/vendor/css/jquery-ui.css" />
  <link rel="stylesheet" href="/assets/vendor/libs/select2/select2.css " />
    <?php include_once __DIR__ . "/../../includes/dashboard/dashboard_head_section_post_vendor.php"; ?>
  <title>GrandMaster ERP | Consultation PO</title>
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
      <nav
        class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
        id="layout-navbar">
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
                  <div class="col-6">
                    <h4 class="my-0"> <?= getPONumberFromConsultationDocumentID($poDATA['poId']) ?> | Edit Purchase Order</h4>
                  </div>
                  <div class="col-6 text-end">
                      <?php
                      switch ($poDATA['poStatus']) {
                          case PO_STATUS_NEW:
                              ?>
                            <button type="button"
                                    class="btn btn-label-linkedin text-nowrap d-inline-flex position-relative me-4">
                              DRAFT
                            </button>
                              <?php
                              break;

                          case PO_STATUS_SPAREPARTS_MANAGER_APPROVED:
                              ?>
                            <label
                              class="no-hand btn btn-label-success text-nowrap d-inline-flex position-relative me-4">
                              SPAREPARTS MANAGER APPROVED
                            </label>
                              <?php
                              break;

                          case PO_STATUS_SPAREPARTS_MANAGER_REJECTED:
                              ?>
                            <label
                              class="no-hand btn btn-label-danger text-nowrap d-inline-flex position-relative me-4">
                              SPAREPARTS MANAGER REJECTED
                            </label>
                              <?php
                              break;

                          case PO_STATUS_ACCOUNTANT_REJECTED:
                              ?>
                            <label
                              class="no-hand btn btn-label-danger text-nowrap d-inline-flex position-relative me-4">
                              ACCOUNTANT REJECTED
                            </label>
                              <?php
                              break;
                          case PO_STATUS_ACCOUNTANT_APPROVED:
                              ?>
                            <label
                              class="no-hand btn btn-label-success text-nowrap d-inline-flex position-relative me-4">
                              ACCOUNTANT APPROVED
                            </label>
                              <?php
                              break;
                          default:
                              ?>
                            <button type="button"
                                    class="btn btn-label-info text-nowrap d-inline-flex position-relative me-4">
                                <?= $poDATA['poStatus'] ?>
                            </button>
                              <?php
                              break;
                      }


                      ?>
                  </div>
                </div>
              </div>
            </div>
          </div>

            <?php
            $rejectReason = "";
            $documentStatus = $poDATA['poStatus'];
            if ($documentStatus == PO_STATUS_SPAREPARTS_MANAGER_REJECTED) {
                $rejectReason = getRejectReasonForConsultationPOID($poID);
                ?>
              <div class="alert alert-solid-danger d-flex align-items-center" role="alert">
                        <span class="alert-icon rounded">
                          <i class="ti ti-ban"></i>
                        </span> Rejection Reason: <?= $rejectReason ?>
              </div>
                <?php
            }

            if ($documentStatus == PO_STATUS_ACCOUNTANT_REJECTED) {
                $rejectReason = getAccountanRejectReasontForConsultationPOID($poID);
                ?>
              <div class="alert alert-solid-danger d-flex align-items-center" role="alert">
                        <span class="alert-icon rounded">
                          <i class="ti ti-ban"></i>
                        </span> Rejection Reason: <?= $rejectReason ?>
              </div>
                <?php
            }

            if ($documentStatus == PO_STATUS_AWAITING_ACCOUNTANT_APPROVAL && (empty($poDATA['paymentRefAttachment']) || empty($poDATA['paymentRefNo']) || empty($poDATA['paymentDate']))) {
                ?>

              <div class="alert alert-solid-info" role="alert">
                Please Attach the Payment Reference No., Payment Reference Attachment and Select the Payment Date to the PO.
              </div>

                <?php
            } if ($documentStatus == PO_STATUS_SENT_TO_SUPPLIER && (empty($poDATA['supplierInvoiceNo']) || empty($poDATA['supplierInvoiceAttachment']) || empty($poDATA['supplierInvoiceDate']))) {
            ?>
              <div class="alert alert-solid-info" role="alert">
                Please Attach the Supplier Invoice to proceed with payment details.
              </div>
            <?php
            }
            ?>
          <div class="row invoice-add">
            <!-- Invoice Add-->
            <div class="col-lg-12 col-12 mb-lg-0 mb-6">
              <div class="card invoice-preview-card p-sm-6 p-6">
                <div class="card-body px-0">

                  <div class="row">
                    <!-- First Row: Dropdowns -->
                    <div class="col-6 mb-4">
                      <div class="d-flex gap-2 justify-content-start">

                          <button class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#showHistoryOffcanvas"> 
                            <span class="d-flex align-items-center justify-content-center text-nowrap">
                              <i class="ti ti-history ti-xs me-2"></i>History
                            </span>
                          </button>
                          <?php

                          // if ($documentStatus == PO_STATUS_SENT_TO_SUPPLIER || $documentStatus == PO_STATUS_SUPPLIER_INVOICE_ATTACHED) {
                              ?>
                            <!-- <a class="btn btn-success" href="/purchaseorder/receivegoods/<?= $poID ?>">
                                                        <span
                                                          class="d-flex align-items-center justify-content-center text-nowrap">
                                                          <i
                                                            class="ti ti-device-floppy ti-xs me-2"></i>Receive Goods</span>
                            </a> -->

                              <?php
                          // }
                          ?>


                      </div>
                    </div>

                    <div class="col-6 mb-4">

                      <div class="d-flex gap-2 justify-content-end">
                        <div class="d-flex gap-2 justify-content-center flex-wrap">

                            <?php
                            if (($currentUserRole == ROLE_SUPERADMIN) && ($documentStatus != PO_STATUS_ACCOUNTANT_APPROVED && $documentStatus != PO_STATUS_SENT_TO_SUPPLIER && $documentStatus != PO_STATUS_SUPPLIER_INVOICE_ATTACHED)) {
                                ?>
                              <button class="btn btn-primary" onclick="savePO()">
                                <span class="d-flex align-items-center justify-content-center text-nowrap">
                                  <i class="ti ti-device-floppy ti-xs me-2"></i>Save
                                </span>
                              </button>
                                <?php
                            } if(($currentUserRole == ROLE_SUPERADMIN) && ($documentStatus == PO_STATUS_SENT_TO_SUPPLIER)) {
                            ?>
                              <button class="btn btn-primary" onclick="updateInvoice()">
                                <span class="d-flex align-items-center justify-content-center text-nowrap">
                                  <i class="ti ti-device-floppy ti-xs me-2"></i>Update Invoice
                                </span>
                              </button>
                            <?php
                            }
                            if (($currentUserRole == ROLE_SUPERADMIN) && in_array($documentStatus,[PO_STATUS_ACCOUNTANT_APPROVED, PO_STATUS_SENT_TO_SUPPLIER, PO_STATUS_SUPPLIER_INVOICE_ATTACHED])) {
                                ?>
                              <button class="btn btn-primary" onclick="printPO()">
                                                            <span
                                                              class="d-flex align-items-center justify-content-center text-nowrap">
                                                              <i class="ti ti-printer ti-xs me-2"></i>Print
                                                            </span>
                              </button>
                                <?php
                                } if (($currentUserRole == ROLE_SUPERADMIN)
                                    && in_array($documentStatus,[PO_STATUS_ACCOUNTANT_APPROVED, PO_STATUS_SENT_TO_SUPPLIER, PO_STATUS_SUPPLIER_INVOICE_ATTACHED])
                                    && (!empty($poDATA['supplierInvoiceNo']) && !empty($poDATA['supplierInvoiceAttachment']) && !empty($poDATA['supplierInvoiceDate']))) {
                                ?>
                              <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#paymentDetailsModal">
                                                                <span class="d-flex align-items-center justify-content-center text-nowrap">
                                                                  <i class="ti ti-credit-card ti-xs me-2"></i>Payment Details
                                                                </span>
                              </button>
                                <?php
                            }
                            if (($currentUserRole == ROLE_SUPERADMIN) && ($documentStatus == PO_STATUS_NEW || $documentStatus == PO_STATUS_SPAREPARTS_MANAGER_REJECTED || $documentStatus == PO_STATUS_ACCOUNTANT_REJECTED)) {
                                ?>
                              <button class="btn btn-primary" onclick="sendForApproval()">
                                                            <span
                                                              class="d-flex align-items-center justify-content-center text-nowrap">
                                                              <i class="ti ti-device-floppy ti-xs me-2"></i>Send for Approval
                                                            </span>
                              </button>
                                <?php
                            }
                            if (($currentUserRole == ROLE_SUPERADMIN) && $documentStatus == PO_STATUS_AWAITING_SPAREPARTS_MANAGER_APPROVAL) {
                                ?>
                              <button class="btn btn-success" onclick="approvePO()">
                                                            <span
                                                              class="d-flex align-items-center justify-content-center text-nowrap">
                                                              <i class="ti ti-device-floppy ti-xs me-2"></i>Approve
                                                            </span>
                              </button>
                              <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectReasonModal">
                                                            <span
                                                              class="d-flex align-items-center justify-content-center text-nowrap">
                                                              <i class="ti ti-circle-dashed-x ti-xs me-2"></i>Reject
                                                            </span>
                              </button>
                                <?php
                            }
                            if (($currentUserRole == ROLE_SUPERADMIN) && $documentStatus == PO_STATUS_SPAREPARTS_MANAGER_APPROVED) {
                                ?>
                              <button class="btn btn-info" onclick="sendforAccountantApproval()">
                                    <span class="d-flex align-items-center justify-content-center text-nowrap">
                                      <i class="ti ti-device-floppy ti-xs me-2"></i>Send for Accountant Approval
                                    </span>
                              </button>
                                <?php
                            }
                            if (($currentUserRole == ROLE_SUPERADMIN) && $documentStatus == PO_STATUS_AWAITING_ACCOUNTANT_APPROVAL && !empty($poDATA['paymentRefAttachment']) && !empty($poDATA['paymentRefNo'])) {
                                ?>
                              <button class="btn btn-success" onclick="accountantApprovePO()">
                                      <span class="d-flex align-items-center justify-content-center text-nowrap">
                                        <i class="ti ti-device-floppy ti-xs me-2"></i>Approve
                                      </span>
                              </button>
                              <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectReasonModal">
                                    <span class="d-flex align-items-center justify-content-center text-nowrap">
                                      <i class="ti ti-device-floppy ti-xs me-2"></i>Reject
                                    </span>
                              </button>
                                <?php
                            }
                            ?>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-12 mb-4" style="display: none">
                      <label class="form-label" for="supplier">Supplier:</label>
                      <select name="supplier" id="supplier" class="form-select w-100 selectpicker"
                              data-style="btn-default" data-live-search="true" tabindex="null" disabled>
                        <option value="">Select supplier</option>
                          <?php
                          $res = $db->query("SELECT * FROM suppliers WHERE status ='Active'");
                          while ($row = mysqli_fetch_assoc($res)) {
                              ?>
                            <option <?php if ($row['supplierId'] == $poDATA['supplierId']) echo "selected"; ?>
                              value="<?= $row['supplierId'] ?>"><?= $row['companyName'] . " | " . $row['companyNameAr'] ?></option>
                              <?php
                          }
                          ?>
                      </select>
                    </div>
                    <div class="col-md-6 col-sm-6 col-12 mb-4" style="display: none">
                    </div>
                  </div>
                </div>
                <!--Grey Header Section-->
                <div class="card-body invoice-preview-header rounded">
                    <?php
                    $supplier = new Supplier();
                    $supplier->loadById($supplierID);
                    ?>
                  <!-- Supplier and Warehouse Details Row -->
                  <div class="row">
                    <!-- Supplier Details (Left) -->
                    <div class="col-md-6 col-12 mb-4">
                      <div class="svg-illustration mb-4 gap-2 align-items-center">
                        <div>
                          <span class="app-brand-text fw-bold fs-4 ms-50"
                                id="companyName"><?= $supplier->companyName ?></span>
                        </div>
                        <div>
                          <span class="app-brand-text fw-bold fs-5 ms-50"
                                id="companyNameAR"><?= $supplier->companyNameAr ?></span>
                        </div>
                      </div>
                      <p class="mb-2" id="customerAddressLine1"><?= $supplier->addressLine1; ?></p>
                      <p class="mb-2" id="customerAddressLine2"><?= $supplier->addressLine2; ?></p>
                      <p class="mb-2"
                         id="customerCityPostalCode"><?= getCityFromID($supplier->cityId) . ", " . $supplier->postalCode ?></p>
                      <p class="mb-2"
                         id="customerStateCountry"><?= getStateFromID($supplier->stateId) . ", " . getCountryFromID($supplier->countryId) ?></p>
                      <p class="mb-2" id="customerVAT">VAT: <?= $supplier->vatNumber ?></p>
                      <p class="mb-2" id="customerCR">CR: <?= $supplier->companyCRNumber ?></p>
                    </div>
                    <!-- Warehouse Details (Right) -->
                    <div class="col-md-6 col-12 mb-4 text-md-end d-flex flex-column align-items-md-end">
                    </div>
                  </div>
                  <hr style="height: 0.5px; background-color: #2c3539; border: none;">
                  <!-- Input Fields in Two Rows -->
                  <div class="row">
                    <!-- First Row of Input Fields -->
                      <?php

                      $poNumber = "DRAFT";
                      if (!empty($poDATA['poNumberPrefix'])) {
                          $poNumber = $poDATA['poNumberPrefix'] . $poDATA['poNumber'];
                      }
                      ?>
                    <div class="col-md-4 col-12 mb-4">
                      <dl class="row mb-0">
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="h5 text-capitalize mb-0 text-nowrap">PO</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <div class="input-group input-group-merge disabled">
                            <span class="input-group-text">#</span>
                            <input name="poNumber" id="poNumber" type="text" class="form-control" disabled
                                   placeholder="Will be generated once saved / confirmed" value="<?= $poNumber ?>" />
                          </div>
                          <input name="poID" id="poID" type="hidden" value="<?= $poDATA['poId'] ?>" />
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Date Created:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <input name="poDate" id="poDate" type="text" class="form-control gmm-date-format"
                                 placeholder="DD - MMM - YYYY" value="<?= $poDATA['poDateCreated'] ?>" />
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">RFP #:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <input type="text" class="form-control"
                                 value="<?= getRFPNumberFromDocumentID($poDATA['rfpId']) ?>" readonly />
                          <input type="hidden" id="rfpID" class="form-control" value="<?= $poDATA['rfpId'] ?>"
                                 readonly />
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Supplier Currency:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7 mb-2">
                          <select name="supplierCurrency" id="supplierCurrency" class="form-control mt">
                            <option disabled selected value="">Select Currency</option>
                              <?php
                              $res = $db->query("SELECT * FROM currencies WHERE active = 1");
                              while ($row = mysqli_fetch_assoc($res)) {
                                  ?>
                                <option <?php if ($poDATA['currencyId'] == $row['currencyId']) echo "SELECTED" ?>
                                  value="<?= $row['currencyId'] ?>"><?= $row['currency'] ?></option>
                                  <?php
                              }
                              ?>
                          </select>
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Supplier Quotation#:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <input type="text" id="supplierQuotationNo" class="form-control due-date"
                                 value="<?= $poDATA['supplierQuotationNumber'] ?>" />
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Attachment:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                            <?php
                            if (!empty($poDATA['supplierQuotationAttachment'])) {
                                ?>
                              <a class="btn btn-info" href="/uploads/<?= $poDATA['supplierQuotationAttachment'] ?>"
                                 target="_blank">Supplier Quotation</a>
                                <?php
                            }
                            ?>
                          <input class="form-control" type="hidden" id="supplierQuotationAttachmentFileName"
                                 name="supplierQuotationAttachmentFileName"
                                 value="<?= $poDATA['supplierQuotationAttachment'] ?>">
                        </dd>
                          <?php
                          if ($currentUserRole == ROLE_SUPERADMIN || $currentUserRole == ROLE_ACCOUNTS_MANAGER) {
                              ?>
                            <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                              <span class="fw-normal">Payment Ref Attachment:</span>
                            </dt>
                            <dd class="col-md-6 col-sm-7">
                              <input class="form-control" type="file" id="paymentRefAttachmentFile"
                                     name="paymentRefAttachmentFile">
                              <input class="form-control" type="hidden" id="paymentRefAttachmentFileName"
                                     name="paymentRefAttachmentFileFileName"
                                     value="<?= $poDATA['paymentRefAttachment'] ?>">
                            </dd>
                            <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                              <span class="fw-normal">Payment Ref #:</span>
                            </dt>
                            <dd class="col-md-6 col-sm-7">
                              <input type="text" id="paymentRefNo" class="form-control due-date"
                                     value="<?= $poDATA['paymentRefNo'] ?>" />
                            </dd>
                            <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                              <span class="fw-normal">Attachment:</span>
                            </dt>
                            <dd class="col-md-6 col-sm-7">
                                <?php
                                if (!empty($poDATA['paymentRefAttachment'])) {
                                    ?>
                                  <a class="btn btn-info" href="/uploads/<?= $poDATA['paymentRefAttachment'] ?>"
                                     target="_blank">View
                                    Payment Ref.</a>
                                    <?php
                                }
                                ?>
                              <input class="form-control" type="hidden" id="paymentRefAttachmentFileName"
                                     name="paymentRefAttachmentFileName" value="<?= $poDATA['paymentRefAttachment'] ?>">
                            </dd>
                              <?php
                          }
                          ?>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Payment Date:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <input name="paymentDate" id="paymentDate" type="text" class="form-control gmm-date-format" placeholder="DD - MMM - YYYY" value="<?= $poDATA['paymentDate'] ?>"/>
                        </dd>
                        <!--                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">-->
                        <!--                          <span class="fw-normal">Amount Paid:</span>-->
                        <!--                        </dt>-->
                        <!--                        <dd class="col-md-6 col-sm-7">-->
                        <!--                          <input type="text" id="amountPaid" class="form-control due-date"-->
                        <!--                                 value="--><?php //= $poDATA['amountPaid'] ?><!--" />-->
                        <!--                        </dd>-->
                      </dl>
                    </div>
                    <!-- Second Row of Input Fields -->
                    <div class="col-md-4 col-12 mb-4">
                      <dl class="row mb-0">
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Sales Person:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <input type="text" id="salesPersonId" readonly class="form-control due-date"
                                 value="<?= getDisplayNameFromUserID($poDATA['salesPersonId']) ?>" />
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Payment Terms:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7 mb-2">
                          <select name="paymentTerms" id="paymentTerms" class="form-control mt">
                            <option disabled selected value="">Select Payment Term</option>
                              <?php
                              $res = $db->query("SELECT * FROM payment_terms WHERE active = 1");
                              while ($row = mysqli_fetch_assoc($res)) {
                                  ?>
                                <option <?php if ($poDATA['paymentTermId'] == $row['termId']) echo "SELECTED" ?>
                                  value="<?= $row['termId'] ?>"><?= $row['termName'] ?></option>
                                  <?php
                              }
                              ?>
                          </select>
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Reason for PO:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <input type="text" id="rfpReason" readonly class="form-control due-date"
                                 value="<?= $poDATA['rfpReason'] ?>" />
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Sales Order #:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <input type="text" class="form-control"
                                 value="<?php if (!empty($poDATA['saleOrderId']) || $poDATA['saleOrderId'] <> '0') echo getSalesOrderNumberFromDocumentID($poDATA['saleOrderId']) ?>"
                                 readonly />
                          <input type="hidden" id="refDocID" class="form-control"
                                 value="<?= $poDATA['saleOrderId'] ?>" readonly />
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">VAT Amount for PO:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <input type="text" id="vatAmount"  class="form-control due-date"
                                 value="<?= $poDATA['vatAmount'] ?>" />
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">ETA:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <input name="etaDate" id="etaDate" type="text" class="form-control gmm-date-format"
                                 placeholder="DD - MMM - YYYY" value="<?= $poDATA['etaDate'] ?>" />
                        </dd>
                          <?php
                          if ($currentUserRole == ROLE_SUPERADMIN || $currentUserRole == ROLE_ACCOUNTS_MANAGER) {
                              ?>
                            <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                              <span class="fw-normal">Supplier INV Attachment:</span>
                            </dt>
                            <dd class="col-md-6 col-sm-7">
                              <input class="form-control" type="file" id="supplierInvoiceAttachmentFile"
                                     name="supplierInvoiceAttachmentFile">
                              <input class="form-control" type="hidden" id="supplierInvoiceAttachmentFileName"
                                     name="supplierInvoiceAttachmentFileFileName"
                                     value="<?= $poDATA['supplierInvoiceAttachment'] ?>">
                            </dd>
                            <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                              <span class="fw-normal">Supplier INV #:</span>
                            </dt>
                            <dd class="col-md-6 col-sm-7">
                              <input type="text" id="supplierInvoiceNo" class="form-control due-date"
                                     value="<?= $poDATA['supplierInvoiceNo'] ?>" />
                            </dd>
                            <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                              <span class="fw-normal">Attachment:</span>
                            </dt>
                            <dd class="col-md-6 col-sm-7">
                                <?php
                                if (!empty($poDATA['supplierInvoiceAttachment'])) {
                                    ?>
                                  <a class="btn btn-info" href="/uploads/<?= $poDATA['supplierInvoiceAttachment'] ?>"
                                     target="_blank">View
                                    Supplier INV.</a>
                                    <?php
                                }
                                ?>
                              <input class="form-control" type="hidden" id="supplierInvoiceAttachmentFileName"
                                     name="supplierInvoiceAttachmentFileName" value="<?= $poDATA['supplierInvoiceAttachment'] ?>">
                            </dd>
                              <?php
                          }
                          ?>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Supplier Invoice Date:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <input name="supplierInvoiceDate" id="supplierInvoiceDate" type="text" class="form-control gmm-date-format" placeholder="DD - MMM - YYYY" value="<?= $poDATA['supplierInvoiceDate'] ?>"/>
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Carrier:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <input type="text" id="carrier" class="form-control due-date"
                                 value="<?= $poDATA['shippingMethod'] ?>" />
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Tracking Number:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <input type="text" id="tracking" class="form-control due-date"
                                 value="<?= $poDATA['trackingNumber'] ?>" />
                        </dd>
                        <!--                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">-->
                        <!--                          <span class="fw-normal">Shipping Cost:</span>-->
                        <!--                        </dt>-->
                        <!--                        <dd class="col-md-6 col-sm-7">-->
                        <!--                          <input type="text" id="shippingCost" class="form-control due-date" value="--><?php //= $poDATA['shippingCost'] ?><!--" />-->
                        <!--                        </dd>-->
                      </dl>
                    </div>
                    <div class="col-md-4 col-12 mb-4">
                      <dl class="row mb-0">
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal"><strong>Cost Adjustment:</strong></span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <a class="btn btn-info btn-sm text-white" data-bs-toggle="modal" data-bs-target="#costSummaryModal"> Manage </a>
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Taxes:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <input type="text" id="taxes" class="form-control" value="<?=$poDATA['taxes']?>" placeholder="SAR 0.00"/>
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Shipping Cost:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <input type="text" id="shippingCost" class="form-control" value="<?=$poDATA['shippingCost']?>" placeholder="SAR 0.00"/>
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal"><strong>Cost Summary:</strong></span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal" <?= (empty($poDATA['insurance']) || $poDATA['insurance'] == '0.00') ? 'style="display:none"' : '' ?>>Insurance:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <input type="text" id="insurance" class="form-control" value="<?= (!empty($poDATA['insurance']) && $poDATA['insurance'] != '0.00') ? $poDATA['insurance'] : '' ?>"
                                 placeholder="SAR 0.00" <?= (empty($poDATA['insurance']) || $poDATA['insurance'] == '0.00') ? 'style="display:none"' : '' ?>/>
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal" <?= (empty($poDATA['foreignTransactionFee']) || $poDATA['foreignTransactionFee'] == '0.00') ? 'style="display:none"' : '' ?>>Foreign Transaction Fee:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <input type="text" id="foreigntransactionfees" class="form-control" value="<?= (!empty($poDATA['foreignTransactionFee']) && $poDATA['foreignTransactionFee'] != '0.00') ? $poDATA['foreignTransactionFee'] : '' ?>"
                                 placeholder="SAR 0.00" <?= (empty($poDATA['foreignTransactionFee']) || $poDATA['foreignTransactionFee'] == '0.00') ? 'style="display:none"' : '' ?>/>
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal" <?= (empty($poDATA['customDuties']) || $poDATA['customDuties'] == '0.00') ? 'style="display:none"' : '' ?>>Custom Duties:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <input type="text" id="customduties" class="form-control" value="<?= (!empty($poDATA['customDuties']) && $poDATA['customDuties'] != '0.00') ? $poDATA['customDuties'] : '' ?>"
                                 placeholder="SAR 0.00" <?= (empty($poDATA['customDuties']) || $poDATA['customDuties'] == '0.00') ? 'style="display:none"' : '' ?>/>
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal" <?= (empty($poDATA['surcharge']) || $poDATA['surcharge'] == '0.00') ? 'style="display:none"' : '' ?>>Surcharge:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <input type="text" id="surcharge" class="form-control" value="<?= (!empty($poDATA['surcharge']) && $poDATA['surcharge'] != '0.00') ? $poDATA['surcharge'] : '' ?>"
                                 placeholder="SAR 0.00" <?= (empty($poDATA['surcharge']) || $poDATA['surcharge'] == '0.00') ? 'style="display:none"' : '' ?>/>
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal" <?= (empty($poDATA['other']) || $poDATA['other'] == '0.00') ? 'style="display:none"' : '' ?>>Other:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <input type="text" id="other" class="form-control" value="<?= (!empty($poDATA['other']) && $poDATA['other'] != '0.00') ? $poDATA['other'] : '' ?>"
                                 placeholder="SAR 0.00" <?= (empty($poDATA['other']) || $poDATA['other'] == '0.00') ? 'style="display:none"' : '' ?>/>
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal"><strong>Subtotal:</strong></span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <span id="shippingSubTotal" class="fw-medium"><?=getCurrencyFromID($currencyID)?> <?= (!empty($poDATA['subTotal']) && $poDATA['subTotal'] != '0.00') ? $poDATA['subTotal'] : '' ?></span>
                        </dd>
                      </dl>
                    </div>
                  </div>
                </div>
                <!--Grey Header Section Ends-->
                <hr class="mt-5 mb-6" />
                <div class="row">
                  <div class="col-md-5 col-sm-5 col-12 mb-sm-0 mb-6">
                    <!--                    <div class="mb-4">-->
                    <!--                      <label class="form-label" for="customerCountry">Add Parts:</label>-->
                    <!--                      <select name="sparepartitem" class="sparepartitem form-control mb-5" data-live-search="true">-->
                    <!--                        <option value="">Select a part number</option>-->
                    <!--                      </select>-->
                    <!--                    </div>-->
                  </div>
                </div>
                <!-- <hr class="mt-0 mb-6" /> -->
                <div class="card-body pt-0 px-0">
                  <div class="row" id="noSparePartsInitialDiv" style="display: none">
                    <div class="col-12 text-center">
                      Add spareparts from the above search box
                    </div>
                  </div>
                  <form class="source-item">
                    <div class="mb-4" data-repeater-list="line-item">
                        <?php
                        $itemCount = 0;
                        $lineitemres = $db->query("SELECT * FROM consultation_po_line_items WHERE poId = ?s", $poID);

                        while ($lineitemrow = mysqli_fetch_assoc($lineitemres)) {
                            ?>
                          <div class="repeater-wrapper pt-0 pt-md-6" data-repeater-item>
                            <div class="d-flex border rounded position-relative pe-0">
                              <div class="row w-100 p-3">
                                <div class="col-md-3 col-12 mb-md-0 mb-4">
                                  <p class="h6 mb-1 itemNumber">Item #<?=++$itemCount ?></p>
                                  <input name="serviceItemName" type="text" class="form-control mb-5" readonly value="<?= getItemNameForItemID($lineitemrow['itemId']); ?>"/>
                                  <input name="serviceItemID" type="hidden" class="form-control mb-5" readonly value="<?=$lineitemrow['itemId'] ?>"/>
                                </div>
                                <div class="col-md-3 col-12 mb-md-0 mb-4">
                                  <p class="h6 mb-1">Description</p>
                                  <input name="servicedescription" type="text" class="form-control mb-5" readonly value="<?=getItemDescriptionForItemID($lineitemrow['itemId']) ?>"/>
                                </div>
                                <div class="col-md-1 col-12 mb-md-0 mb-4">
                                  <p class="h6 mb-1">Qty</p>
                                  <input name="qty" type="text" class="form-control numbers-only calculation-trigger" placeholder="Qty" min="1" required value="<?=$lineitemrow['quantity'] ?>"/>
                                </div>
                                <div class="col-md-2 col-12 mb-md-0 mb-4">
                                  <p class="h6 mb-1">UOM</p>
                                  <input name="uom" type="text" class="form-control mb-5" readonly value="<?= getUOMNameFromID($lineitemrow['UOM']); ?>"/>
                                  <input name="uomId" type="hidden" class="form-control mb-5" readonly value="<?=$lineitemrow['UOM'] ?>"/>
                                </div>
                                <div class="col-md-1 col-12 mb-md-0 mb-4">
                                  <p class="h6 mb-1">Cost</p>
                                  <input name="cost" type="text" class="form-control numbers-only calculation-trigger" placeholder="Cost" min="1" required value="<?= $lineitemrow['costPrice'] ?>" readonly/>
                                </div>
                                <div class="col-md-1 col-12 mb-md-0 mb-4">
                                  <p class="h6 mb-1">Sub Total</p>
                                  <input name="subTotal" type="text" class="form-control numbers-only calculation-trigger" placeholder="Total" min="1" required value="<?= $lineitemrow['subTotal'] ?>" readonly/>
                                </div>
                              </div>
                              <div class="row p-3 justify-content-end"></div>
                              <div
                                class="d-flex flex-column align-items-center justify-content-between border-start p-2">
                                <i class="ti ti-x ti-lg cursor-pointer" data-repeater-delete></i>
                              </div>
                            </div>
                          </div>
                            <?php

                        }

                        ?>
                    </div>
                    <div class="col-12" style="display: none">
                      <button type="button" id="repeaterAddItemButton" data-repeater-create style="display: none">
                        Add Item
                      </button>
                    </div>
                  </form>
                </div>
                <hr class="my-0" />
                <div class="card-body px-0">
                  <div class="row row-gap-4">
                    <div class="col-md-6 d-flex justify-content-start"></div>
                    <div class="col-md-6 d-flex justify-content-end">
                      <div class="invoice-calculations">
                        <div class="d-flex justify-content-between ">
                          <span class="px-5">Total Amount before VAT and Shipping: </span>
                          <span id="totalAmountSpan" class="fw-medium text-heading"><?=getCurrencyFromID($currencyID)?> 0.00</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <hr class="my-0" />
                <div class="card-body px-0">
                  <div class="row">
                    <div class="col-12">
                      <div>
                        <label for="note" class="text-heading mb-1 fw-medium">Note:</label>
                        <textarea class="form-control" rows="2" id="note" placeholder="Invoice note">It was a pleasure working with you and your team. We hope you will keep us in mind for future. Thank You!</textarea>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- /Invoice Add-->
          </div>
          <!-- Offcanvas -->
            <!--Document History Canvas starts-->
                    <div class="offcanvas offcanvas-end" id="showHistoryOffcanvas" aria-hidden="true">
                        <div class="offcanvas-header mb-6 border-bottom">
                            <h5 class="offcanvas-title">History</h5>
                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body pt-0 flex-grow-1">
                            <!-- Timeline Basic-->
                            <div class="mt-5">
                                <ul class="timeline mb-0">
                                    <?php

                                    $consultationPODocumentHistory = new ConsultationPOHistory();
                                    $consultationPODocumentHistory->loadByPOId($poID);

                                    foreach ($consultationPODocumentHistory->timeline as $historyItem) {
                                        ?>
                                        <li class="timeline-item timeline-item-transparent">
                                          <span class="timeline-point timeline-point-<?= $historyItem->stickerPottu ?>"></span>
                                            <div class="timeline-event">
                                                <div class="timeline-header mb-3">
                                                    <h6 class="mb-0"><?= $historyItem->title ?></h6>
                                                    <small class="text-muted"><?= $historyItem->updatedAt ?></small>
                                                </div>
<!--                                                <p class="mb-2">--><?php //echo "remark"; ?><!--</p>-->
                                                <!-- Display remark if exists -->
                                              <div class="d-flex justify-content-between flex-wrap gap-2 mb-2">
                                                <div class="d-flex flex-wrap align-items-center mb-50">
                                                  <div class="avatar avatar-sm me-2">
                                                      <?php if ($historyItem->updatedBy > 0) { ?>
                                                        <img src="<?= getProfilePicFromUserID($historyItem->updatedBy) ?>" alt="Avatar" class="rounded-circle">
                                                      <?php } ?>
                                                  </div>
                                                  <div>
                                                    <p class="mb-0 small fw-medium"><?= getDisplayNameFromUserID($historyItem->updatedBy) ?></p>
                                                  </div>
                                                </div>
                                              </div>
                                                <?php if (!empty($historyItem->remark)) {
                                                    // Check if the title contains 'rejected'
                                                    $remarkClass = 'bg-info'; // Default class
                                                    if (strpos(strtolower($historyItem->title), 'rejected') !== false) {
                                                        $remarkClass = 'bg-danger text-white'; // Apply danger class if title contains 'rejected'
                                                    }
                                                    ?>
                                                  <p class="badge <?= $remarkClass ?> rounded d-flex align-items-center">
                                                      <?= htmlspecialchars($historyItem->remark) ?>
                                                  </p>
                                                <?php } ?>
                                            </div>
                                        </li>
                                        <?php
                                    }

                                    ?>

                                    <?php

                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!--Document History Canvas ENDS-->
          <!-- /Offcanvas -->
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
<!--Reject Reason Modal-->
<div class="modal fade" id="rejectReasonModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel1">Reject Reason</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col mb-4">
            <label for="nameBasic" class="form-label">Reject Reason</label>
            <input type="text" id="rejectReason" class="form-control" placeholder="Enter your reason for rejection" />
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
          Close
        </button>
          <?php
          if ($poDATA['poStatus'] == PO_STATUS_AWAITING_SPAREPARTS_MANAGER_APPROVAL || $poDATA['poStatus'] == PO_STATUS_ACCOUNTANT_REJECTED) {
              ?>
            <button type="button" class="btn btn-danger" onclick="rejectPO()">Reject</button>
              <?php
          }
          if ($poDATA['poStatus'] == PO_STATUS_AWAITING_ACCOUNTANT_APPROVAL) {
              ?>
            <button type="button" class="btn btn-danger" onclick="rejectPOAccountant()">Reject</button>
              <?php
          }
          ?>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="paymentDetailsModal" tabindex="-1" aria-hidden="true">
    <?php
    $paymentDATA = '';
    $res = $db->query("SELECT * FROM accounts_payable WHERE poId = ?s", $poID);
    while ($row = mysqli_fetch_assoc($res)) {
        $paymentDATA = $row;
    }
    if($paymentDATA) {
        $accountsPayableID = $paymentDATA['id'];
        $totalAmount = $paymentDATA['totalAmount'];
        $paidAmount = $paymentDATA['paidAmount'];
        $pendingAmount = $totalAmount - $paidAmount;
    }

    ?>
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel1">Payment Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <?php
          if($paymentDATA && $paymentDATA['status'] != 'PAID') {
              ?>
            <div class="alert alert-solid-info mb-4" role="alert">
              You've to pay <strong>SAR <?=$pendingAmount?></strong> to this Purchase Order
            </div>
              <?php
          } if($paymentDATA && $paymentDATA['status'] == 'PAID') {
              ?>
            <div class="alert alert-solid-success mb-4" role="alert">
              You've Fully Paid for this Purchase Order
            </div>
              <?php
          }
          ?>
        <div class="row">
          <div class="col-md-6 mb-4">
            <label for="nameBasic" class="form-label">Total Amount</label>
            <input type="text" id="totalAmount" readonly class="form-control" value="<?=getTotalAmountWithShippingByPOID($poID)?>" placeholder="Enter the amount paid" />
            <input type="hidden" id="accountsPayableID" value="<?=$paymentDATA ?$accountsPayableID : ''?>">
          </div>
          <div class="col-md-6 mb-4">
            <label for="nameBasic" class="form-label">Amount Paid</label>
            <input type="text" id="amountPaid" readonly class="form-control" value="<?=getTotalAmountWithShippingByPOID($poID)?>" placeholder="Enter the amount paid" />
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 mb-4">
            <label for="nameBasic" class="form-label">Mode of Payment</label>
            <select id="paymentMode" name="paymentMode" class="form-control">
              <option disabled selected value="">Select Mode</option>
                <?php
                $res = $db->query("SELECT * FROM payment_methods WHERE active = 1");
                while ($row = mysqli_fetch_assoc($res)) {
                    ?>
                  <option value="<?= $row['id'] ?>"><?= $row['methodName'] ?></option>
                    <?php
                }
                ?>
            </select>
          </div>
          <div class="col-md-6 mb-4">
            <label for="nameBasic" class="form-label">Reference</label>
            <input type="text" id="reference" class="form-control" value="" placeholder="Reference" />
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 mb-4">
            <label for="nameBasic" class="form-label">Paid On</label>
            <input name="paymentDate" id="paymentDate" type="text" class="form-control gmm-date-format" placeholder="DD - MMM - YYYY" value="<?php echo date('Y-m-d'); ?>"/>
          </div>
          <div class="col-md-6 mb-4">
            <label for="nameBasic" class="form-label">Remarks</label>
            <input type="text" id="remarks" class="form-control" value="" placeholder="Remarks" />
          </div>
        </div>
      </div>
        <?php
        if($paymentDATA && $paymentDATA['status'] != 'PAID') {
        ?>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
          Close
        </button>
        <button type="button" class="btn btn-primary" onclick="savePayment()">Save</button>
      </div>
        <?php
        }
        ?>
    </div>
  </div>
</div>
<div class="modal fade" id="costSummaryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel1">Manage Cost Summary</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row align-items-center">
          <!--          <div class="col-6">-->
          <!--            <label class="form-label">Shipping</label>-->
          <!--          </div>-->
          <!--          <div class="col-6 text-end">-->
          <!--            <input type="text" class="form-control" id="shippingCost" value="" placeholder="SAR 0.00"  />-->
          <!--          </div>-->
        </div>
        <div>
          <a class="btn" id="addAdjustment">+ Add Adjustment</a>
        </div>

        <div class="row align-items-center mb-3">
          <div class="col-6">
            <select class="form-select" id="costSelect">
              <option value="">Select one</option>
                <?php
                $res = $db->query("SELECT * FROM cost_summary where active = 1");
                while($row = mysqli_fetch_assoc($res)){
                    ?>
                  <option value="<?=$row['costSummaryId']?>"><?=$row['costSummaryName']?></option>
                    <?php
                }
                ?>
            </select>
          </div>
          <div class="col-6 text-end">
            <input type="text" class="form-control" id="costValue" value="" placeholder="SAR 0.00"  />
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-primary">Save</button>
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
          Close
        </button>
        <!--        <button type="button" class="btn btn-danger" onclick="rejectQuotation()">Reject</button>-->
        <!--        <button type="button" class="btn btn-danger" onclick="rejectQuotationSMSO()">Reject</button>-->
        <!--        <button type="button" class="btn btn-danger" onclick="rejectQuotationAccountantSO()">Reject</button>-->
      </div>
    </div>
  </div>
</div>
<!-- / Layout wrapper -->
<?php include_once __DIR__ . "/../../includes/dashboard/dashboard_footer_scripts.php"; ?>
<script>
  let sparepartsAutoCompleteData = [];

  $(document).ready(function(e) {

    $('#costSelect').on('change', function() {
      const selectedValue = $(this).val();
      const costValue = $('#costValue').val() || '0.00';
      console.log('Selected Value:', selectedValue);
      console.log('Cost Value:', costValue);

      if (selectedValue) {
        const selectedText = $(this).find('option:selected').text();
        console.log('Selected Text:', selectedText);
        const fieldMap = {
          'Insurance': 'insurance',
          'Foreign Transaction Fees': 'foreigntransactionfees',
          'Custom Duties': 'customduties',
          'Surcharge': 'surcharge',
          'Other': 'other'
        };

        const fieldId = fieldMap[selectedText];
        if (fieldId) {
          const $targetField = $(`#${fieldId}`);
          const $targetLabel = $targetField.closest('dd').prev('dt').find('span');

          $targetField.show();
          $targetLabel.show();

          const numericValue = parseFloat(costValue) || 0;
          $targetField.val(numericValue.toFixed(2));

          updateShippingTotal();
        }
      }
    });

    $('#addAdjustment').on('click', function(e) {
      e.preventDefault();
      $('#costSelect').val('').trigger('change');
      $('#costValue').val('');
    });

    $('.modal-footer .btn-primary').on('click', function() {
      updateShippingTotal();
      $('#costSummaryModal').modal('hide');
    });

    function updateShippingTotal() {
      let total = 0;
      const fields = ['insurance', 'foreigntransactionfees', 'customduties', 'surcharge', 'other'];

      fields.forEach(field => {
        const value = $(`#${field}`).val();
        if (value) {
          total += parseFloat(value) || 0;
        }
      });

      const taxes = parseFloat($('#taxes').val()) || 0;
      total += taxes;

      const shippingCost = parseFloat($('#shippingCost').val()) || 0;
      total += shippingCost;

      $('span#shippingSubTotal').text(`<?=getCurrencyFromID($currencyID)?> ${total.toFixed(2)}`);
    }

    function formatNumberInput($element) {
      let value = $element.val();
      if (value !== '' && !/^\d*\.?\d*$/.test(value)) {
        value = value.replace(/[^0-9.]/g, '');
      }
      const numericValue = parseFloat(value) || 0;
      $element.val(numericValue.toFixed(2));
    }

    // Handle input for real-time calculation without formatting
    $('#taxes, #shippingCost, #costValue, #insurance, #foreigntransactionfees, #customduties, #surcharge, #other').on('input', function() {
      let value = $(this).val();
      if (value !== '' && !/^\d*\.?\d*$/.test(value)) {
        $(this).val(value.replace(/[^0-9.]/g, ''));
      }

      if ($(this).attr('id') === 'costValue' && $('#costSelect').val()) {
        $('#costSelect').trigger('change');
      } else {
        updateShippingTotal();
      }
    });

    $('#taxes, #shippingCost, #costValue, #insurance, #foreigntransactionfees, #customduties, #surcharge, #other').on('blur', function() {
      formatNumberInput($(this));
      updateShippingTotal();
    });

    $(document).on('change', '#supplier', function(e) {

      blockArea($('.invoice-preview-header'));
      var formData = new FormData();
      formData.append('supplierID', $(this).val());

      $.ajax({
        url: '/ajax/supplier/get_supplier_detail.php',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        // dataType: 'json', // Expect JSON response
        success: function(data, status) {
          console.log(data);
          unBlockArea($('.invoice-preview-header'));

          $('#companyName').html(data.companyName);
          $('#companyNameAR').html(data.companyNameAr);
          $('#customerAddressLine1').html(data.addressLine1 === 'null' ? '' : data.addressLine1);
          $('#customerAddressLine2').html(data.addressLine2 === 'null' ? '' : data.addressLine2);
          $('#customerCityPostalCode').html((data.city ? data.city + ', ' : '') + ' ' + (data.postalCode || ''));
          $('#customerStateCountry').html((data.state ? data.state + ', ' : '') + data.country || '');
          $('#customerVAT').html('VAT: ' + data.vatNumber);
          $('#customerCR').html('CR: ' + (data.companyCRNumber || '-'));
          $('#paymentTerms').val(data.salesPaymentTermId);
          $('#supplierCurrency').val(data.currencyId);
        },

        error: function(error) {
          unBlockArea($('.invoice-preview-header'));
          console.log(error);
        }
      });

    });
  });

  function printPO() {

    let documentID = '<?=$poID; ?>';
    updatePOStatus(documentID, "<?=PO_STATUS_SENT_TO_SUPPLIER ?>");

    const baseUrl = window.location.origin;
    const relativeUrl = '/ajax/consultation/generate_po_pdf.php?poID=' + '<?=$poID ?>';
    const fullUrl = baseUrl + relativeUrl;
    window.open(fullUrl, '_blank');
  }


  function preparePOData() {
    console.log($('.source-item').repeaterVal());
    let repeaterData = $('.source-item').repeaterVal(); // Get the repeater data
    // Add additional data
    let requestData = {
      lineItems: repeaterData,
      poID: $('#poID').val(),
      supplierID: $('#supplier').val(), // Assuming this is the ID of the customer input field
      poDate: $('#poDate').val(), // Assuming this is the ID of the quotation date field
      paymentTerms: $('#paymentTerms').val(),
      supplierCurrency: $('#supplierCurrency').val(),
      rfpID: $('#rfpID').val(),
      rfpReason: $('#rfpReason').val(),
      refDocID: $('#refDocID').val(),
      supplierQuotationAttachmentFileName: $('#supplierQuotationAttachmentFileName').val(),
      supplierQuotationNo: $('#supplierQuotationNo').val(),
      etaDate: $('#etaDate').val(),
      carrier: $('#carrier').val(),
      tracking: $('#tracking').val(),
      vatAmount: $('#vatAmount').val() || '0.00',
      shippingCost: $('#shippingCost').val(),
      paymentRefNo: $('#paymentRefNo').val(),
      paymentRefAttachmentFileName: $('#paymentRefAttachmentFileName').val(),
      paymentDate: $('#paymentDate').val(),
      supplierInvoiceNo: $('#supplierInvoiceNo').val(),
      supplierInvoiceAttachmentFileName: $('#supplierInvoiceAttachmentFileName').val(),
      supplierInvoiceDate: $('#supplierInvoiceDate').val(),
      amountPaid: $('#amountPaid').val(),
      taxes: $('#taxes').val() || '0.00',
      customduties: $('#customduties').val() || '0.00',
      foreigntransactionfees: $('#foreigntransactionfees').val() || '0.00',
      insurance: $('#insurance').val() || '0.00',
      surcharge: $('#surcharge').val() || '0.00',
      other: $('#other').val() || '0.00',
      shippingSubTotal: extractNumbers($('#shippingSubTotal').text()) || '0.00'
    };
    console.log('requestData');
    console.log(requestData);

    return requestData;
  }

  function extractNumbers(value) {
    if (!value) return value;
    // Extracts numbers (including decimals)
    return String(value).replace(/[^\d.-]/g, '');
  }

  function preparePaymentData()
  {
    let requestData = {
      poID: $('#poID').val(),
      totalAmount: $('#totalAmount').val(),
      accountsPayableID: $('#accountsPayableID').val(),
      amountPaid: $('#amountPaid').val(),
      paymentMode: $('#paymentMode').val(),
      reference: $('#reference').val(),
      paymentDate: $('#paymentDate').val(),
      remarks: $('#remarks').val(),
    };
    console.log('Payment Details: ' + requestData);
    console.log(requestData);

    return requestData;
  }

  function savePayment() {

    if ($('#amountPaid').val().length > 0 && $('#paymentMode').val().length > 0) {
      blockArea($('body'));
      let requestData = preparePaymentData();

      $.ajax(
        {
          url: '/ajax/consultation/update_payment_details.php', // Update with your PHP script URL
          method: 'POST',
          data: JSON.stringify(requestData), // Send the combined data as a JSON string
          contentType: 'application/json', // Indicate that the data is JSON
          dataType: 'json', // Expect a JSON response
          success: function (response) {
            console.log('Server Response:', response);

            if (typeof response === 'string') {
              response = JSON.parse(response);
            }

            // Check the response status
            if (response.status === 'success') {
              // console.log(response.message); // Output: Quotation saved successfully.
              // console.log(response.documentId); // Access documentId
              unBlockArea($('body'));
              // let documentId = response.documentId;
              // $("#transferID").val(documentId);
              // $("#transferNumber").val("DRAFT");
              showSuccessMessage(response.message, gotoPage, "/consultation/purchaseorder/edit/" + '<?=$poID?>');


            } else {
              console.error('Error:', response.message);
              showErrorMessage(`Error: ${response.message}`);
            }

          },
          error: function (xhr, status, error) {
            console.error('Error:', error);
          }
        });
    } else {
      showErrorMessage("Please choose Payment Method and enter paid amount");
    }
  }


  // Save New Quotation
  function savePO() {
    // Check if supplier and destination warehouse are selected
    if ($('#supplier').val().length > 0) {
      blockArea($('body'));
      // let requestData = preparePOData();

      // Check if file is selected
      var paymentFileInput = document.getElementById('paymentRefAttachmentFile');
      var supplierFileInput = document.getElementById('supplierInvoiceAttachmentFile');
      var fileInput = null;
      var fileType = '';

      // Determine which file input is being used (only one at a time)
      if (paymentFileInput && paymentFileInput.files.length > 0) {
        fileInput = paymentFileInput;
        fileType = 'paymentRefAttachmentFile';
      } else if (supplierFileInput && supplierFileInput.files.length > 0) {
        fileInput = supplierFileInput;
        fileType = 'supplierInvoiceAttachmentFile';
      }

      if (fileInput && fileInput.files.length > 0) {
        // File is selected, proceed with upload
        var file = fileInput.files[0];
        var formData = new FormData();
        formData.append(fileType, file);

        $.ajax({
          url: '/ajax/consultation/po_file_upload.php',
          type: 'POST',
          data: formData,
          processData: false,
          contentType: false,
          success: function(response) {
            if (typeof response === 'string') {
              response = JSON.parse(response);
            }

            if (response.status === 'success') {
              // Add the uploaded filename to requestData if needed
              // requestData.uploadedFileName = uploadResponse.message;
              // Proceed with saving PO
              if (fileType === 'paymentRefAttachmentFile') {
                $('#paymentRefAttachmentFileName').val(response.message);
                console.log('Payment file attached');
              } else if (fileType === 'supplierInvoiceAttachmentFile') {
                $('#supplierInvoiceAttachmentFileName').val(response.message);
                console.log('Supplier invoice file attached');
              }
              let requestData = preparePOData();
              finalizeSavePO(requestData);
            } else {
              unBlockArea($('body'));
              showErrorMessage('File upload failed: ' + response.message);
            }
          },
          error: function(xhr, status, error) {
            unBlockArea($('body'));
            showErrorMessage('Error uploading file: ' + error);
          }
        });
      } else {
        // No file selected, proceed directly with saving PO
        let requestData = preparePOData();
        finalizeSavePO(requestData);
      }
    } else {
      showErrorMessage('Please choose Supplier and Destination Warehouse');
    }
  }

  // Separate function to handle the PO saving
  function finalizeSavePO(requestData) {
    $.ajax({
      url: '/ajax/consultation/save_po.php',
      method: 'POST',
      data: JSON.stringify(requestData),
      contentType: 'application/json',
      dataType: 'json',
      success: function(response) {
        console.log('Server Response:', response);

        if (typeof response === 'string') {
          response = JSON.parse(response);
        }

        if (response.status === 'success') {
          console.log(response.message);
          console.log(response.documentId);
          unBlockArea($('body'));
          let documentId = response.documentId;
          showSuccessMessage(response.message, gotoPage, '/consultation/purchaseorder/edit/' + documentId);
        } else {
          console.error('Error:', response.message);
          unBlockArea($('body'));
          showErrorMessage(`Error: ${response.message}`);
        }
      },
      error: function(xhr, status, error) {
        console.error('Error:', error);
        unBlockArea($('body'));
        showErrorMessage('An error occurred while saving the purchase order');
      }
    });
  }

  function sendForApproval() {
    savePOThread('<?=PO_STATUS_AWAITING_SPAREPARTS_MANAGER_APPROVAL ?>');
  }

  function approvePO() {
    let documentID = '<?=$poID; ?>';
    updatePOStatus(documentID, "<?=PO_STATUS_SPAREPARTS_MANAGER_APPROVED ?>");
  }

  function sendforAccountantApproval() {
    let documentID = '<?=$poID; ?>';
    updatePOStatus(documentID, "<?=PO_STATUS_AWAITING_ACCOUNTANT_APPROVAL ?>");
  }

  function accountantApprovePO() {
    let documentID = '<?=$poID; ?>';

    // Check if a file is attached (either in the file input or the hidden field)
    var fileInput = document.getElementById('paymentRefAttachmentFile');
    var fileName = $('#paymentRefAttachmentFileName').val();
    var paymentRefNo = $('#paymentRefNo').val();

    // Check if both file and payment reference number are provided
    if (fileInput && (fileInput.files.length > 0 || fileName) && paymentRefNo) {
      // Both file and payment reference number are provided, proceed with approval
      updatePOStatus(documentID, "<?=PO_STATUS_ACCOUNTANT_APPROVED ?>");
    } else {
      // Show error message if either file or payment reference number is missing
      let errorMessage = 'Payment Reference Attachment and Payment Reference Number must be provided before approving.';
      if (!fileInput || (!fileInput.files.length > 0 && !fileName)) {
        errorMessage = 'Payment Reference Attachment is not attached. Please attach the file before approving.';
      } else if (!paymentRefNo) {
        errorMessage = 'A Payment Reference Number must be provided before approving.';
      }
      showErrorMessage(errorMessage);
    }
  }

  function rejectPO() {
    let documentID = '<?=$poID; ?>';

    let rejectReason = $('#rejectReason').val();
    console.log(rejectReason);
    if (rejectReason.length) {
      updatePOStatus(documentID, "<?=PO_STATUS_SPAREPARTS_MANAGER_REJECTED ?>", true, rejectReason);
    } else {
      showErrorMessage('You must enter a reason for rejection');
    }
  }

  function rejectPOAccountant() {
    let documentID = '<?=$poID; ?>';

    let rejectReason = $('#rejectReason').val();
    console.log(rejectReason);
    if (rejectReason.length) {
      updatePOStatus(documentID, "<?=PO_STATUS_ACCOUNTANT_REJECTED ?>", true, rejectReason);
    } else {
      showErrorMessage('You must enter a reason for rejection');
    }
  }

  function updateInvoice() {
    // Check if supplier invoice number is provided
    let supplierInvoiceNo = $('#supplierInvoiceNo').val();
    if (!supplierInvoiceNo || supplierInvoiceNo.trim().length === 0) {
      showErrorMessage('Please provide a Supplier Invoice Number');
      return;
    }

    // Check if supplier invoice file is attached
    let supplierFileInput = document.getElementById('supplierInvoiceAttachmentFile');
    if (!supplierFileInput || supplierFileInput.files.length === 0) {
      showErrorMessage('Please attach the Supplier Invoice file');
      return;
    }

    // If all checks pass, proceed with file upload and saving PO
    blockArea($('body'));
    let documentID = '<?=$poID; ?>';
    let file = supplierFileInput.files[0];
    let formData = new FormData();
    formData.append('supplierInvoiceAttachmentFile', file);

    $.ajax({
      url: '/ajax/consultation/po_file_upload.php',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        if (typeof response === 'string') {
          response = JSON.parse(response);
        }

        if (response.status === 'success') {
          $('#supplierInvoiceAttachmentFileName').val(response.message);
          console.log('Supplier invoice file attached');
          // Proceed with saving PO and updating status
          let requestData = preparePOData();
          finalizeSavePO(requestData);
          updatePOStatus(documentID, "<?=PO_STATUS_SUPPLIER_INVOICE_ATTACHED ?>");
        } else {
          unBlockArea($('body'));
          showErrorMessage('File upload failed: ' + response.message);
        }
      },
      error: function(xhr, status, error) {
        unBlockArea($('body'));
        showErrorMessage('Error uploading file: ' + error);
      }
    });
  }

  function savePOThread(status) {

    //Then proceed with saving the quotaion.
    if ($('#supplier').val().length > 0) {
      // blockArea($('body'));
      let requestData = preparePOData();
      $.ajax({
        url: '/ajax/consultation/save_po.php', // Update with your PHP script URL
        method: 'POST',
        data: JSON.stringify(requestData), // Send the combined data as a JSON string
        contentType: 'application/json', // Indicate that the data is JSON
        dataType: 'json', // Expect a JSON response
        success: function(response) {
          console.log('Server Response after saving PO:', response);

          if (typeof response === 'string') {
            response = JSON.parse(response);
          }

          console.log('Response: ' + response.status);
          // Check the response status
          if (response.status === 'success') {
            unBlockArea($('body'));
            let documentID = response.documentId;
            updatePOStatus(documentID, status);
          } else {
            console.error('Error:', response.message);
            showErrorMessage(`Error: ${response.message}`);
          }
        },
        error: function(xhr, status, error) {
          console.error('Error:', error);
        }
      });
    } else {
      showErrorMessage('Nothing to save.');
    }
  }

  function updatePOStatus(documentID, status, showMessage = true, rejectReason = '') {

    console.log('Updating PO Status: ' + documentID + ' - ' + status + ' - ' + showMessage + ' - ' + rejectReason);

    console.log(rejectReason);
    var formData = new FormData();
    formData.append('documentID', documentID);
    formData.append('status', status);
    formData.append('rejectReason', rejectReason);

    console.log(formData);

    $.ajax({
      url: '/ajax/consultation/update_po_status.php',
      type: 'POST',
      data: formData,
      contentType: false,
      processData: false,
      success: function(data, status) {
        console.log(data);

        var statusmessage = data.trim().split('|')[0];
        var message = data.trim().split('|')[1];

        if (statusmessage == 'SUCCESS') {
          if (showMessage)
            showSuccessMessage(message, gotoPage, '/consultation/purchaseorder/edit/' + documentID);
        }
      },

      error: function(error) {
        console.log(error);
      }
    });

  }

  function sendForSOApproval() {
    let supplierQuotationNo = $('#supplierQuotationNo').val();

    if (supplierQuotationNo.length > 3) {


      if ($('#supplierQuotationAttachmentFileName').val().length > 0) {
        //PO Was already attached. so Proceed with SO Approval
        console.log('File not attached');
      } else {
        //PO is not attached. so prompt or upload the new PO.
        var fileInput = document.getElementById('supplierQuotationAttachmentFile');
        if (fileInput.files.length === 0) {
          showErrorMessage('Supplier Quotation is not attached');
        } else {

          //Do the upload
          var file = fileInput.files[0];

          var formData = new FormData();
          formData.append('supplierQuotationAttachmentFile', file); // Add the file to FormData

          // Make the AJAX request
          $.ajax({
            url: '/ajax/consultation/file_upload.php', // Replace with your PHP file handling URL
            type: 'POST',
            data: formData,
            processData: false, // Don't process the data
            contentType: false, // Don't set content type
            success: function(response) {
              if (typeof response === 'string') {
                response = JSON.parse(response);
              }
              if (response.status === 'success') {
                $('#supplierQuotationAttachmentFileName').val(response.message);
                console.log('File attached');
              }
              console.log(response); // Server response
            },
            error: function(xhr, status, error) {
              console.error(error);
            }
          });
        }
      }


      ////////////////////////////////////////////////////
    } else {
      Swal.fire({
        title: 'Supplier Quotation Number not entered!',
        icon: 'error',
        text: 'Supplier Quotation Number is not entered.',
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
  }

  function saveQuotationThread() {

    //Then proceed with saving the quotaion.
    if ($('#customer').val().length > 0) {
      // blockArea($('body'));
      let requestData = prepareQuotationData();
      $.ajax({
        url: '/ajax/documents/save_quotation.php', // Update with your PHP script URL
        method: 'POST',
        data: JSON.stringify(requestData), // Send the combined data as a JSON string
        contentType: 'application/json', // Indicate that the data is JSON
        dataType: 'json', // Expect a JSON response
        success: function(response) {
          console.log('Server Response:', response);


          if (typeof response === 'string') {
            response = JSON.parse(response);
          }

          // Check the response status
          if (response.status === 'success') {

            unBlockArea($('body'));

            let documentID = response.documentId;

            updateQuotationStatus(documentID);

          } else {
            console.error('Error:', response.message);
            alert(`Error: ${response.message}`);
          }

        },
        error: function(xhr, status, error) {
          console.error('Error:', error);
        }
      });
    } else {
      Swal.fire({
        title: 'Error!',
        icon: 'error',
        text: 'Nothing to save.',
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


  }


  function updateQuotationStatus(documentID) {


    var formData = new FormData();
    formData.append('documentID', documentID);
    formData.append('status', 'AWAITING APPROVAL');

    $.ajax({
      url: '/ajax/documents/update_quotation_status.php',
      type: 'POST',
      data: formData,
      contentType: false,
      processData: false,
      success: function(data, status) {
        console.log(data);

        var statusmessage = data.trim().split('|')[0];
        var message = data.trim().split('|')[1];

        if (statusmessage == 'SUCCESS') {
          location.href = '/quotation/edit/' + documentID;
        }

        if (statusmessage == 'ERROR') {

        }
      },

      error: function(error) {

        console.log(error);
      }
    });

  }

  // repeater (jquery)
  $(function() {
    var sourceItem = $('.source-item');

    // Repeater init
    if (sourceItem.length) {
      sourceItem.on('submit', function(e) {
        e.preventDefault();
      });

      sourceItem.repeater({
        ready: function(setIndexes) {
          initializeSelect2($('select[name="sparepartitem"]'), null);
          applyDatePicker();
          applySelectPicker();
          doCalculation();
        },

        show: function() {

          $(this).slideDown();
          updateItemNumbers();
          const index = $(this).index();

        },
        hide: function(remove) {
          $(this).slideUp(300, function() {
            remove(); // Ensures the element is removed
            doCalculation(); // Call after removal from DOM
          });
        }
      });
    }

    // Item details select onchange
    $(document).on('change', '.item-details', function() {
      var $this = $(this),
        value = adminDetails[$this.val()];
      if ($this.next('textarea').length) {
        $this.next('textarea').val(value);
      } else {
        $this.after('<textarea class="form-control" rows="2">' + value + '</textarea>');
      }
    });
  });

  function updateItemNumbers() {
    const items = document.querySelectorAll('.itemNumber');
    items.forEach((item, index) => {
      item.textContent = 'Item #' + (index + 1).toString();
    });
  }

  function applySelectPicker() {
    let sparepartitem;
    let preselectedValue;

    sparepartitem = $('[name="line-item[0][sparepartitem]"]');
    preselectedValue = sparepartitem.data('selected') || null;
    initializeSelect2(sparepartitem, preselectedValue);
  }

  function applyDatePicker() {
    const flatpickrFriendly = $('[name="line-item[0][eta]"]');
    flatpickrFriendly.flatpickr({
      dateFormat: 'Y-m-d',
      altInput: true,
      altFormat: 'd - M - Y'
    });
  }


  //On sparepart Item Changed
  $(document).on('select2:select', '.sparepartitem', function(e) {

    $('#noSparePartsInitialDiv').hide();
    $('#repeaterAddItemButton').trigger('click');

    e.stopPropagation();
    console.log('Spare part Item Changed');
    const selectedData = e.params.data;

    try {

      const selectedSparepartID = selectedData.id;
      const selectedInternalReference = selectedData.internalReference;
      const selectedDesc = selectedData.desc;
      const selectedUOMID = selectedData.uomid;

      setTimeout(function() {

        var lastIndex = $('.source-item [data-repeater-item]').length - 1;
        console.log('Last Added Index:', lastIndex);

        const index = lastIndex;

        const sparepartID = getElementByIndexAndName(index, 'sparepartID');
        sparepartID.val(selectedSparepartID);

        const sparepartInternalReferenceNumber = getElementByIndexAndName(index, 'sparepartinternalrefnumber');
        sparepartInternalReferenceNumber.val(selectedInternalReference);

        const sparepartdescription = getElementByIndexAndName(index, 'sparepartdescription');
        sparepartdescription.val(selectedDesc);

        const qty = getElementByIndexAndName(index, 'qty');
        qty.val('1');

        const uom = getElementByIndexAndName(index, 'uom');
        $(uom).val(selectedUOMID);
        doCalculation();

      }, 100);

    } catch (err) {

    }

  });


  $(document).on('change', '.calculation-trigger', function(e) {
    doCalculation();
  });


  function doCalculation() {
    const totalItemRows = getRepeaterRowCount();

    let rowSubTotal = 0;

    let totalAmount = 0.00;

    for (let index = 0; index < totalItemRows; index++) {

      let qty = parseFloat(getElementByIndexAndName(index, 'qty').val()) || 0;
      let cost = parseFloat(getElementByIndexAndName(index, 'cost').val()) || 0;
      let uomRatio = parseFloat(getElementByIndexAndName(index, 'uom').find(':selected').data('ratio')) || 1;


      // Calculate base subtotal
      let baseSubtotal = qty * uomRatio * cost;

      totalAmount += baseSubtotal;


      // Set values to respective fields
      getElementByIndexAndName(index, 'subTotal').val(toTwoDecimal(baseSubtotal));


    }

    const totalAmountSpan = $('#totalAmountSpan');
    totalAmountSpan.html('<?=getCurrencyFromID($currencyID)?> ' + toTwoDecimal(totalAmount).toString());

  }


  // Initialize Select2 with AJAX
  function initializeSelect2(selectElement, preselectedValue = null) {
    console.log('preselectedValue: ' + preselectedValue);

    console.log(selectElement);
    // Initialize Select2
    $(selectElement).select2({
      placeholder: 'Select a part number',
      minimumInputLength: 3,
      ajax: {
        url: '/ajax/spareparts/get_spareparts.php',
        dataType: 'json',
        delay: 250, // Delay AJAX requests to reduce load
        data: function(params) {
          return { q: params.term }; // Send search query to PHP
        },
        processResults: function(data) {
          return { results: data.results };
        },
        cache: true
      },
      templateResult: function(data) {
        if (!data.id) return data.text; // For placeholder
        return $(`<div>
                            <strong>${data.text}</strong><br/>
                            <small>${data.desc}</small>
                          </div>`
        );
      },
      templateSelection: function(data) {
        return data.text || 'Select a part number';
      }
    });

    // // If a preselected value is provided, add it to the dropdown
    // if (preselectedValue) {
    //     const preselectedItem = sparePartsOptions.find(option => option.id == preselectedValue);
    //     if (preselectedItem) {
    //         // Add the preselected item as an option
    //         const newOption = new Option(preselectedItem.text, preselectedItem.id, true, true);
    //         $(selectElement).append(newOption).trigger('change'); // Append and trigger change event
    //         // $(selectElement).append(newOption); // Append and trigger change event
    //     }
    // }
  }


  // Initialize existing select boxes on page load
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.sparepartitem').forEach(select => {
      const preselectedValue = select.dataset.selected || null;
      // initializeSelect2(select, preselectedValue);
    });
  });
</script>
<style>
    .no-hand {
        cursor: default !important;
    }

    #showHistoryOffcanvas {
        width: 650px !important;
    }
</style>
</body>
</html>