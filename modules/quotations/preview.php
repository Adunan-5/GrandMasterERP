<?php
$PAGE_ID = "QUOTATION_PREVIEW";
include_once __DIR__ . "/../../includes/baseIncludes.php";
include_once __DIR__ . "/../../includes/auth_check.php";

$documentID = "";
$documentID = filter_input(INPUT_GET, 'documentID', FILTER_VALIDATE_INT);
if ($documentID === null || $documentID === false || filter_var($documentID, FILTER_VALIDATE_INT) === false) {
    header("location:/quotation/list");
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
    <!-- Page CSS -->
    <link rel="stylesheet" href="/assets/vendor/css/pages/app-invoice.css"/>
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
                                    <div class="col-6">
                                        <?php

                                        $keyDocument = new KeyDocument();
                                        $keyDocument->loadById($documentID);

                                        $documentInfo = '';
                                        $lineItems    = array();
                                        $res          = $db->query("SELECT * FROM key_documents WHERE documentId = ?s", $documentID);
                                        $documentInfo = mysqli_fetch_assoc($res);

                                        $res = $db->query("SELECT * FROM line_items WHERE documentId = ?s", $documentID);
                                        while ($row = mysqli_fetch_assoc($res)) {
                                            $lineItems[] = $row;
                                        }

                                        ?>
                                        <h4 class="my-0"><?= getQuotationNumberFromDocumentID($documentID) ?> | Preview Quotation </h4>
                                    </div>
                                    <div class="col-6 text-end">
                                        <?php
                                        switch ($keyDocument->quotationStatus) {
                                            case QUOTATION_STATUS_NEW:
                                                ?>
                                                <button type="button" class="btn btn-label-linkedin text-nowrap d-inline-flex position-relative me-4">
                                                    DRAFT
                                                </button>
                                                <?php
                                                break;

                                            case QUOTATION_STATUS_APPROVED:
                                                ?>
                                                <button type="button" class="btn btn-label-success text-nowrap d-inline-flex position-relative me-4">
                                                    APPROVED
                                                </button>
                                                <?php
                                                break;

                                            case QUOTATION_STATUS_REJECTED:
                                                ?>
                                                <button type="button" class="btn btn-label-danger text-nowrap d-inline-flex position-relative me-4">
                                                    REJECTED
                                                </button>
                                                <?php
                                                break;

                                            default:
                                                ?>
                                                <button type="button" class="btn btn-label-info text-nowrap d-inline-flex position-relative me-4">
                                                    <?= $keyDocument->quotationStatus ?>
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

                    $companyInfo = new Company();
                    $companyInfo->loadById(1);

                    $keyDocument = new KeyDocument();
                    $keyDocument->loadById($documentID);

                    $customerInfo = new Customer();
                    $customerInfo->loadById($keyDocument->customerId);

                    ?>

                    <div class="row invoice-preview">
                        <!-- Invoice -->
                        <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-6">
                            <div class="card invoice-preview-card p-sm-12 p-6">
                                <div class="card-body invoice-preview-header rounded">
                                    <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                                        <div class="mb-xl-0 mb-6 text-heading">
                                            <div class="d-flex svg-illustration mb-6 gap-2 align-items-center">
<!--                                                <div class="app-brand-logo demo">-->
                                                <div class="app-brand-logo demo">
                                                    <img src="/assets/img/branding/G-Logo.png" alt="" width="14px">
                                                </div>
                                                <span class="app-brand-text fw-bold fs-4 ms-50"> GrandMaster</span>
                                            </div>

                                            <p class="mb-2"><strong><?=$companyInfo->companyName ?></strong></p>
                                            <p class="mb-2"><strong><?=$companyInfo->companyNameAr ?></strong></p>
                                            <p class="mb-2"><?=$companyInfo->addressLine1 ?></p>
                                            <p class="mb-2"><?=$companyInfo->addressLine2 ?></p>
                                            <p class="mb-2"><?=getCityFromID($companyInfo->cityId)?>, <?=$companyInfo->postalCode ?>, <?=getStateFromID($companyInfo->stateId) ?>, <?=getCountryFromID($companyInfo->countryId) ?></p>
                                            <p class="mb-2">VAT: <?=$companyInfo->vatNumber ?>, CR: <?=$companyInfo->companyCRNumber ?></p>
<!--                                            <p class="mb-0">+1 (123) 456 7891, +44 (876) 543 2198</p>-->
                                        </div>
                                        <div>
                                            <h5 class="mb-6">Quotation #<?=getQuotationNumberFromDocumentID($keyDocument->documentId) ?></h5>
                                            <div class="mb-1 text-heading">
                                                <span>Date Issued:</span> <span><?=formatDate($keyDocument->quotationDateIssued, false) ?></span>
                                            </div>
                                            <div class="mb-1 text-heading">
                                                <span>Date Due:</span> <span><?=formatDate($keyDocument->quotationDateExpiry, false) ?></span>
                                            </div>
                                            <div class="mb-1 text-heading">
                                                <span>Payment Terms:</span> <span><?=$keyDocument->getPaymentTermName() ?></span>
                                            </div> <div class="mb-1 text-heading">
                                                <span>Sales Person:</span> <span><?=getDisplayNameFromUserID($keyDocument->salesPersonId) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body px-0">
                                    <div class="row">
                                        <div class="col-xl-6 col-md-12 col-sm-5 col-12 mb-xl-0 mb-md-6 mb-sm-0 mb-6">
                                            <h6>Quotation For:</h6>
                                            <p class="mb-1"><?=$customerInfo->companyName ?></p>
                                            <p class="mb-1"><?=$customerInfo->companyNameAr ?></p>
                                            <p class="mb-1"><?=getFormattedAddress($customerInfo) ?></p>
                                            <p class="mb-1"><?=$customerInfo->phone ?></p>
                                            <p class="mb-1"><?=$customerInfo->email ?></p>

                                        </div>

                                    </div>
                                </div>
                                <div class="table-responsive border border-bottom-0 border-top-0 rounded">
                                    <table class="table m-0">
                                        <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Item No.</th>
                                            <th>Description</th>
                                            <th>Unit</th>
                                            <th>Qty</th>
                                            <th>Unit Price</th>
                                            <th>VAT</th>
                                            <th>Total Value</th>
                                            <th>ETA</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        <?php
                                        $resLineItems = $db->query("SELECT * FROM line_items WHERE `documentId` = ?s", $keyDocument->documentId);
                                        $itemSerialNumber = 0;
                                        while($rowLineItem=mysqli_fetch_assoc($resLineItems))
                                        {
                                        	?>
                                            <tr>
                                                <td><?=++$itemSerialNumber ?></td>
                                                <td class="text-nowrap"><?=getPartNumberForSparepartID($rowLineItem['itemId']) ?></td>
                                                <td class="text-nowrap text-heading"><?=getItemDescriptionForSparepartID($rowLineItem['itemId']) ?></td>
                                                <td><?=getUOMNameFromID($rowLineItem['UOM']) ?></td>
                                                <td><?=$rowLineItem['quantity'] ?></td>
                                                <td><?=$rowLineItem['unitPrice'] ?></td>
                                                <td><?=$rowLineItem['vatAmount'] ?></td>
                                                <td><?=$rowLineItem['subTotal'] ?></td>
                                                <td><?=formatDate($rowLineItem['eta'], false) ?></td>
                                            </tr>

                                            <?php
                                        }
                                        ?>


                                        </tbody>
                                    </table>
                                </div>
                                <div class="table-responsive">
                                    <table class="table m-0 table-borderless">
                                        <tbody>
                                        <tr>
                                            <td class="align-top pe-6 ps-0 py-6">
<!--                                                <p class="mb-1">-->
<!--                                                    <span class="me-2 h6">Salesperson:</span>-->
<!--                                                    <span>Alfie Solomons</span>-->
<!--                                                </p>-->
<!--                                                <span>Thanks for your business</span>-->
                                            </td>
                                            <td class="px-0 py-6 w-px-100">
                                                <p class="mb-2">Subtotal:</p>
                                                <p class="mb-2">Discount:</p>
                                                <p class="mb-2 border-bottom pb-2">Tax:</p>
                                                <p class="mb-0 pt-2">Total:</p>
                                            </td>
                                            <td class="text-end px-0 py-6 w-px-100 fw-medium text-heading">
                                                <p class="fw-medium mb-2"><?=getTotalAmountByDocumentID($keyDocument->documentId)['totalAmountBeforeVAT'] ?></p>
                                                <p class="fw-medium mb-2"><?=getTotalAmountByDocumentID($keyDocument->documentId)['totalDiscountAmount'] ?></p>
                                                <p class="fw-medium mb-2 border-bottom pb-2"><?=getTotalAmountByDocumentID($keyDocument->documentId)['totalVATAmount'] ?></p>
                                                <p class="fw-medium mb-0 pt-2"><?=getTotalAmountByDocumentID($keyDocument->documentId)['totalAmountAfterVAT'] ?></p>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <hr class="mt-0 mb-6"/>
                                <div class="card-body p-0">
                                    <div class="row">
                                        <div class="col-12">
<!--                                            <span class="fw-medium text-heading">Note:</span> <span>It was a pleasure working with you and your team. We hope you will keep us in mind for-->
<!--                            future freelance projects. Thank You!</span>-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Invoice -->
                        <!-- Invoice Actions -->
                        <div class="col-xl-3 col-md-4 col-12 invoice-actions">
                            <div class="card">
                                <div class="card-body">
                                    <button class="btn btn-primary d-grid w-100 mb-4" data-bs-toggle="offcanvas" data-bs-target="#sendInvoiceOffcanvas">
                                        <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-send ti-xs me-2"></i>Send Invoice</span>
                                    </button>
                                    <button class="btn btn-label-secondary d-grid w-100 mb-4">Download</button>
                                    <div class="d-flex mb-4">
                                        <a class="btn btn-label-secondary d-grid w-100 me-4" target="_blank" href="./app-invoice-print.html">
                                            Print </a>
                                        <a href="/quotation/edit/<?=$keyDocument->documentId?>" class="btn btn-label-secondary d-grid w-100">
                                            Edit </a>
                                    </div>
                                    <button class="btn btn-success d-grid w-100" data-bs-toggle="offcanvas" data-bs-target="#addPaymentOffcanvas">
                                        <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-currency-dollar ti-xs me-2"></i>Add Payment</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- /Invoice Actions -->
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
                    <!-- Add Payment Sidebar -->
                    <div class="offcanvas offcanvas-end" id="addPaymentOffcanvas" aria-hidden="true">
                        <div class="offcanvas-header border-bottom">
                            <h5 class="offcanvas-title">Add Payment</h5>
                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body flex-grow-1">
                            <div class="d-flex justify-content-between bg-lighter p-2 mb-4">
                                <p class="mb-0">Invoice Balance:</p>
                                <p class="fw-medium mb-0">$5000.00</p>
                            </div>
                            <form>
                                <div class="mb-6">
                                    <label class="form-label" for="invoiceAmount">Payment Amount</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="text" id="invoiceAmount" name="invoiceAmount" class="form-control invoice-amount" placeholder="100"/>
                                    </div>
                                </div>
                                <div class="mb-6">
                                    <label class="form-label" for="payment-date">Payment Date</label>
                                    <input id="payment-date" class="form-control invoice-date" type="text"/>
                                </div>
                                <div class="mb-6">
                                    <label class="form-label" for="payment-method">Payment Method</label>
                                    <select class="form-select" id="payment-method">
                                        <option value="" selected disabled>Select payment method</option>
                                        <option value="Cash">Cash</option>
                                        <option value="Bank Transfer">Bank Transfer</option>
                                        <option value="Debit Card">Debit Card</option>
                                        <option value="Credit Card">Credit Card</option>
                                        <option value="Paypal">Paypal</option>
                                    </select>
                                </div>
                                <div class="mb-6">
                                    <label class="form-label" for="payment-note">Internal Payment Note</label>
                                    <textarea class="form-control" id="payment-note" rows="2"></textarea>
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
                    <!-- /Add Payment Sidebar -->
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

</body>
</html>