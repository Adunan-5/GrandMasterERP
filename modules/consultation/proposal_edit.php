<?php
$PAGE_ID = "CONSULTATION_PROPOSAL_EDIT";
include_once __DIR__ . "/../../includes/baseIncludes.php";
include_once __DIR__ . "/../../includes/auth_check.php";
$proposalID = "";

if (isset($_GET['proposalID']) && !empty($_GET['proposalID'])) {
    $proposalID = filter_var($_GET['proposalID'], FILTER_SANITIZE_SPECIAL_CHARS);
}

if (empty($proposalID)) {
    header("location:/consultation/proposal/list");
}

$resProposal = $db->query("SELECT * FROM consultation_proposals WHERE proposalID = ?s", $proposalID);
$rowProposal = mysqli_fetch_assoc($resProposal);

$proposalNumber = getProposalNumberFromProposalID($rowProposal['proposalID']);

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
                                    <div class="col-6">
                                        <h4 class="my-0">Proposal: <?= $proposalNumber ?></h4>
                                    </div>
                                    <div class="col-6 text-end">
                                        <?php

                                        //Check if this has SMA
                                        $associatedSMAID = getSMAIDForProposalID($proposalID);

                                        if (!empty($associatedSMAID)) {
                                            ?>
                                            <a target="_blank" href="/consultation/sma/edit?smaID=<?= $associatedSMAID ?>" class="btn btn-label-linkedin text-nowrap d-inline-flex position-relative me-4">
                                                <?= getSMANumberFromSMAID($associatedSMAID) ?>
                                            </a>
                                            <?php
                                        }

                                        switch ($rowProposal['status']) {
                                            case CONSULTATION_PROPOSAL_STATUS_DRAFT:
                                                ?>
                                                <button type="button" class="btn btn-label-linkedin text-nowrap d-inline-flex position-relative me-4">
                                                    DRAFT
                                                </button>
                                                <?php
                                                break;

                                            case CONSULTATION_PROPOSAL_STATUS_APPROVED:
                                                ?>
                                                <label class="no-hand btn btn-label-success text-nowrap d-inline-flex position-relative me-4">
                                                    APPROVED
                                                </label>
                                                <?php
                                                break;

                                            case CONSULTATION_PROPOSAL_STATUS_REJECTED:
                                                ?>
                                                <label class="no-hand btn btn-label-danger text-nowrap d-inline-flex position-relative me-4">
                                                    REJECTED
                                                </label>
                                                <?php
                                                break;

                                            case QUOTATION_STATUS_SALES_MANAGER_SO_REJECTED:
                                                ?>
                                                <label class="no-hand btn btn-label-danger text-nowrap d-inline-flex position-relative me-4">
                                                    SALES MANAGER REJECTED
                                                </label>
                                                <?php
                                                break;

                                            case QUOTATION_STATUS_ACCOUNTANT_SO_REJECTED:
                                                ?>
                                                <label class=" no-hand btn btn-label-danger text-nowrap d-inline-flex position-relative me-4">
                                                    ACCOUNTANT REJECTED
                                                </label>
                                                <?php
                                                break;

                                            case CONSULTATION_PROPOSAL_STATUS_CONFIRMED:
                                                ?>
                                                <!--<a href="/salesorders/edit/--><?php //= $keyDocument->documentId ?><!--" class="btn btn-label-info">--><?php //= $keyDocument->getSalesOrderNumberWithPrefix() ?><!--</a>-->
                                                <label class="no-hand btn btn-label-success text-nowrap d-inline-flex position-relative me-4">
                                                    CONFIRMED
                                                </label>
                                                <?php
                                                break;

                                            case CONSULTATION_PROPOSAL_STATUS_CUSTOMER_REJECTED:
                                                ?>
                                                <label class="no-hand btn btn-label-danger text-nowrap d-inline-flex position-relative me-4">
                                                    CUSTOMER REJECTED
                                                </label>
                                                <?php
                                                break;
                                            case CONSULTATION_PROPOSAL_STATUS_CUSTOMER_ACCEPTED:
                                                ?>
                                                <label class="no-hand btn btn-label-success text-nowrap d-inline-flex position-relative me-4">
                                                    CUSTOMER ACCEPTED
                                                </label>
                                                <?php
                                                break;
                                            default:
                                                ?>
                                                <button type="button" class="btn btn-label-info text-nowrap d-inline-flex position-relative me-4">
                                                    <?= $rowProposal['status'] ?>
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
                    $rejectReason   = "";
                    $proposalStatus = $rowProposal['status'];
                    if ($proposalStatus == CONSULTATION_PROPOSAL_STATUS_REJECTED) {
                        $rejectReason = getRejectReasonForProposalID($proposalID);
                        ?>
                        <div class="alert alert-solid-danger d-flex align-items-center" role="alert">
                        <span class="alert-icon rounded">
                          <i class="ti ti-ban"></i>
                        </span> Rejection Reason: <?= $rejectReason ?>
                        </div>
                        <?php
                    }

                    if ($proposalStatus == CONSULTATION_PROPOSAL_STATUS_CUSTOMER_REJECTED) {
                        $rejectReason = getCustomerRejectReasonForProposalID($proposalID);
                        ?>
                        <div class="alert alert-solid-danger d-flex align-items-center" role="alert">
                        <span class="alert-icon rounded">
                          <i class="ti ti-ban"></i>
                        </span> Rejection Reason: <?= $rejectReason ?>
                        </div>
                        <?php
                    }


                    //Temporary for Faisal
                    if ($proposalStatus == CONSULTATION_PROPOSAL_STATUS_SENT_TO_CUSTOMER) {
                        ?>
                        <div class="alert alert-outline-warning" role="alert">
                            This is a temporary way to simulate customer acceptance of the proposal.
                            <a href="/consultation/proposal/view/<?= $proposalID ?>" target="_blank">Click here to open</a>.
                            Password: <?= $rowProposal['customerPassword'] ?>
                        </div>
                        <?php
                    }

                    ?>
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
                                                    $customerID = $rowProposal['customerID'];
                                                    $companyId  = $_SESSION['SES_SELECTED_COMPANY'];
                                                    $res        = $db->query("SELECT * FROM customers WHERE status ='Active' and companyId = $companyId");
                                                    while ($row = mysqli_fetch_assoc($res)) {
                                                        ?>
                                                        <option value="<?= $row['customerId'] ?>" <?php if ($customerID == $row['customerId']) echo 'selected' ?>><?= $row['companyName'] . " | " . $row['companyNameAr'] ?></option>
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
                                                <?php
                                                if ($rowProposal['status'] == CONSULTATION_PROPOSAL_STATUS_DRAFT || $rowProposal['status'] == CONSULTATION_PROPOSAL_STATUS_REJECTED || $rowProposal['status'] == CONSULTATION_PROPOSAL_STATUS_CUSTOMER_REJECTED) {
                                                    ?>
                                                    <button class="btn btn-success mb-4" onclick="sendForApproval()">
                                                        <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-file-like ti-xs me-2"></i>Send For Approval</span>
                                                    </button>
                                                    <?php
                                                }

                                                if ($rowProposal['status'] == CONSULTATION_PROPOSAL_STATUS_AWAITING_APPROVAL) {
                                                    ?>
                                                    <button class="btn btn-success mb-4" onclick="approveProposal()">
                                                        <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-eye-check ti-xs me-2"></i>Approve</span>
                                                    </button>
                                                    <button class="btn btn-danger mb-4" data-bs-toggle="modal" data-bs-target="#rejectReasonModal">
                                                        <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-circle-dashed-x ti-xs me-2"></i>Reject</span>
                                                    </button>
                                                    <?php
                                                }

                                                if ($rowProposal['status'] == CONSULTATION_PROPOSAL_STATUS_APPROVED) {
                                                    ?>
                                                    <button class="btn btn-success mb-4" data-bs-toggle="offcanvas" data-bs-target="#sendQuotationOffcanvas">
                                                        <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-send ti-xs me-2"></i>Send To Customer</span>
                                                    </button>
                                                    <?php
                                                } 

                                                if ($rowProposal['status'] == CONSULTATION_PROPOSAL_STATUS_APPROVED || $rowProposal['status'] == CONSULTATION_PROPOSAL_STATUS_SENT_TO_CUSTOMER || $rowProposal['status'] == CONSULTATION_PROPOSAL_STATUS_CUSTOMER_ACCEPTED || $rowProposal['status'] == CONSULTATION_PROPOSAL_STATUS_CONFIRMED) {
                                                    ?>
                                                    <button class="btn btn-primary mb-4" onclick="printProposal()">
                                                        <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-printer ti-xs me-2"></i>Print</span>
                                                    </button>
                                                    <?php
                                                }

                                                if ($rowProposal['status'] == CONSULTATION_PROPOSAL_STATUS_SENT_TO_CUSTOMER) {
                                                    ?>
                                                    <button class="btn btn-success mb-4" data-bs-toggle="offcanvas" data-bs-target="#sendQuotationOffcanvas">
                                                        <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-reload ti-xs me-2"></i>Re-send To Customer</span>
                                                    </button>
                                                    <?php
                                                }

                                                if ($rowProposal['status'] == CONSULTATION_PROPOSAL_STATUS_CUSTOMER_ACCEPTED) {
                                                    ?>
                                                    <button class="btn btn-success mb-4" onclick="confirmProposal()">
                                                        <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-thumb-up ti-xs me-2"></i>Confirm & Create Quotation</span>
                                                    </button>
                                                    <?php
                                                }

                                                if ($rowProposal['status'] == CONSULTATION_PROPOSAL_STATUS_CONFIRMED) {

                                                    $quotationID = getQuotationIDForProposalID($proposalID);
                                                    if(!empty($quotationID))
                                                    {
                                                        ?>
                                                        <a href="/consultation/quotation/edit/<?=$quotationID ?>" class="btn btn-success mb-4" target="_blank" >
                                                            <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-file-arrow-left ti-xs me-2"></i><?=getQuotationNumberFromConsultationDocumentID($quotationID) ?></span>
                                                        </a>
                                                        <?php
                                                    }
                                                    else{
                                                    ?>
                                                        <a href="/modules/consultation/quotation_new_v2.php?proposalID=<?=$proposalID ?>" class="btn btn-success mb-4">
                                                            <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-thumb-up ti-xs me-2"></i>Create Quotation</span>
                                                        </a>

                                                        <?php
                                                    }


                                                    ?>

                                                    <?php
                                                }

                                                ?>
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
                                            $customer->loadById($customerID);
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
                                                    <span class="h5 text-capitalize mb-0 text-nowrap">Proposal</span>
                                                </dt>
                                                <dd class="col-sm-7">
                                                    <div class="input-group input-group-merge disabled">
                                                        <span class="input-group-text">#</span>
                                                        <input name="proposalNumber" id="proposalNumber" type="text" class="form-control" disabled placeholder="Will be generated once saved / confirmed" value="<?= $proposalNumber ?>"/>
                                                        <input name="proposalId" id="proposalId" type="hidden" value="<?= $proposalID ?>"/>
                                                    </div>
                                                </dd>
                                                <dt class="col-sm-5 mb-2 d-md-flex align-items-center justify-content-end">
                                                    <span class="fw-normal">Proposal Date:</span>
                                                </dt>
                                                <dd class="col-sm-7">
                                                    <input name="proposalDate" id="proposalDate" type="text" class="form-control gmm-date-format" placeholder="DD - MMM - YYYY" value="<?= $rowProposal['preparedOn'] ?>"/>
                                                </dd>
                                                <dt class="col-sm-5 d-md-flex align-items-center justify-content-end">
                                                    <span class="fw-normal">Valid Until:</span>
                                                </dt>
                                                <dd class="col-sm-7">
                                                    <input name="proposalValidUntil" id="proposalValidUntil" type="text" class="form-control gmm-date-format" placeholder="DD - MMM - YYYY" value="<?= $rowProposal['validUntil'] ?>"/>
                                                </dd>
                                                <dt class="col-sm-5 mb-2 d-md-flex align-items-center justify-content-end">
                                                    <span class="fw-normal">Sales Person:</span>
                                                </dt>
                                                <dd class="col-sm-7">
                                                    <input type="text" readonly class="form-control due-date" value="<?= getDisplayNameFromUserID($rowProposal['createdBy']) ?>"/>
                                                </dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                                <!--Grey Header Section Ends-->
                                <!--Proposal Template and Project Title Starts-->
                                <div class="card-body px-0" id="proposalTemplateSelectionDIV">
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
                                                        <option <?php if ($row['proposalTemplateID'] == $rowProposal['proposalTemplateID']) echo 'selected' ?> value="<?= $row['proposalTemplateID'] ?>"><?= $row['templateName'] ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-sm-0 mb-6">
                                            <div class="mb-4">
                                                <label class="form-label" for="customerCountry">Proposal Title:</label>
                                                <input value="<?= $rowProposal['proposalTitle'] ?>" type="text" class="form-control" id="proposalTitle" name="proposalTitle" placeholder="" aria-describedby="defaultFormControlHelp" required/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--Proposal Template and Project Title Ends-->
                                <!--Services Items Builder-->
                                <div id="proposalServiceItemsSelectionDIV">
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
                                    <div class="table-responsive" id="proposalLineItemsContainer">
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
                                            <?php

                                            $resProposalLineItem = $db->query("SELECT * FROM consultation_proposal_line_items WHERE documentId = ?s", $proposalID);
                                            $lineItemCount       = 0;
                                            while ($rowProposalLineItem = mysqli_fetch_assoc($resProposalLineItem)) {
                                                $lineItemCount++;
                                                $lineItemInfo = getItemInfoForConsultationServiceItemID($rowProposalLineItem['itemId']);
                                                ?>
                                                <tr>
                                                    <td class="sno"><?= $lineItemCount ?></td>
                                                    <td>
                                                        <input type="hidden" name="serviceItemName" value="<?= $lineItemInfo['itemName'] ?>" required/>
                                                        <input type="hidden" name="serviceItemDescription" value="<?= $lineItemInfo['description'] ?>" required/>
                                                        <input type="hidden" name="serviceItemID" value="<?= $rowProposalLineItem['itemId'] ?>" required/>
                                                        <strong><?= $lineItemInfo['itemName'] ?></strong><br><?= $lineItemInfo['description'] ?>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control qty" name="qty" value="<?= $rowProposalLineItem['quantity'] ?>" min="1" required/>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" name="uom" readonly value="<?= $rowProposalLineItem['UOM'] ?>" required/>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control rate" name="rate" readonly value="<?= $rowProposalLineItem['unitPrice'] ?>" required/>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control total" name="total" readonly value="<?php
                                                        $qty   = $rowProposalLineItem['quantity'];
                                                        $rate  = $rowProposalLineItem['unitPrice'];
                                                        $total = $qty * $rate;
                                                        echo number_format($total, 2); ?>"/>
                                                    </td>
                                                    <td><a class="btn btn-danger removeRow"><i class="ti ti-trash"></i></a></td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!--/Services Items Builder-->
                            </div>
                        </div>
                    </div>
                    <!--***********************************-->
                    <div class="row invoice-preview mt-6" id="proposalContainer">
                        <!-- Proposal -->
                        <div class="col-xl-12 col-md-8 col-12 mb-md-0 mb-6">
                            <div class="card invoice-preview-card p-sm-12 p-6" id="proposalContentContainer">
                                <?php
                                $baseQuery          = "SELECT * from consultation_proposal_templates WHERE proposalTemplateID = ?s";
                                $resTemplateContent = $db->query($baseQuery, $rowProposal['proposalTemplateID']);
                                $rowTemplateContent = mysqli_fetch_assoc($resTemplateContent);
                                ?>
                                <h2>Introduction</h2>
                                <?= $rowTemplateContent['contentIntroduction'] ?>
                                <h2>Service & Products</h2>
                                <div id='proposalServicesListInlineDIV' class='mb-6'></div>
                                <h2>Why Choose Us</h2>
                                <?= $rowTemplateContent['contentWhyChooseUs'] ?>
                                <h2>Scope Of Work</h2>
                                <div id="proposalScopeOfWork" name="proposalScopeOfWork" class="snow-editor">
                                    <?= $rowProposal['contentScopeOfWork'] ?>
                                </div>
                                <h2>Client Responsibilites</h2>
                                <?= $rowTemplateContent['contentClientResponsibilities'] ?>
                                <h2>Terms & Conditions</h2>
                                <?= $rowTemplateContent['contentTermsAndConditions'] ?>
                                <h2>Next Steps</h2>
                                <?= $rowTemplateContent['contentNextSteps'] ?>
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
                    <div class="row mt-6">
                        <div class="col-xl-12 col-md-12 col-12 mb-md-0 mb-6">
                            <div class="card p-sm-12">
                                <div class="d-flex gap-2 justify-content-end">

                                    <?php
                                    //Check if this has SMA
                                    $associatedSMAID = getSMAIDForProposalID($proposalID);

                                    if (!empty($associatedSMAID)) {
                                        ?>
                                        <a target="_blank" href="/consultation/sma/edit?smaID=<?= $associatedSMAID ?>" class="btn btn-label-linkedin text-nowrap d-inline-flex position-relative me-4">
                                            <?= getSMANumberFromSMAID($associatedSMAID) ?>
                                        </a>
                                        <?php
                                    }
                                    else {
                                    ?>
                                        <button class="btn btn-success" type="button" data-bs-toggle="modal" data-bs-target="#addSMAModal">Add SMA</button>
                                        <?php
                                    }
                                    ?>






                                </div>
                            </div>
                        </div>
                    </div>
                    <!--***********************************-->
                    <!-- Offcanvas -->
                    <!-- Send Proposal Sidebar -->
                    <div class="offcanvas offcanvas-end" id="sendQuotationOffcanvas" aria-hidden="true">
                        <div class="offcanvas-header mb-6 border-bottom">
                            <h5 class="offcanvas-title">Send Quotation</h5>
                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body pt-0 flex-grow-1">
                            <form id="sendProposalEmailForm" name="sendProposalEmailForm" method="POST">
                                <div class="mb-6">
                                    <label for="quotation-to" class="form-label">To</label>
                                    <input type="text" class="form-control" id="quotation-to" name="quotation-to" value="<?= $customer->email ?>" placeholder="customer@email.com"/>
                                    <small>Use comma to send to multiple recipients</small>
                                </div>
                                <div class="mb-6">
                                    <label for="quotation-cc" class="form-label">CC</label>
                                    <input type="text" class="form-control" id="quotation-cc" name="quotation-cc" value="" placeholder=""/>
                                </div>
                                <div class="mb-6">
                                    <label for="quotation-bcc" class="form-label">BCC</label>
                                    <input type="text" class="form-control" id="quotation-bcc" name="quotation-bcc" value="consultation@ggm.com.co" placeholder="customer@email.com"/>
                                </div>
                                <div class="mb-6">
                                    <label for="quotation-subject" class="form-label">Subject</label>
                                    <input type="text" class="form-control" id="quotation-subject" name="quotation-subject" value="GrandMaster Proposal <?= getProposalNumberFromProposalID($proposalID) ?>" placeholder=""/>
                                </div>
                                <div class="mb-6" style="display: none">
                                    <label for="quotation-message" class="form-label">Message</label>
                                    <textarea class="form-control" name="quotation-message" id="quotation-message" cols="3" rows="8"></textarea>
                                </div>
                                <div class="mb-6" style="display: none">
                      <span class="badge bg-label-primary">
                        <i class="ti ti-link ti-xs"></i>
                        <span class="align-middle">Quotation Attached</span>
                      </span>
                                </div>
                                <div class="mb-6 d-flex flex-wrap">
                                    <button type="button" class="btn btn-primary me-4" onclick="sendProposalEmail()">
                                        Send
                                    </button>
                                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- /Send Proposal Sidebar -->
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
    <!--MODALS-->
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
                            <input type="text" id="rejectReason" class="form-control" placeholder="Enter your reason for rejection"/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-danger" onclick="rejectProposal()">Reject</button>
                </div>
            </div>
        </div>
    </div>
    <!--Add SMA Modal-->
    <div class="modal modal-top fade" id="addSMAModal" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTopTitle">Add SMA</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-4">
                            <label for="smaTemplateSelect" class="form-label">SMA Template</label>
                            <select id="smaTemplateSelect" class="selectpicker w-100" data-style="btn-default">
                                <?php
                                $resSMATemplates = $db->query("SELECT * FROM consultation_sma_templates WHERE active = 1");
                                while ($rowSMATemplates = mysqli_fetch_assoc($resSMATemplates)) {
                                    ?>
                                    <option value="<?= $rowSMATemplates['smaTemplateID'] ?>"><?= $rowSMATemplates['templateName'] ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="row g-4">
                        <div class="col mb-0">
                            <label for="smaAmount" class="form-label">Amount</label>
                            <input type="number" id="smaAmount" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" onclick="addSMA()" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
    <!--/MODALS-->
</div>
<!-- / Layout wrapper -->
<?php include_once __DIR__ . "/../../includes/dashboard/dashboard_footer_scripts.php"; ?>
<!-- Page JS -->
<!--<script src="/assets/js/offcanvas-send-invoice.js"></script>-->
<script>
    var quillEditors = {};

    $(document).ready(function (e) {

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
                            <td><input type="text" class="form-control total" name="total" readonly value="` + data.price + `" required/></td>
                            <td><a class="btn btn-danger removeRow"><i class="ti ti-trash"></i></a></td>
                        </tr>
                    `;
                    $('#proposalLineItemsTable tbody').append(newRow);
                    updateSerialNumbers(); // ✅ Recalculate S.No for all rows

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
        console.log("Updating - Saving Proposal: ");

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
            proposalID: $("#proposalId").val(),
            customerID: $("#customer").val(),
            proposalDate: $("#proposalDate").val(),
            validUntil: $("#proposalValidUntil").val(),
            proposalTemplateID: $("#proposalTemplate").val(),
            proposalTitle: $("#proposalTitle").val(),
            scopeOfWork: quillEditors['proposalScopeOfWork'].root.innerHTML,
            lineItems: lineItemRows
        };

        $.ajax({
            url: '/ajax/consultation/update_proposal.php',
            type: 'POST',
            data: JSON.stringify(dataToSend),
            contentType: 'application/json',
            dataType: 'json',
            success: function (response) {
                console.log("Server Response: ", response);


                unBlockArea($('body'));
                if (response.status === "success") {

                    showSuccessMessage(response.message, reloadPage);

                } else if (response.status === "error") {
                    showErrorMessage(response.message);

                }
            },

            error: function (xhr, status, error) {
                console.error("AJAX error:", error);
            }
        });
    }

    function sendForApproval() {
        console.log("Updating - Status Proposal: ");

        var dataToSend = {
            proposalID: $("#proposalId").val(),
            status: "<?=CONSULTATION_PROPOSAL_STATUS_AWAITING_APPROVAL ?>"
        };

        $.ajax({
            url: '/ajax/consultation/update_proposal_status.php',
            type: 'POST',
            data: JSON.stringify(dataToSend),
            contentType: 'application/json',
            dataType: 'json',
            success: function (response) {
                console.log("Server Response: ", response);

                unBlockArea($('body'));
                if (response.status === "success") {

                    showSuccessMessage(response.message, reloadPage);

                } else if (response.status === "error") {
                    showErrorMessage(response.message);

                }
            },

            error: function (xhr, status, error) {
                console.error("AJAX error:", error);
            }
        });
    }

    function confirmProposal() {
        console.log("Updating - Status Proposal: Confirm Proposal");
let proposalID = $("#proposalId").val();
        var dataToSend = {
            proposalID: proposalID,
            status: "<?=CONSULTATION_PROPOSAL_STATUS_CONFIRMED ?>"
        };

        $.ajax({
            url: '/ajax/consultation/update_proposal_status.php',
            type: 'POST',
            data: JSON.stringify(dataToSend),
            contentType: 'application/json',
            dataType: 'json',
            success: function (response) {
                console.log("Server Response: ", response);

                unBlockArea($('body'));
                if (response.status === "success") {

                    showSuccessMessage(response.message, gotoPage, '/modules/consultation/quotation_new_v2.php?proposalID=' + proposalID);

                } else if (response.status === "error") {
                    showErrorMessage(response.message);

                }
            },

            error: function (xhr, status, error) {
                console.error("AJAX error:", error);
            }
        });
    }

    function approveProposal() {
        console.log("Updating - Status Proposal: ");

        var dataToSend = {
            proposalID: $("#proposalId").val(),
            status: "<?=CONSULTATION_PROPOSAL_STATUS_APPROVED ?>"
        };

        $.ajax({
            url: '/ajax/consultation/update_proposal_status.php',
            type: 'POST',
            data: JSON.stringify(dataToSend),
            contentType: 'application/json',
            dataType: 'json',
            success: function (response) {
                console.log("Server Response: ", response);

                unBlockArea($('body'));
                if (response.status === "success") {

                    showSuccessMessage(response.message, reloadPage);

                } else if (response.status === "error") {
                    showErrorMessage(response.message);

                }
            },

            error: function (xhr, status, error) {
                console.error("AJAX error:", error);
            }
        });
    }

    function updateProposalStatusSentToCustomer() {
        console.log("Updating - Status Proposal: Sent to customer ");

        var dataToSend = {
            proposalID: $("#proposalId").val(),
            status: "<?=CONSULTATION_PROPOSAL_STATUS_SENT_TO_CUSTOMER ?>"
        };

        $.ajax({
            url: '/ajax/consultation/update_proposal_status.php',
            type: 'POST',
            data: JSON.stringify(dataToSend),
            contentType: 'application/json',
            dataType: 'json',
            success: function (response) {
                console.log("Server Response: ", response);

                unBlockArea($('body'));
                if (response.status === "success") {

                    // showSuccessMessage(response.message, reloadPage);

                } else if (response.status === "error") {
                    showErrorMessage(response.message);

                }
            },

            error: function (xhr, status, error) {
                console.error("AJAX error:", error);
            }
        });
    }

    function rejectProposal() {
        console.log("Updating - Status Proposal: reject ");

        let rejectReason = $("#rejectReason").val();
        if (rejectReason.length) {
            //Do nothing
        } else {
            showErrorMessage("You must enter a reason for rejection");
            return false;
        }


        var dataToSend = {
            proposalID: $("#proposalId").val(),
            status: "<?=CONSULTATION_PROPOSAL_STATUS_REJECTED ?>",
            rejectReason: rejectReason
        };

        $.ajax({
            url: '/ajax/consultation/update_proposal_status.php',
            type: 'POST',
            data: JSON.stringify(dataToSend),
            contentType: 'application/json',
            dataType: 'json',
            success: function (response) {
                console.log("Server Response: ", response);

                unBlockArea($('body'));
                if (response.status === "success") {
                    showSuccessMessage(response.message, reloadPage);
                } else if (response.status === "error") {
                    showErrorMessage(response.message);

                }
            },

            error: function (xhr, status, error) {
                console.error("AJAX error:", error);
            }
        });
    }

    function sendProposalEmail() {
        blockArea($('body'));
        let proposalID = '<?=$proposalID ?>';

        var form = $('#sendProposalEmailForm')[0];
        var formData = new FormData(form);
        formData.append("proposalID", proposalID);

        $.ajax({
            url: '/ajax/consultation/send_proposal_email.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (data, status) {
                console.log(data);
                var statusmessage = data.trim().split("|")[0];
                var message = data.trim().split("|")[1];

                if (statusmessage == "SUCCESS") {
                    updateProposalStatusSentToCustomer();
                    showSuccessMessage("Email sent to customer.", reloadPage);

                }
                unBlockArea($('body'));
            },

            error: function (error) {
                unBlockArea($('body'));
                console.log(error);
            }
        });

    }

    function addSMA() {

        console.log("Adding SMA");


        let smaTemplateID = $("#smaTemplateSelect").val();
        let smaAmount=  $("#smaAmount").val();
        if (!smaTemplateID) {
            showErrorMessage("Please select an SMA Template");
            return; // stop further execution
        }

        if (!smaAmount) {
            showErrorMessage("Please enter the amount.");
            return; // stop further execution
        }


        var dataToSend = {
            customerID: $("#customer").val(),
            proposalID: $("#proposalId").val(),
            smaTemplateID: smaTemplateID,
            smaAmount: smaAmount
        };

        $.ajax({
            url: '/ajax/consultation/create_sma.php',
            type: 'POST',
            data: JSON.stringify(dataToSend),
            contentType: 'application/json',
            dataType: 'json',
            success: function (response) {
                console.log("Server Response: ", response);

                unBlockArea($('body'));
                if (response.status === "success") {

                    showSuccessMessage(response.message, reloadPage);

                } else if (response.status === "error") {
                    showErrorMessage(response.message);

                }
            },

            error: function (xhr, status, error) {
                console.error("AJAX error:", error);
            }
        });
    }

    $('#addSMAModal').on('show.bs.modal', function (event) {
        let totalSum = 0;

        $('.total').each(function () {
            let val = $(this).val().replace(/,/g, '').trim(); // Remove commas
            if (val !== '' && !isNaN(val)) {
                totalSum += parseFloat(val);
            }
        });

        let fortyPercent = (totalSum * 0.40).toFixed(2);
        $('#smaAmount').val(fortyPercent);
    });

    function printProposal() {
        // Get the base URL of the current page
        const baseUrl = window.location.origin;
        // Define the relative URL to open
        const relativeUrl = '/ajax/consultation/generate_proposal_pdf.php?proposalID=' + '<?=$proposalID ?>';
        // Combine the base URL and relative URL
        const fullUrl = baseUrl + relativeUrl;
        // Open the URL in a new tab
        window.open(fullUrl, '_blank');

    }



    injectServicesIntoProposal();
    applySnowEditor();
</script>
</body>
</html>