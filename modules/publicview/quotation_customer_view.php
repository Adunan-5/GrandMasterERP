<?php
session_set_cookie_params(1800); // Cookie valid for 30 minutes

$PAGE_ID = "QUOTATION_CUSTOMER_VIEW";
include_once __DIR__ . "/../../includes/baseIncludes.php";

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
    <!-- Page CSS -->
    <link rel="stylesheet" href="/assets/vendor/css/pages/app-invoice.css"/>
    <link rel="stylesheet" href="/assets/vendor/libs/animate-css/animate.css"/>
    <link rel="stylesheet" href="/assets/vendor/libs/sweetalert2/sweetalert2.css"/>
    <?php include_once __DIR__ . "/../../includes/dashboard/dashboard_head_section_post_vendor.php"; ?>
    <title>GrandMaster ERP | Quotations</title>
</head>
<body>
<!-- Layout wrapper -->
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <!-- Layout container -->
        <div class="container-fluid">
<!--        <div>-->
            <!-- Content wrapper -->
            <div class="content-wrapper">
                <!-- Content -->
                <div class="container-fluid flex-grow-1 container-p-y">
                    <?php

                    $companyInfo = new Company();
                    $companyInfo->loadById(1);

                    $keyDocument = new KeyDocument();
                    $keyDocument->loadById($documentID);

                    $customerInfo = new Customer();
                    $customerInfo->loadById($keyDocument->customerId);

