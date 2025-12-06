<?php
$PAGE_ID = "PO_NEW";
include_once __DIR__ . "/../../includes/baseIncludes.php";
include_once __DIR__ . "/../../includes/auth_check.php";

$rfpID = '';
if(isset($_GET['rfpID']) && !empty($_GET['rfpID'])){
    $rfpID = filter_var($_GET['rfpID'], FILTER_SANITIZE_SPECIAL_CHARS);
} else {
    header("location:/purchaseorder/list");
    exit();
}

?>
<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
<head>
    <?php include_once __DIR__ . "/../../includes/dashboard/dashboard_head_section.php"; ?>
  <!-- Vendors CSS -->
  <link rel="stylesheet" href="../../assets/vendor/libs/node-waves/node-waves.css"/>
  <link rel="stylesheet" href="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css"/>
  <link rel="stylesheet" href="../../assets/vendor/libs/typeahead-js/typeahead.css"/>
  <link rel="stylesheet" href="../../assets/vendor/libs/flatpickr/flatpickr.css"/>
  <link rel="stylesheet" href="/assets/vendor/libs/bootstrap-select/bootstrap-select.css"/>
  <link rel="stylesheet" href="/assets/vendor/libs/animate-css/animate.css"/>
  <link rel="stylesheet" href="/assets/vendor/libs/sweetalert2/sweetalert2.css"/>
  <!-- Page CSS -->
  <link rel="stylesheet" href="../../assets/vendor/css/pages/app-invoice.css"/>
  <link rel="stylesheet" href="../../assets/vendor/css/jquery-ui.css"/>
  <link rel="stylesheet" href="../../assets/vendor/libs/select2/select2.css "/>
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
                  <h4 class="my-0">Create Purchase Order</h4>
                </div>
              </div>
            </div>
          </div>
            <?php

            $currentUserRole = getAuthenticatedUser()->getRoles()[0];

            $rfpDATA = "";
            $res     = $db->query("SELECT * FROM consultation_rfp_documents WHERE active = 1 and rfpId = ?s", $rfpID);
            while ($row = mysqli_fetch_assoc($res)) {
                $rfpDATA = $row;
            }

            $supplierID = $rfpDATA['supplierId'];
            $currencyID = $rfpDATA['currencyId'];

            $companyId = intval($_SESSION['SES_SELECTED_COMPANY']);

            ?>
          <div class="row invoice-add">
            <!-- Invoice Add-->
            <div class="col-lg-12 col-12 mb-lg-0 mb-6">
              <div class="card invoice-preview-card p-sm-6 p-6">
                <div class="card-body px-0">
                  <div class="row">
                    <div class="col-md-5 col-sm-5 col-12 mb-sm-0 mb-6" style="display: none">
                      <div class="mb-4">
                        <label class="form-label" for="customerCountry">Supplier:</label>
                        <select name="supplier" id="supplier" class="form-select mb-4 w-50 selectpicker w-100" data-style="btn-default" data-live-search="true" tabindex="null" disabled>
                          <option value="">Select supplier</option>
                            <?php
                            $res = $db->query("SELECT * FROM suppliers WHERE status ='Active' and companyId = $companyId");
                            while ($row = mysqli_fetch_assoc($res)) {
                                ?>
                              <option <?php if ($row['supplierId'] == $rfpDATA['supplierId']) echo "selected"; ?> value="<?= $row['supplierId'] ?>"><?= $row['companyName'] . " | " . $row['companyNameAr'] ?></option>
                                <?php
                            }
                            ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-5 col-sm-5 col-12 mb-sm-0 mb-6"  style="display: none">
                      <div class="mb-4">
                      </div>
                    </div>
                    <div class="col-md-12 col-sm-12 col-12">
                      <div class="mt-6 d-flex gap-2 justify-content-end">
                        <button class="btn btn-primary mb-4" onclick="savePO()">
                          <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-device-floppy ti-xs me-2"></i>Save</span>
                        </button>
                      </div>
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
                          <span class="app-brand-text fw-bold fs-4 ms-50" id="companyName"><?= $supplier->companyName ?></span>
                        </div>
                        <div>
                          <span class="app-brand-text fw-bold fs-5 ms-50" id="companyNameAR"><?= $supplier->companyNameAr ?></span>
                        </div>
                      </div>
                      <p class="mb-2" id="customerAddressLine1"><?= $supplier->addressLine1; ?></p>
                      <p class="mb-2" id="customerAddressLine2"><?= $supplier->addressLine2; ?></p>
                      <p class="mb-2" id="customerCityPostalCode"><?= getCityFromID($supplier->cityId) . ", " . $supplier->postalCode ?></p>
                      <p class="mb-2" id="customerStateCountry"><?= getStateFromID($supplier->stateId) . ", " . getCountryFromID($supplier->countryId) ?></p>
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
                    <div class="col-md-4 col-12 mb-4">
                      <dl class="row mb-0">
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="h5 text-capitalize mb-0 text-nowrap">PO</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <div class="input-group input-group-merge disabled">
                            <span class="input-group-text">#</span>
                            <input name="poNumber" id="poNumber" type="text" class="form-control" disabled placeholder="Will be generated once saved / confirmed" value="NEW"/>
                          </div>
                          <input name="poID" id="poID" type="hidden" value=""/>
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Date Created:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <input name="poDate" id="poDate" type="text" class="form-control gmm-date-format" placeholder="DD - MMM - YYYY" value="<?php echo date('Y-m-d'); ?>"/>
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">RFP #:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <input type="text" class="form-control" value="<?= getRFPNumberFromDocumentID($rfpID) ?>" readonly/>
                          <input type="hidden" id="rfpID" class="form-control" value="<?= $rfpDATA['rfpId'] ?>" readonly/>
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
                                <option <?php if ($rfpDATA['currencyId'] == $row['currencyId']) echo "SELECTED" ?> value="<?= $row['currencyId'] ?>"><?= $row['currency'] ?></option>
                                  <?php
                              }
                              ?>
                          </select>
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Supplier Quotation#:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <input type="text" id="supplierQuotationNo" class="form-control due-date" value="<?= $rfpDATA['supplierQuotationNumber'] ?>" readonly/>
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Attachment:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                            <?php
                            if (!empty($rfpDATA['supplierQuotationAttachment'])) {
                                ?>
                              <a class="btn btn-info" href="/uploads/<?= $rfpDATA['supplierQuotationAttachment'] ?>" target="_blank">Supplier Quotation</a>
                                <?php
                            }
                            ?>
                          <input class="form-control" type="hidden" id="supplierQuotationAttachmentFileName" name="supplierQuotationAttachmentFileName" value="<?= $rfpDATA['supplierQuotationAttachment'] ?>">
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Payment Ref Attachment:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <input class="form-control" type="file" id="paymentRefAttachmentFile" name="paymentRefAttachmentFile">
                          <input class="form-control" type="hidden" id="paymentRefAttachmentFileName" name="paymentRefAttachmentFileFileName" value="">
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Payment Ref #:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <input type="text" id="paymentRefNo" class="form-control due-date" value=""/>
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Payment Date:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <input name="paymentDate" id="paymentDate" type="text" class="form-control gmm-date-format" placeholder="DD - MMM - YYYY" value="<?php echo date('Y-m-d'); ?>"/>
                        </dd>
                      </dl>
                    </div>

                    <!-- Second Row of Input Fields -->
                    <div class="col-md-4 col-12 mb-4">
                      <dl class="row mb-0">
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Sales Person:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <input type="text" id ="salesPersonId" readonly class="form-control due-date" value="<?= getDisplayNameFromUserID($rfpDATA['salesPersonId']) ?>"/>
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
                                <option <?php if ($rfpDATA['paymentTermId'] == $row['termId']) echo "SELECTED" ?> value="<?= $row['termId'] ?>"><?= $row['termName'] ?></option>
                                  <?php
                              }
                              ?>
                          </select>
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Reason for PO:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <input type="text" id="rfpReason" readonly class="form-control due-date" value="<?= $rfpDATA['rfpReason'] ?>"/>
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Sales Order #:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <input type="text" class="form-control"
                                 value="<?php if (!empty($rfpDATA['saleOrderId']) || $rfpDATA['saleOrderId'] <> '0') echo getSalesOrderNumberFromDocumentID($rfpDATA['saleOrderId']) ?>"
                                 readonly />
                          <input type="hidden" id="refDocID" class="form-control"
                                 value="<?= $rfpDATA['saleOrderId'] ?>" readonly />
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">VAT Amount for PO:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <input type="text" id="vatAmount" class="form-control due-date"
                                 value="" />
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">ETA:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <input name="etaDate" id="etaDate" type="text" class="form-control gmm-date-format" placeholder="DD - MMM - YYYY" value="<?= $rfpDATA['etaDate'] ?>"/>
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
                                     value="">
                            </dd>
                            <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                              <span class="fw-normal">Supplier INV #:</span>
                            </dt>
                            <dd class="col-md-6 col-sm-7">
                              <input type="text" id="supplierInvoiceNo" class="form-control due-date"
                                     value="" />
                            </dd>
                              <?php
                          }
                          ?>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Payment Date:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <input name="supplierInvoiceDate" id="supplierInvoiceDate" type="text" class="form-control gmm-date-format" placeholder="DD - MMM - YYYY" value="<?php echo date('Y-m-d'); ?>"/>
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Carrier:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <input type="text" id="carrier"  class="form-control due-date" value="<?= $rfpDATA['shippingMethod'] ?>"/>
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Tracking Number:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <input type="text" id="tracking" class="form-control due-date" value="<?= $rfpDATA['trackingNumber'] ?>"/>
                        </dd>
