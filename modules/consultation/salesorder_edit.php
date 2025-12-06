<?php
$PAGE_ID = "CONSULTATION_SALESORDER_EDIT";
include_once __DIR__ . "/../../includes/baseIncludes.php";
include_once __DIR__ . "/../../includes/auth_check.php";

$documentID = "";
$documentID = filter_input(INPUT_GET, 'documentID', FILTER_VALIDATE_INT);
if ($documentID === null || $documentID === false || filter_var($documentID, FILTER_VALIDATE_INT) === false) {
    header("location:/consultation/salesorder/list");
    exit();
}

$currentUserRole = getAuthenticatedUser()->getRoles()[0];
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
    <title>GrandMaster ERP | Sales Order</title>
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
                                        $documentInfo = '';
                                        $lineItems = array();


                                        $res = $db->query("SELECT * FROM consultation_key_documents WHERE documentId = ?s", $documentID);
                                        $documentInfo = mysqli_fetch_assoc($res);


                                        $keyDocument = new ConsultationKeyDocument();
                                        $keyDocument->loadById($documentID);


                                        $res = $db->query("SELECT * FROM consultation_line_items WHERE documentId = ?s", $documentID);
                                        while ($row = mysqli_fetch_assoc($res)) {
                                            $lineItems[] = $row;
                                        }

                                        ?>
                                        <h4 class="my-0"><?=getSalesOrderNumberFromConsultationDocumentID($documentID) ?></h4>

                                    </div>
                                    <div class="col-6 text-end">
                                        Quotation <a class="btn btn-label-linkedin text-nowrap d-inline-flex position-relative me-4" href="/quotation/preview/<?=$keyDocument->documentId ?>"><?=$keyDocument->getQuotationOrderNumberWithPrefix() ?>
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="row invoice-add">
                        <!-- Invoice Add-->
                        <div class="col-lg-12 col-12 mb-lg-0 mb-6">
                            <div class="card invoice-preview-card p-sm-6 p-6 pt-0">
                                <div class="card-body px-0 mt-0 pt-0">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-12 mb-sm-0 mb-6">
                                            <div class="mt-6 d-flex gap-2 justify-content-start">
<!--                                                LEFT SIDE BUTTONS-->

<!--                                                Create Project Button-->
                                                <button class="btn btn-success mb-4" onclick="checkBoxDataID()">
                                                    <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-a-b-2 ti-xs me-2"></i>Create Project</span>
                                                </button>




                                                <!-- <button class="btn btn-success mb-4" onclick="">
                                                    <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-device-floppy ti-xs me-2"></i>Confirm Sales</span>
                                                </button>


                                                <button class="btn btn-warning mb-4" onclick="">
                                                    <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-file-like ti-xs me-2"></i>Hold Sales</span>
                                                </button>
                                                <button class="btn btn-danger mb-4" onclick="">
                                                    <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-eye-check ti-xs me-2"></i>Cancel Sales</span>
                                                </button> -->
                                                <button class="btn btn-info mb-4" onclick="generateInvoice()">
                                                    <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-printer ti-xs me-2"></i>Invoice</span>
                                                </button>
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
                                        <div class="col-md-6 col-sm-6">
                                            <div class="mt-6 d-flex gap-2 justify-content-end">
                                                <?php
                                                $documentStatus = $documentInfo['quotationStatus'];
                                                ?>
<!--                                                <button class="btn btn-success mb-4" onclick="">-->
<!--                                                    <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-device-floppy ti-xs me-2"></i>Pick & Pack</span>-->
<!--                                                </button>-->
<!--                                                <button class="btn btn-success mb-4" onclick="">-->
<!--                                                    <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-file-like ti-xs me-2"></i>Delivery Slip</span>-->
<!--                                                </button>-->
<!--                                                <button class="btn btn-success mb-4" onclick="">-->
<!--                                                    <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-eye-check ti-xs me-2"></i>Partial Delivery</span>-->
<!--                                                </button>-->
                                                <button class="btn btn-success mb-4" onclick="">
                                                    <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-printer ti-xs me-2"></i>PO to Supplier</span>
                                                </button>
