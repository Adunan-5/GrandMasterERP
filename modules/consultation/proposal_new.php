<?php
$PAGE_ID = "CONSULTATION_PROPOSAL_NEW";
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
    <link rel="stylesheet" href="/assets/vendor/libs/select2/select2.css "/>
    <?php include_once __DIR__ . "/../../includes/dashboard/dashboard_head_section_post_vendor.php"; ?>
    <title>GrandMaster ERP | Proposals</title>
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
                                    <h4 class="my-0">New Proposal</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row invoice-add">
                        <!-- Proposal Header-->
                        <div class="col-lg-12 col-12 mb-lg-0 mb-6">
                            <div class="card invoice-preview-card p-sm-6 p-6">
                                <div class="card-body px-0">
                                    <div class="row">
                                        <div class="col-md-5 col-sm-5 col-12 mb-sm-0 mb-6">
                                            <div class="mb-4">
                                                <label class="form-label" for="customerCountry">Proposal For:</label>
                                                <select name="customer" id="customer" class="form-select mb-4 w-50 selectpicker w-100" data-style="btn-default" data-live-search="true" tabindex="null">
                                                    <option value="">Select Customer</option>
                                                    <?php
                                                    $companyId = $_SESSION['SES_SELECTED_COMPANY'];
                                                    $res       = $db->query("SELECT * FROM customers WHERE status ='Active' and companyId = $companyId");
                                                    while ($row = mysqli_fetch_assoc($res)) {
                                                        ?>
                                                        <option value="<?= $row['customerId'] ?>"><?= $row['companyName'] . " | " . $row['companyNameAr'] ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-7 col-sm-7">
                                            <div class="mt-6 d-flex gap-2 justify-content-end">
                                                <button class="btn btn-primary mb-4" onclick="saveProposal()">
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
                                        </div>
                                        <div class="col-md-5 col-8 pe-0 ps-0 ps-md-2">
                                            <dl class="row mb-0">
                                                <dt class="col-sm-5 mb-2 d-md-flex align-items-center justify-content-end">
                                                    <span class="h5 text-capitalize mb-0 text-nowrap">Proposal</span>
                                                </dt>
                                                <dd class="col-sm-7">
                                                    <div class="input-group input-group-merge disabled">
                                                        <span class="input-group-text">#</span>
                                                        <input name="proposalNumber" id="proposalNumber" type="text" class="form-control" disabled placeholder="Will be generated once saved / confirmed" value="NEW"/>
                                                        <input name="proposalId" id="proposalId" type="hidden" value=""/>
                                                    </div>
                                                </dd>
                                                <dt class="col-sm-5 mb-2 d-md-flex align-items-center justify-content-end">
                                                    <span class="fw-normal">Proposal Date:</span>
                                                </dt>
                                                <dd class="col-sm-7">
                                                    <input name="proposalDate" id="proposalDate" type="text" class="form-control gmm-date-format" placeholder="DD - MMM - YYYY" value="<?php echo date('Y-m-d'); ?>"/>
                                                </dd>
                                                <dt class="col-sm-5 d-md-flex align-items-center justify-content-end">
                                                    <span class="fw-normal">Valid Until:</span>
                                                </dt>
                                                <dd class="col-sm-7">
                                                    <input name="proposalValidUntil" id="proposalValidUntil" type="text" class="form-control gmm-date-format" placeholder="DD - MMM - YYYY" value="<?= date('Y-m-d', strtotime('+30 days')) ?>"/>
                                                </dd>
                                                <dt class="col-sm-5 mb-2 d-md-flex align-items-center justify-content-end">
                                                    <span class="fw-normal">Sales Person:</span>
                                                </dt>
                                                <dd class="col-sm-7">
                                                    <input type="text" readonly class="form-control due-date" value="<?= getDisplayNameOfCurrentUser() ?>"/>
                                                </dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                                <!--Grey Header Section Ends-->
                                <!--Proposal Template and Project Title Starts-->
                                <div class="card-body px-0 d-none" id="proposalTemplateSelectionDIV">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-12 mb-sm-0 mb-6">
                                            <div class="mb-4">
                                                <label class="form-label" for="customerCountry">Proposal Template:</label>
                                                <select name="proposalTemplate" id="proposalTemplate" class="form-select mb-4 w-50 selectpicker w-100" data-style="btn-default" data-live-search="true" tabindex="null">
                                                    <option value="">Select a Template</option>
                                                    <?php
                                                    $res = $db->query("SELECT * FROM consultation_proposal_templates WHERE active ='1'");
                                                    while ($row = mysqli_fetch_assoc($res)) {
                                                        ?>
                                                        <option value="<?= $row['proposalTemplateID'] ?>"><?= $row['templateName'] ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-sm-0 mb-6">
                                            <div class="mb-4">
                                                <label class="form-label" for="customerCountry">Proposal Title:</label>
                                                <input type="text" class="form-control" id="proposalTitle" name="proposalTitle" placeholder="" aria-describedby="defaultFormControlHelp" required/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--Proposal Template and Project Title Ends-->
                                <!--Services Items Builder-->
                                <div class="d-none" id="proposalServiceItemsSelectionDIV">
                                    <div class="row">
                                        <div class="col-md-5 col-sm-5 col-12 mb-sm-0 mb-6">
                                            <div class="mb-4">
                                                <label class="form-label" for="customerCountry">Services / Products:</label>
                                                <select name="servicesList" id="servicesList" class="form-select mb-4 w-50 selectpicker w-100" data-style="btn-default" data-live-search="true" tabindex="null">
                                                    <option value="">Select a service or product</option>
                                                    <?php
                                                    $res = $db->query("SELECT * FROM consultation_services_items WHERE active ='1'");
                                                    while ($row = mysqli_fetch_assoc($res)) {
                                                        ?>
                                                        <option value="<?= $row['itemId'] ?>"><?= $row['itemName'] ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-7 col-sm-7">
                                            <div class="mt-6 d-flex gap-2 justify-content-start">
                                                <button class="btn btn-primary mb-4" id="addRowBtn" onclick="addServiceItemToProposal()">
                                                    <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-plus ti-xs me-2"></i>Add</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive d-none" id="proposalLineItemsContainer">
                                        <table class="table mb-0" id="proposalLineItemsTable">
                                            <thead>
                                            <tr>
                                                <th style="width: 40px">S.No</th>
                                                <th>Service</th>
                                                <th style="width: 120px">Qty</th>
                                                <th style="width: 120px">UOM</th>
                                                <th style="width: 120px">Rate</th>
                                                <th style="width: 150px">Total</th>
                                                <th style="width: 150px">Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!--/Services Items Builder-->
                            </div>
                        </div>
                    </div>
                    <!--***********************************-->
                    <div class="row invoice-preview mt-6 d-none" id="proposalContainer">
                        <!-- Proposal -->
                        <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-6">
                            <div class="card invoice-preview-card p-sm-12 p-6" id="proposalContentContainer">
                                <div class="table-responsive border border-bottom-0 border-top-0 rounded">
                                    <table class="table m-0">
                                        <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th>Description</th>
                                            <th>Cost</th>
                                            <th>Qty</th>
                                            <th>Price</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td class="text-nowrap text-heading">Sparepart xyz</td>
                                            <td class="text-nowrap">HTML Admin Template</td>
                                            <td>$32</td>
                                            <td>1</td>
                                            <td>$32.00</td>
                                        </tr>
                                        <tr>
                                            <td class="text-nowrap text-heading">Frest Admin Template</td>
                                            <td class="text-nowrap">Angular Admin Template</td>
                                            <td>$22</td>
                                            <td>1</td>
                                            <td>$22.00</td>
                                        </tr>
                                        <tr>
                                            <td class="text-nowrap text-heading">Apex Admin Template</td>
                                            <td class="text-nowrap">HTML Admin Template</td>
                                            <td>$17</td>
                                            <td>2</td>
                                            <td>$34.00</td>
                                        </tr>
                                        <tr>
                                            <td class="text-nowrap text-heading">Robust Admin Template</td>
                                            <td class="text-nowrap">React Admin Template</td>
                                            <td>$66</td>
                                            <td>1</td>
                                            <td>$66.00</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="table-responsive">
                                    <table class="table m-0 table-borderless">
                                        <tbody>
                                        <tr>
                                            <td class="align-top pe-6 ps-0 py-6">
                                                <p class="mb-1">
                                                    <span class="me-2 h6">Salesperson:</span>
                                                    <span>Alfie Solomons</span>
                                                </p>
                                                <span>Thanks for your business</span>
                                            </td>
                                            <td class="px-0 py-6 w-px-100">
                                                <p class="mb-2">Subtotal:</p>
                                                <p class="mb-2">Discount:</p>
                                                <p class="mb-2 border-bottom pb-2">Tax:</p>
                                                <p class="mb-0 pt-2">Total:</p>
                                            </td>
                                            <td class="text-end px-0 py-6 w-px-100 fw-medium text-heading">
                                                <p class="fw-medium mb-2">$1800</p>
                                                <p class="fw-medium mb-2">$28</p>
                                                <p class="fw-medium mb-2 border-bottom pb-2">21%</p>
                                                <p class="fw-medium mb-0 pt-2">$1690</p>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <hr class="mt-0 mb-6"/>
                                <div class="card-body p-0">
                                    <div class="row">
                                        <div class="col-12">
                                            <span class="fw-medium text-heading">Note:</span>
                                            <span
                                            >It was a pleasure working with you and your team. We hope you will keep us in mind for
                            future freelance projects. Thank You!</span
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Proposal  -->
                        <!-- Proposal Actions -->
                        <div class="col-xl-3 col-md-4 col-12 invoice-actions d-none">
                            <div class="card">
                                <div class="card-body">
                                    <button
                                            class="btn btn-primary d-grid w-100 mb-4"
                                            data-bs-toggle="offcanvas"
                                            data-bs-target="#sendInvoiceOffcanvas">
                        <span class="d-flex align-items-center justify-content-center text-nowrap"
                        ><i class="ti ti-send ti-xs me-2"></i>Send Invoice</span
                        >
                                    </button>
                                    <button class="btn btn-label-secondary d-grid w-100 mb-4">Download</button>
                                    <div class="d-flex mb-4">
                                        <a
                                                class="btn btn-label-secondary d-grid w-100 me-4"
                                                target="_blank"
                                                href="./app-invoice-print.html">
                                            Print
                                        </a>
                                        <a href="./app-invoice-edit.html" class="btn btn-label-secondary d-grid w-100"> Edit </a>
                                    </div>
                                    <button
                                            class="btn btn-success d-grid w-100"
                                            data-bs-toggle="offcanvas"
                                            data-bs-target="#addPaymentOffcanvas">
                        <span class="d-flex align-items-center justify-content-center text-nowrap"
                        ><i class="ti ti-currency-dollar ti-xs me-2"></i>Add Payment</span
                        >
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- /Proposal Actions -->
                    </div>
                    <!--***********************************-->
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
<!-- Page JS -->
<script src="/assets/js/offcanvas-send-invoice.js"></script>
<script>
    var quillEditors = {};

    $(document).ready(function (e) {

        //Initialize Editors
        $('.snow-editor').each(function (index, editorElem) {
            var toolbarElem = $('.snow-toolbar').eq(index)[0];

            new Quill(editorElem, {
                modules: {
                    formula: true,
                    toolbar: toolbarElem
                },
                theme: 'snow'
            });
        });

        //Customer dropdown change handler
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

                    $("#proposalTemplateSelectionDIV").removeClass('d-none');
                },

                error: function (error) {
                    unBlockArea($('.invoice-preview-header'));
                    console.log(error);
                }
            });

        });

        $(document).on("change", "#proposalTemplate", function (e) {

            var selectedTemplateID = $(this).val();
            if (selectedTemplateID.length > 0) {
                $("#proposalContainer").removeClass('d-none');
                $("#proposalServiceItemsSelectionDIV").removeClass('d-none');

                loadProposalTemplate(selectedTemplateID);
            } else {
                $("#proposalContainer").addClass('d-none');
            }
        });

    });


    function loadProposalTemplate(proposalTemplateID) {

        console.log("Proposal loading: ", proposalTemplateID);

        var dataToSend = {templateID: proposalTemplateID};

        $.ajax({
            url: '/ajax/consultation/fetch_proposal_template.php',
            type: 'POST',
            data: JSON.stringify(dataToSend),
            contentType: 'application/json',
            dataType: 'json',
            success: function (response) {
                console.log("Server Response: ", response);


                unBlockArea($('body'));
                if (response.status === "success") {

                    var data = response.data;

                    $("#proposalContentContainer").html("<h2>Introduction</h2>");
                    $("#proposalContentContainer").append(data.contentIntroduction);

                    $("#proposalContentContainer").append("<h2>Service & Products</h2>");
                    $("#proposalContentContainer").append("<div id='proposalServicesListInlineDIV' class='mb-6'></div>");

                    $("#proposalContentContainer").append("<h2>Why Choose Us</h2>");
                    $("#proposalContentContainer").append(data.contentWhyChooseUs);

                    $("#proposalContentContainer").append("<h2>Scope Of Work</h2>");
                    $("#proposalContentContainer").append(`<div id="proposalScopeOfWork" name="proposalScopeOfWork" class="snow-editor"></div>`);

                    $("#proposalContentContainer").append("<h2>Client Responsibilites</h2>");
                    $("#proposalContentContainer").append(data.contentClientResponsibilities);

                    $("#proposalContentContainer").append("<h2>Terms & Conditions</h2>");
                    $("#proposalContentContainer").append(data.contentTermsAndConditions);

                    $("#proposalContentContainer").append("<h2>Next Steps</h2>");
                    $("#proposalContentContainer").append(data.contentNextSteps);

                    applySnowEditor();

                } else if (response.status === "error") {
                    // Handle error


                }
            },

            error: function (xhr, status, error) {
                console.error("AJAX error:", error);
            }
        });


    }


    function addServiceItemToProposal() {
        $("#proposalLineItemsContainer").removeClass("d-none");

        blockArea($("#proposalLineItemsContainer"));
        var selectedServiceItem = $("#servicesList").val();

        var dataToSend = {itemId: selectedServiceItem};
        $.ajax({
            url: '/ajax/consultation/fetch_service_item.php',
            type: 'POST',
            data: JSON.stringify(dataToSend),
            contentType: 'application/json',
            dataType: 'json',
            success: function (response) {
                console.log("Server Response: ", response);

                unBlockArea($("#proposalLineItemsContainer"));
                if (response.status === "success") {
                    var data = response.data;

                    //Add row
                    let newRow = `
                        <tr>
                            <td class="sno"></td>
                            <td>
                                <input type="hidden" name="serviceItemName" value="` + data.itemName + `" required/>
                                <input type="hidden" name="serviceItemDescription" value="` + data.description + `" required/>
                                <input type="hidden" name="serviceItemID" value="` + data.itemId + `" required/>
                                <strong>` + data.itemName + `</strong><br>` + data.description + `
                            </td>
                            <td><input type="number" class="form-control qty" name="qty" value="1" min="1" required/></td>
                            <td><input type="text" class="form-control" name="uom" readonly value="` + data.uom + `" required/></td>
                            <td><input type="text" class="form-control rate" name="rate" readonly value="` + data.price + `" required/></td>
                            <td><input type="text" class="form-control total" name="total" readonly value="` + data.price + `" /></td>
                            <td><a class="btn btn-danger removeRow"><i class="ti ti-trash"></i></a></td>
                        </tr>
                    `;
                    $('#proposalLineItemsTable tbody').append(newRow);
                    updateSerialNumbers();

                    unBlockArea($("#proposalLineItemsContainer"));


                } else if (response.status === "error") {
                    // Handle error


                }
            },

            error: function (xhr, status, error) {
                console.error("AJAX error:", error);
            }
        });
    }

    let rowIndex = 1;
    // Auto-calculate total on qty or rate change
    $(document).on('input', '.qty, .rate', function () {
        const row = $(this).closest('tr');
        const qty = parseFloat(row.find('.qty').val()) || 0;
        const rate = parseFloat(row.find('.rate').val()) || 0;
        row.find('.total').val((qty * rate).toFixed(2));
        injectServicesIntoProposal();

    });

    // Remove row
    $(document).on('click', '.removeRow', function () {
        $(this).closest('tr').remove();
        updateSerialNumbers();
    });

    // Update S.No after deletion
    function updateSerialNumbers() {
        $('#proposalLineItemsTable tbody tr').each(function (index) {
            $(this).find('.sno').text(index + 1);
        });
        rowIndex = $('#proposalLineItemsTable tbody tr').length + 1;

        injectServicesIntoProposal();
    }

    // Save button click - Collect data & send via AJAX
    $('#saveRowsBtn').click(function () {
        const rows = [];
        $('#proposalLineItemsTable tbody tr').each(function () {
            const row = {
                service: $(this).find('input[name="serviceItemID"]').val(),
                qty: $(this).find('input[name="qty"]').val(),
                uom: $(this).find('input[name="uom"]').val(),
                rate: $(this).find('input[name="rate"]').val(),
                total: $(this).find('input[name="total"]').val()
            };
            rows.push(row);
        });

        // Send via AJAX
        $.ajax({
            url: '/ajax/save_proposal_items.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({lineItems: rows}),
            success: function (response) {
                console.log('Saved:', response);
                Swal.fire('Success', 'Line items saved!', 'success');
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
                Swal.fire('Error', 'Failed to save line items.', 'error');
            }
        });
    });

    function injectServicesIntoProposal() {


        console.log("Injecting Services Items into proposal");

        $("#proposalServicesListInlineDIV").html(`<div class="table-responsive border border-bottom-0 border-top-0 rounded">
                                    <table class="table m-0">
                                        <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th>Qty</th>
                                            <th>UOM</th>
                                            <th>Rate</th>
                                            <th>Total</th>
                                        </tr>
                                        </thead>
                                        <tbody id="proposalServicesListItemInlineTbody">
                                        </tbody>
                                    </table>
                                </div>`);


        //Loop through the services and inject them in the proposal
        $('#proposalLineItemsTable tbody tr').each(function () {

            let newServiceRow = `<tr>
                                    <td class="text-nowrap text-heading"><strong>` + $(this).find('input[name="serviceItemName"]').val() + `</strong><br>` + $(this).find('input[name="serviceItemDescription"]').val() + `</td>
                                    <td class="text-nowrap">` + $(this).find('input[name="qty"]').val() + `</td>
                                    <td>` + $(this).find('input[name="uom"]').val() + `</td>
                                    <td>` + $(this).find('input[name="rate"]').val() + `</td>
                                    <td>` + $(this).find('input[name="total"]').val() + `</td>
                                </tr>`

            $("#proposalServicesListItemInlineTbody").append(newServiceRow);

        });
    }

    function applySnowEditor() {
        $('.snow-editor').each(function (index, editorElem) {
            var toolbarElem = $('.snow-toolbar').eq(index)[0];
            var editorID = $(editorElem).attr('id');
            if (editorID) {
                let quill = new Quill(editorElem, {
                    modules: {
                        formula: true,
                        toolbar: toolbarElem
                    },
                    theme: 'snow'
                });

                quillEditors[editorID] = quill;
            }
        });
    }

    function saveProposal() {

        console.log("Saving Proposal: ");

        const lineItemRows = [];
        $('#proposalLineItemsTable tbody tr').each(function () {
            const row = {
                service: $(this).find('input[name="serviceItemID"]').val(),
                qty: $(this).find('input[name="qty"]').val(),
                uom: $(this).find('input[name="uom"]').val(),
                rate: $(this).find('input[name="rate"]').val(),
                total: $(this).find('input[name="total"]').val()
            };
            lineItemRows.push(row);
        });

        var dataToSend = {
            customerID: $("#customer").val(),
            proposalDate: $("#proposalDate").val(),
            validUntil: $("#proposalValidUntil").val(),
            proposalTemplateID: $("#proposalTemplate").val(),
            proposalTitle: $("#proposalTitle").val(),
            scopeOfWork: quillEditors['proposalScopeOfWork'].root.innerHTML,
            lineItems: lineItemRows
        };

        $.ajax({
            url: '/ajax/consultation/save_proposal.php',
            type: 'POST',
            data: JSON.stringify(dataToSend),
            contentType: 'application/json',
            dataType: 'json',
            success: function (response) {
                console.log("Server Response: ", response);
                unBlockArea($('body'));
                if (response.status === "success") {

                    showSuccessMessage(response.message, gotoPage, '/consultation/proposal/edit?proposalID=' + response.data);
                    //showSuccessMessage(response.message);

                } else if (response.status === "error") {
                    // Handle error

                }
            },

            error: function (xhr, status, error) {
                console.error("AJAX error:", error);
            }
        });
    }
</script>
</body>
</html>