//                    unset($_SESSION[SES_CUSTOMER_AUTHENTICATED]);

                    $password                  = "";
                    $wrongPasswordAlertMessage = "";
                    if (isset($_POST['password'])) {
                        $password = filter_var($_POST['password'], FILTER_SANITIZE_SPECIAL_CHARS);
                        $password = strip_tags($password);



                        if ($keyDocument->customerPassword === $password) {
                            $_SESSION[SES_CUSTOMER_AUTHENTICATED] = true;
                        } else {
                            $_SESSION[SES_CUSTOMER_AUTHENTICATED] = false;
                            $wrongPasswordAlertMessage            = "Wrong Password. Try Again!";
                        }


                    }

                    ?>
                    <div class="row invoice-preview">
                        <!-- Invoice -->
                        <div class="col-xl-10 col-md-10 col-12 mb-md-0 mb-6">
                            <!--Heading Start-->
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
                                                <h4 class="my-0"><?= getQuotationNumberFromDocumentID($documentID) ?> </h4>
                                            </div>
                                            <div class="col-6 text-end">
                                                <?php
                                                switch ($keyDocument->quotationStatus) {
                                                    case QUOTATION_STATUS_SENT_TO_CUSTOMER:
                                                        ?>
                                                        <button type="button" class="btn btn-label-linkedin text-nowrap d-inline-flex position-relative me-4">
                                                            AWAITING ACCEPTANCE
                                                        </button>
                                                        <?php
                                                        break;

                                                    case QUOTATION_STATUS_CUSTOMER_ACCEPTED:
                                                        ?>
                                                        <button type="button" class="btn btn-label-success text-nowrap d-inline-flex position-relative me-4">
                                                            ACCEPTED
                                                        </button>
                                                        <?php
                                                        break;

                                                    case QUOTATION_STATUS_CUSTOMER_REJECTED:
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
                            <!--Heading End-->
                            <?php
                            $isCustomerAuthenticated = false;
                            if (isset($_SESSION[SES_CUSTOMER_AUTHENTICATED]) && $_SESSION[SES_CUSTOMER_AUTHENTICATED] === true) {
                                $isCustomerAuthenticated = true;
                            }

                            if ($isCustomerAuthenticated === true) {
                                ?>
                                <!--Invoice Body-->
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
                                                <p class="mb-2"><strong><?= $companyInfo->companyName ?></strong></p>
                                                <p class="mb-2"><strong><?= $companyInfo->companyNameAr ?></strong></p>
                                                <p class="mb-2"><?= $companyInfo->addressLine1 ?></p>
                                                <p class="mb-2"><?= $companyInfo->addressLine2 ?></p>
                                                <p class="mb-2"><?= getCityFromID($companyInfo->cityId) ?>
                                                    , <?= $companyInfo->postalCode ?>
                                                    , <?= getStateFromID($companyInfo->stateId) ?>
                                                    , <?= getCountryFromID($companyInfo->countryId) ?></p>
                                                <p class="mb-2">VAT: <?= $companyInfo->vatNumber ?>,
                                                    CR: <?= $companyInfo->companyCRNumber ?></p>
                                                <!--                                            <p class="mb-0">+1 (123) 456 7891, +44 (876) 543 2198</p>-->
                                            </div>
                                            <div>
                                                <h5 class="mb-6">Quotation
                                                    #<?= getQuotationNumberFromDocumentID($keyDocument->documentId) ?></h5>
                                                <div class="mb-1 text-heading">
                                                    <span>Date Issued:</span>
                                                    <span><?= formatDate($keyDocument->quotationDateIssued, false) ?></span>
                                                </div>
                                                <div class="mb-1 text-heading">
                                                    <span>Date Due:</span>
                                                    <span><?= formatDate($keyDocument->quotationDateExpiry, false) ?></span>
                                                </div>
                                                <div class="mb-1 text-heading">
                                                    <span>Payment Terms:</span>
                                                    <span><?= $keyDocument->getPaymentTermName() ?></span>
                                                </div>
                                                <div class="mb-1 text-heading">
                                                    <span>Sales Person:</span>
                                                    <span><?= getDisplayNameFromUserID($keyDocument->salesPersonId) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body px-0">
                                        <div class="row">
                                            <div class="col-xl-6 col-md-12 col-sm-5 col-12 mb-xl-0 mb-md-6 mb-sm-0 mb-6">
                                                <h6>Quotation For:</h6>
                                                <p class="mb-1"><?= $customerInfo->companyName ?></p>
                                                <p class="mb-1"><?= $customerInfo->companyNameAr ?></p>
                                                <p class="mb-1"><?= getFormattedAddress($customerInfo) ?></p>
                                                <p class="mb-1"><?= $customerInfo->phone ?></p>
                                                <p class="mb-1"><?= $customerInfo->email ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive border border-bottom-0 border-top-0 rounded">
                                        <table class="table m-0">
                                            <thead>
                                            <tr>
                                                <th>S.No.</th>
                                                <th>Item No.</th>
                                                <th style="width: 10%">Description</th>
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
                                            $resLineItems     = $db->query("SELECT * FROM line_items WHERE `documentId` = ?s", $keyDocument->documentId);
                                            $itemSerialNumber = 0;
                                            while ($rowLineItem = mysqli_fetch_assoc($resLineItems)) {
                                                ?>
                                                <tr>
                                                    <td width="20"><?= ++$itemSerialNumber ?></td>
                                                    <td width="20" class="text-nowrap"><?= getPartNumberForSparepartID($rowLineItem['itemId']) ?></td>
                                                    <td ><?= getItemDescriptionForSparepartID($rowLineItem['itemId']) ?></td>
                                                    <td><?= getUOMNameFromID($rowLineItem['UOM']) ?></td>
                                                    <td><?= $rowLineItem['quantity'] ?></td>
                                                    <td><?= $rowLineItem['unitPrice'] ?></td>
                                                    <td><?= $rowLineItem['vatAmount'] ?></td>
                                                    <td><?= $rowLineItem['subTotal'] ?></td>
                                                    <td><?= formatDate($rowLineItem['eta'], false) ?></td>
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
                                                    <p class="fw-medium mb-2"><?= getTotalAmountByDocumentID($keyDocument->documentId)['totalAmountBeforeVAT'] ?></p>
                                                    <p class="fw-medium mb-2"><?= getTotalAmountByDocumentID($keyDocument->documentId)['totalDiscountAmount'] ?></p>
                                                    <p class="fw-medium mb-2 border-bottom pb-2"><?= getTotalAmountByDocumentID($keyDocument->documentId)['totalVATAmount'] ?></p>
                                                    <p class="fw-medium mb-0 pt-2"><?= getTotalAmountByDocumentID($keyDocument->documentId)['totalAmountAfterVAT'] ?></p>
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
                                </div><!--Invoice Body-->
                                <?php
                            } else {
                                ?>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="alert alert-warning" role="alert">This quotation is password
                                            protected. Please enter the password received in your email.
                                        </div>
                                        <div class="d-flex align-items-center justify-content-center h-px-500">
                                            <form class="w-px-500 border rounded p-3 p-md-5" method="post" name="customerAuthorizeForm">
                                                <!--                                                <h3 class="mb-6">Authorize</h3>                                               -->
                                                <div class="row mb-6 form-password-toggle">
                                                    <?php if (strlen($wrongPasswordAlertMessage)) {
                                                        ?>
                                                        <div class="alert alert-danger" role="alert"><?= $wrongPasswordAlertMessage ?>
                                                        </div>
                                                        <?php
                                                    } ?>
                                                    <label class="col-sm-3 col-form-label" for="form-alignment-password">
                                                        Password
                                                    </label>
                                                    <div class="col-sm-9">
                                                        <div class="input-group input-group-merge">
                                                            <input type="password" id="password" name="password" class="form-control" placeholder="············" aria-describedby="form-alignment-password2">
                                                            <span class="input-group-text cursor-pointer" id="form-alignment-password2"><i class="ti ti-eye-off"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="d-grid">
                                                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                                                        Authorize
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <!-- /Invoice -->
                        <?php
                        if ($isCustomerAuthenticated) {
                            ?>
                            <!-- Invoice Actions -->
                            <div class="col-xl-2 col-md-2 col-12 invoice-actions">
                                <div class="card">
                                    <div class="card-body">
                                        <?php
                                        if($keyDocument->quotationAcceptedByCustomer == STATUS_GENERIC_APPROVED)
                                        {
                                            ?>
                                            <div class="alert alert-primary" role="alert">
                                                <p>You have already accepted this quotation. If you have any queries, please contact us.</p>
                                            </div>

                                            <?php
                                        }
                                        elseif($keyDocument->quotationAcceptedByCustomer == STATUS_GENERIC_REJECTED)
                                        {
                                            ?>
                                            <div class="alert alert-danger" role="alert">
                                                <p>You have rejected this quotation. If you have any queries, please contact us.</p>
                                            </div>
                                            <?php
                                        }else{
                                            ?>
                                            <button class="btn btn-success d-grid w-100 mb-4" id="acceptButton">
                                                <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-eye-check ti-xs me-2"></i>Accept</span>
                                            </button>
                                            <button class="btn btn-label-danger d-grid w-100 mb-4" id="rejectButton">
                                                <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-circle-dashed-x ti-xs me-2"></i>Reject</span>
                                            </button>
                                            <?php
                                        }

                                        if($keyDocument->quotationAcceptedByCustomer == STATUS_GENERIC_APPROVED || $keyDocument->quotationAcceptedByCustomer == STATUS_GENERIC_NEUTRAL)
                                        {
                                            ?>
                                            <div class="d-flex mb-1">
                                                <a class="btn btn-label-info d-grid w-100 me-1" target="_blank" href="/quotation/print/<?=encryptString($keyDocument->documentId) ?>">
                                                    <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-printer ti-xs me-2"></i>Print</span>
                                                </a>
                                                <a href="/quotation/download/<?=encryptString($keyDocument->documentId) ?>" class="btn btn-label-info d-grid w-100">
                                                    <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-download ti-xs me-2"></i>Download</span>
                                                </a>
                                            </div>
                                            <?php
                                        }
                                        ?>

<!--                                        <button class="btn btn-linkedin d-grid w-100">-->
<!--                                            <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-message ti-xs me-2"></i>Leave a message</span>-->
<!--                                        </button>-->
                                    </div>
                                </div>
                            </div><!-- /Invoice Actions -->
                            <?php
                        }
                        ?>
                    </div>
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

    <div class="modal fade" id="rejectReasonModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Reject Reason</h5>
                    <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
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
                    <button type="button" class="btn btn-danger" onclick="rejectQuotation()">Reject</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- / Layout wrapper -->
<?php include_once __DIR__ . "/../../includes/dashboard/dashboard_footer_scripts.php"; ?>

<script>
    const acceptButton = document.querySelector('#acceptButton'),
        rejectButton = document.querySelector('#rejectButton');

    // ALERT WITH FUNCTIONAL CONFIRM BUTTON
    acceptButton.onclick = function () {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, I accept!',
            customClass: {
                confirmButton: 'btn btn-success me-1',
                cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
        }).then(function (result) {
            if (result.value) {
                acceptQuotation();
            }
        });
    }


    rejectButton.onclick = function () {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, I Reject!',
            customClass: {
                confirmButton: 'btn btn-danger me-1',
                cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
        }).then(function (result) {
            if (result.value) {
                $("#rejectReasonModal").modal('show');
            }
        });
    }

    function acceptQuotation() {
        blockArea($('body'));
        let documentID = '<?=$keyDocument->documentId ?>';


        // var form = $('form')[0]; // You need to use standard javascript object here
        var formData = new FormData();
        formData.append('documentID', documentID);
        formData.append('status', '<?=QUOTATION_STATUS_CUSTOMER_ACCEPTED ?>');
        formData.append('rejectReason', "");

        $.ajax({
            url: '/ajax/documents/customer_update_quotation_status.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (data, status) {
                console.log(data);

                var statusmessage = data.trim().split("|")[0];
                var message = data.trim().split("|")[1];

                if (statusmessage == "SUCCESS") {
                    unBlockArea($('body'));
                    Swal.fire({
                        icon: 'success',
                        title: 'Accepted!',
                        text: 'Thank you for accepting the quotation. Have a great day!',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    }).then(function(){
                        location.href = window.location.href;
                    });
                }

                if (statusmessage == "ERROR") {
                    unBlockArea($('body'));
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops! Something went wrong!',
                        text: 'Something went wrong. Please try again later or contact our support team!',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    });
                }
            },

            error: function (error) {

                console.log(error);
            }
        });


    }


    function rejectQuotation(){
        blockArea($('body'));
        let documentID = '<?=$keyDocument->documentId ?>';

        let rejectReason = $("#rejectReason").val();

        if(rejectReason.length){

            var formData = new FormData();
            formData.append('documentID', documentID);
            formData.append('status', '<?=QUOTATION_STATUS_CUSTOMER_REJECTED ?>');
            formData.append('rejectReason', rejectReason);

            $.ajax({
                url: '/ajax/documents/customer_update_quotation_status.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (data, status) {
                    console.log(data);

                    var statusmessage = data.trim().split("|")[0];
                    var message = data.trim().split("|")[1];

                    if (statusmessage == "SUCCESS") {
                        unBlockArea($('body'));
                        Swal.fire({
                            icon: 'error',
                            title: 'Rejected!',
                            text: 'You have rejected the quotation. Please contact us if you need any further clarifications.',
                            customClass: {
                                confirmButton: 'btn btn-success'
                            }
                        }).then(function(){
                            location.href = window.location.href;
                        });
                    }

                    if (statusmessage == "ERROR") {
                        unBlockArea($('body'));
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops! Something went wrong!',
                            text: 'Something went wrong. Please try again later or contact our support team!',
                            customClass: {
                                confirmButton: 'btn btn-success'
                            }
                        });
                    }
                },

                error: function (error) {

                    console.log(error);
                }
            });


        }
        else
        {
            Swal.fire({
                title: 'Oops!',
                text: "You must enter a reason for rejection",
                icon: 'error',
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
</script>
</body>
</html>