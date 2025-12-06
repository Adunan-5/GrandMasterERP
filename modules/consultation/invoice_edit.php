<?php
$PAGE_ID = "CONSULTATION_INVOICE_EDIT";
include_once __DIR__ . "/../../includes/baseIncludes.php";
include_once __DIR__ . "/../../includes/auth_check.php";

$invoiceID = "";
$invoiceID = filter_input(INPUT_GET, 'invoiceID', FILTER_VALIDATE_INT);
if ($invoiceID === null || $invoiceID === false || filter_var($invoiceID, FILTER_VALIDATE_INT) === false) {
    header("location:/invoice/list");
    exit();
}


?>
<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
<head>
    <?php include_once __DIR__ . "/../../includes/dashboard/dashboard_head_section.php"; ?>
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="/assets/vendor/libs/node-waves/node-waves.css"/>
    <link rel="stylesheet" href="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css"/>
    <link rel="stylesheet" href="/assets/vendor/libs/typeahead-js/typeahead.css"/>
    <link rel="stylesheet" href="/assets/vendor/libs/flatpickr/flatpickr.css"/>
    <link rel="stylesheet" href="/assets/vendor/libs/bootstrap-select/bootstrap-select.css"/>
    <link rel="stylesheet" href="/assets/vendor/libs/animate-css/animate.css"/>
    <link rel="stylesheet" href="/assets/vendor/libs/sweetalert2/sweetalert2.css"/>
    <!-- Page CSS -->
    <link rel="stylesheet" href="/assets/vendor/css/pages/app-invoice.css"/>
    <link rel="stylesheet" href="/assets/vendor/css/jquery-ui.css"/>
    <link rel="stylesheet" href="/assets/vendor/libs/select2/select2.css "/>
    <?php include_once __DIR__ . "/../../includes/dashboard/dashboard_head_section_post_vendor.php"; ?>
    <title>GrandMaster ERP | Invoice</title>
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
                                    <div class="col-6">
                                        <?php

                                        $invoiceDATA = "";
                                        $res = $db->query("SELECT * FROM invoice_documents WHERE invoiceId = ?s", $invoiceID);
                                        while ($row = mysqli_fetch_assoc($res)) {
                                            $invoiceDATA = $row;
                                        }

                                        $refDocID = $invoiceDATA['saleOrderId'];

                                        $res = $db->query("SELECT * FROM consultation_invoice_line_items WHERE invoiceId = ?s", $invoiceID);
                                        while ($row = mysqli_fetch_assoc($res)) {
                                            $lineItems[] = $row;
                                        }

                                        $currentUserRole = getAuthenticatedUser()->getRoles()[0];

                                        $invoiceNumber = "DRAFT";
                                        if (!empty($invoiceDATA['invoiceNumberPrefix'])) {
                                            $invoiceNumber = $invoiceDATA['invoiceNumberPrefix'] . $invoiceDATA['invoiceNumber'];
                                        }

                                        $invoiceStatus = $invoiceDATA['invoiceStatus'];

                                        ?>
                                        <h4 class="my-0"><?= getInvoiceNumberFromInvoiceID($invoiceID) ?></h4>
                                    </div>
                                    <div class="col-6 text-end">
                                        <?php
                                        switch ($invoiceStatus) {
                                            case INVOICE_STATUS_NEW:
                                                ?>
                                                <button type="button"
                                                        class="btn btn-label-linkedin text-nowrap d-inline-flex position-relative me-4">
                                                    DRAFT
                                                </button>
                                                <?php
                                                break;

                                            case INVOICE_STATUS_ACCOUNTANT_APPROVED:
                                                ?>
                                                <label
                                                    class="no-hand btn btn-label-success text-nowrap d-inline-flex position-relative me-4">
                                                    ACCOUNTANT APPROVED
                                                </label>
                                                <?php
                                                break;

                                            case INVOICE_STATUS_ACCOUNTANT_REJECTED:
                                                ?>
                                                <label
                                                    class="no-hand btn btn-label-danger text-nowrap d-inline-flex position-relative me-4">
                                                    ACCOUNTANT REJECTED
                                                </label>
                                                <?php
                                                break;

                                            default:
                                                ?>
                                                <button type="button"
                                                        class="btn btn-label-info text-nowrap d-inline-flex position-relative me-4">
                                                    <?= $invoiceDATA['invoiceStatus'] ?>
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
                    if ($invoiceStatus == INVOICE_STATUS_ACCOUNTANT_REJECTED) {
                        $rejectReason = getRejectReasonForInvoiceID($invoiceID);
                        ?>
                        <div class="alert alert-solid-danger d-flex align-items-center" role="alert">
                        <span class="alert-icon rounded">
                          <i class="ti ti-ban"></i>
                        </span> Rejection Reason: <?= $rejectReason ?>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="row invoice-add">
                        <!-- Invoice Add-->
                        <div class="col-lg-12 col-12 mb-lg-0 mb-6">
                            <div class="card invoice-preview-card p-sm-6 p-6 pt-0">
                                <div class="card-body px-0 mt-0 pt-0">
                                    <!--Alerts and info on the workflow-->
                                    <?php







                                    ?>
                                    <!--Alerts and info on the workflow ENDS-->

                                    <!--                                    --><?php
                                    //                                    if($orderStatus == ORDER_WH_STATUS_ALLOCATED) {
                                    //                                        ?>
                                    <!--                                        <div class="alert alert-primary" role="alert"> Order has been sent to WH Operations-->
                                    <!--                                        </div>-->
                                    <!--                                        --><?php
                                    //                                    }
                                    //                                    ?>
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-12 mb-sm-0 mb-6">
                                            <div class="mt-6 d-flex gap-2 justify-content-start">
                                                <!--                                                <button class="btn btn-success mb-4" onclick="">-->
                                                <!--                                                    <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-device-floppy ti-xs me-2"></i>Confirm Sales</span>-->
                                                <!--                                                </button>-->
                                                <!--                                                <button class="btn btn-warning mb-4" onclick="">-->
                                                <!--                                                    <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-file-like ti-xs me-2"></i>Hold Sales</span>-->
                                                <!--                                                </button>-->
                                                <!--                                                <button class="btn btn-danger mb-4" onclick="">-->
                                                <!--                                                    <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-eye-check ti-xs me-2"></i>Cancel Sales</span>-->
                                                <!--                                                </button>-->
                                                <!--                                                <button class="btn btn-info mb-4" onclick="">-->
                                                <!--                                                    <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-printer ti-xs me-2"></i>Invoice</span>-->
                                                <!--                                                </button>-->
                                            </div>
                                            <?php
                                            /*?>
                                        <div class="mb-4">


                                            <label class="form-label" for="customerCountry">Quotation For:</label>
                                            <select name="customer" id="customer" class="form-select mb-4 w-50 selectpicker w-100" data-style="btn-default" data-live-search="true" tabindex="null">
                                                <option value="">Select Customer</option>
                                                <?php
                                                $res = $db->query("SELECT * FROM customers WHERE status ='Active'");
                                                $customerID = $documentInfo['customerId'];
                                                while ($row = mysqli_fetch_assoc($res)) {
                                                    $selected = "";
                                                    if($customerID ==  $row['customerId']) $selected = "selected";
                                                    ?>
                                                    <option value="<?= $row['customerId'] ?>" <?=$selected ?>><?= $row['companyName'] . " | " . $row['companyNameAr'] ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>

                                            </div> */ ?>
                                        </div>
                                      <div class="row">
                                          <div class="col-md-6 col-sm-6 col-12">
                                            <div class="mt-6 d-flex gap-2 justify-content-start">
                                              <?php
                                              if (($currentUserRole == ROLE_SUPERADMIN) && ($invoiceStatus == INVOICE_STATUS_ACCOUNTANT_APPROVED)) {
                                                ?>
                                              <button class="btn btn-info mb-4" data-bs-toggle="modal" data-bs-target="#paymentDetailsModal">
                                                <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-credit-card ti-xs me-2"></i>Payment Details</span>
                                              </button>
                                                <?php
                                              }
                                                ?>
                                            </div>
                                          </div>
                                          <div class="col-md-6 col-sm-6 col-12">
                                            <div class="mt-6 d-flex gap-2 justify-content-end">

                                                <button class="btn btn-primary mb-4" onclick="saveInvoice()">
                                                    <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-device-floppy ti-xs me-2"></i>Save</span>
                                                </button>
                                                <?php
                                                if (($currentUserRole == ROLE_SUPERADMIN) && ($invoiceStatus == INVOICE_STATUS_NEW || $invoiceStatus == INVOICE_STATUS_ACCOUNTANT_REJECTED)) {
                                                ?>
                                                <button class="btn btn-primary mb-4" onclick="sendForApproval()">
                                                    <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-file-like ti-xs me-2"></i>Send for Approval</span>
                                                </button>
                                                <?php
                                                }
                                                if (($currentUserRole == ROLE_SUPERADMIN) && ($invoiceStatus == INVOICE_STATUS_ACCOUNTANT_APPROVED)) {
                                                  ?>
                                                  <button class="btn btn-primary mb-4" onclick="printInvoice()">
                                                    <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-printer ti-xs me-2"></i>Print</span>
                                                </button>
                                                  <button class="btn btn-primary mb-4" onclick="printSDN()">
                                                    <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-printer ti-xs me-2"></i>Print SDN</span>
                                                </button>
                                                <?php
                                                }
                                                if (($currentUserRole == ROLE_SUPERADMIN) && $invoiceStatus == INVOICE_STATUS_ACCOUNTANT_APPROVAL) {
                                                ?>
                                                <button class="btn btn-success mb-4" onclick="approveInvoice()">
                                              <span class="d-flex align-items-center justify-content-center text-nowrap">
                                                <i class="ti ti-device-floppy ti-xs me-2"></i>Approve
                                              </span>
                                                </button>
                                                <button class="btn btn-danger mb-4" data-bs-toggle="modal" data-bs-target="#rejectReasonModal">
                                              <span class="d-flex align-items-center justify-content-center text-nowrap">
                                                <i class="ti ti-circle-dashed-x ti-xs me-2"></i>Reject
                                              </span>
                                                </button>
                                                <?php
                                                }
                                                ?>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                </div>
                                <!--Grey Header Section-->
                                <div class="card-body invoice-preview-header rounded">
                                    <div class="d-flex flex-wrap flex-column flex-sm-row justify-content-between text-heading">
                                        <div class="mb-md-0 mb-6">
                                            <?php
                                            $customer = new Customer();
                                            $customer->loadById($invoiceDATA['customerId']);
                                            ?>
                                            <div class="svg-illustration mb-6 gap-2 align-items-center">
                                                <!--                                                <div class="app-brand-logo demo">-->
                                                <!--                                                    <img src="/assets/img/gmm-g-logo.png" alt="">-->
                                                <!--                                                </div>-->
                                                <div>
                                                    <span class="app-brand-text fw-bold fs-4 ms-50" id="companyName"><?= $customer->companyName ?></span>
                                                </div>
                                                <div>
                                                    <span class="app-brand-text fw-bold fs-5 ms-50" id="companyNameAR"><?= $customer->companyNameAr ?></span>
                                                </div>
                                            </div>
                                            <p class="mb-2" id="customerAddressLine1"><?= $customer->addressLine1; ?></p>
                                            <p class="mb-2" id="customerAddressLine2"><?= $customer->addressLine2; ?></p>
                                            <p class="mb-2" id="customerCityPostalCode"><?= getCityFromID($customer->cityId) . ", " . $customer->postalCode ?></p>
                                            <p class="mb-2" id="customerStateCountry"><?= getStateFromID($customer->stateId) . ", " . getCountryFromID($customer->countryId) ?></p>
                                            <p class="mb-2" id="customerVAT">VAT: <?= $customer->vatNumber ?></p>
                                            <p class="mb-2" id="customerCR">CR: <?= $customer->companyCRNumber ?></p>
                                        </div>
                                        <div class="col-md-5 col-8 pe-0 ps-0 ps-md-2">
                                            <dl class="row mb-0">
                                                <dt class="col-sm-5 mb-2 d-md-flex align-items-center justify-content-end">
                                                    <span class="h5 text-capitalize mb-0 text-nowrap">Invoice</span>
                                                </dt>
                                                <dd class="col-sm-7">
                                                    <div class="input-group input-group-merge disabled">
                                                        <span class="input-group-text">#</span>
                                                        <input name="invoiceNumber" id="invoiceNumber" type="text" class="form-control" disabled placeholder="Will be generated once saved / confirmed" value="<?= getInvoiceNumberFromInvoiceID($invoiceID)?>"/>
                                                    </div>
                                                    <input name="invoiceId" id="invoiceId" type="hidden" value="<?= $invoiceDATA['invoiceId']?>"/>
                                                    <input name="refDocID" id="refDocID" type="hidden" value="<?= $refDocID ?>"/>
                                                </dd>
                                                <dt class="col-sm-5 mb-2 d-md-flex align-items-center justify-content-end">
                                                    <span class="fw-normal">Date Created:</span>
                                                </dt>
                                                <dd class="col-sm-7">
                                                    <input name="invoiceDate" id="invoiceDate" type="text" class="form-control gmm-date-format" placeholder="DD - MMM - YYYY" value="<?= $invoiceDATA['invoiceDateIssued'] ?>"/>
                                                </dd>
                                                <!--                                                <dt class="col-sm-5 d-md-flex align-items-center justify-content-end">-->
                                                <!--                                                    <span class="fw-normal">Expiration Date:</span>-->
                                                <!--                                                </dt>-->
                                                <!--                                                <dd class="col-sm-7">-->
                                                <!--                                                    <input name="quotationExpiryDate" id="quotationExpiryDate" type="text" class="form-control gmm-date-format" placeholder="DD - MMM - YYYY" value="--><?php //= $documentInfo['quotationDateExpiry'] ?><!--"/>-->
                                                <!--                                                </dd>-->
                                                <dt class="col-sm-5 mb-2 d-md-flex align-items-center justify-content-end">
                                                    <span class="fw-normal">Sales Person:</span>
                                                </dt>
                                                <dd class="col-sm-7">
                                                    <input type="text" readonly class="form-control due-date" value="<?= getDisplayNameFromUserID($invoiceDATA['salesPersonId']) ?>"/>
                                                </dd>
                                                <dt class="col-sm-5 mb-2 d-md-flex align-items-center justify-content-end">
                                                    <span class="fw-normal">Payment Terms:</span>
                                                </dt>
                                                <dd class="col-sm-7">
                                                    <select name="paymentTerms" id="paymentTerms" class="form-control mt">
                                                        <option disabled selected value="">Select Payment Term</option>
                                                        <?php

                                                        $res = $db->query("SELECT * FROM payment_terms WHERE active = 1");
                                                        while ($row = mysqli_fetch_assoc($res)) {
                                                            $selected = "";
                                                            if ($invoiceDATA['paymentTermId'] == $row['termId']) $selected = "selected";
                                                            ?>
                                                            <option value="<?= $row['termId'] ?>" <?= $selected ?>><?= $row['termName'] ?></option> <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </dd>
                                                <dt class="col-sm-5 mb-2 d-md-flex align-items-center justify-content-end">
                                                    <span class="fw-normal">Customer P.O:</span>
                                                </dt>
                                                <dd class="col-sm-7">
                                                    <input type="text" class="form-control" readonly id="customerPO" name="customerPO" value="<?= $invoiceDATA['PONumber'] ?>"/>
                                                    <input type="hidden" class="form-control" readonly id="poAttachmentFileName" name="poAttachmentFileName" value="<?= $invoiceDATA['POAttachment'] ?>"/>
                                                </dd>
                                                <dt class="col-sm-5 mb-2 d-md-flex align-items-center justify-content-end">
                                                    <span class="fw-normal">P.O Attachment:</span>
                                                </dt>
                                                <dd class="col-sm-7">
                                                    <?php
                                                    if (!empty($invoiceDATA['POAttachment'])) {
                                                        ?>
                                                        <a class="btn btn-info" href="/uploads/<?= $invoiceDATA['POAttachment'] ?>" target="_blank">View
                                                            Customer PO</a>
                                                        <?php
                                                    }
                                                    ?>
                                                </dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                                <!--Grey Header Section Ends-->
                                <hr class="mt-0 mb-6"/>
                                <div class="card-body pt-0 px-0">
                                    <form class="source-item">
                                        <div class="mb-4" data-repeater-list="line-item">
                                            <?php
                                            $itemCount = 0;
                                            foreach ($lineItems as $item) {
                                              $lineItemInfo = getItemInfoForConsultationServiceItemID($item['itemId']);
                                                $originalRes = $db->query("SELECT * FROM consultation_line_items WHERE documentId = ?s AND itemId = ?s", $refDocID, $item['itemId']);
                                                $originalItem = mysqli_fetch_assoc($originalRes);
                                                ?>
                                                <div class="repeater-wrapper pt-0 pt-md-6" data-repeater-item>
                                                    <div class="d-flex border rounded position-relative pe-0">
                                                        <div class="row w-100 p-3">
                                                            <div class="d-flex justify-content-start mb-2">
                                                            </div>
                                                            <div class="col-md-2 col-12 mb-md-0 mb-4">
                                                                <p class="h6 mb-1 itemNumber">Item
                                                                    #<?= ++$itemCount; ?></p>
                                                                <select name="serviceitem" class="serviceitem form-control mb-5" data-selected="<?= $item['itemId'] ?>">
                                                                    <option value="">Service / Product</option>
                                                                    <?php
                                                                    $res = $db->query("SELECT * FROM consultation_services_items WHERE active = 1 and itemId = ?s", $item['itemId']);
                                                                    while ($row = mysqli_fetch_assoc($res)) {
                                                                        ?>
                                                                        <option selected value="<?= $row['itemId'] ?>" data-desc="<?= $row['description'] ?>"data-price="<?= $row['price'] ?>" data-uom="<?= $row['uom'] ?>" ><?= $row['itemName'] ?></option>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-2 col-12 mb-md-0 mb-4">
                                                                <p class="h6 mb-1">Description</p>
                                                                <input name="sparepartdescription" type="text" class="form-control mb-5" readonly value="<?= $lineItemInfo['description'] ?>"/>
                                                                <div class="text-heading" style="display: none">
                                                                    <div class="mb-1">Discount:</div>
                                                                    <span class="discount me-2">0%</span>
                                                                    <span class="tax-1 me-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Tax 1">0%</span>
                                                                    <span class="tax-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Tax 2">0%</span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1 col-12 mb-md-0 mb-4">
                                                                <p class="h6 mb-1">Qty</p>
                                                                <input name="qty" type="text" class="form-control numbers-only calculation-trigger" readonly placeholder="Qty" min="1" required value="<?= $item['quantity'] ?>"/>
                                                            </div>
                                                            <!--                                                            <div class="col-md-1 col-12 mb-md-0 mb-4">-->
                                                            <!--                                                                <p class="h6 mb-1">ETA</p>-->
                                                            <!--                                                                <input name="eta" type="text" class="form-control gmmdatepicker-friendly" placeholder="Date" value="--><?php //= empty($item['eta']) ? "" : $item['eta'] ?><!--"/>-->
                                                            <!--                                                            </div>-->
                                                            <div class="col-md-1 col-12 mb-md-0 mb-4" style="display: none">
                                                                <p class="h6 mb-1">UOM</p>
                                                                <input name="uom" type="text" class="form-control mb-6 calculation-trigger" data-placeholder="UOM" value="<?= $item['UOM'] ?>"  />
                                                            </div>
                                                            <div class="col-md-1 col-12 mb-md-0 mb-4">
                                                                <p class="h6 mb-1">Unit Price</p>
                                                                <input name="unitPrice" type="text" class="form-control numbers-only calculation-trigger" readonly placeholder="" value="<?= $item['unitPrice'] ?>"/>
                                                            </div>
                                                            <div class="col-md-1 col-12 mb-md-0 mb-4">
                                                                <p class="h6 mb-1">VAT %</p>
                                                                <input name="vatPercentage" type="text" class="form-control numbers-only calculation-trigger" placeholder="" value="<?= $item['vatPercentage'] ?>"/>
                                                            </div>
                                                            <div class="col-md-1 col-12 mb-md-0 mb-4">
                                                                <p class="h6 mb-1">VAT Amount</p>
                                                                <input name="vatAmount" type="text" class="form-control" placeholder="" readonly/>
                                                            </div>
                                                            <div class="col-md-2 col-12 mb-md-0 mb-4">
                                                                <p class="h6 mb-1">Sub Total</p>
                                                                <input name="subTotal" type="text" class="form-control" placeholder="" min="1" readonly/>
                                                            </div>
                                                            <div class="col-md-1 col-12 mb-md-0 mb-4">
                                                                <p class="h6 mb-1">Discount %</p>
                                                                <input name="discountPercentage" type="text" class="form-control numbers-only calculation-trigger" placeholder="" value="<?= $item['discountPercentage'] ?>"/>
                                                            </div>
                                                            <div class="col-md-1 col-12 mb-md-0 mb-4">
                                                                <p class="h6 mb-1 text-nowrap">Discount Amt</p>
                                                                <input name="discountAmount" type="text" class="form-control" placeholder="" readonly/>
                                                            </div>
                                                        </div>
                                                        <div class="row p-3 justify-content-end"></div>
                                                        <div class="d-flex flex-column align-items-center justify-content-between border-start p-2" style="display: none">
                                                            <i class="ti ti-x ti-lg cursor-pointer" data-repeater-delete style="display: none"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            if ($itemCount == 0) {
                                                ?>
                                                <div class="repeater-wrapper pt-0 pt-md-6" data-repeater-item>
                                                    <div class="d-flex border rounded position-relative pe-0">
                                                        <div class="row w-100 p-3">
                                                            <div class="col-md-2 col-12 mb-md-0 mb-4">
                                                                <p class="h6 mb-1 itemNumber">Item #1</p>
                                                                <select name="serviceitem" class="serviceitem form-control mb-5" data-live-search="true">
                                                                    <option value="">Service / Product</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-2 col-12 mb-md-0 mb-4">
                                                                <p class="h6 mb-1">Description</p>
                                                                <input name="sparepartdescription" type="text" class="form-control mb-5"/>
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
                                                                    <option value="">Select UOM</option>
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
                                                                <input name="unitPrice" type="text" class="form-control numbers-only calculation-trigger" placeholder=""/>
                                                            </div>
                                                            <div class="col-md-1 col-12 mb-md-0 mb-4">
                                                                <p class="h6 mb-1">VAT %</p>
                                                                <input name="vatPercentage" type="text" class="form-control numbers-only calculation-trigger" placeholder=""/>
                                                            </div>
                                                            <div class="col-md-1 col-12 mb-md-0 mb-4">
                                                                <p class="h6 mb-1">VAT Amount</p>
                                                                <input name="vatAmount" type="text" class="form-control" placeholder="" readonly/>
                                                            </div>
                                                            <div class="col-md-2 col-12 mb-md-0 mb-4">
                                                                <p class="h6 mb-1">Sub Total</p>
                                                                <input name="subTotal" type="text" class="form-control" placeholder="" min="1" readonly/>
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
                                        <div class="row" style="display:none">
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
                                                <textarea class="form-control" rows="2" id="note" placeholder="Invoice note">
It was a pleasure working with you and your team. We hope you will keep us in mind for future. Thank You!</textarea>
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
                if ($invoiceStatus == INVOICE_STATUS_ACCOUNTANT_APPROVAL) {
                    ?>
                    <button type="button" class="btn btn-danger" onclick="rejectInvoice()">Reject</button>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
</div>
<!-- / Layout wrapper -->
<?php include_once __DIR__ . "/../../includes/dashboard/dashboard_footer_scripts.php"; ?>
<!-- Page JS -->
<script src="/assets/js/offcanvas-send-invoice.js"></script>
<script>

  $(document).ready(function (e) {
  });
  // Handle Select All checkbox
  $('#select-all').on('change', function() {
    let isChecked = $(this).is(':checked');
    console.log('Select All changed:', isChecked);
    $('.line-item-checkbox').prop('checked', isChecked);
  });

  // Handle individual checkbox changes to update Select All state
  $(document).on('change', '.line-item-checkbox', function() {
    let totalCheckboxes = $('.line-item-checkbox').length;
    let checkedCheckboxes = $('.line-item-checkbox:checked').length;
    $('#select-all').prop('checked', totalCheckboxes === checkedCheckboxes);
  });

  function preparePaymentData()
  {
    let requestData = {
      invoiceID: $('#invoiceId').val(),
      totalAmount: $('#totalAmount').val(),
      accountsReceivableID: $('#accountsReceivableID').val(),
      amountReceived: $('#amountReceived').val(),
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

    if ($('#amountReceived').val().length > 0 && $('#paymentMode').val().length > 0) {
      blockArea($('body'));
      let requestData = preparePaymentData();

      $.ajax(
        {
          url: '/ajax/invoice/update_payment_details.php', // Update with your PHP script URL
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
              showSuccessMessage(response.message, gotoPage, "/invoice/edit/" + '<?=$invoiceID?>');


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


  function prepareInvoiceData() {
    console.log($('.source-item').repeaterVal());

    let repeaterData = $('.source-item').repeaterVal(); // Get the repeater data

    // Add additional data
    let requestData = {
      lineItems: repeaterData,
      customerID: <?= $invoiceDATA['customerId'] ?>,
      invoiceDate: $('#invoiceDate').val(),
      paymentTerms: $('#paymentTerms').val(),
      invoiceId: $('#invoiceId').val(),
      refDocID: $('#refDocID').val(),
      invoiceNumber: $('#invoiceNumber').val(),
      poNumber: $('#customerPO').val(),
      poAttachment: $('#poAttachmentFileName').val()
    };
    console.log(requestData);

    return requestData;
  }


  function saveInvoice() {

    blockArea($('body'));

    let requestData = prepareInvoiceData();
    console.log(requestData);


    $.ajax({
      url: '/ajax/consultation/save_invoice.php', // Update with your PHP script URL
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
          let invoiceId = response.documentId;
          $("#invoiceId").val(invoiceId);
          $("#quotationNumber").val("DRAFT");

          showSuccessMessage(response.message, gotoPage, "/consultation/invoice/edit/" + invoiceId);

        } else {
          console.error('Error:', response.message);
          alert(`Error: ${response.message}`);
        }

      },
      error: function (xhr, status, error) {
        console.error('Error:', error);
      }
    });


  }

  function sendForApproval() {
    saveInvoiceThread('<?=INVOICE_STATUS_ACCOUNTANT_APPROVAL ?>');
  }

  function saveInvoiceThread(status) {

    //Then proceed with saving the quotaion.
      let requestData = prepareInvoiceData();
      $.ajax({
        url: '/ajax/consultation/save_invoice.php', // Update with your PHP script URL
        method: 'POST',
        data: JSON.stringify(requestData), // Send the combined data as a JSON string
        contentType: 'application/json', // Indicate that the data is JSON
        dataType: 'json', // Expect a JSON response
        success: function(response) {
          console.log('Server Response after saving Invoice:', response);

          if (typeof response === 'string') {
            response = JSON.parse(response);
          }

          console.log('Response: ' + response.status);
          // Check the response status
          if (response.status === 'success') {
            unBlockArea($('body'));
            let documentID = response.documentId;
            console.log('Invoice Status: ' + status);
            updateInvoiceStatus(documentID, status);
          } else {
            console.error('Error:', response.message);
            showErrorMessage(`Error: ${response.message}`);
          }
        },
        error: function(xhr, status, error) {
          console.error('Error:', error);
        }
      });
  }

  function updateInvoiceStatus(documentID, status, showMessage = true, rejectReason = '') {

    console.log('Updating Invoice Status: ' + documentID + ' - ' + status + ' - ' + showMessage + ' - ' + rejectReason);

    console.log(rejectReason);
    var formData = new FormData();
    formData.append('documentID', documentID);
    formData.append('status', status);
    formData.append('rejectReason', rejectReason);

    console.log('DocumentID: ' + documentID);
    console.log('Status: ' + status);
    console.log('Show Message: ' + showMessage);


    $.ajax({
      url: '/ajax/consultation/update_invoice_status.php',
      type: 'POST',
      data: formData,
      contentType: false,
      processData: false,
      success: function(data, status) {
        console.log('Data: ' + data);

        var statusmessage = data.trim().split('|')[0];
        var message = data.trim().split('|')[1];

        console.log('Status Message: ' + statusmessage);
        console.log('Message: ' + message);

        if (statusmessage == 'SUCCESS') {
          if (showMessage)
            showSuccessMessage(message, gotoPage, '/consultation/invoice/edit/' + documentID);
        }
      },

      error: function(error) {
        console.log(error);
      }
    });

  }

  function approveInvoice() {
    let documentID = '<?=$invoiceID; ?>';
    updateInvoiceStatus(documentID, "<?=INVOICE_STATUS_ACCOUNTANT_APPROVED?>");
  }

  function rejectInvoice() {
    let documentID = '<?=$invoiceID; ?>';

    let rejectReason = $('#rejectReason').val();
    console.log(rejectReason);
    if (rejectReason.length) {
      updateInvoiceStatus(documentID, "<?=INVOICE_STATUS_ACCOUNTANT_REJECTED ?>", true, rejectReason);
    } else {
      showErrorMessage('You must enter a reason for rejection');
    }
  }

  function printInvoice() {
        // Get the base URL of the current page
        const baseUrl = window.location.origin;
        // Define the relative URL to open
        const relativeUrl = '/ajax/consultation/generate_invoice_pdf.php?invoiceID=' + '<?=$invoiceID ?>';
        // Combine the base URL and relative URL
        const fullUrl = baseUrl + relativeUrl;
        // Open the URL in a new tab
        window.open(fullUrl, '_blank');

    }

  function printSDN() {
        // Get the base URL of the current page
        const baseUrl = window.location.origin;
        // Define the relative URL to open
        const relativeUrl = '/ajax/consultation/generate_sdn_pdf.php?invoiceID=' + '<?=$invoiceID ?>';
        // Combine the base URL and relative URL
        const fullUrl = baseUrl + relativeUrl;
        // Open the URL in a new tab
        window.open(fullUrl, '_blank');

    }


  // repeater (jquery)
  $(function () {
    var applyChangesBtn = $('.btn-apply-changes'),
      discount,
      tax1,
      tax2,
      discountInput,
      tax1Input,
      tax2Input,
      sourceItem = $('.source-item'),
      adminDetails = {
        'App Design': 'Designed UI kit & app pages.',
        'App Customization': 'Customization & Bug Fixes.',
        'ABC Template': 'Bootstrap 4 admin template.',
        'App Development': 'Native App Development.'
      };

    // Prevent dropdown from closing on tax change
    $(document).on('click', '.tax-select', function (e) {
      e.stopPropagation();
    });

    // On tax change update it's value value
    function updateValue(listener, el) {
      listener.closest('.repeater-wrapper').find(el).text(listener.val());
    }

    // Apply item changes btn
    if (applyChangesBtn.length) {
      $(document).on('click', '.btn-apply-changes', function (e) {
        var $this = $(this);
        tax1Input = $this.closest('.dropdown-menu').find('#taxInput1');
        tax2Input = $this.closest('.dropdown-menu').find('#taxInput2');
        discountInput = $this.closest('.dropdown-menu').find('#discountInput');
        tax1 = $this.closest('.repeater-wrapper').find('.tax-1');
        tax2 = $this.closest('.repeater-wrapper').find('.tax-2');
        discount = $('.discount');

        if (tax1Input.val() !== null) {
          updateValue(tax1Input, tax1);
        }

        if (tax2Input.val() !== null) {
          updateValue(tax2Input, tax2);
        }

        if (discountInput.val().length) {
          $this
            .closest('.repeater-wrapper')
            .find(discount)
            .text(discountInput.val() + '%');
        }
      });
    }

    // Repeater init
    if (sourceItem.length) {
      sourceItem.on('submit', function (e) {
        e.preventDefault();
      });

      sourceItem.repeater({
        ready: function (setIndexes) {
          applyDatePicker();
          applySelectPicker();

          doCalculation();
        },

        show: function () {
          $(this).slideDown();

          updateItemNumbers();

          const index = $(this).index();
          //line-item[0][eta]

          //Initialize datepicker
          const item = $('[name="line-item[' + index + '][eta]"]');
          item.flatpickr({
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd - M - Y'
          });

          //Initialize selectpicker
          const serviceitem = $('[name="line-item[' + index + '][serviceitem]"]');
          initializeSelect2(serviceitem, null);


          // Initialize tooltip on load of each item
          const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
          tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
          });
        },
        hide: function (remove) {
          $(this).slideUp(remove);
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

    let serviceitem;
    let preselectedValue;
    // serviceitem = $('[name="line-item[0][serviceitem]"]');
    // serviceitem.selectpicker();

    serviceitem = $('[name="line-item[0][serviceitem]"]');
    preselectedValue = serviceitem.data('selected') || null;
    initializeSelect2(serviceitem, preselectedValue);


      <?php
      for($itemIndex = 1; $itemIndex <= $itemCount - 1; $itemIndex++  )
      {
      ?>
    serviceitem = $('[name="line-item[<?=$itemIndex ?>][serviceitem]"]');
    preselectedValue = serviceitem.data('selected') || null;
    initializeSelect2(serviceitem, preselectedValue);
      <?php
      }
      ?>
  }

  function applyDatePicker() {

    let flatpickrFriendly;

    flatpickrFriendly = $('[name="line-item[0][eta]"]');
    flatpickrFriendly.flatpickr({dateFormat: 'Y-m-d', altInput: true, altFormat: 'd - M - Y'});
      <?php
      for($itemIndex = 1; $itemIndex <= $itemCount - 1; $itemIndex++  )
      {
      ?>
    flatpickrFriendly = $('[name="line-item[<?=$itemIndex ?>][eta]"]');
    flatpickrFriendly.flatpickr({dateFormat: 'Y-m-d', altInput: true, altFormat: 'd - M - Y'});
      <?php
      }
      ?>

  }

  //On sparepart Item Changed
  // $(document).on('change', '.serviceitem', function (e) {
  $(document).on('select2:select', '.serviceitem', function (e) {
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
      // eta.val((selectedleadtime == null || parseFloat(selectedleadtime) === 0) ? '' : selectedleadtime);
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
    let calculationCheck = parseFloat(getElementByIndexAndName(0, 'qty').val()) || 0;

    if (calculationCheck > 0) {
      let rowSubTotal = 0;
      let totalAmountBeforeVAT = 0.00;
      let totalDiscountAmount = 0.00;
      let totalVATAmount = 0.00;
      let totalAmountAfterVAT = 0.00;

      for (let index = 0; index < totalItemRows; index++) {
        // Parse all input values as floats with fallback to 0
        let qty = parseFloat(getElementByIndexAndName(index, 'qty').val()) || 0;
        // let invoiceQty = parseFloat(getElementByIndexAndName(index, 'invoiceQty').val()) || 0;
        // let uomRatio = parseFloat(getElementByIndexAndName(index, 'uom').find(':selected').data("ratio")) || 1;
        let unitPrice = parseFloat(getElementByIndexAndName(index, 'unitPrice').val()) || 0;
        let vatPercentage = parseFloat(getElementByIndexAndName(index, 'vatPercentage').val()) || 0;
        let discountPercentage = parseFloat(getElementByIndexAndName(index, 'discountPercentage').val()) || 0;

        // Calculate base subtotal
        let baseSubtotal = qty * unitPrice;

        // Apply discount
        let discountAmount = baseSubtotal * (discountPercentage / 100);
        let discountedSubtotal = baseSubtotal - discountAmount;

        // Calculate VAT amount (based on the discounted subtotal)
        let vatAmount = discountedSubtotal * (vatPercentage / 100);

        // Add totals
        totalAmountBeforeVAT += discountedSubtotal;
        totalDiscountAmount += discountAmount;
        totalVATAmount += vatAmount;
        totalAmountAfterVAT += (vatAmount + discountedSubtotal);

        // Set values to respective fields
        getElementByIndexAndName(index, 'subTotal').val(toTwoDecimal(discountedSubtotal));
        getElementByIndexAndName(index, 'vatAmount').val(toTwoDecimal(vatAmount));
        getElementByIndexAndName(index, 'discountAmount').val(toTwoDecimal(discountAmount));
      }

      // Update summary fields
      const totalAmountBeforeVATSpan = $("#totalAmountBeforeVATSpan");
      const totalDiscountAmountSpan = $("#totalDiscountAmountSpan");
      const totalVATAmountSpan = $("#totalVATAmountSpan");
      const totalAmountAfterVATSpan = $("#totalAmountAfterVATSpan");

      totalAmountBeforeVATSpan.html("SAR " + toTwoDecimal(totalAmountBeforeVAT));
      totalDiscountAmountSpan.html("SAR " + toTwoDecimal(totalDiscountAmount));
      totalVATAmountSpan.html("SAR " + toTwoDecimal(totalVATAmount));
      totalAmountAfterVATSpan.html("SAR " + toTwoDecimal(totalAmountAfterVAT));
    }
  }

  // Helper function to format numbers to 2 decimal places
  function toTwoDecimal(num) {
    return parseFloat(num).toFixed(2);
  }


  // Initialize Select2 with AJAX
  function initializeSelect2(selectElement, preselectedValue = null) {
    console.log("preselectedValue: " + preselectedValue);

    // Initialize Select2
    $(selectElement).select2({
      placeholder: "Select a part number",
      minimumInputLength: 0,
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
        return $(
          `<div>
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
    document.querySelectorAll('.serviceitem').forEach(select => {
      const preselectedValue = select.dataset.selected || null;
      // initializeSelect2(select, preselectedValue);
    });
  });
</script>
</body>
</html>