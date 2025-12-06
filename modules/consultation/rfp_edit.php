<?php
$PAGE_ID = "CONSULTATION_RFP_EDIT";
include_once __DIR__ . "/../../includes/baseIncludes.php";
include_once __DIR__ . "/../../includes/auth_check.php";

$rfpID = "";
$rfpID = filter_input(INPUT_GET, 'rfpID', FILTER_VALIDATE_INT);
if ($rfpID === null || $rfpID === false || filter_var($rfpID, FILTER_VALIDATE_INT) === false) {
    header("location:/rfp/list");
    exit();
}

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
  <title>GrandMaster ERP | Consultation RFP</title>
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
          <?php

          $rfpDATA = "";
          $res = $db->query("SELECT * FROM consultation_rfp_documents WHERE active = 1 and rfpId = ?s", $rfpID);
          while ($row = mysqli_fetch_assoc($res)) {
              $rfpDATA = $row;
          }

          $supplierID = $rfpDATA['supplierId'];
          $currencyID = $rfpDATA['currencyId'];

          $currentUserRole = getAuthenticatedUser()->getRoles()[0];
          $documentStatus = $rfpDATA['rfpStatus'];

          $companyId = intval($_SESSION['SES_SELECTED_COMPANY']);

          ?>
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">
          <div class="card mb-6">
            <div class="card-widget-separator-wrapper">
              <div class="card-body card-widget-separator py-2">
                <div class="row gy-1 gy-sm-1">
                  <div class="col-6">
                    <h4 class="my-0"><?= getRFPNumberFromConsultationDocumentID($rfpDATA['rfpId']) ?> | Edit RFP</h4>
                  </div>
                  <div class="col-6 text-end">
                      <?php
                      switch ($rfpDATA['rfpStatus']) {
                          case RFP_STATUS_NEW:
                              ?>
                            <button type="button"
                                    class="btn btn-label-linkedin text-nowrap d-inline-flex position-relative me-4">
                              DRAFT
                            </button>
                              <?php
                              break;

                          case RFP_STATUS_PROCUREMENT_MANAGER_APPROVED:
                              ?>
                            <label
                              class="no-hand btn btn-label-success text-nowrap d-inline-flex position-relative me-4">
                              PROCUREMENT MANAGER APPROVED
                            </label>
                              <?php
                              break;

                          case RFP_STATUS_PROCUREMENT_MANAGER_REJECTED:
                              ?>
                            <label
                              class="no-hand btn btn-label-danger text-nowrap d-inline-flex position-relative me-4">
                              PROCUREMENT MANAGER REJECTED
                            </label>
                              <?php
                              break;

                          default:
                              ?>
                            <button type="button"
                                    class="btn btn-label-info text-nowrap d-inline-flex position-relative me-4">
                                <?= $rfpDATA['rfpStatus'] ?>
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
            $documentStatus = $rfpDATA['rfpStatus'];
            if ($documentStatus == RFP_STATUS_PROCUREMENT_MANAGER_REJECTED) {
                $rejectReason = getRejectReasonForConsultationRFPID($rfpID);
                ?>
              <div class="alert alert-solid-danger d-flex align-items-center" role="alert">
                        <span class="alert-icon rounded">
                          <i class="ti ti-ban"></i>
                        </span> Rejection Reason: <?= $rejectReason ?>
              </div>
                <?php
            }

            if ($rfpDATA['rfpStatus'] == RFP_STATUS_SENT_TO_SUPPLIER && (!checkIfRFPEligibleForPO($rfpID) || empty($rfpDATA['supplierQuotationAttachment']) || empty($rfpDATA['supplierQuotationNumber']))) {
                ?>
              <div class="alert alert-solid-info" role="alert">
                To create Purchase Order, Please Enter the Supplier Quotation No., Supplier Quotation Attachment File,
                and Cost for Line Items.
              </div>

                <?php
            }

            if ($rfpDATA['rfpStatus'] == RFP_STATUS_PROCUREMENT_MANAGER_APPROVED) {
                ?>
              <div class="alert alert-solid-info" role="alert">
                In order to proceed further, please print and send it to the Supplier
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
                    <!-- Buttons Row (Left-Aligned) -->
                    <div class="col-12 mb-4">
                      <div class="d-flex justify-content-between gap-2">
                        <div>
                          <button class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#showHistoryOffcanvas"> 
                            <span class="d-flex align-items-center justify-content-center text-nowrap">
                              <i class="ti ti-history ti-xs me-2"></i>History
                            </span>
                          </button>
                        </div>
                      <div class="d-flex gap-2 justify-content-end">
                        <button class="btn btn-primary" onclick="saveRFP()">
                          <span class="d-flex align-items-center justify-content-center text-nowrap">
                            <i class="ti ti-device-floppy ti-xs me-2"></i>Save
                          </span>
                        </button>
                          <?php
                          if (($currentUserRole == ROLE_SUPERADMIN) && ($documentStatus == RFP_STATUS_NEW || $documentStatus == RFP_STATUS_PROCUREMENT_MANAGER_REJECTED)) {
                              ?>
                            <button class="btn btn-primary" onclick="sendForApproval()">
                          <span class="d-flex align-items-center justify-content-center text-nowrap">
                            <i class="ti ti-file-like ti-xs me-2"></i>Send for Approval
                          </span>
                            </button>
                              <?php
                          }
                          if (($currentUserRole == ROLE_SUPERADMIN) && $documentStatus == RFP_STATUS_PROCUREMENT_MANAGER_APPROVAL) {
                              ?>
                            <button class="btn btn-success" onclick="approveRFP()">
                          <span class="d-flex align-items-center justify-content-center text-nowrap">
                            <i class="ti ti-device-floppy ti-xs me-2"></i>Approve
                          </span>
                            </button>
                            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectReasonModal">
                          <span class="d-flex align-items-center justify-content-center text-nowrap">
                            <i class="ti ti-circle-dashed-x ti-xs me-2"></i>Reject
                          </span>
                            </button>
                              <?php
                          }
                          if (($currentUserRole == ROLE_SUPERADMIN) && ($documentStatus == RFP_STATUS_PROCUREMENT_MANAGER_APPROVED || $documentStatus == RFP_STATUS_SENT_TO_SUPPLIER)) {
                              ?>
                            <button class="btn btn-info" onclick="printRFP()">
                          <span class="d-flex align-items-center justify-content-center text-nowrap">
                            <i class="ti ti-printer ti-xs me-2"></i>Print
                          </span>
                            </button>
                              <?php
                          }
                          //Check if the document can be converted to PO
                          if (checkIfRFPEligibleForPO($rfpID) && $documentStatus == RFP_STATUS_SENT_TO_SUPPLIER && !empty($rfpDATA['supplierQuotationAttachment']) && !empty($rfpDATA['supplierQuotationNumber'])) {
                              ?>
                            <button class="btn btn-success" onclick="createPO()">
                          <span class="d-flex align-items-center justify-content-center text-nowrap">
                            <i class="ti ti-file-check ti-xs me-2"></i>Create PO
                          </span>
                            </button>
                              <?php
                          }

                          ?>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-5 col-sm-5 col-12 mb-sm-0 mb-6">
                      <div class="mb-4">
                        <label class="form-label" for="customerCountry">Supplier:</label>
                        <select name="supplier" id="supplier" class="form-select mb-4 w-50 selectpicker w-100"
                                data-style="btn-default" data-live-search="true" tabindex="null">
                          <option value="">Select supplier</option>
                            <?php
                            $res = $db->query("SELECT * FROM suppliers WHERE status ='Active' and companyId = $companyId");
                            while ($row = mysqli_fetch_assoc($res)) {
                                ?>
                              <option <?php if ($row['supplierId'] == $rfpDATA['supplierId']) echo "selected"; ?>
                                value="<?= $row['supplierId'] ?>"><?= $row['companyName'] . " | " . $row['companyNameAr'] ?></option>
                                <?php
                            }
                            ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-5 col-sm-5 col-12 mb-sm-0 mb-6">
                      <div class="mb-4">
                      </div>
                    </div>
                  </div>
                </div>
                <!--Grey Header Section-->
                <div class="card-body invoice-preview-header rounded">
                  <!-- Supplier and Warehouse Details Row -->
                  <div class="row">
                    <!-- Supplier Details (Left) -->
                    <div class="col-md-6 col-12 mb-4">
                        <?php
                        $supplier = new Supplier();
                        $supplier->loadById($supplierID);
                        ?>
                      <div class="svg-illustration mb-6 gap-2 align-items-center">
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
                         id="customerCityPostalCode"><?= getCityFromID($supplier->cityId) . " " . $supplier->postalCode ?></p>
                      <p class="mb-2"
                         id="customerStateCountry"><?= getStateFromID($supplier->stateId) . " " . getCountryFromID($supplier->countryId) ?></p>
                      <p class="mb-2" id="customerVAT">VAT: <?= $supplier->vatNumber ?></p>
                      <p class="mb-2" id="customerCR">
                        CR: <?= $supplier->companyCRNumber ?></p>
                    </div>
                    <!-- Warehouse Details (Right) -->
                    <div class="col-md-6 col-12 mb-4 text-md-end d-flex flex-column align-items-md-end">
                    </div>
                  </div>
                  <hr style="height: 0.5px; background-color: #2c3539; border: none;">
                    <?php

                    $rfpNumber = "DRAFT";
                    if (!empty($rfpDATA['rfpNumberPrefix'])) {
                        $rfpNumber = $rfpDATA['rfpNumberPrefix'] . $rfpDATA['rfpNumber'];
                    }
                    ?>
                  <div class="row">
                    <!-- First Row of Input Fields -->
                    <div class="col-md-6 col-12 mb-4">
                      <dl class="row mb-0">
                        <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="h5 text-capitalize mb-0 text-nowrap">RFP</span>
                        </dt>
                        <dd class="col-md-8 col-sm-7">
                          <div class="input-group input-group-merge disabled">
                            <span class="input-group-text">#</span>
                            <input name="rfpNumber" id="rfpNumber" type="text" class="form-control" disabled
                                   placeholder="Will be generated once saved / confirmed" value="<?= $rfpNumber ?>" />
                          </div>
                          <input name="rfpID" id="rfpID" type="hidden" value="<?= $rfpDATA['rfpId'] ?>" />
                        </dd>
                        <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Date Created:</span>
                        </dt>
                        <dd class="col-md-8 col-sm-7">
                          <input name="rfpDate" id="rfpDate" type="text" class="form-control gmm-date-format"
                                 placeholder="DD - MMM - YYYY" value="<?= $rfpDATA['rfpDateCreated'] ?>" />
                        </dd>
                        <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Supplier Currency:</span>
                        </dt>
                        <dd class="col-md-8 col-sm-7">
                          <select name="supplierCurrency" id="supplierCurrency" class="form-control mt">
                            <option disabled selected value="">Select Currency
                            </option>
                              <?php
                              $res = $db->query("SELECT * FROM currencies WHERE active = 1");
                              while ($row = mysqli_fetch_assoc($res)) {
                                  ?>
                                <option <?php if ($rfpDATA['currencyId'] == $row['currencyId']) echo "SELECTED" ?>
                                  value="<?= $row['currencyId'] ?>"><?= $row['currency'] ?></option> <?php
                              }
                              ?>
                          </select>
                        </dd>
                        <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Supplier Quotation#:</span>
                        </dt>
                        <dd class="col-md-8 col-sm-7">
                          <input type="text" id="supplierQuotationNo" class="form-control due-date"
                                 value="<?= $rfpDATA['supplierQuotationNumber'] ?>" />
                        </dd>
                        <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Quotation Attachment:</span>
                        </dt>
                        <dd class="col-md-8 col-sm-7">
                          <input class="form-control" type="file" id="supplierQuotationAttachmentFile"
                                 name="supplierQuotationAttachmentFile">
                          <input class="form-control" type="hidden" id="supplierQuotationAttachmentFileName"
                                 name="supplierQuotationAttachmentFileName"
                                 value="<?= $rfpDATA['supplierQuotationAttachment'] ?>" />
                        </dd>
                        <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Attachment:</span>
                        </dt>
                        <dd class="col-md-8 col-sm-7">
                            <?php
                            if (!empty($rfpDATA['supplierQuotationAttachment'])) {
                                ?>
                              <a class="btn btn-info" href="/uploads/<?= $rfpDATA['supplierQuotationAttachment'] ?>"
                                 target="_blank">View
                                Supplier Quotation</a>
                                <?php
                            }
                            ?>
                        </dd>
                      </dl>
                    </div>
                    <div class="col-md-6 col-12 mb-4">
                      <dl class="row mb-0">
                        <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Sales Person:</span>
                        </dt>
                        <dd class="col-md-8 col-sm-7">
                          <input type="text" readonly class="form-control due-date"
                                 value="<?= getDisplayNameFromUserID($rfpDATA['salesPersonId']) ?>" />
                        </dd>
                        <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Payment Terms:</span>
                        </dt>
                        <dd class="col-md-8 col-sm-7">
                          <select name="paymentTerms" id="paymentTerms" class="form-control mt">
                            <option disabled selected value="">Select Payment Term
                            </option>
                              <?php
                              $res = $db->query("SELECT * FROM payment_terms WHERE active = 1");
                              while ($row = mysqli_fetch_assoc($res)) {
                                  ?>
                                <option <?php if ($rfpDATA['paymentTermId'] == $row['termId']) echo "SELECTED" ?>
                                  value="<?= $row['termId'] ?>"><?= $row['termName'] ?></option> <?php
                              }
                              ?>
                          </select>
                        </dd>
                        <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Reason for RFP:</span>
                        </dt>
                        <dd class="col-md-8 col-sm-7">
                          <input type="text" id="reasonForRFP" class="form-control"
                                 value="<?= $rfpDATA['rfpReason'] ?>" readonly />
                        </dd>
                        <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Sales Order #:</span>
                        </dt>
                        <dd class="col-md-8 col-sm-7">
                          <input type="text" class="form-control"
                                 value="<?php if (!empty($rfpDATA['saleOrderId']) || $rfpDATA['saleOrderId'] <> '0') echo getSalesOrderNumberFromDocumentID($rfpDATA['saleOrderId']) ?>"
                                 readonly />
                          <input type="hidden" id="refDocID" class="form-control"
                                 value="<?= $rfpDATA['saleOrderId'] ?>" readonly />
                        </dd>
                        <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">ETA:</span>
                        </dt>
                        <dd class="col-md-8 col-sm-7">
                          <input name="etaDate" id="etaDate" type="text" class="form-control gmm-date-format"
                                 placeholder="DD - MMM - YYYY" value="<?= $rfpDATA['etaDate'] ?>" />
                        </dd>
                        <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Carrier:</span>
                        </dt>
                        <dd class="col-md-8 col-sm-7">
                          <input type="text" id="carrier" class="form-control due-date"
                                 value="<?= $rfpDATA['shippingMethod'] ?>" />
                        </dd>
                        <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Tracking Number:</span>
                        </dt>
                        <dd class="col-md-8 col-sm-7">
                          <input type="text" id="tracking" class="form-control due-date"
                                 value="<?= $rfpDATA['trackingNumber'] ?>" />
                        </dd>
                      </dl>
                    </div>
                  </div>
                </div>
                <!--Grey Header Section Ends-->
                <hr class="mt-5 mb-6" />
                <div class="row">
                  <div class="col-md-5 col-sm-5 col-12 mb-sm-0 mb-6">
                    <div class="mb-4">
                      <label class="form-label" for="customerCountry">Add Service/Product:</label>
                      <select name="sparepartitem" class="sparepartitem form-control mb-5" data-live-search="true">
                        <option value="">Select a Service/Product</option>
                      </select>
                    </div>
                  </div>
                </div>
                <hr class="mt-0 mb-6" />
                <div class="card-body pt-0 px-0">
                  <div class="row" id="noSparePartsInitialDiv" style="display: none">
                    <div class="col-12 text-center">
                      Add Service/Product from the above search box
                    </div>
                  </div>
                  <form class="source-item">
                    <div class="mb-4" data-repeater-list="line-item">
                        <?php
                        $itemCount = 0;
                        $lineitemres = $db->query("SELECT * FROM consultation_rfp_line_items WHERE rfpId = ?s", $rfpID);

                        while ($lineitemrow = mysqli_fetch_assoc($lineitemres)) {
                            ?>
                          <div class="repeater-wrapper pt-0 pt-md-6" data-repeater-item>
                            <div class="d-flex border rounded position-relative pe-0">
                              <div class="row w-100 p-3">
                                <div class="col-md-3 col-12 mb-md-0 mb-4">
                                  <p class="h6 mb-1 itemNumber">Item
                                    #<?= ++$itemCount ?></p>
                                  <input name="serviceItemName" type="text" class="form-control mb-5"
                                         readonly
                                         value="<?= getItemNameForItemID($lineitemrow['itemId']); ?>" />
                                  <input name="serviceItemID" type="hidden" class="form-control mb-5" readonly
                                         value="<?= $lineitemrow['itemId'] ?>" />
                                </div>
                                <div class="col-md-3 col-12 mb-md-0 mb-4">
                                  <p class="h6 mb-1">Description</p>
                                  <input name="servicedescription" type="text" class="form-control mb-5" readonly
                                         value="<?= getItemDescriptionForItemID($lineitemrow['itemId']) ?>" />
                                </div>
                                <div class="col-md-1 col-12 mb-md-0 mb-4">
                                  <p class="h6 mb-1">Qty</p>
                                  <input name="qty" type="text" class="form-control numbers-only calculation-trigger"
                                         placeholder="Qty" min="1" required value="<?= $lineitemrow['quantity'] ?>" />
                                </div>
                                <div class="col-md-2 col-12 mb-md-0 mb-4">
                                  <p class="h6 mb-1">UOM</p>
                                  <select name="uom" class="form-control mb-6 calculation-trigger"
                                          data-placeholder="Select UOM">
                                    <option disabled value="">Select UOM</option>
                                      <?php
                                      $res = $db->query("SELECT * FROM uoms WHERE active = 1");
                                      while ($row = mysqli_fetch_assoc($res)) {
                                          ?>
                                        <option <?php if ($lineitemrow['UOM'] == $row['uomId']) echo 'SELECTED' ?>
                                          value="<?= $row['uomId'] ?>" data-type="<?= $row['type'] ?>"
                                          data-ratio="<?= $row['ratio'] ?>"><?= $row['uomName'] ?></option>
                                          <?php
                                      }
                                      ?>
                                  </select>
                                </div>
                                <div class="col-md-1 col-12 mb-md-0 mb-4">
                                  <p class="h6 mb-1">Cost</p>
                                  <input name="cost" type="text" class="form-control numbers-only calculation-trigger"
                                         placeholder="Cost" min="1" required
                                         value="<?= $lineitemrow['costPrice'] ?>" />
                                </div>
                                <div class="col-md-1 col-12 mb-md-0 mb-4">
                                  <p class="h6 mb-1">Sub Total</p>
                                  <input name="subTotal" type="text"
                                         class="form-control numbers-only calculation-trigger" placeholder="Total"
                                         min="1" required value="<?= $lineitemrow['subTotal'] ?>" readonly />
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
                        <div class="d-flex justify-content-between">
                          <span class="px-5">Total Amount: </span>
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
          <!-- Send Invoice Sidebar -->
          <div class="offcanvas offcanvas-end" id="sendInvoiceOffcanvas" aria-hidden="true">
            <div class="offcanvas-header mb-6 border-bottom">
              <h5 class="offcanvas-title">Send Invoice</h5>
              <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                      aria-label="Close"></button>
            </div>
            <div class="offcanvas-body pt-0 flex-grow-1">
              <form>
                <div class="mb-6">
                  <label for="invoice-from" class="form-label">From</label>
                  <input type="text" class="form-control" id="invoice-from" value="shelbyComapny@email.com"
                         placeholder="company@email.com" />
                </div>
                <div class="mb-6">
                  <label for="invoice-to" class="form-label">To</label>
                  <input type="text" class="form-control" id="invoice-to" value="qConsolidated@email.com"
                         placeholder="company@email.com" />
                </div>
                <div class="mb-6">
                  <label for="invoice-subject" class="form-label">Subject</label>
                  <input type="text" class="form-control" id="invoice-subject"
                         value="Invoice of purchased Admin Templates" placeholder="Invoice regarding goods" />
                </div>
                <div class="mb-6">
                  <label for="invoice-message" class="form-label">Message</label>
                  <textarea class="form-control" name="invoice-message" id="invoice-message" cols="3" rows="8">
Dear Queen Consolidated,
          Thank you for your business, always a pleasure to work with you!
          We have generated a new invoice in the amount of $95.59
          We would appreciate payment of this invoice by 05/11/2021</textarea>
                </div>
                <div class="mb-6">
                      <span class="badge bg-label-primary">
                        <i class="ti ti-link ti-xs"></i>
                        <span class="align-middle">Invoice Attached</span>
                      </span>
                </div>
                <div class="mb-6 d-flex flex-wrap">
                  <button type="button" class="btn btn-primary me-4" data-bs-dismiss="offcanvas">
                    Send
                  </button>
                  <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">
                    Cancel
                  </button>
                </div>
              </form>
            </div>
          </div>
          <!-- /Send Invoice Sidebar -->
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

                                    $consultationRFPDocumentHistory = new ConsultationRFPHistory();
                                    $consultationRFPDocumentHistory->loadByRFPId($rfpID);

                                    foreach ($consultationRFPDocumentHistory->timeline as $historyItem) {
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
<!--  Reject Reason Modal-->
<!-- / Layout wrapper -->
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
          if ($rfpDATA['rfpStatus'] == RFP_STATUS_PROCUREMENT_MANAGER_APPROVAL) {
              ?>
            <button type="button" class="btn btn-danger" onclick="rejectRFP()">Reject</button>
              <?php
          }
          ?>
      </div>
    </div>
  </div>
</div>
<?php include_once __DIR__ . "/../../includes/dashboard/dashboard_footer_scripts.php"; ?>
<script>
  let sparepartsAutoCompleteData = [];

  $(document).ready(function(e) {

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

  function printRFP() {

    let documentID = "<?= $rfpID ?>";
    updateRFPStatus(documentID, "<?= RFP_STATUS_SENT_TO_SUPPLIER ?>");
    const baseUrl = window.location.origin;
    const relativeUrl = '/ajax/consultation/generate_rfp_pdf.php?rfpID=' + '<?=$rfpID ?>';
    const fullUrl = baseUrl + relativeUrl;
    window.open(fullUrl, '_blank');

  }

  function createPO() {
    // Check if a file is attached (either in the file input or the hidden field)
    var fileInput = document.getElementById('supplierQuotationAttachmentFile');
    var fileName = $('#supplierQuotationAttachmentFileName').val();
    var supplierQuotationNo = $('#supplierQuotationNo').val();

    // Check if both file and supplier quotation number are provided
    if (fileInput && (fileInput.files.length > 0 || fileName) && supplierQuotationNo) {
      // Both file and supplier quotation number are provided, check if PO exists for this RFP
      blockArea($('body')); // Optional: Show loading state while checking
      $.ajax({
        url: '/ajax/consultation/check_po_exists.php', // Backend script to check PO existence
        type: 'POST',
        data: { rfpID: '<?=$rfpID ?>' }, // Send the rfpID to check
        dataType: 'json',
        success: function(response) {
          unBlockArea($('body')); // Hide loading state
          if (response.status === 'success') {
            if (response.poExists && response.poId) {
              // PO exists, navigate to the edit page for that PO
              const baseUrl = window.location.origin;
              const editUrl = baseUrl + '/consultation/purchaseorder/edit/' + response.poId;
              window.open(editUrl, '_blank');
            } else {
              // No PO exists, navigate to the new PO creation page
              const baseUrl = window.location.origin;
              const relativeUrl = '/consultation/purchaseorder/new?rfpID=' + '<?=$rfpID ?>';
              const fullUrl = baseUrl + relativeUrl;
              window.open(fullUrl, '_blank');
            }
          } else {
            // Error in checking PO existence
            showErrorMessage('Failed to check Purchase Order existence: ' + response.message);
          }
        },
        error: function(xhr, status, error) {
          unBlockArea($('body')); // Hide loading state
          showErrorMessage('Error checking Purchase Order existence. Please try again.');
          console.error('Error:', error);
        }
      });
    } else {
      // Show error message if either file or supplier quotation number is missing
      let errorMessage = 'Quotation must be attached and a Supplier Quotation Number must be provided before creating a Purchase Order.';
      if (!fileInput || (!fileInput.files.length > 0 && !fileName)) {
        errorMessage = 'Quotation must be attached. Please attach the file before creating a Purchase Order.';
      } else if (!supplierQuotationNo) {
        errorMessage = 'A Supplier Quotation Number must be provided before creating a Purchase Order.';
      }
      showErrorMessage(errorMessage);
    }
  }

  function prepareRFPData() {
    console.log($('.source-item').repeaterVal());
    let repeaterData = $('.source-item').repeaterVal(); // Get the repeater data
    // Add additional data
    let requestData = {
      lineItems: repeaterData,
      rfpID: $('#rfpID').val(),
      supplierID: $('#supplier').val(), // Assuming this is the ID of the customer input field
      rfpDate: $('#rfpDate').val(), // Assuming this is the ID of the quotation date field
      paymentTerms: $('#paymentTerms').val(),
      supplierCurrency: $('#supplierCurrency').val(),
      refDocID: $('#refDocID').val(),
      reasonForRFP: $('#reasonForRFP').val(),
      supplierQuotationAttachmentFileName: $('#supplierQuotationAttachmentFileName').val(),
      supplierQuotationNo: $('#supplierQuotationNo').val(),
      etaDate: $('#etaDate').val(),
      carrier: $('#carrier').val(),
      tracking: $('#tracking').val(),
      shippingCost: $('#shippingCost').val()
    };
    console.log('requestData');
    console.log(requestData);

    return requestData;
  }


  //Save RFP
  function saveRFP() {

    if ($('#supplier').val().length > 0 ) {
      blockArea($('body'));
      // let requestData = prepareRFPData();

      // Check if a file is attached
      let fileInput = document.getElementById('supplierQuotationAttachmentFile');
      if (fileInput && fileInput.files.length > 0) {
        // File is attached, upload it first
        let file = fileInput.files[0];
        let formData = new FormData();
        formData.append('supplierQuotationAttachmentFile', file);

        $.ajax({
          url: '/ajax/consultation/rfp_file_upload.php', // Replace with your file upload endpoint
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
              // Proceed with saving RFP
              $('#supplierQuotationAttachmentFileName').val(response.message);
              console.log('File attached');
              let requestData = prepareRFPData();
              // Proceed with saving PO after file upload
              finalizeSaveRFP(requestData);

            } else {
              console.error('File Upload Error:', response.message);
              showErrorMessage('File upload failed. ' + response.message);
              unBlockArea($('body'));
            }
          },
          error: function(xhr, status, error) {
            console.error('File Upload Error:', error);
            showErrorMessage('File upload failed. Please try again.');
            unBlockArea($('body'));
          }
        });
      } else {
        // No file attached, proceed with saving PO normally
        let requestData = prepareRFPData();
        finalizeSaveRFP(requestData);
      }
    } else {
      showErrorMessage('Please choose Supplier and Destination Warehouse');
    }
  }

  function finalizeSaveRFP(requestData) {
    $.ajax({
      url: '/ajax/consultation/save_rfp.php', // Update with your PHP script URL
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
          console.log(response.message);
          console.log(response.documentId);
          unBlockArea($('body'));
          let documentId = response.documentId;
          showSuccessMessage(response.message, gotoPage, '/consultation/rfp/edit/' + documentId);
        } else {
          console.error('Error:', response.message);
          showErrorMessage(`Error: ${response.message}`);
        }
      },
      error: function(xhr, status, error) {
        console.error('Error:', error);
        showErrorMessage('Failed to save RFP. Please try again.');
      }
    });
  }

  function sendForApproval() {
    saveRFPThread('<?=RFP_STATUS_PROCUREMENT_MANAGER_APPROVAL ?>');
  }

  function approveRFP() {
    let documentID = '<?=$rfpID; ?>';
    updateRFPStatus(documentID, "<?=RFP_STATUS_PROCUREMENT_MANAGER_APPROVED ?>");
  }

  function rejectRFP() {
    let documentID = '<?=$rfpID; ?>';

    let rejectReason = $('#rejectReason').val();
    console.log(rejectReason);
    if (rejectReason.length) {
      updateRFPStatus(documentID, "<?=RFP_STATUS_PROCUREMENT_MANAGER_REJECTED ?>", true, rejectReason);
    } else {
      showErrorMessage('You must enter a reason for rejection');
    }
  }

  function saveRFPThread(status) {

    //Then proceed with saving the quotaion.
    if ($('#supplier').val().length > 0 ) {
      // blockArea($('body'));
      let requestData = prepareRFPData();
      $.ajax({
        url: '/ajax/consultation/save_rfp.php', // Update with your PHP script URL
        method: 'POST',
        data: JSON.stringify(requestData), // Send the combined data as a JSON string
        contentType: 'application/json', // Indicate that the data is JSON
        dataType: 'json', // Expect a JSON response
        success: function(response) {
          console.log('Server Response after saving RFP:', response);

          if (typeof response === 'string') {
            response = JSON.parse(response);
          }

          console.log('Response: ' + response.status);
          // Check the response status
          if (response.status === 'success') {
            unBlockArea($('body'));
            let documentID = response.documentId;
            updateRFPStatus(documentID, status);
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

  function updateRFPStatus(documentID, status, showMessage = true, rejectReason = '') {

    console.log('Updating RFP Status: ' + documentID + ' - ' + status + ' - ' + showMessage + ' - ' + rejectReason);

    console.log(rejectReason);
    var formData = new FormData();
    formData.append('documentID', documentID);
    formData.append('status', status);
    formData.append('rejectReason', rejectReason);

    console.log(formData);


    $.ajax({
      url: '/ajax/consultation/update_rfp_status.php',
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
            showSuccessMessage(message, gotoPage, '/consultation/rfp/edit/' + documentID);
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
    console.log('Service Item Changed');
    const selectedData = e.params.data;

    try {

      const selectedServiceID = selectedData.id;
      const selectedtext = selectedData.text;
      const selectedDesc = selectedData.desc;
      const selectedUOMID = selectedData.uomid;

      setTimeout(function() {

        var lastIndex = $('.source-item [data-repeater-item]').length - 1;
        console.log('Last Added Index:', lastIndex);

        const index = lastIndex;

        const serviceItemID = getElementByIndexAndName(index, 'serviceItemID');
        serviceItemID.val(selectedServiceID);

        const serviceItemName = getElementByIndexAndName(index, 'serviceItemName');
        serviceItemName.val(selectedtext);

        const servicedescription = getElementByIndexAndName(index, 'servicedescription');
        servicedescription.val(selectedDesc);

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
      placeholder: 'Select a Service/Product',
      minimumInputLength: 3,
      ajax: {
        url: '/ajax/consultation/get_service_items.php',
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