<!--                                                <button class="btn btn-primary mb-4" data-bs-toggle="offcanvas" data-bs-target="#sendInvoiceOffcanvas">-->
<!--                                                    <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-send ti-xs me-2"></i>Send To Customer</span>-->
<!--                                                </button>-->
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
                                            $customer->loadById($documentInfo['customerId']);
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
                                                        <input name="quotationNumber" id="quotationNumber" type="text" class="form-control" disabled placeholder="Will be generated once saved / confirmed" value="<?= getQuotationNumberFromConsultationDocumentID($documentInfo['documentId']) ?>"/>
                                                        <input name="documentId" id="documentId" type="hidden" value="<?= $documentInfo['documentId'] ?>"/>
                                                    </div>
                                                </dd>
                                                <dt class="col-sm-5 mb-2 d-md-flex align-items-center justify-content-end">
                                                    <span class="fw-normal">Date Issued:</span>
                                                </dt>
                                                <dd class="col-sm-7">
                                                    <input name="quotationDate" id="quotationDate" type="text" class="form-control gmm-date-format" placeholder="DD - MMM - YYYY" value="<?= $documentInfo['quotationDateIssued'] ?>"/>
                                                </dd>
                                                <dt class="col-sm-5 d-md-flex align-items-center justify-content-end">
                                                    <span class="fw-normal">Expiration Date:</span>
                                                </dt>
                                                <dd class="col-sm-7">
                                                    <input name="quotationExpiryDate" id="quotationExpiryDate" type="text" class="form-control gmm-date-format" placeholder="DD - MMM - YYYY" value="<?= $documentInfo['quotationDateExpiry'] ?>"/>
                                                </dd>
                                                <dt class="col-sm-5 mb-2 d-md-flex align-items-center justify-content-end">
                                                    <span class="fw-normal">Sales Person:</span>
                                                </dt>
                                                <dd class="col-sm-7">
                                                    <input type="text" readonly class="form-control due-date" value="<?= getDisplayNameFromUserID($documentInfo['salesPersonId']) ?>"/>
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
                                                            if ($documentInfo['paymentTermId'] == $row['termId']) $selected = "selected";
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
                                                    <input type="text" class="form-control" id="customerPO" name="customerPO" value="<?= $documentInfo['PONumber'] ?>"/>
                                                </dd>
                                                <dt class="col-sm-5 mb-2 d-md-flex align-items-center justify-content-end">
                                                    <span class="fw-normal">P.O Attachment:</span>
                                                </dt>
                                                <dd class="col-sm-7">
                                                    <?php
                                                    if (!empty($documentInfo['POAttachment'])) {
                                                        ?>
                                                        <a class="btn btn-info" href="/uploads/<?= $documentInfo['POAttachment'] ?>" target="_blank">View
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
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="select-all">
                                            <label class="form-check-label" for="select-all"> Select All</label>
                                        </div>
                                        <div class="mb-4" data-repeater-list="line-item">
                                            <?php
                                            $itemCount = 0;
                                            foreach ($lineItems as $item) {
                                                ?>
                                                <div class="repeater-wrapper pt-0 pt-md-6" data-repeater-item>
                                                    <div class="d-flex border rounded position-relative pe-0">
                                                        <div class="row w-100 p-3">
                                                            <div class="d-flex justify-content-start mb-2">
                                                            <div class="form-check me-3">
                                                                <input type="checkbox" class="form-check-input line-item-checkbox" name="line-item-checkbox"
                                                                       data-item-id="<?=$item['itemId']?>"
                                                                       data-item-name="<?=getItemNameForItemID($item['itemId'])?>"
                                                                       data-item-description="<?= getItemDescriptionForConsultationServiceItemID($item['itemId']) ?>"
                                                                       data-item-quantity="<?= $item['quantity']?>">
                                                                <label class="form-check-label"> Select </label>
                                                            </div>
                                                            </div>
                                                            <div class="col-md-2 col-12 mb-md-0 mb-4">
                                                                <p class="h6 mb-1 itemNumber">Item
                                                                    #<?= ++$itemCount; ?></p>
                                                                <select name="serviceitem" class="serviceitem form-control mb-5" data-selected="<?= $item['itemId'] ?>">
                                                                    <option value="">Service / Product</option>
                                                                    <?php
                                                                    $selected = "";
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-2 col-12 mb-md-0 mb-4">
                                                                <p class="h6 mb-1">Description</p>
                                                                <input name="serviceitemdescription" type="text" class="form-control mb-5" value="<?= getItemDescriptionForConsultationServiceItemID($item['itemId']) ?>"/>
                                                            </div>
                                                            <div class="col-md-1 col-12 mb-md-0 mb-4">
                                                                <p class="h6 mb-1">Qty</p>
                                                                <input name="qty" type="text" class="form-control numbers-only calculation-trigger" placeholder="Qty" min="1" required value="<?= $item['quantity'] ?>"/>
                                                            </div>
                                                            <div class="col-md-1 col-12 mb-md-0 mb-4">
                                                                <p class="h6 mb-1">UOM</p>
                                                                <input name="uom" type="text" class="form-control mb-6 calculation-trigger" data-placeholder="EACH"  value="<?= $item['UOM'] ?>" />

                                                            </div>
                                                            <div class="col-md-1 col-12 mb-md-0 mb-4">
                                                                <p class="h6 mb-1">Unit Price</p>
                                                                <input name="price" type="text" class="form-control numbers-only calculation-trigger" placeholder="" value="<?= $item['unitPrice'] ?>"/>
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
                                                        <div class="d-flex flex-column align-items-center justify-content-between border-start p-2 deleteItemCrossButton">
                                                            <i class="ti ti-x ti-lg cursor-pointer" data-repeater-delete></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                            }

                                            //If the itemCount is zero, it means that, on edit page, there are previous items. So the item template would have not been populated leaving the add item button, not to function. So the below snippet is necessary.
                                            if ($itemCount == 0) {
                                                ?>
                                                <div class="repeater-wrapper pt-0 pt-md-6" data-repeater-item>
                                                    <div class="d-flex border rounded position-relative pe-0">
                                                        <div class="row w-100 p-3">
                                                            <div class="col-md-2 col-12 mb-md-0 mb-4">
                                                                <p class="h6 mb-1 itemNumber">Item #1</p>
                                                                <select name="serviceitem" class="serviceitem form-control mb-5"">
                                                                <option value="">Service / Product</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-2 col-12 mb-md-0 mb-4">
                                                                <p class="h6 mb-1">Description</p>
                                                                <input name="serviceitemdescription" type="text" class="form-control mb-5" />
                                                            </div>
                                                            <div class="col-md-1 col-12 mb-md-0 mb-4">
                                                                <p class="h6 mb-1">Qty</p>
                                                                <input name="qty" type="text" class="form-control numbers-only calculation-trigger" placeholder="Qty" min="1" required />
                                                            </div>
                                                            <div class="col-md-1 col-12 mb-md-0 mb-4">
                                                                <p class="h6 mb-1">UOM</p>
                                                                <input name="uom" type="text" class="form-control mb-6 calculation-trigger" data-placeholder="LOT" />

                                                            </div>
                                                            <div class="col-md-1 col-12 mb-md-0 mb-4">
                                                                <p class="h6 mb-1">Unit Price</p>
                                                                <input name="price" type="text" class="form-control numbers-only calculation-trigger" placeholder="" />
                                                            </div>
                                                            <div class="col-md-1 col-12 mb-md-0 mb-4">
                                                                <p class="h6 mb-1">VAT %</p>
                                                                <input name="vatPercentage" type="text" class="form-control numbers-only calculation-trigger" placeholder="" />
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
                                                        <div class="d-flex flex-column align-items-center justify-content-between border-start p-2 deleteItemCrossButton">
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
                                                <button type="button" class="btn btn-sm btn-primary addItemButton" data-repeater-create>
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
<div class="modal fade" id="createProjectModal" tabindex="-1" aria-hidden=true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createProjectModalLabel">Create Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="projectTitle" class="form-label"> Project Title</label>
                        <input type="text" class="form-control" id="projectTitle" placeholder="Project Title" required>
                    </div>
                    <div class="col-md-6">
                        <label for="projectHead" class="form-label">Project Head</label>
                        <select id="projectHeadId" class="form-select selectpicker w-100 mb-4" data-style="btn-default" data-live-search="true" tabindex="null" required>
                            <option value="">Select Project Head</option>
                            <?php
                            $companyId = $_SESSION['SES_SELECTED_COMPANY'];
                            $res = $db->query("SELECT u.userID, u.firstName, u.lastName 
                                                  FROM users u
                                                  INNER JOIN user_company_mapping ucm ON u.userID = ucm.userId
                                                  WHERE u.active = 1 AND ucm.companyId = ?i
                                                  ORDER BY u.firstName ASC", $companyId);
                            while ($row = mysqli_fetch_assoc($res)) {
                                ?>
                                <option value="<?= $row['userID'] ?>"><?= $row['firstName'] . ' ' . $row['lastName'] ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="startDate" class="form-label"> Project Start Date</label>
                        <input id="startDate" type="text" class="form-control gmm-date-format" placeholder="DD - MMM - YYYY" value="<?php echo date('Y-m-d'); ?>"/>
                    </div>
                    <div class="col-md-6">
                        <label for="endDate" class="form-label"> Project End Date</label>
                        <input id="endDate" type="text" class="form-control gmm-date-format" placeholder="DD - MMM - YYYY" value="<?php echo date('Y-m-d'); ?>"/>
                    </div>
                </div>
                <div class="row mt-4">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Item #</th>
                            <th>Item Name</th>
                            <th>Description</th>
                            <th>Quantity</th>
                        </tr>
                        </thead>
                        <tbody id="selectedItemsTableBody">
                        <!-- Populated dynamically by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Discard</button>
                <button type="button" class="btn btn-primary" onclick="createProject()">Create</button>
            </div>
        </div>
    </div>
</div>
<!-- / Layout wrapper  arafat-->
<?php include_once __DIR__ . "/../../includes/dashboard/dashboard_footer_scripts.php"; ?>

<!-- Page JS arafataraft -->
<script src="/assets/js/offcanvas-send-invoice.js"></script>

<script>
    let quotationStatus = '<?=$keyDocument->quotationStatus ?>';
    let alreadyExistingItemCount = <?=$itemCount ?>;

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
                },

                error: function (error) {
                    unBlockArea($('.invoice-preview-header'));
                    console.log(error);
                }
            });

        });

        <?php
        if(
        ($currentUserRole != ROLE_SUPERADMIN) &&
        (
            ($currentUserRole != ROLE_SALES_EXECUTIVE) ||
            (!in_array($documentStatus, [QUOTATION_STATUS_NEW, QUOTATION_STATUS_REJECTED, QUOTATION_STATUS_CUSTOMER_REJECTED]))
        )
        )
        {
        ?>
        checkStatusAndDisableQuotationEditing();
        <?php
        }
        ?>
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

    function checkBoxDataID() {
        let selectedItems = $('.line-item-checkbox:checked').map(function() {
            let $row = $(this).closest('.repeater-wrapper');
            return {
                lineItemId: $(this).data('item-id'),
                itemName: $(this).data('item-name'),
                description: $(this).data('item-description'),
                quantity: $(this).data('item-quantity')
            };
        }).get();

        console.log('Selected Items:', selectedItems);

        if (selectedItems.length === 0) {
            showErrorMessage('Please select at least one line item to create a project.');
            return;
        }

        // Populate modal table
        let tableBody = $('#selectedItemsTableBody');
        tableBody.empty();
        selectedItems.forEach((item, index) => {
            tableBody.append(`
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.itemName}</td>
                    <td>${item.description}</td>
                    <td>${item.quantity}</td>
                </tr>
            `);
        });

        // Show modal
        $('#createProjectModal').modal('show');
    }

    function createProject() {
        let projectHeadId = $('#projectHeadId').val();
        let projectTitle = $('#projectTitle').val();
        let documentId = '<?php echo $documentID; ?>'; 
        let customerId = '<?php echo $documentInfo['customerId']; ?>'; 
        let startDate = $('#startDate').val();
        let endDate = $('#endDate').val();
        let selectedItems = $('.line-item-checkbox:checked').map(function() {
            return $(this).data('item-id');
        }).get();

        if(!projectTitle) {
            showErrorMessage('Please Enter a Suitable Title for the project');
            return;
        }

        if (!projectHeadId) {
            showErrorMessage('Please select a project head.');
            return;
        }

        if(!startDate || !endDate) {
            showErrorMessage('Please select start and end dates for the project.');
            return;
        }

        // Prepare FormData for AJAX
        var formData = new FormData();
        formData.append('documentId', documentId);
        formData.append('customerId', customerId);
        formData.append('projectTitle', projectTitle);
        formData.append('projectHeadId', projectHeadId);
        formData.append('startDate', startDate);
        formData.append('endDate', endDate);
        formData.append('lineItemIds', JSON.stringify(selectedItems));

        $.ajax({
            url: '/ajax/consultation/save_project.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(data) {
                console.log(data);
                var result = JSON.parse(data);
                if (result.status === 'SUCCESS') {
                    showSuccessMessage(result.message, gotoPage, "/consultation/salesorders/edit/" + documentId);
                    $('#createProjectModal').modal('hide');
                } else {
                    showErrorMessage(result.message);
                }
            },
            error: function(error) {
                console.log(error);
                showErrorMessage('An error occurred while creating the project.');
            }
        });
    }

    function generateInvoice() {
      // Get the selected item IDs from checked checkboxes
      let selectedItems = $('.line-item-checkbox:checked').map(function() {
        return $(this).data('item-id');
      }).get();

      let documentID = $('#documentId').val();

      console.log('Invoice button clicked for itemIds:', selectedItems);
      console.log('DocumentID:', documentID);

      if (selectedItems.length === 0) {
        console.log('No items selected');
        showErrorMessage('Please select at least one item to generate an invoice.');
        return;
      }

      // Create a FormData object
      let formData = new FormData();
      formData.append('itemIds', JSON.stringify(selectedItems));
      formData.append('documentID', documentID);

      let form = $('<form>', {
        action: '/consultation/invoice/new?refDocID=<?=$documentID?>', // URL of the new invoice page
        method: 'POST',
        target: '_blank' // Opens in a new tab
      });

      // Append FormData entries as hidden inputs
      form.append($('<input>', {
        type: 'hidden',
        name: 'documentID',
        value: documentID
      }));
      form.append($('<input>', {
        type: 'hidden',
        name: 'itemIds',
        value: JSON.stringify(selectedItems)
      }));

      // Append form to body and submit
      $('body').append(form);
      form.submit();
      form.remove(); // Clean up after submission
    }


    function checkStatusAndDisableQuotationEditing() {

        $(".invoice-preview-card input, .invoice-preview-card select").prop("disabled", true);
        $(".invoice-preview-card select.selectpicker").selectpicker('refresh').prop("disabled", true);
        $(".addItemButton").prop("disabled", true);
        $(".deleteItemCrossButton").attr("style", "display: none !important;");

        <?php
        if(
        ($currentUserRole == ROLE_SALES_EXECUTIVE || $currentUserRole == ROLE_SUPERADMIN) &&
        in_array($documentStatus, [QUOTATION_STATUS_CUSTOMER_ACCEPTED, QUOTATION_STATUS_ACCOUNTANT_SO_REJECTED, QUOTATION_STATUS_SALES_MANAGER_SO_REJECTED])
        )
        {
        ?>
        $("#customerPO").prop("disabled", false);
        $("#poAttachmentFile").prop("disabled", false);

        $("#customerPO").attr("style", "background-color: white;");
        $("#poAttachmentFile").attr("style", "background-color: white;");

        <?php
        }
        ?>


    }

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
        console.log("requestData");
        console.log(requestData);

        return requestData;
    }


    function saveQuotation() {

        blockArea($('body'));

        let requestData = prepareQuotationData();
        console.log(requestData);

        $.ajax({
            url: '/ajax/consultation/save_quotation.php', // Update with your PHP script URL
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
                    $("#documentId").val(response.documentId);

                    showSuccessMessage(response.message);

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
        saveQuotationThread('<?=QUOTATION_STATUS_AWAITING_APPROVAL ?>');
    }


    function sendForSOApproval() {
        let customerPO = $("#customerPO").val();

        if (customerPO.length > 3) {
            // Check if the PO attachment file is already uploaded or selected
            if ($("#poAttachmentFileName").val().length > 0) {
                // Proceed with SO Approval if attachment is already there
                saveQuotationThread('<?=QUOTATION_STATUS_AWAITING_SALES_MANAGER_SO_APPROVAL ?>');
            } else {
                var fileInput = document.getElementById('poAttachmentFile');

                if (fileInput.files.length === 0) {
                    // No PO attachment file selected, ask user to confirm
                    Swal.fire({
                        title: 'No PO attachment found. Do you want to proceed without it?',
                        icon: 'question',
                        text: "Customer PO is not attached.",
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Proceed',
                        cancelButtonText: 'No, Go Back',
                        customClass: {
                            confirmButton: 'btn btn-primary',
                            cancelButton: 'btn btn-secondary'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // User confirmed to proceed without attachment
                            saveQuotationThread('<?=QUOTATION_STATUS_AWAITING_SALES_MANAGER_SO_APPROVAL ?>');
                        } else {
                            console.log('User chose to not proceed without PO attachment.');
                        }
                    });
                } else {
                    // Proceed with file upload if attachment is selected
                    var file = fileInput.files[0];
                    var formData = new FormData();
                    formData.append('poAttachmentFile', file);

                    // Make the AJAX request
                    $.ajax({
                        url: '/ajax/file_upload.php',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            if (typeof response === 'string') {
                                response = JSON.parse(response);
                            }
                            if (response.status === "success") {
                                $("#poAttachmentFileName").val(response.message);
                                saveQuotationThread('<?=QUOTATION_STATUS_AWAITING_SALES_MANAGER_SO_APPROVAL ?>');
                            }
                            console.log(response);
                        },
                        error: function (xhr, status, error) {
                            console.error(error);
                        }
                    });
                }
            }
        } else {
            // Customer PO number not entered, show confirmation to proceed without it
            Swal.fire({
                title: 'Customer PO Number not entered! Do you want to proceed without it?',
                icon: 'question',
                text: "The Customer PO Number is not entered.",
                showCancelButton: true,
                confirmButtonText: 'Yes, Proceed',
                cancelButtonText: 'No, Go Back',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // User confirmed to proceed without PO number
                    saveQuotationThread('<?=QUOTATION_STATUS_AWAITING_SALES_MANAGER_SO_APPROVAL ?>');
                } else {
                    console.log('User chose not to proceed without PO number.');
                }
            });
        }
    }


    function approveQuotation() {
        let documentID = '<?=$documentID; ?>';
        updateQuotationStatus(documentID, "<?=QUOTATION_STATUS_APPROVED ?>");
    }

    function approveQuotationSOSM() {
        let documentID = '<?=$documentID; ?>';
        updateQuotationStatus(documentID, "<?=QUOTATION_STATUS_AWAITING_ACCOUNTANT_SO_APPROVAL ?>");
    }

    function approveQuotationSOAccountant() {
        let documentID = '<?=$documentID; ?>';
        updateQuotationStatus(documentID, "<?=QUOTATION_STATUS_SO_APPROVED ?>");
    }

    function confirmQuotationAndGenerateSalesOrder() {
        let documentID = '<?=$documentID; ?>';
        updateQuotationStatus(documentID, "<?=QUOTATION_STATUS_CONFIRMED ?>");
    }

    function rejectQuotation() {
        let documentID = '<?=$documentID; ?>';

        let rejectReason = $("#rejectReason").val();
        if (rejectReason.length) {
            updateQuotationStatus(documentID, "<?=QUOTATION_STATUS_REJECTED ?>", true, rejectReason);
        } else {
            showErrorMessage("You must enter a reason for rejection");
        }
    }

    function rejectQuotationSMSO() {
        let documentID = '<?=$documentID; ?>';
        let rejectReason = $("#rejectReason").val();
        if (rejectReason.length) {
            updateQuotationStatus(documentID, "<?=QUOTATION_STATUS_SALES_MANAGER_SO_REJECTED ?>", true, rejectReason);
        } else {
            showErrorMessage("You must enter a reason for rejection");
        }
    }

    function rejectQuotationAccountantSO() {
        let documentID = '<?=$documentID; ?>';
        let rejectReason = $("#rejectReason").val();
        if (rejectReason.length) {
            updateQuotationStatus(documentID, "<?=QUOTATION_STATUS_ACCOUNTANT_SO_REJECTED ?>", true, rejectReason);
        } else {
            showErrorMessage("You must enter a reason for rejection");
        }
    }


    function saveQuotationThread(status) {

        //Then proceed with saving the quotaion.
        if ($('#customer').val().length > 0) {
            // blockArea($('body'));
            let requestData = prepareQuotationData();
            $.ajax({
                url: '/ajax/consultation/save_quotation.php', // Update with your PHP script URL
                method: 'POST',
                data: JSON.stringify(requestData), // Send the combined data as a JSON string
                contentType: 'application/json', // Indicate that the data is JSON
                dataType: 'json', // Expect a JSON response
                success: function (response) {
                    console.log('Server Response after saving quotation:', response);

                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }

                    console.log("REsponse: " + response.status);
                    // Check the response status
                    if (response.status === 'success') {
                        unBlockArea($('body'));
                        let documentID = response.documentId;
                        updateQuotationStatus(documentID, status);
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
            showErrorMessage("Nothing to save.");
        }
    }

    function updateQuotationStatus(documentID, status, showMessage = true, rejectReason = "") {

        console.log("Updating QuotationStatus: " + documentID + " - " + status + " - " + showMessage + " - " + rejectReason);

        var formData = new FormData();
        formData.append("documentID", documentID);
        formData.append("status", status);
        formData.append("rejectReason", rejectReason);


        $.ajax({
            url: '/ajax/consultation/update_quotation_status.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (data, status) {
                console.log(data);

                var statusmessage = data.trim().split("|")[0];
                var message = data.trim().split("|")[1];

                if (statusmessage == "SUCCESS") {
                    if (showMessage)
                        showSuccessMessage(message, gotoPage, "/consultation/quotation/edit/" + documentID);
                }
            },

            error: function (error) {
                console.log(error);
            }
        });

    }


    function sendQuotationEmail() {
        let documentID = '<?=$documentID ?>';

        var form = $('#sendQuotationEmailForm')[0]; // You need to use standard javascript object here
        var formData = new FormData(form);
        formData.append("documentID", documentID);

        $.ajax({
            url: '/ajax/consultation/send_email.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (data, status) {
                console.log(data);
                var statusmessage = data.trim().split("|")[0];
                var message = data.trim().split("|")[1];

                if (statusmessage == "SUCCESS") {
                    //updateQuotationStatus(documentID, '<?php //=QUOTATION_STATUS_SENT_TO_CUSTOMER ?>//', false);
                    // showSuccessMessage("Email sent to customer.", gotoPage, "/consultation/quotation/edit/" + documentID);
                }
            },

            error: function (error) {
                console.log(error);
            }
        });

    }


    function printQuotation() {
        // Get the base URL of the current page
        const baseUrl = window.location.origin;
        // Define the relative URL to open
        const relativeUrl = '/ajax/consultation/generate_pdf.php?documentID=' + '<?=$documentID ?>';
        // Combine the base URL and relative URL
        const fullUrl = baseUrl + relativeUrl;
        // Open the URL in a new tab
        window.open(fullUrl, '_blank');

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
                    doCalculation();
                },
                defaultValues: {
                    "eta": "<?=date('Y-m-d', strtotime('+14 days')) ?>"
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
                    if(fp.length > 0)
                        fp.clear();

                    //Initialize selectpicker
                    const serviceitem = $('[name="line-item[' + index + '][serviceitem]"]');
                    initializeSelect2(serviceitem, null);

                },
                hide: function (remove) {
                    $(this).slideUp(remove);
                },


            });
        }
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
    $(document).on('select2:select', '.serviceitem', function (e) {
        e.stopPropagation();
        console.log("Spare part Item Changed");
        const selectedData = e.params.data;
        console.log("Selected Data : " + selectedData);
        console.log( selectedData);
        try {

            /*id: item.id,
            text: item.text,
            desc: item.desc,
            price: item.price,
            cost: item.cost,
            uom: item.uom*/
            const selectedDesc = selectedData.desc;
            const selectedUnitPrice = selectedData.price;
            const selectedUOM = selectedData.uom;

            const index = getRepeaterRowIndex($(this));
            const serviceItemdescription = getElementByIndexAndName(index, 'serviceitemdescription');
            serviceItemdescription.val(selectedDesc);

            const qty = getElementByIndexAndName(index, 'qty');
            qty.val('1');

            const price = getElementByIndexAndName(index, 'price');
            price.val(selectedUnitPrice);

            const vatPercentage = getElementByIndexAndName(index, 'vatPercentage');
            vatPercentage.val("15");

            const discountPercentage = getElementByIndexAndName(index, 'discountPercentage');
            discountPercentage.val('0');

            const discountAmount = getElementByIndexAndName(index, 'discountAmount');
            discountAmount.val('0.00');

            const uom = getElementByIndexAndName(index, 'uom');
            $(uom).val(selectedUOM);


            doCalculation();
        } catch (err) {
            console.log(err);
        }


    });

    $(document).on('change', '.calculation-trigger', function (e) {
        doCalculation();
    });

    function doCalculation() {
        const totalItemRows = getRepeaterRowCount();

        let calculationCheck = getElementByIndexAndName(0, 'qty').val();

        if (calculationCheck > 0) {

            let rowSubTotal = 0;

            let totalAmountBeforeVAT = 0.00;
            let totalDiscountAmount = 0.00;
            let totalVATAmount = 0.00;
            let totalAmountAfterVAT = 0.00;

            for (let index = 0; index < totalItemRows; index++) {
                let qty = getElementByIndexAndName(index, 'qty').val();
                let unitPrice = getElementByIndexAndName(index, 'price').val();
                let vatPercentage = getElementByIndexAndName(index, 'vatPercentage').val();
                let discountPercentage = getElementByIndexAndName(index, 'discountPercentage').val();


                // Calculate base subtotal
                let baseSubtotal = qty * unitPrice;

                // Apply discount
                let discountAmount = baseSubtotal * (discountPercentage / 100);
                let discountedSubtotal = baseSubtotal - (baseSubtotal * (discountPercentage / 100));


                // Calculate VAT amount (based on the discounted subtotal)
                let vatAmount = discountedSubtotal * (vatPercentage / 100);


                //Add totals
                totalAmountBeforeVAT += discountedSubtotal;
                totalDiscountAmount += discountAmount;
                totalVATAmount += vatAmount;
                totalAmountAfterVAT += (parseFloat(vatAmount) + parseFloat(discountedSubtotal));


                // Set values to respective fields
                getElementByIndexAndName(index, 'subTotal').val(toTwoDecimal(discountedSubtotal)); // Set the discounted subtotal
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
    }



    const serviceItemsOptions = [
        <?php
        $res = $db->query("SELECT * FROM consultation_services_items WHERE active = 1");
        while ($row = mysqli_fetch_assoc($res)) {
        // Safely encode values to prevent JS errors
        $itemID = json_encode($row['itemId']);
        $itemName = json_encode($row['itemName']);
        $description = json_encode($row['description']);
        $uom = json_encode($row['uom']);
        $categoryId = json_encode($row['categoryId']);
        $price = json_encode($row['price']);
        $cost = json_encode($row['cost']);
        ?>
        {
            id: <?= $itemID ?>,
            text: <?= $itemName ?>,
            desc: <?= $description ?>,
            uom: <?= $uom ?>,
            price: <?= $price ?>,
            cost: <?= $cost ?>
        },
        <?php
        }
        ?>
    ];

    // Function to fetch filtered results
    function fetchFilteredResults(query) {
        const filtered = serviceItemsOptions.filter(option => {
            const textMatch = option.text.toLowerCase().includes(query.toLowerCase());
            const descMatch = option.desc.toLowerCase().includes(query.toLowerCase());
            return textMatch || descMatch;
        });
        return filtered.slice(0, 50); // Limit results to the first 10 matches
    }

    // Initialize Select2 with AJAX
    function initializeSelect2(selectElement, preselectedValue = null) {
        console.log("preselectedValue: " + preselectedValue);

        // Initialize Select2
        $(selectElement).select2({
            placeholder: "Service / Product",
            minimumInputLength: 0,
            ajax: {
                transport: function (params, success, failure) {
                    const query = params.data.term || ""; // Search term
                    const results = fetchFilteredResults(query); // Filter and limit results
                    success({
                        results: results.map(item => ({
                            id: item.id,
                            text: item.text,
                            desc: item.desc,
                            price: item.price,
                            cost: item.cost,
                            uom: item.uom
                        }))
                    });
                }
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

        // If a preselected value is provided, add it to the dropdown
        if (preselectedValue) {
            const preselectedItem = serviceItemsOptions.find(option => option.id == preselectedValue);
            if (preselectedItem) {
                // Add the preselected item as an option
                const newOption = new Option(preselectedItem.text, preselectedItem.id, true, true);
                $(selectElement).append(newOption).trigger('change'); // Append and trigger change event
                // $(selectElement).append(newOption); // Append and trigger change event
            }
        }
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