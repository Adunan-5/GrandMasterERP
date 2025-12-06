<?php
$PAGE_ID = "QUOTATION_NEW";
include_once __DIR__ . "/../../includes/baseIncludes.php";
include_once __DIR__ . "/../../includes/auth_check.php";
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
    <title>GrandMaster ERP | Quotations</title>
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
                                    <h4 class="my-0">New Quotation</h4>
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
                                                <label class="form-label" for="customerCountry">Quotation For:</label>
                                                <select name="customer" id="customer" class="form-select mb-4 w-50 selectpicker w-100" data-style="btn-default" data-live-search="true" tabindex="null">
                                                    <option value="">Select Customer</option>
                                                    <?php
                                                    $companyId = intval($_SESSION['SES_SELECTED_COMPANY']);
                                                    $res = $db->query("SELECT * FROM customers WHERE status ='Active' AND companyId = $companyId");
                                                    while ($row = mysqli_fetch_assoc($res)) {
                                                        ?>
                                                        <option value="<?= $row['customerId'] ?>"><?= "GMM" . $row['customerCode'] . " | " . $row['companyName'] . " | " . $row['companyNameAr'] ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-7 col-sm-7">
                                            <div class="mt-6 d-flex gap-2 justify-content-end">
                                                <button class="btn btn-primary mb-4" onclick="saveQuotation()">
                                                    <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-device-floppy ti-xs me-2"></i>Save</span>
                                                </button>
                                                <!--                                                <button class="btn btn-primary mb-4" onclick="sendForApproval()">-->
                                                <!--                                                    <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-file-like ti-xs me-2"></i>Send for Approval</span>-->
                                                <!--                                                </button>-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--Grey Header Section-->
                                <div class="card-body invoice-preview-header rounded">
                                    <div class="d-flex flex-wrap flex-column flex-sm-row justify-content-between text-heading">
                                        <div class="mb-md-0 mb-6">
                                            <div class="svg-illustration mb-6 gap-2 align-items-center">
                                                <!--                                                <div class="app-brand-logo demo">-->
                                                <!--                                                    <img src="/assets/img/gmm-g-logo.png" alt="">-->
                                                <!--                                                </div>-->
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
                                            <table style="display: none">
                                                <tbody>
                                                <tr>
                                                    <td class="pe-4">Total Due:</td>
                                                    <td>$12,110.55</td>
                                                </tr>
                                                <tr>
                                                    <td class="pe-4">Bank name:</td>
                                                    <td>American Bank</td>
                                                </tr>
                                                <tr>
                                                    <td class="pe-4">Country:</td>
                                                    <td>United States</td>
                                                </tr>
                                                <tr>
                                                    <td class="pe-4">IBAN:</td>
                                                    <td>ETD95476213874685</td>
                                                </tr>
                                                <tr>
                                                    <td class="pe-4">SWIFT code:</td>
                                                    <td>BR91905</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-5 col-8 pe-0 ps-0 ps-md-2">
                                            <dl class="row mb-0">
                                                <dt class="col-sm-5 mb-2 d-md-flex align-items-center justify-content-end">
                                                    <span class="h5 text-capitalize mb-0 text-nowrap">Quotation</span>
                                                </dt>
                                                <dd class="col-sm-7">
                                                    <div class="input-group input-group-merge disabled">
                                                        <span class="input-group-text">#</span>
                                                        <input name="quotationNumber" id="quotationNumber" type="text" class="form-control" disabled placeholder="Will be generated once saved / confirmed" value="NEW"/>
                                                    </div>
                                                  <input name="documentId" id="documentId" type="hidden" value=""/>
                                                </dd>
                                                <dt class="col-sm-5 mb-2 d-md-flex align-items-center justify-content-end">
                                                    <span class="fw-normal">Date Issued:</span>
                                                </dt>
                                                <dd class="col-sm-7">
                                                    <input name="quotationDate" id="quotationDate" type="text" class="form-control gmm-date-format" placeholder="DD - MMM - YYYY" value="<?php echo date('Y-m-d'); ?>"/>
                                                </dd>
                                                <dt class="col-sm-5 d-md-flex align-items-center justify-content-end">
                                                    <span class="fw-normal">Expiration Date:</span>
                                                </dt>
                                                <dd class="col-sm-7">
                                                    <input name="quotationExpiryDate" id="quotationExpiryDate" type="text" class="form-control gmm-date-format" placeholder="DD - MMM - YYYY" value="<?= date('Y-m-d', strtotime('+30 days')) ?>"/>
                                                </dd>
                                                <dt class="col-sm-5 mb-2 d-md-flex align-items-center justify-content-end">
                                                    <span class="fw-normal">Sales Person:</span>
                                                </dt>
                                                <dd class="col-sm-7">
                                                    <input type="text" readonly class="form-control due-date" value="<?= getDisplayNameOfCurrentUser() ?>"/>
                                                </dd>
                                                <dt class="col-sm-5 mb-2 d-md-flex align-items-center justify-content-end">
                                                    <span class="fw-normal">Payment Terms:</span>
                                                </dt>
                                                <dd class="col-sm-7 mb-2">
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
                                                <!--                                                <dt class="col-sm-5 mb-2 d-md-flex align-items-center justify-content-end">-->
                                                <!--                                                    <span class="fw-normal">Customer P.O:</span>-->
                                                <!--                                                </dt>-->
                                                <!--                                                <dd class="col-sm-7">-->
                                                <!--                                                    <input type="text" class="form-control" id="customerPO" name="customerPO"/>-->
                                                <!--                                                </dd>-->
                                                <!--                                                <dt class="col-sm-5 mb-2 d-md-flex align-items-center justify-content-end">-->
                                                <!--                                                    <span class="fw-normal">P.O Attachment:</span>-->
                                                <!--                                                </dt>-->
                                                <!--                                                <dd class="col-sm-7">-->
                                                <!--                                                    <input class="form-control" type="file" id="poAttachmentFile" name="poAttachmentFile">-->
                                                <!--                                                    <input class="form-control" type="hidden" id="poAttachmentFileName" name="poAttachmentFileName">-->
                                                <!--                                                </dd>-->
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                                <!--Grey Header Section Ends-->
                                <hr class="mt-0 mb-6"/>
                                <div class="card-body pt-0 px-0">
                                    <form class="source-item">
                                        <div class="row " style="display: none">
                                            <div class="col-md-2">
                                                <p class="h6 mb-1">Item</p>
                                            </div>
                                            <div class="col-md-2">
                                                <p class="h6 mb-1">Description</p>
                                            </div>
                                            <div class="col-md-1">
                                                <p class="h6 mb-1">ETA</p>
                                            </div>
                                            <div class="col-md-1">
                                                <p class="h6 mb-1">Qty</p>
                                            </div>
                                            <div class="col-md-1">
                                                <p class="h6 mb-1">Unit Price</p>
                                            </div>
                                        </div>
                                        <div class="mb-4" data-repeater-list="line-item">
                                            <div class="repeater-wrapper pt-0 pt-md-6" data-repeater-item>
                                                <div class="d-flex border rounded position-relative pe-0">
                                                    <div class="row w-100 p-3">
                                                        <div class="col-md-2 col-12 mb-md-0 mb-4">
                                                            <p class="h6 mb-1 itemNumber">Item #1</p>
                                                            <!--   arafat                                                           <input name="sparepartitem" type="text" class="sparepartitem form-control mb-5" placeholder="Start typing..." onchange="onSparePartItemChanged(this)"/>-->
                                                            <select name="sparepartitem" class="sparepartitem form-control mb-5" data-live-search="true">
                                                                <option value="">Select a part number</option>
                                                                <?php
                                                                $selected = "";
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2 col-12 mb-md-0 mb-4">
                                                            <p class="h6 mb-1">Description</p>
                                                            <input name="sparepartdescription" type="text" class="form-control mb-5" readonly/>
                                                            <div class="text-heading" style="display: none">
                                                                <div class="mb-1">Discount:</div>
                                                                <span class="discount me-2">0%</span>
                                                                <span class="tax-1 me-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Tax 1">0%</span>
                                                                <span class="tax-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Tax 2">0%</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1 col-12 mb-md-0 mb-4">
                                                            <p class="h6 mb-1">Qty</p>
                                                            <input name="qty" type="text" class="form-control numbers-only calculation-trigger" placeholder="Qty" min="1" required/>
                                                        </div>
                                                        <div class="col-md-1 col-12 mb-md-0 mb-4">
                                                            <p class="h6 mb-1">ETA</p>
                                                            <!--                                                            <p class="mb-0 text-heading">$24.00</p>-->
                                                            <input name="eta" type="text" class="form-control gmmdatepicker-friendly" placeholder="Date"/>
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
                                                            <p class="h6 mb-1">Unit Price</p>
                                                            <input name="unitPrice" type="text" class="form-control numbers-only calculation-trigger" placeholder="" readonly/>
                                                        </div>
                                                        <div class="col-md-1 col-12 mb-md-0 mb-4">
                                                          <p class="h6 mb-1">Total</p>
                                                          <input name="total" type="text" class="form-control" placeholder="" min="1" readonly/>
                                                        </div>
                                                        <div class="col-md-1 col-12 mb-md-0 mb-4">
                                                            <p class="h6 mb-1">VAT %</p>
                                                            <input name="vatPercentage" type="text" class="form-control numbers-only calculation-trigger" placeholder="" readonly/>
                                                        </div>
                                                        <div class="col-md-1 col-12 mb-md-0 mb-4">
                                                            <p class="h6 mb-1">VAT Amt</p>
                                                            <input name="vatAmount" type="text" class="form-control" placeholder="" readonly/>
                                                        </div>
                                                        <div class="col-md-1 col-12 mb-md-0 mb-4">
                                                            <p class="h6 mb-1">HS Code</p>
                                                            <input name="hsCode" type="text" class="form-control" placeholder=""/>
                                                        </div>
                                                        <div class="col-md-1 col-12 mb-md-0 mb-4">
                                                            <p class="h6 mb-1">HS %</p>
                                                            <input name="hsPercentage" type="text" class="form-control numbers-only calculation-trigger" placeholder=""/>
                                                        </div>
                                                        <div class="col-md-1 col-12 mb-md-0 mb-4">
                                                            <p class="h6 mb-1">Discount %</p>
                                                            <input name="discountPercentage" type="text" class="form-control numbers-only calculation-trigger" placeholder=""/>
                                                        </div>
                                                        <div class="col-md-1 col-12 mb-md-0 mb-4">
                                                            <p class="h6 mb-1 text-nowrap">Discount Amt</p>
                                                            <input name="discountAmount" type="text" class="form-control" placeholder="" readonly/>
                                                        </div>
                                                        <div class="col-md-2 col-12 mb-md-0 mb-4">
                                                          <p class="h6 mb-1">Sub Total</p>
                                                          <input name="subTotal" type="text" class="form-control" placeholder="" min="1" readonly/>
                                                        </div>
                                                    </div>
                                                    <div class="row p-3 justify-content-end"></div>
                                                    <div class="d-flex flex-column align-items-center justify-content-between border-start p-2">
                                                        <i class="ti ti-x ti-lg cursor-pointer" data-repeater-delete></i>
                                                        <div class="dropdown" style="display: none">
                                                            <i class="ti ti-settings ti-lg cursor-pointer more-options-dropdown" role="button" id="dropdownMenuButton" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false"> </i>
                                                            <div class="dropdown-menu dropdown-menu-end w-px-300 p-4" aria-labelledby="dropdownMenuButton">
                                                                <div class="row g-3">
                                                                    <div class="col-12">
                                                                        <label for="discountInput" class="form-label">
                                                                            Discount(%)
                                                                        </label>
                                                                        <input type="number" class="form-control" id="discountInput" min="0" max="100"/>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label for="taxInput1" class="form-label">Tax 1
                                                                        </label>
                                                                        <select name="tax-1-input" id="taxInput1" class="form-select tax-select">
                                                                            <option value="0%" selected>0%</option>
                                                                            <option value="1%">1%</option>
                                                                            <option value="10%">10%</option>
                                                                            <option value="18%">18%</option>
                                                                            <option value="40%">40%</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label for="taxInput2" class="form-label">Tax 2
                                                                        </label>
                                                                        <select name="tax-2-input" id="taxInput2" class="form-select tax-select">
                                                                            <option value="0%" selected>0%</option>
                                                                            <option value="1%">1%</option>
                                                                            <option value="10%">10%</option>
                                                                            <option value="18%">18%</option>
                                                                            <option value="40%">40%</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="dropdown-divider my-4"></div>
                                                                <button type="button" class="btn btn-label-primary btn-apply-changes">
                                                                    Apply
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <button type="button" class="btn btn-sm btn-primary" data-repeater-create>
                                                    <i class="ti ti-plus ti-14px me-1_5"></i>Add Item
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <hr class="my-0"/>
                                <div class="card-body px-0">
                                    <div class="row row-gap-4">
                                        <div class="col-md-6 d-flex justify-content-start"></div>
                                        <div class="col-md-6 d-flex justify-content-end">
                                            <div class="invoice-calculations">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="px-5">Total Amount before VAT: </span>
                                                    <span id="totalAmountBeforeVATSpan" class="fw-medium text-heading">SAR 0.00</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="px-5">Total Discount Amount: </span>
                                                    <span id="totalDiscountAmountSpan" class="fw-medium text-heading">SAR 0.00</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="px-5">Total VAT Amount: </span>
                                                    <span id="totalVATAmountSpan" class="fw-medium text-heading">SAR 0.00</span>
                                                </div>
                                                <hr/>
                                                <div class="d-flex justify-content-between">
                                                    <span class="px-5">Total Amount After VAT: </span>
                                                    <span id="totalAmountAfterVATSpan" class="fw-medium text-heading">SAR 0.00</span>
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
                    <div class="offcanvas offcanvas-end" id="sendInvoiceOffcanvas" aria-hidden="true">
                        <div class="offcanvas-header mb-6 border-bottom">
                            <h5 class="offcanvas-title">Send Invoice</h5>
                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body pt-0 flex-grow-1">
                            <form>
                                <div class="mb-6">
                                    <label for="invoice-from" class="form-label">From</label>
                                    <input type="text" class="form-control" id="invoice-from" value="shelbyComapny@email.com" placeholder="company@email.com"/>
                                </div>
                                <div class="mb-6">
                                    <label for="invoice-to" class="form-label">To</label>
                                    <input type="text" class="form-control" id="invoice-to" value="qConsolidated@email.com" placeholder="company@email.com"/>
                                </div>
                                <div class="mb-6">
                                    <label for="invoice-subject" class="form-label">Subject</label>
                                    <input type="text" class="form-control" id="invoice-subject" value="Invoice of purchased Admin Templates" placeholder="Invoice regarding goods"/>
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


        $(document).on("change", "#customer", function (e) {

            blockArea($('.invoice-preview-header'));

            var formData = new FormData();
            formData.append("customerID", $(this).val());

            $.ajax({
                url: '/ajax/customers/get_customer_detail.php',
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
                },

                error: function (error) {
                    unBlockArea($('.invoice-preview-header'));
                    console.log(error);
                }
            });

        });
    });

    function prepareQuotationData() {
        console.log($('.source-item').repeaterVal());

        let repeaterData = $('.source-item').repeaterVal(); // Get the repeater data
        // Add additional data
        let requestData = {
            lineItems: repeaterData,
            customerID: $('#customer').val(), // Assuming this is the ID of the customer input field
            quotationDate: $('#quotationDate').val(), // Assuming this is the ID of the quotation date field
            quotationExpiryDate: $('#quotationExpiryDate').val(), // Assuming this is the ID of the quotation date field
            paymentTerms: $('#paymentTerms').val(), // Assuming this is the ID of the payment terms field
            documentId: $('#documentId').val(),
            quotationNumber: $('#quotationNumber').val(),
            poNumber: $('#customerPO').val(),
            poAttachment: $('#poAttachmentFileName').val()
        };
        console.log(requestData);
        return requestData;
    }


    //Save New Quotation
    function saveQuotation() {
        if ($('#customer').val().length > 0) {
            blockArea($('body'));

            let requestData = prepareQuotationData();

            $.ajax(
                {
                    url: '/ajax/documents/save_quotation.php', // Update with your PHP script URL
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
                            console.log(response.message); // Output: Quotation saved successfully.
                            console.log(response.documentId); // Access documentId
                            unBlockArea($('body'));
                            let documentId = response.documentId;
                            $("#documentId").val(documentId);
                            $("#quotationNumber").val("DRAFT");
                            showSuccessMessage(response.message, gotoPage, "/quotation/edit/" + documentId);


                        } else {
                            console.error('Error:', response.message);
                            alert(`Error: ${response.message}`);
                        }

                    },
                    error: function (xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
        } else {
            showErrorMessage("Nothing to save.");
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
                success: function (response) {
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
                error: function (xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        } else {
            Swal.fire({
                title: 'Error!',
                icon: 'error',
                text: "Nothing to save.",
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

    function sendForApproval() {

        let customerPO = $("#customerPO").val();

        if (customerPO.length > 3) {

            // Get the file input element
            var fileInput = document.getElementById('poAttachmentFile');
            if (fileInput.files.length === 0) {
                saveQuotationThread();
            } else {
                //Do the upload
                var file = fileInput.files[0];

                var formData = new FormData();
                formData.append('poAttachmentFile', file); // Add the file to FormData

                // Make the AJAX request
                $.ajax({
                    url: '/ajax/file_upload.php', // Replace with your PHP file handling URL
                    type: 'POST',
                    data: formData,
                    processData: false, // Don't process the data
                    contentType: false, // Don't set content type
                    success: function (response) {
                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }
                        if (response.status === "success") {
                            $("#poAttachmentFileName").val(response.message);
                            saveQuotationThread();
                        }

                        console.log(response); // Server response

                    },
                    error: function (xhr, status, error) {
                        console.error(error);
                    }
                });
            }

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
                    success: function (response) {
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
                    error: function (xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    icon: 'error',
                    text: "Nothing to save.",
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


            ////////////////////////////////////////////////////


        } else {
            Swal.fire({
                title: 'Customer PO Number not entered!',
                icon: 'error',
                text: "Customer PO Number is not entered.",
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
        formData.append("documentID", documentID);
        formData.append("status", "AWAITING APPROVAL");

        $.ajax({
            url: '/ajax/documents/update_quotation_status.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (data, status) {
                console.log(data);

                var statusmessage = data.trim().split("|")[0];
                var message = data.trim().split("|")[1];

                if (statusmessage == "SUCCESS") {
                    location.href = "/quotation/edit/" + documentID;
                }

                if (statusmessage == "ERROR") {

                }
            },

            error: function (error) {

                console.log(error);
            }
        });

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
                    applyDatePicker();
                    applySelectPicker();
                },

                show: function () {
                    $(this).slideDown();

                    updateItemNumbers();

                    const index = $(this).index();
                    //line-item[0][eta]

                    //Initialize datepicker
                    const etaDatePicker = $('[name="line-item[' + index + '][eta]"]');
                    let fp = etaDatePicker.flatpickr({
                        dateFormat: 'Y-m-d',
                        altInput: true,
                        altFormat: 'd - M - Y'
                    });
                    fp.clear();

                    //Initialize selectpicker
                    const sparepartitem = $('[name="line-item[' + index + '][sparepartitem]"]');
                    initializeSelect2(sparepartitem, null);


                    // Initialize tooltip on load of each item
                    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                    tooltipTriggerList.map(function (tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });
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
        e.stopPropagation();
        console.log("Spare part Item Changed");
        const selectedData = e.params.data;
        console.log("Selected Data : " + selectedData);
        try {

            const selectedDesc = selectedData.desc;
            const selectedUnitPrice = selectedData.salesprice;
            const selectedhsCode = selectedData.hscode;
            const selectedhsPercentage = selectedData.hspercentage;
            const selectedUOMID = selectedData.uomid;
            const selectedleadtime = selectedData.leadtime;

            console.log("Selected Lead time: " + selectedleadtime);

            const index = getRepeaterRowIndex($(this));
            const sparepartdescription = getElementByIndexAndName(index, 'sparepartdescription');
            sparepartdescription.val(selectedDesc);

            const qty = getElementByIndexAndName(index, 'qty');
            qty.val('1');

            const unitPrice = getElementByIndexAndName(index, 'unitPrice');
            unitPrice.val(selectedUnitPrice);

            const vatPercentage = getElementByIndexAndName(index, 'vatPercentage');
            vatPercentage.val("15");

            const hsCode = getElementByIndexAndName(index, 'hsCode');
            hsCode.val(selectedhsCode);

            const hsPercentage = getElementByIndexAndName(index, 'hsPercentage');
            hsPercentage.val(selectedhsPercentage);

            const discountPercentage = getElementByIndexAndName(index, 'discountPercentage');
            discountPercentage.val('0');

            const discountAmount = getElementByIndexAndName(index, 'discountAmount');
            discountAmount.val('0.00');

            console.log("selectedleadtime: " + selectedleadtime);
            const eta = getElementByIndexAndName(index, 'eta');
            eta.val(selectedleadtime);
            eta.flatpickr({dateFormat: 'Y-m-d', altInput: true, altFormat: 'd - M - Y'});

            const uom = getElementByIndexAndName(index, 'uom');
            $(uom).val(selectedUOMID);

            doCalculation();
        } catch (err) {

        }

    });


    $(document).on('change', '.calculation-trigger', function (e) {
        doCalculation();
    });

    function onSparePartItemChanged(inputElement) {

        console.log("Changed " + $(inputElement).val());


        // const inputValue = $(inputElement).val().trim();
        //
        // // Check if the entered value matches any label in the autocompleteData array
        // const isValid = sparepartsAutoCompleteData.some((item) => item.label === inputValue);
        //
        // if (isValid) {
        //     // Auto-select the matching item
        //     const matchedItem = sparepartsAutoCompleteData.find((item) => item.label === inputValue);
        //     $(inputElement).val(matchedItem.label); // Set the input value to the matched label
        //     $(inputElement).data("selected", true); // Mark the input as valid
        // } else {
        //     // Clear the input if no match is found
        //     $(inputElement).val("");
        //     $(inputElement).data("selected", false);
        // }
    }

    function doCalculation() {
        const totalItemRows = getRepeaterRowCount();

        let rowSubTotal = 0;

        let totalAmountBeforeVAT = 0.00;
        let totalDiscountAmount = 0.00;
        let totalVATAmount = 0.00;
        let totalAmountAfterVAT = 0.00;

        for (let index = 0; index < totalItemRows; index++) {

            let qty = parseFloat(getElementByIndexAndName(index, 'qty').val()) || 0;
            let unitPrice = parseFloat(getElementByIndexAndName(index, 'unitPrice').val()) || 0;
            let vatPercentage = parseFloat(getElementByIndexAndName(index, 'vatPercentage').val()) || 0;
            let discountPercentage = parseFloat(getElementByIndexAndName(index, 'discountPercentage').val()) || 0;
            let uomRatio = parseFloat(getElementByIndexAndName(index, 'uom').find(':selected').data("ratio")) || 1;


                // Calculate base subtotal
                let baseSubtotal = qty * uomRatio * unitPrice;

                // Apply discount
                let discountAmount = baseSubtotal * (discountPercentage / 100);
                let discountedSubtotal = baseSubtotal - (baseSubtotal * (discountPercentage / 100));


                // Calculate VAT amount (based on the discounted subtotal)
                let vatAmount = discountedSubtotal * (vatPercentage / 100);

                let subTotal = discountedSubtotal + vatAmount;


                //Add totals
                totalAmountBeforeVAT += discountedSubtotal;
                totalDiscountAmount += discountAmount;
                totalVATAmount += vatAmount;
                totalAmountAfterVAT += (parseFloat(vatAmount) + parseFloat(discountedSubtotal));


                // Set values to respective fields
                getElementByIndexAndName(index, 'subTotal').val(toTwoDecimal(subTotal)); // Set the discounted subtotal
                getElementByIndexAndName(index, 'total').val(toTwoDecimal(discountedSubtotal)); // Set the discounted subtotal
                getElementByIndexAndName(index, 'vatAmount').val(toTwoDecimal(vatAmount));         // Set the VAT amount
                getElementByIndexAndName(index, 'discountAmount').val(toTwoDecimal(discountAmount));         // Set the Discount amount


        }


        const totalAmountBeforeVATSpan = $("#totalAmountBeforeVATSpan");
        const totalDiscountAmountSpan = $("#totalDiscountAmountSpan");
        const totalVATAmountSpan = $("#totalVATAmountSpan");
        const totalAmountAfterVATSpan = $("#totalAmountAfterVATSpan");

        totalAmountBeforeVATSpan.html("SAR " + toTwoDecimal(totalAmountBeforeVAT).toString());
        totalDiscountAmountSpan.html("SAR " + toTwoDecimal(totalDiscountAmount).toString());
        totalVATAmountSpan.html("SAR " + toTwoDecimal(totalVATAmount).toString());
        totalAmountAfterVATSpan.html("SAR " + toTwoDecimal(totalAmountAfterVAT).toString());

    }



    // Initialize Select2 with AJAX
    function initializeSelect2(selectElement, preselectedValue = null) {
        console.log("preselectedValue: " + preselectedValue);

        // Initialize Select2
        $(selectElement).select2({
            placeholder: "Select a part number",
            minimumInputLength:3,
            ajax: {
                url: "/ajax/spareparts/get_spareparts.php",
                dataType: "json",
                delay: 250, // Delay AJAX requests to reduce load
                data: function (params) {
                    return { q: params.term }; // Send search query to PHP
                },
                processResults: function (data) {
                    return { results: data.results };
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