<!--                        <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">-->
<!--                          <span class="fw-normal">Shipping Cost:</span>-->
<!--                        </dt>-->
<!--                        <dd class="col-md-8 col-sm-7">-->
<!--                          <input type="text" id="shippingCost" class="form-control due-date" value="--><?php //= $rfpDATA['shippingCost'] ?><!--"/>-->
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
                          <input type="text" id="taxes" class="form-control" value="" placeholder="SAR 0.00"/>
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Shipping Cost:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <input type="text" id="shippingCost" class="form-control" value="" placeholder="SAR 0.00"/>
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal"><strong>Cost Summary:</strong></span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal" style="display: none">Insurance:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <input type="text" id="insurance" class="form-control" value="" placeholder="SAR 0.00" style="display: none"/>
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal" style="display: none">Foreign Transaction Fee:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <input type="text" id="foreigntransactionfees" class="form-control" value=""
                                 placeholder="SAR 0.00" style="display: none"/>
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal" style="display: none">Custom Duties:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <input type="text" id="customduties" class="form-control" value="" placeholder="SAR 0.00" style="display: none"/>
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal" style="display: none">Surcharge:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <input type="text" id="surcharge" class="form-control" value="" placeholder="SAR 0.00" style="display: none"/>
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal" style="display: none">Other:</span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <input type="text" id="other" class="form-control" value="" placeholder="SAR 0.00" style="display: none"/>
                        </dd>
                        <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal"><strong>Subtotal:</strong></span>
                        </dt>
                        <dd class="col-md-6 col-sm-7">
                          <span id="shippingSubTotal" class="fw-medium"><?=getCurrencyFromID($currencyID)?> 0.00</span>
                        </dd>
                      </dl>
                    </div>
                  </div>

                  <!-- RFQ Number Row -->
