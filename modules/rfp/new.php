<?php
$PAGE_ID = "RFP_NEW";
include_once __DIR__ . "/../../includes/baseIncludes.php";
include_once __DIR__ . "/../../includes/auth_check.php";

$refDocID = '';
$reasonForRFP = "MANUAL";
if(isset($_GET['refDocID']) && !empty($_GET['refDocID'])){
    $refDocID = filter_var($_GET['refDocID'], FILTER_SANITIZE_SPECIAL_CHARS);
    $reasonForRFP = "SALESORDER";
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
  <title>GrandMaster ERP | RFP</title>
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
                  <h4 class="my-0">New Request for Proposal</h4>
                </div>
              </div>
            </div>
          </div>
          <div class="row invoice-add">
            <!-- Invoice Add-->
            <div class="col-lg-12 col-12 mb-lg-0 mb-6">
              <div class="card invoice-preview-card p-sm-6 p-6">
                <div class="card-body px-0">
                  <div class="row">
                    <div class="col-md-5 col-sm-5 col-12 mb-sm-0 mb-6">
                      <div class="mb-4">
                        <label class="form-label" for="customerCountry">Supplier:</label>
                        <select name="supplier" id="supplier" class="form-select mb-4 w-50 selectpicker w-100" data-style="btn-default" data-live-search="true" tabindex="null">
                          <option value="">Select supplier</option>
                            <?php
                            $res = $db->query("SELECT * FROM suppliers WHERE status ='Active'");
                            while ($row = mysqli_fetch_assoc($res)) {
                                ?>
                              <option value="<?= $row['supplierId'] ?>"><?= $row['companyName'] . " | " . $row['companyNameAr'] ?></option>
                                <?php
                            }
                            ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-5 col-sm-5 col-12 mb-sm-0 mb-6">
                      <div class="mb-4">
                        <label class="form-label" for="destinationWH">Destination WH:</label>
                        <select name="destinationWH" id="destinationWH" class="form-select mb-4 w-50 selectpicker w-100" data-style="btn-default" data-live-search="true" tabindex="null">
                          <option value="">Select Warehouse</option>
                            <?php
                            $res = $db->query("SELECT * FROM warehouses WHERE active ='1'");
                            while ($row = mysqli_fetch_assoc($res)) {
                                ?>
                              <option value="<?= $row['warehouseId'] ?>"><?= $row['warehouseName'] . " | " . $row['warehouseCode'] ?></option>
                                <?php
                            }
                            ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-2 col-sm-2 col-12">
                      <div class="mt-6 d-flex gap-2 justify-content-end">
                        <button class="btn btn-primary mb-4" onclick="saveRFP()">
                          <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-device-floppy ti-xs me-2"></i>Save</span>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
                <!--Grey Header Section-->
                <div class="card-body invoice-preview-header rounded">

                  <!-- Supplier and Warehouse Details Row -->
                  <div class="row" style="display:none" id="rowHeader">
                    <!-- Supplier Details (Left) -->
                    <div class="col-md-6 col-12 mb-4">
                      <div class="svg-illustration mb-6 gap-2 align-items-center">
                        <div>
                          <span class="app-brand-text fw-bold fs-4 ms-50" id="companyName"></span>
                        </div>
                        <div>
                          <span class="app-brand-text fw-bold fs-5 ms-50" id="companyNameAR"></span>
                        </div>
                      </div>
                      <p class="mb-2" id="customerAddressLine1"></p>
                      <p class="mb-2" id="customerAddressLine2"></p>
                      <p class="mb-2" id="customerCityPostalCode"></p>
                      <p class="mb-2" id="customerStateCountry"></p>
                      <p class="mb-2" id="customerVAT"></p>
                      <p class="mb-2" id="customerCR"></p>
                    </div>
                    <!-- Warehouse Details (Right) -->
                    <div class="col-md-6 col-12 mb-4 text-md-end d-flex flex-column align-items-md-end">
                      <div class="svg-illustration mb-4 gap-2 align-items-center">
                        <div>
                          <span class="app-brand-text fw-bold fs-4 ms-50" id="warehouseName"></span>
                        </div>
                        <div>
                          <span class="app-brand-text fw-bold fs-5 ms-50" id="warehouseCode"></span>
                        </div>
                      </div>
                      <p class="mb-2" id="warehouseAddressLine1"></p>
                      <p class="mb-2" id="warehouseAddressLine2"></p>
                      <p class="mb-2" id="warehouseCityPostalCode"></p>
                      <p class="mb-2" id="warehouseStateCountry"></p>
                      <p class="mb-2" id="warehouseContact"></p>
                      <p class="mb-2" id="warehousePhone"></p>
                    </div>
                  </div>
                   <hr id="initialDisplayNone" style="height: 0.5px; background-color: #2c3539; border: none; display: none">

                  <!-- Input Fields in Two Rows -->
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
                            <input name="rfpNumber" id="rfpNumber" type="text" class="form-control" disabled placeholder="Will be generated once saved / confirmed" value="NEW"/>
                          </div>
                          <input name="rfpID" id="rfpID" type="hidden" value=""/>
                        </dd>
                        <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Date Created:</span>
                        </dt>
                        <dd class="col-md-8 col-sm-7">
                          <input name="rfpDate" id="rfpDate" type="text" class="form-control gmm-date-format" placeholder="DD - MMM - YYYY" value="<?php echo date('Y-m-d'); ?>"/>
                        </dd>
                        <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Supplier Currency:</span>
                        </dt>
                        <dd class="col-md-8 col-sm-7">
                          <select name="supplierCurrency" id="supplierCurrency" class="form-control mt">
                            <option disabled selected value="">Select Currency</option>
                              <?php
                              $res = $db->query("SELECT * FROM currencies WHERE active = 1");
                              while ($row = mysqli_fetch_assoc($res)) {
                                  ?>
                                <option value="<?= $row['currencyId'] ?>"><?= $row['currency'] ?></option> <?php
                              }
                              ?>
                          </select>
                        </dd>
                        <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Supplier Quotation#:</span>
                        </dt>
                        <dd class="col-md-8 col-sm-7">
                          <input type="text" id="supplierQuotationNo" class="form-control due-date" value=""/>
                        </dd>
                        <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Quotation Attachment:</span>
                        </dt>
                        <dd class="col-md-8 col-sm-7">
                          <input class="form-control" type="file" id="supplierQuotationAttachmentFile" name="supplierQuotationAttachmentFile">
                          <input class="form-control" type="hidden" id="supplierQuotationAttachmentFileName" name="supplierQuotationAttachmentFileName" value="">
                        </dd>
                        <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Attachment:</span>
                        </dt>
                        <dd class="col-md-8 col-sm-7">
                            <?php
                            if (!empty($rfpDATA['supplierQuotationAttachment'])) {
                                ?>
                              <a class="btn btn-info" href="/uploads/<?= $rfpDATA['supplierQuotationAttachment'] ?>" target="_blank">View Supplier Quotation</a>
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
                          <input type="text" readonly class="form-control due-date" value="<?= getDisplayNameOfCurrentUser() ?>"/>
                        </dd>
                        <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Payment Terms:</span>
                        </dt>
                        <dd class="col-md-8 col-sm-7">
                          <select name="paymentTerms" id="paymentTerms" class="form-control mt">
                            <option disabled selected value="">Select Payment Term</option>
                              <?php
                              $res = $db->query("SELECT * FROM payment_terms WHERE active = 1");
                              while ($row = mysqli_fetch_assoc($res)) {
                                  ?>
                                <option value="<?= $row['termId'] ?>"><?= $row['termName'] ?></option> <?php
                              }
                              ?>
                          </select>
                        </dd>
                        <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Reason for RFP:</span>
                        </dt>
                        <dd class="col-md-8 col-sm-7">
                          <input type="text" id="reasonForRFP" readonly class="form-control" value="<?= $reasonForRFP ?>"/>
                        </dd>
                        <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Sales Order #:</span>
                        </dt>
                        <dd class="col-md-8 col-sm-7">
                          <input type="text" class="form-control" readonly value="<?php if(!empty($refDocID) && $refDocID <> '0') echo getSalesOrderNumberFromDocumentID($refDocID)?>"/>
                          <input type="hidden"  id="refDocID" class="form-control" value="<?= $refDocID?>"/>
                        </dd>
                        <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">ETA:</span>
                        </dt>
                        <dd class="col-md-8 col-sm-7">
                          <input name="etaDate" id="etaDate" type="text" class="form-control gmm-date-format" placeholder="DD - MMM - YYYY" value="<?php echo date('Y-m-d'); ?>"/>
                        </dd>

                        <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Carrier:</span>
                        </dt>
                        <dd class="col-md-8 col-sm-7">
                          <input type="text" id="carrier"  class="form-control due-date" value=""/>
                        </dd>
                        <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                          <span class="fw-normal">Tracking Number:</span>
                        </dt>
                        <dd class="col-md-8 col-sm-7">
                          <input type="text" id="tracking" class="form-control due-date" value=""/>
                        </dd>
<!--                        <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">-->
<!--                          <span class="fw-normal">Shipping Cost:</span>-->
<!--                        </dt>-->
<!--                        <dd class="col-md-8 col-sm-7">-->
<!--                          <input type="text" id="shippingCost" class="form-control due-date" value=""/>-->
<!--                        </dd>-->
                      </dl>
                    </div>
                  </div>
                </div>
                <!--Grey Header Section Ends-->
                <hr class="mt-5 mb-6"/>
                <div class="row">
                  <div class="col-md-5 col-sm-5 col-12 mb-sm-0 mb-6">
                    <div class="mb-4">
                      <label class="form-label" for="customerCountry">Add Parts:</label>
                      <select name="sparepartitem" class="sparepartitem form-control mb-5" data-live-search="true">
                        <option value="">Select a part number</option>
                      </select>
                    </div>
                  </div>
                </div>
                <hr class="mt-0 mb-6"/>
                <div class="card-body pt-0 px-0">
                  <div class="row" id="noSparePartsInitialDiv">
                    <div class="col-12 text-center">
                      Add spareparts from the above search box
                    </div>
                  </div>
                  <form class="source-item">
                    <div class="mb-4" data-repeater-list="line-item">
                      <div class="repeater-wrapper pt-0 pt-md-6" data-repeater-item style="display: none">
                        <div class="d-flex border rounded position-relative pe-0">
                          <div class="row w-100 p-3">
                            <div class="col-md-2 col-12 mb-md-0 mb-4">
                              <p class="h6 mb-1 itemNumber">Item #1</p>
                              <input name="sparepartinternalrefnumber" type="text" class="form-control mb-5" readonly/>
                              <input name="sparepartID" type="hidden" class="form-control mb-5" readonly/>
                            </div>
                            <div class="col-md-2 col-12 mb-md-0 mb-4">
                              <p class="h6 mb-1">Description</p>
                              <input name="sparepartdescription" type="text" class="form-control mb-5" readonly/>
                            </div>
                            <div class="col-md-1 col-12 mb-md-0 mb-4">
                              <p class="h6 mb-1">Qty</p>
                              <input name="qty" type="text" class="form-control numbers-only calculation-trigger" placeholder="Qty" min="1" required/>
                            </div>
                            <div class="col-md-1 col-12 mb-md-0 mb-4">
                              <p class="h6 mb-1">UOM</p>
                              <select name="uom" class="form-control mb-6 calculation-trigger" data-placeholder="Select UOM">
                                <option disabled value="">Select UOM</option>
                                  <?php
                                  $res = $db->query("SELECT * FROM uoms WHERE active = 1");

                                  while ($row = mysqli_fetch_assoc($res)) {
                                      ?>
                                    <option value="<?= $row['uomId'] ?>" data-type="<?= $row['type'] ?>" data-ratio="<?= $row['ratio'] ?>"><?= $row['uomName'] ?></option>
                                      <?php
                                  }
                                  ?>
                              </select>
                            </div>
                            <div class="col-md-1 col-12 mb-md-0 mb-4">
                            <p class="h6 mb-1">Cost</p>
                            <input name="cost" type="text" class="form-control numbers-only calculation-trigger" placeholder="Cost" min="1" required value=""/>
                          </div>
                          <div class="col-md-1 col-12 mb-md-0 mb-4">
                            <p class="h6 mb-1">HS Code</p>
                            <input name="hsCode" type="text" class="form-control" placeholder="HS Code" min="1" required value=""/>
                          </div>
                          <div class="col-md-1 col-12 mb-md-0 mb-4">
                            <p class="h6 mb-1">Aisle</p>
                            <input name="aisle" type="text" class="form-control" placeholder="Aisle" min="1"  value=""/>
                          </div>
                          <div class="col-md-1 col-12 mb-md-0 mb-4">
                            <p class="h6 mb-1">Bin</p>
                            <input name="bin" type="text" class="form-control" placeholder="Bin" min="1"  value=""/>
                          </div>
                          <div class="col-md-1 col-12 mb-md-0 mb-4">
                            <p class="h6 mb-1">Lot#</p>
                            <input name="lot" type="text" class="form-control" placeholder="Lot" min="1"  value=""/>
                          </div>
                          <div class="col-md-1 col-12 mb-md-0 mb-4">
                            <p class="h6 mb-1">Sub Total</p>
                            <input name="subTotal" type="text" class="form-control numbers-only calculation-trigger" placeholder="Total" min="1" value="" readonly/>
                          </div>
                          </div>
                          <div class="row p-3 justify-content-end"></div>
                          <div class="d-flex flex-column align-items-center justify-content-between border-start p-2">
                            <i class="ti ti-x ti-lg cursor-pointer" data-repeater-delete></i>
                          </div>
                        </div>
                      </div>
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
                          <span id="totalAmountSpan" class="fw-medium text-heading">SAR 0.00</span>
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
<?php include_once __DIR__ . "/../../includes/dashboard/dashboard_footer_scripts.php"; ?>
<script>
  let sparepartsAutoCompleteData = [];

  $(document).ready(function (e) {

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

          $("#rowHeader").show();
          $("#initialDisplayNone").show();
        },

        error: function (error) {
          unBlockArea($('.invoice-preview-header'));
          console.log(error);
        }
      });

    });
    $(document).on("change", "#destinationWH", function (e) {

      blockArea($('.invoice-preview-header'));
      var formData = new FormData();
      formData.append("warehouseID", $(this).val());

      $.ajax({
        url: '/ajax/warehouses/get_warehouse_detail.php',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        // dataType: 'json', // Expect JSON response
        success: function (data, status) {
          console.log(data);
          unBlockArea($('.invoice-preview-header'));

          $("#warehouseName").html(data.warehouseName);
          $("#warehouseCode").html(data.warehouseCode);
          $("#warehouseAddressLine1").html(data.addressLine1 === 'null' ? "" : data.addressLine1);
          $("#warehouseAddressLine2").html(data.addressLine2 === 'null' ? "" : data.addressLine2);
          $("#warehouseCityPostalCode").html((data.city ? data.city + ", " : "") + " " + (data.postalCode || ""));
          $("#warehouseStateCountry").html((data.state ? data.state + ", " : "") + data.country ? data.country : "");
          $("#warehousePhone").html("Phone: " + data.phone ? data.phone : "");
          $("#warehouseContact").html("Email: " + data.email ? data.email : "");

          $("#rowHeader").show();
          $("#initialDisplayNone").show();
        },

        error: function (error) {
          unBlockArea($('.invoice-preview-header'));
          console.log(error);
        }
      });

    });
  });

  function prepareRFPData() {
    console.log($('.source-item').repeaterVal());
    let repeaterData = $('.source-item').repeaterVal(); // Get the repeater data
    // Add additional data
    let requestData = {
      lineItems: repeaterData,
      rfpID: $("#rfpID").val(),
      supplierID: $('#supplier').val(), // Assuming this is the ID of the customer input field
      warehouseID: $('#destinationWH').val(),
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

    console.log(requestData);
    return requestData;
  }


  //Save New Quotation
  function saveRFP() {

    if ($('#supplier').val().length > 0 && $('#destinationWH').val().length > 0) {
      blockArea($('body'));
      let requestData = prepareRFPData();

      // Check if a file is attached
      let fileInput = document.getElementById('supplierQuotationAttachmentFile');
      if (fileInput.files.length > 0) {
        // File is attached, upload it first
        let file = fileInput.files[0];
        let formData = new FormData();
        formData.append('supplierQuotationAttachmentFile', file);

        $.ajax({
          url: '/ajax/rfpdocument/file_upload.php', // Replace with your file upload endpoint
          type: 'POST',
          data: formData,
          processData: false,
          contentType: false,
          success: function (response) {
            if (typeof response === 'string') {
              response = JSON.parse(response);
            }
            if (response.status === "success") {
              $("#supplierQuotationAttachmentFileName").val(response.message); // Store file name

              // Proceed with saving PO after file upload
              finalizeSaveRFP(requestData);

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
        finalizeSaveRFP(requestData);
      }
    } else {
      showErrorMessage("Please choose Supplier and Destination Warehouse");
    }
  }

  function finalizeSaveRFP(requestData) {
    $.ajax({
      url: '/ajax/rfpdocument/save_rfp.php', // Update with your PHP script URL
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
          showSuccessMessage(response.message, gotoPage, "/rfp/edit/" + documentId);
        } else {
          console.error('Error:', response.message);
          showErrorMessage(`Error: ${response.message}`);
        }
      },
      error: function (xhr, status, error) {
        console.error('Error:', error);
        showErrorMessage("Failed to save RFP. Please try again.");
      }
    });
  }

  // function saveQuotationThread() {
  //
  //   //Then proceed with saving the quotaion.
  //   if ($('#customer').val().length > 0) {
  //     // blockArea($('body'));
  //     let requestData = prepareQuotationData();
  //     $.ajax({
  //       url: '/ajax/documents/save_quotation.php', // Update with your PHP script URL
  //       method: 'POST',
  //       data: JSON.stringify(requestData), // Send the combined data as a JSON string
  //       contentType: 'application/json', // Indicate that the data is JSON
  //       dataType: 'json', // Expect a JSON response
  //       success: function (response) {
  //         console.log('Server Response:', response);
  //
  //
  //         if (typeof response === 'string') {
  //           response = JSON.parse(response);
  //         }
  //
  //         // Check the response status
  //         if (response.status === 'success') {
  //
  //           unBlockArea($('body'));
  //
  //           let documentID = response.documentId;
  //
  //           updateQuotationStatus(documentID);
  //
  //         } else {
  //           console.error('Error:', response.message);
  //           alert(`Error: ${response.message}`);
  //         }
  //
  //       },
  //       error: function (xhr, status, error) {
  //         console.error('Error:', error);
  //       }
  //     });
  //   } else {
  //     Swal.fire({
  //       title: 'Error!',
  //       icon: 'error',
  //       text: "Nothing to save.",
  //       type: 'error',
  //       customClass: {
  //         confirmButton: 'btn btn-primary'
  //       },
  //       showClass: {
  //         popup: 'animate__animated animate__shakeX'
  //       },
  //       buttonsStyling: false
  //     });
  //   }
  //
  //
  // }
  //
  // function sendForApproval() {
  //
  //   let customerPO = $("#customerPO").val();
  //
  //   if (customerPO.length > 3) {
  //
  //     // Get the file input element
  //     var fileInput = document.getElementById('poAttachmentFile');
  //     if (fileInput.files.length === 0) {
  //       saveQuotationThread();
  //     } else {
  //       //Do the upload
  //       var file = fileInput.files[0];
  //
  //       var formData = new FormData();
  //       formData.append('poAttachmentFile', file); // Add the file to FormData
  //
  //       // Make the AJAX request
  //       $.ajax({
  //         url: '/ajax/file_upload.php', // Replace with your PHP file handling URL
  //         type: 'POST',
  //         data: formData,
  //         processData: false, // Don't process the data
  //         contentType: false, // Don't set content type
  //         success: function (response) {
  //           if (typeof response === 'string') {
  //             response = JSON.parse(response);
  //           }
  //           if (response.status === "success") {
  //             $("#poAttachmentFileName").val(response.message);
  //             saveQuotationThread();
  //           }
  //
  //           console.log(response); // Server response
  //
  //         },
  //         error: function (xhr, status, error) {
  //           console.error(error);
  //         }
  //       });
  //     }
  //
  //     //Then proceed with saving the quotaion.
  //     if ($('#customer').val().length > 0) {
  //       // blockArea($('body'));
  //       let requestData = prepareQuotationData();
  //       $.ajax({
  //         url: '/ajax/documents/save_quotation.php', // Update with your PHP script URL
  //         method: 'POST',
  //         data: JSON.stringify(requestData), // Send the combined data as a JSON string
  //         contentType: 'application/json', // Indicate that the data is JSON
  //         dataType: 'json', // Expect a JSON response
  //         success: function (response) {
  //           console.log('Server Response:', response);
  //
  //
  //           if (typeof response === 'string') {
  //             response = JSON.parse(response);
  //           }
  //
  //           // Check the response status
  //           if (response.status === 'success') {
  //
  //             unBlockArea($('body'));
  //
  //             let documentID = response.documentId;
  //
  //             updateQuotationStatus(documentID);
  //
  //           } else {
  //             console.error('Error:', response.message);
  //             alert(`Error: ${response.message}`);
  //           }
  //
  //         },
  //         error: function (xhr, status, error) {
  //           console.error('Error:', error);
  //         }
  //       });
  //     } else {
  //       Swal.fire({
  //         title: 'Error!',
  //         icon: 'error',
  //         text: "Nothing to save.",
  //         type: 'error',
  //         customClass: {
  //           confirmButton: 'btn btn-primary'
  //         },
  //         showClass: {
  //           popup: 'animate__animated animate__shakeX'
  //         },
  //         buttonsStyling: false
  //       });
  //     }
  //
  //
  //     ////////////////////////////////////////////////////
  //
  //
  //   } else {
  //     Swal.fire({
  //       title: 'Customer PO Number not entered!',
  //       icon: 'error',
  //       text: "Customer PO Number is not entered.",
  //       type: 'error',
  //       customClass: {
  //         confirmButton: 'btn btn-primary'
  //       },
  //       showClass: {
  //         popup: 'animate__animated animate__shakeX'
  //       },
  //       buttonsStyling: false
  //     });
  //   }
  //
  // }
  //
  // function updateQuotationStatus(documentID) {
  //
  //
  //   var formData = new FormData();
  //   formData.append("documentID", documentID);
  //   formData.append("status", "AWAITING APPROVAL");
  //
  //   $.ajax({
  //     url: '/ajax/documents/update_quotation_status.php',
  //     type: 'POST',
  //     data: formData,
  //     contentType: false,
  //     processData: false,
  //     success: function (data, status) {
  //       console.log(data);
  //
  //       var statusmessage = data.trim().split("|")[0];
  //       var message = data.trim().split("|")[1];
  //
  //       if (statusmessage == "SUCCESS") {
  //         location.href = "/quotation/edit/" + documentID;
  //       }
  //
  //       if (statusmessage == "ERROR") {
  //
  //       }
  //     },
  //
  //     error: function (error) {
  //
  //       console.log(error);
  //     }
  //   });
  //
  // }

  // repeater (jquery)
  $(function () {
    var sourceItem = $('.source-item');

    // Repeater init
    if (sourceItem.length) {
      sourceItem.on('submit', function (e) {
        e.preventDefault();
      });

      sourceItem.repeater({
        initEmpty: true,
        ready: function (setIndexes) {
          initializeSelect2($('select[name="sparepartitem"]'), null);
          applyDatePicker();
          applySelectPicker();
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
    console.log(selectedData);

    try {

      const selectedSparepartID = selectedData.id;
      const selectedInternalReference = selectedData.internalReference;
      const selectedDesc = selectedData.desc;
      const selectedUOMID = selectedData.uomid;
      const selectedHSCode = selectedData.hscode;

      setTimeout(function () {

        var lastIndex = $('.source-item [data-repeater-item]').length - 1;
        console.log("Last Added Index:", lastIndex);

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

        const hsCode = getElementByIndexAndName(index, 'hsCode');
        hsCode.val(selectedHSCode);

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
    totalAmountSpan.html("SAR " + toTwoDecimal(totalAmount).toString());

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
</style>
</body>
</html>