<!--                  <div class="row">-->
<!--                    <div class="col-md-6 col-12 mb-4">-->
<!--                      <dl class="row mb-0">-->
<!--                        <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">-->
<!--                          <span class="fw-normal">RFQ #:</span>-->
<!--                        </dt>-->
<!--                        <dd class="col-md-8 col-sm-7">-->
<!--                          <input type="text" class="form-control" value="--><?php //= getRFPNumberFromDocumentID($rfpID) ?><!--" readonly/>-->
<!--                          <input type="hidden" id="rfpID" class="form-control" value="--><?php //= $rfpDATA['rfpId'] ?><!--" readonly/>-->
<!--                        </dd>-->
<!--                      </dl>-->
<!--                    </div>-->
<!--                  </div>-->

                </div>
                <!--Grey Header Section Ends-->
                <hr class="mt-5 mb-6"/>
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
                <!-- <hr class="mt-0 mb-6"/> -->
                <div class="card-body pt-0 px-0">
                  <div class="row" id="noSparePartsInitialDiv" style="display: none">
                    <div class="col-12 text-center">
                      Add spareparts from the above search box
                    </div>
                  </div>
                  <form class="source-item">
                    <div class="mb-4" data-repeater-list="line-item">

                        <?php
                        $itemCount   = 0;
                        $lineitemres = $db->query("SELECT * FROM consultation_rfp_line_items WHERE rfpId = ?s", $rfpID);

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
                              <div class="d-flex flex-column align-items-center justify-content-between border-start p-2">
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
                <hr class="my-0"/>
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
                <hr class="my-0"/>
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
          <!-- /Send Invoice Sidebar -->
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
<!-- / Layout wrapper -->
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
<?php include_once __DIR__ . "/../../includes/dashboard/dashboard_footer_scripts.php"; ?>
<script>
  let sparepartsAutoCompleteData = [];

  $(document).ready(function (e) {

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

    $(document).on("change", "#supplier", function (e) {

      blockArea($('.invoice-preview-header'));
      var formData = new FormData();
      formData.append("supplierID", $(this).val());

      $.ajax({
        url: '/ajax/supplier/get_supplier_detail.php',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        // dataType: 'json', // Expect JSON response
        success: function (data, status) {
          console.log(data);
          unBlockArea($('.invoice-preview-header'));

          $("#companyName").html(data.companyName);
          $("#companyNameAR").html(data.companyNameAr);
          $("#customerAddressLine1").html(data.addressLine1 === 'null' ? "" : data.addressLine1);
          $("#customerAddressLine2").html(data.addressLine2 === 'null' ? "" : data.addressLine2);
          $("#customerCityPostalCode").html((data.city ? data.city + ", " : "") + " " + (data.postalCode || ""));
          $("#customerStateCountry").html((data.state ? data.state + ", " : "") + data.country || "");
          $("#customerVAT").html("VAT: " + data.vatNumber);
          $("#customerCR").html("CR: " + (data.companyCRNumber || "-"));
          $("#paymentTerms").val(data.salesPaymentTermId);
          $("#supplierCurrency").val(data.currencyId);
        },

        error: function (error) {
          unBlockArea($('.invoice-preview-header'));
          console.log(error);
        }
      });

    });
  });

  function printPO() {
    // Get the base URL of the current page
    const baseUrl = window.location.origin;
    // Define the relative URL to open
    const relativeUrl = '/ajax/rfpdocument/generate_pdf.php?rfpID=' + '<?=$rfpID ?>';
    // Combine the base URL and relative URL
    const fullUrl = baseUrl + relativeUrl;
    // Open the URL in a new tab
    window.open(fullUrl, '_blank');

  }

  function savePO() {
    if ($('#supplier').val().length > 0 ) {
      blockArea($('body'));
      // let requestData = preparePOData();

      // Check if a file is attached
      let fileInput = document.getElementById('paymentRefAttachmentFile');
      if (fileInput.files.length > 0) {
        // File is attached, upload it first
        let file = fileInput.files[0];
        let formData = new FormData();
        formData.append('paymentRefAttachmentFile', file);

        $.ajax({
          url: '/ajax/consultation/po_file_upload.php', // Replace with your file upload endpoint
          type: 'POST',
          data: formData,
          processData: false,
          contentType: false,
          success: function (response) {
            if (typeof response === 'string') {
              response = JSON.parse(response);
            }
            if (response.status === "success") {
              $("#paymentRefAttachmentFileName").val(response.message); // Store file name
              console.log('File attached');
              let requestData = preparePOData();

              // Proceed with saving PO after file upload
              finalizeSavePO(requestData);

              // Call sendForSOApproval after saving PO
              // sendForSOApproval();
            } else {
              console.error('File Upload Error:', response.message);
              showErrorMessage("File upload failed. " + response.message);
              unBlockArea($('body'));
            }
          },
          error: function (xhr, status, error) {
            console.error('File Upload Error:', error);
            showErrorMessage("File upload failed. Please try again.");
            unBlockArea($('body'));
          }
        });
      } else {
        // No file attached, proceed with saving PO normally
        let requestData = preparePOData();
        finalizeSavePO(requestData);
      }
    } else {
      showErrorMessage("Please choose Supplier and Destination Warehouse");
    }
  }

  // Function to finalize saving the PO
  function finalizeSavePO(requestData) {
    $.ajax({
      url: '/ajax/consultation/save_po.php', // Update with your PHP script URL
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
          console.log(response.message);
          console.log(response.documentId);
          unBlockArea($('body'));
          let documentId = response.documentId;
          showSuccessMessage(response.message, gotoPage, "/consultation/purchaseorder/edit/" + documentId);
        } else {
          console.error('Error:', response.message);
          showErrorMessage(`Error: ${response.message}`);
        }
      },
      error: function (xhr, status, error) {
        console.error('Error:', error);
        showErrorMessage("Failed to save PO. Please try again.");
      }
    });
  }


  function preparePOData() {
    console.log($('.source-item').repeaterVal());
    let repeaterData = $('.source-item').repeaterVal(); // Get the repeater data
    // Add additional data
    let requestData = {
      lineItems: repeaterData,
      poID: $("#poID").val(),
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
      paymentRefNo: $('#paymentRefNo').val(),
      paymentRefAttachmentFileName: $('#paymentRefAttachmentFileName').val(),
      paymentDate: $('#paymentDate').val(),
      supplierInvoiceNo: $('#supplierInvoiceNo').val(),
      supplierInvoiceAttachmentFileName: $('#supplierInvoiceAttachmentFileName').val(),
      supplierInvoiceDate: $('#supplierInvoiceDate').val(),
      taxes: $('#taxes').val() || '0.00',
      shippingCost: $('#shippingCost').val() || '0.00',
      customduties: $('#customduties').val() || '0.00',
      foreigntransactionfees: $('#foreigntransactionfees').val() || '0.00',
      insurance: $('#insurance').val() || '0.00',
      surcharge: $('#surcharge').val() || '0.00',
      other: $('#other').val() || '0.00',
      shippingSubTotal: extractNumbers($('#shippingSubTotal').text()) || '0.00'
    };
    console.log("requestData");
    console.log(requestData);

    return requestData;
  }

  function extractNumbers(value) {
    if (!value) return value;
    // Extracts numbers (including decimals)
    return String(value).replace(/[^\d.-]/g, '');
  }


  // repeater (jquery)
  $(function () {
    var sourceItem = $('.source-item');

    // Repeater init
    if (sourceItem.length) {
      sourceItem.on('submit', function (e) {
        e.preventDefault();
      });

      sourceItem.repeater({
        ready: function (setIndexes) {
          initializeSelect2($('select[name="sparepartitem"]'), null);
          applyDatePicker();
          applySelectPicker();
          doCalculation();
        },

        show: function () {

          $(this).slideDown();
          updateItemNumbers();
          const index = $(this).index();

        },
        hide: function (remove) {
          $(this).slideUp(300, function () {
            remove(); // Ensures the element is removed
            doCalculation(); // Call after removal from DOM
          });
        }
      });
    }

    // Item details select onchange
    $(document).on('change', '.item-details', function () {
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
      item.textContent = "Item #" + (index + 1).toString();
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
  $(document).on('select2:select', '.sparepartitem', function (e) {

    $("#noSparePartsInitialDiv").hide();
    $("#repeaterAddItemButton").trigger('click');

    e.stopPropagation();
    console.log("Spare part Item Changed");
    const selectedData = e.params.data;

    try {

      const selectedSparepartID = selectedData.id;
      const serviceItemName = selectedData.internalReference;
      const selectedDesc = selectedData.desc;
      const selectedUOMID = selectedData.uomid;

      setTimeout(function () {

        var lastIndex = $('.source-item [data-repeater-item]').length - 1;
        console.log("Last Added Index:", lastIndex);

        const index = lastIndex;

        const sparepartID = getElementByIndexAndName(index, 'sparepartID');
        sparepartID.val(selectedSparepartID);

        const serviceItemName = getElementByIndexAndName(index, 'serviceItemName');
        serviceItemName.val(serviceItemName);

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


  $(document).on('change', '.calculation-trigger', function (e) {
    doCalculation();
  });


  function doCalculation() {
    const totalItemRows = getRepeaterRowCount();

    let rowSubTotal = 0;

    let totalAmount = 0.00;

    for (let index = 0; index < totalItemRows; index++) {

      let qty = parseFloat(getElementByIndexAndName(index, 'qty').val()) || 0;
      let cost = parseFloat(getElementByIndexAndName(index, 'cost').val()) || 0;
      let uomRatio = parseFloat(getElementByIndexAndName(index, 'uom').find(':selected').data("ratio")) || 1;


      // Calculate base subtotal
      let baseSubtotal = qty * uomRatio * cost;

      totalAmount += baseSubtotal;


      // Set values to respective fields
      getElementByIndexAndName(index, 'subTotal').val(toTwoDecimal(baseSubtotal));


    }

    const totalAmountSpan = $("#totalAmountSpan");
    totalAmountSpan.html("<?=getCurrencyFromID($currencyID)?> " + toTwoDecimal(totalAmount).toString());

  }


  // Initialize Select2 with AJAX
  function initializeSelect2(selectElement, preselectedValue = null) {
    console.log("preselectedValue: " + preselectedValue);

    console.log(selectElement);
    // Initialize Select2
    $(selectElement).select2({
      placeholder: "Select a part number",
      minimumInputLength: 3,
      ajax: {
        url: "/ajax/spareparts/get_spareparts.php",
        dataType: "json",
        delay: 250, // Delay AJAX requests to reduce load
        data: function (params) {
          return {q: params.term}; // Send search query to PHP
        },
        processResults: function (data) {
          return {results: data.results};
        },
        cache: true
      },
      templateResult: function (data) {
        if (!data.id) return data.text; // For placeholder
        return $(`<div>
                            <strong>${data.text}</strong><br/>
                            <small>${data.desc}</small>
                          </div>`
        );
      },
      templateSelection: function (data) {
        return data.text || "Select a part number";
      }
    });
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
</style>
</body>
</html>