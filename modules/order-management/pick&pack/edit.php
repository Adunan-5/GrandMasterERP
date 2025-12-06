<?php
$PAGE_ID = "ORDER_MANAGEMENT_PICK_AND_PACK_NEW";
include_once __DIR__ . "/../../../includes/baseIncludes.php";
include_once __DIR__ . "/../../../includes/auth_check.php";

$gdnID = "";
$gdnID = filter_input(INPUT_GET, 'gdnID', FILTER_VALIDATE_INT);
if ($gdnID === null || $gdnID === false || filter_var($gdnID, FILTER_VALIDATE_INT) === false) {
    header("location:/order-management/list?filter=ALL");
    exit();
}

?>
<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
      data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
<head>
    <?php include_once __DIR__ . "/../../../includes/dashboard/dashboard_head_section.php"; ?>
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
    <?php include_once __DIR__ . "/../../../includes/dashboard/dashboard_head_section_post_vendor.php"; ?>
    <title>GrandMaster ERP | Pick & Pack</title>
</head>
<body>
<!-- Layout wrapper -->
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <!-- Menu -->
        <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
            <?php include_once __DIR__ . "/../../../includes/dashboard/menu_ceo.php" ?>
        </aside>
        <!-- / Menu -->
        <!-- Layout container -->
        <div class="layout-page">
            <!-- Navbar -->
            <nav
                class="layout-navbar container-fluid navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
                id="layout-navbar">
                <?php include_once __DIR__ . "/../../../includes/dashboard/top_navbar.php"; ?>
            </nav>
            <!-- / Navbar -->
            <!-- Content wrapper -->
            <div class="content-wrapper">
                <?php
                $currentUserRole = getAuthenticatedUser()->getRoles()[0];
                $gdnDATA = [];

                $res = $db->query("SELECT * FROM `pick_and_pack_documents` where gdnId = ?s", $gdnID);
                while($row = mysqli_fetch_assoc($res)) {
                    $gdnDATA = $row;
                }
                $originWHID = null;
                $orderType = $gdnDATA['transactionType'];
                $orderManagementID = $gdnDATA['orderManagementId'];

                if (!empty($gdnDATA)) {
                    $originWHID = $gdnDATA['originWHID'] ?? null;
                    if ($originWHID) {
                        $originWH = new Warehouse();
                        $originWH->loadById($originWHID);
                    }

                    if ($orderType == "TRANSFER") {
                        $destinationWHID = $gdnDATA['destinationWHID'] ?? null;
                        if ($destinationWHID) {
                            $destinationWH = new Warehouse();
                            $destinationWH->loadById($destinationWHID);
                        }
                    } elseif ($orderType == "SALESORDER") {
                        $customerID = $gdnDATA['customerId'] ?? null;
                        if ($customerID) {
                            $customer = new Customer();
                            $customer->loadById($customerID);
                        }
                    }

                    $gdnNumber = "DRAFT";
                    if (!empty($gdnDATA['gdnNumberPrefix'])) {
                        $gdnNumber = $gdnDATA['gdnNumberPrefix'] . $gdnDATA['gdnNumber'];
                    }

                    // Get order number
//                    if ($gdnDATA == 'SALESORDER' && !empty($documentID)) {
//                        $orderNumber = getSalesOrderNumberFromDocumentID($documentID);
//                    } elseif ($gdnDATA == 'TRANSFER' && !empty($documentID)) {
//                        $orderNumber = getTransferNumberFromDocumentID($documentID);
//                    }
                }

                $lineitemRes = $db->query("SELECT * FROM `pick_and_pack_line_items` where gdnId = ?s", $gdnID);
                $itemIdRes = $db->query("SELECT * FROM `pick_and_pack_line_items` where gdnId = ?s", $gdnID);
                $itemIds = array();
                while($row = mysqli_fetch_assoc($itemIdRes)) {
                    $itemIds[] = $row['itemId'];
                }

                $itemIds = json_encode($itemIds);

//                $pickAndPackIds = [];
//                $ppItemRes = $db->query("SELECT pickAndPackLineItemId FROM `pick_and_pack_items` WHERE orderManagementId = ?s", $orderManagementID);
//                while ($ppRow = mysqli_fetch_assoc($ppItemRes)) {
//                    $pickAndPackIds[] = $ppRow['pickAndPackLineItemId']; // Store only IDs
//                }

                ?>
                <!-- Content -->
                <div class="container-fluid flex-grow-1 container-p-y">
                    <div class="card mb-6">
                        <div class="card-widget-separator-wrapper">
                            <div class="card-body card-widget-separator py-2">
                                <div class="row gy-1 gy-sm-1">
                                    <div class="col-6">
                                        <h4 class="my-0"><?= getGDNNumberFromDocumentID($gdnID) ?> </h4>
                                    </div>
                                                                            <div class="col-6 text-end">
                                                                                <?php
                                                                                switch ($gdnDATA['gdnStatus']) {
                                                                                    case GDN_STATUS_NEW:
                                                                                        ?>
                                                                                        <button type="button"
                                                                                                class="btn btn-label-linkedin text-nowrap d-inline-flex position-relative me-4">
                                                                                            DRAFT
                                                                                        </button>
                                                                                        <?php
                                                                                        break;

                                                                                    case GDN_STATUS_PROCUREMENT_MANAGER_APPROVED:
                                                                                        ?>
                                                                                        <label
                                                                                            class="no-hand btn btn-label-success text-nowrap d-inline-flex position-relative me-4">
                                                                                            PROCUREMENT MANAGER APPROVED
                                                                                        </label>
                                                                                        <?php
                                                                                        break;

                                                                                    case GDN_STATUS_PROCUREMENT_MANAGER_REJECTED:
                                                                                        ?>
                                                                                        <label
                                                                                            class="no-hand btn btn-label-danger text-nowrap d-inline-flex position-relative me-4">
                                                                                            PROCUREMENT MANAGER REJECTED
                                                                                        </label>
                                                                                        <?php
                                                                                        break;

                                                                                    case GDN_CREATED:
                                                                                        ?>
                                                                                        <label
                                                                                            class="no-hand btn btn-label-info text-nowrap d-inline-flex position-relative me-4">
                                                                                            <?=getGDNNumberFromGDNID($gdnID);?>
                                                                                        </label>
                                                                                        <?php
                                                                                        break;

                                                                                    default:
                                                                                        ?>
                                                                                        <button type="button"
                                                                                                class="btn btn-label-info text-nowrap d-inline-flex position-relative me-4">
                                                                                            <?= $gdnDATA['gdnStatus'] ?>
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
                                            $documentStatus = $gdnDATA['gdnStatus'];
                                            if ($documentStatus == GDN_STATUS_PROCUREMENT_MANAGER_REJECTED) {
                                                $rejectReason = getRejectReasonForGDNID($gdnID);
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
                            <div class="card invoice-preview-card p-sm-6 p-6">
                                <div class="card-body px-0">
                                    <div class="row">
                                        <!-- Buttons Row  -->
                                      <div class="col-md-6 col-sm-6 col-12 mb-sm-0 mb-6">
                                        <div class="mt-6 d-flex gap-2 justify-content-start">
                                            <?php
                                            if($gdnDATA['gdnStatus'] == GDN_STATUS_PROCUREMENT_MANAGER_APPROVED && $gdnDATA['transactionType'] == 'SALESORDER' && $currentUserRole == ROLE_SUPERADMIN) {
                                            ?>
<!--                                            <button class="btn btn-info mb-4" onclick="generateInvoice()">-->
<!--                                              <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-printer ti-xs me-2"></i>Invoice</span>-->
<!--                                            </button>-->

                                              <button class="btn btn-info mb-4" onclick="createInvoice()">
                                                <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-printer ti-xs me-2"></i>Invoice</span>
                                              </button>
                                            <?php
                                            }
                                            ?>
                                        </div>
                                      </div>
                                      <div class="col-md-6 col-sm-6">
                                        <div class="mt-6 d-flex gap-2 justify-content-end">
                                                                                                <?php
                                                                                                if($gdnDATA['gdnStatus'] == GDN_STATUS_PROCUREMENT_MANAGER_APPROVED  && $currentUserRole == ROLE_SUPERADMIN) {
                                                                                                    ?>
<!--                                                                                                    <button class="btn btn-primary" onclick="createGDN()">-->
<!--                                                                          <span class="d-flex align-items-center justify-content-center text-nowrap">-->
<!--                                                                            <i class="ti ti-device-floppy ti-xs me-2"></i>Create GDN-->
<!--                                                                          </span>-->
<!--                                                                                                    </button>-->
                                                                                                    <?php
                                                                                                }
                                                                                                //                          if($bolPresent && ($bolDATA['bolStatus'] != BOL_STATUS_PROCUREMENT_MANAGER_APPROVED && $bolDATA['bolStatus'] != BOL_CREATED)  && $currentUserRole == ROLE_SUPERADMIN) {
                                                                                                ?>
                                                <button class="btn btn-primary" onclick="saveGDN()">
                                                  <span class="d-flex align-items-center justify-content-center text-nowrap">
                                                    <i class="ti ti-device-floppy ti-xs me-2"></i>Save
                                                  </span>
                                                </button>

                                                <button class="btn btn-primary" onclick="printPP()">
                                                  <span class="d-flex align-items-center justify-content-center text-nowrap">
                                                    <i class="ti ti-printer ti-xs me-2"></i>Print PP
                                                  </span>
                                                </button>
                                                                                                <?php
                                                                                                //                          }
                                                                                                if(($gdnDATA['gdnStatus'] == GDN_STATUS_NEW || $gdnDATA['gdnStatus'] == GDN_STATUS_PROCUREMENT_MANAGER_REJECTED) && $currentUserRole == ROLE_SUPERADMIN) {
                                                                                                    ?>
                                                                                                    <button class="btn btn-primary" onclick="sendForApproval()">
                                                                                                  <span class="d-flex align-items-center justify-content-center text-nowrap">
                                                                                                    <i class="ti ti-file-like ti-xs me-2"></i>Send for Approval
                                                                                                  </span>
                                                                                                    </button>
                                                                                                    <?php
                                                                                                }
                                                                                                if (($currentUserRole == ROLE_SUPERADMIN) && $gdnDATA['gdnStatus'] == GDN_STATUS_PROCUREMENT_MANAGER_APPROVAL) {
                                                                                                    ?>
                                                                                                    <button class="btn btn-success" onclick="approveGDN()">
                                                                          <span class="d-flex align-items-center justify-content-center text-nowrap">
                                                                            <i class="ti ti-eye-check ti-xs me-2"></i>Approve
                                                                          </span>
                                                                                                    </button>
                                                                                                    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectReasonModal">
                                                                          <span class="d-flex align-items-center justify-content-center text-nowrap">
                                                                            <i class="ti ti-circle-dashed-x ti-xs me-2"></i>Reject
                                                                          </span>
                                                                                                    </button>
                                                                                                    <?php
                                                                                                }if (($currentUserRole == ROLE_SUPERADMIN) && $gdnDATA['gdnStatus'] == GDN_STATUS_PROCUREMENT_MANAGER_APPROVED) {
                                                                                                    ?>
                                                                                                    <button class="btn btn-primary" onclick="printGDN()">
                                                                          <span class="d-flex align-items-center justify-content-center text-nowrap">
                                                                            <i class="ti ti-printer ti-xs me-2"></i>Print GDN
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
                                                <label class="form-label" for="originWH">Origin:</label>
                                                <input type="text" id="originWH" class="form-control mb-5" readonly value="<?=getWarehouseNameFromID($originWHID)?>">
                                                <input type="hidden" id="originWHID" value="<?=$originWHID?>">
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-sm-5 col-12 mb-sm-0 mb-6">
                                            <div class="mb-4">
                                                <label class="form-label" for="destinationWH">Destination:
                                                </label>
                                                <input type="text" id="destinationWH" class="form-control mb-5" readonly value="<?=$orderType == 'SALESORDER' ? getFormattedCustomerAddressByID($customerID): getWarehouseNameFromID($destinationWHID) ;?>">
                                                <?php
                                                if($orderType == 'SALESORDER'){
                                                    ?>
                                                    <input type="hidden" id="customerID" value="<?=$customerID?>">
                                                    <input type="hidden" id="destinationWHID" value="">
                                                    <?php
                                                } else if($orderType == 'TRANSFER') {
                                                    ?>
                                                    <input type="hidden" id="customerID" value="">
                                                    <input type="hidden" id="destinationWHID" value="<?=$destinationWHID?>">
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--Grey Header Section-->
                                <div class="card-body invoice-preview-header rounded "> <!-- col-md-9 col-12 mb-4 -->
                                    <div class="row">
                                        <!-- First Row of Input Fields -->
                                        <div class="col-md-4 col-12 mb-4">
                                            <!--                                            <input type="hidden" id="bolId" value="--><?php //=$bolPresent && !empty($bolDATA['bolId']) ? $bolDATA['bolId'] : ''?><!--">-->
                                            <dl class="row mb-0">
<!--                                              <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">-->
<!--                                                <span class="fw-normal">--><?php //= getDisplayTransactionType($orderType)?><!--:</span>-->
<!--                                              </dt>-->
<!--                                              <dd class="col-md-8 col-sm-7">-->
<!--                                                <input type="text" id="salesOrder" class="form-control" readonly value="--><?php //=getSalesOrderNumberFromDocumentID($gdnDATA['documentId'])?><!--"/>-->
<!--                                              </dd>-->
                                                <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                                                    <span class="h5 text-capitalize mb-0 text-nowrap">GDN</span>
                                                </dt>
                                                <dd class="col-md-8 col-sm-7">
                                                    <div class="input-group input-group-merge disabled">
                                                        <span class="input-group-text">#</span>
                                                        <input name="gdnNumber" id="gdnNumber" type="text" class="form-control" disabled placeholder="Will be generated once saved / confirmed" value="<?=$gdnNumber?>"/>
                                                    </div>
                                                    <input name="gdnID" id="gdnID" type="hidden" value="<?=$gdnID?>"/>
                                                </dd>
                                                <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                                                    <span class="fw-normal">Carrier:</span>
                                                </dt>
                                                <dd class="col-md-8 col-sm-7">
                                                    <input type="text" id="carrier" class="form-control" value="<?=$gdnDATA['carrier']?>"/>
                                                </dd>
                                                <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                                                    <span class="fw-normal">Carrier Waybill#:</span>
                                                </dt>
                                                <dd class="col-md-8 col-sm-7">
                                                    <input type="text" id="carrierWayBillNo" class="form-control" value="<?=$gdnDATA['carrierWaybill']?>"/>
                                                </dd>
                                            </dl>
                                        </div>

                                        <div class="col-md-4 col-12 mb-4">
                                            <dl class="row mb-0">
                                                <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                                                    <span class="fw-normal">Package Value:</span>
                                                </dt>
                                                <dd class="col-md-8 col-sm-7">
                                                    <input type="text" id="packageValue" class="form-control" value="<?=$gdnDATA['packageValue']?>"/>
                                                </dd>
                                                <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                                                    <span class="fw-normal">Box Qty:</span>
                                                </dt>
                                                <dd class="col-md-8 col-sm-7">
                                                    <input type="text" id="boxQty" class="form-control" value="<?=$gdnDATA['boxQty']?>"/>
                                                </dd>
                                                <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                                                    <span class="fw-normal">Shipping Date:</span>
                                                </dt>
                                                <dd class="col-md-8 col-sm-7">
                                                    <input name="shippingDate" id="shippingDate" type="text" class="form-control gmm-date-format" placeholder="DD - MMM - YYYY" value="<?= $gdnDATA['shippingDate'] ?>"/>
                                                </dd>
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
                                                    <input type="text" id="taxes" class="form-control" value="<?=$gdnDATA['taxes']?>" placeholder="SAR 0.00"/>
                                                </dd>
                                                <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                                                    <span class="fw-normal">Shipping Total:</span>
                                                </dt>
                                                <dd class="col-md-6 col-sm-7">
                                                    <input type="text" id="shippingCost" class="form-control" value="<?=$gdnDATA['shippingCost']?>" placeholder="SAR 0.00"/>
                                                </dd>
                                                <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                                                    <span class="fw-normal"><strong>Cost Summary:</strong></span>
                                                </dt>
                                                <dd class="col-md-6 col-sm-7">
                                                </dd>
                                                <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                                                    <span class="fw-normal" <?= (empty($gdnDATA['insurance']) || $gdnDATA['insurance'] == '0.00') ? 'style="display:none"' : '' ?>>Insurance:</span>
                                                </dt>
                                                <dd class="col-md-6 col-sm-7">
                                                    <input type="text" id="insurance" class="form-control" value="<?= (!empty($gdnDATA['insurance']) && $gdnDATA['insurance'] != '0.00') ? $gdnDATA['insurance'] : '' ?>"
                                                           placeholder="SAR 0.00" <?= (empty($gdnDATA['insurance']) || $gdnDATA['insurance'] == '0.00') ? 'style="display:none"' : '' ?>/>
                                                </dd>
                                                <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                                                    <span class="fw-normal" <?= (empty($gdnDATA['foreignTransactionFee']) || $gdnDATA['foreignTransactionFee'] == '0.00') ? 'style="display:none"' : '' ?>>Foreign Transaction Fee:</span>
                                                </dt>
                                                <dd class="col-md-6 col-sm-7">
                                                    <input type="text" id="foreigntransactionfees" class="form-control" value="<?= (!empty($gdnDATA['foreignTransactionFee']) && $gdnDATA['foreignTransactionFee'] != '0.00') ? $gdnDATA['foreignTransactionFee'] : '' ?>"
                                                           placeholder="SAR 0.00" <?= (empty($gdnDATA['foreignTransactionFee']) || $gdnDATA['foreignTransactionFee'] == '0.00') ? 'style="display:none"' : '' ?>/>
                                                </dd>
                                                <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                                                    <span class="fw-normal" <?= (empty($gdnDATA['customDuties']) || $gdnDATA['customDuties'] == '0.00') ? 'style="display:none"' : '' ?>>Custom Duties:</span>
                                                </dt>
                                                <dd class="col-md-6 col-sm-7">
                                                    <input type="text" id="customduties" class="form-control" value="<?= (!empty($gdnDATA['customDuties']) && $gdnDATA['customDuties'] != '0.00') ? $gdnDATA['customDuties'] : '' ?>"
                                                           placeholder="SAR 0.00" <?= (empty($gdnDATA['customDuties']) || $gdnDATA['customDuties'] == '0.00') ? 'style="display:none"' : '' ?>/>
                                                </dd>
                                                <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                                                    <span class="fw-normal" <?= (empty($gdnDATA['surcharge']) || $gdnDATA['surcharge'] == '0.00') ? 'style="display:none"' : '' ?>>Surcharge:</span>
                                                </dt>
                                                <dd class="col-md-6 col-sm-7">
                                                    <input type="text" id="surcharge" class="form-control" value="<?= (!empty($gdnDATA['surcharge']) && $gdnDATA['surcharge'] != '0.00') ? $gdnDATA['surcharge'] : '' ?>"
                                                           placeholder="SAR 0.00" <?= (empty($gdnDATA['surcharge']) || $gdnDATA['surcharge'] == '0.00') ? 'style="display:none"' : '' ?>/>
                                                </dd>
                                                <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                                                    <span class="fw-normal" <?= (empty($gdnDATA['other']) || $gdnDATA['other'] == '0.00') ? 'style="display:none"' : '' ?>>Other:</span>
                                                </dt>
                                                <dd class="col-md-6 col-sm-7">
                                                    <input type="text" id="other" class="form-control" value="<?= (!empty($gdnDATA['other']) && $gdnDATA['other'] != '0.00') ? $gdnDATA['other'] : '' ?>"
                                                           placeholder="SAR 0.00" <?= (empty($gdnDATA['other']) || $gdnDATA['other'] == '0.00') ? 'style="display:none"' : '' ?>/>
                                                </dd>


                                                <?php
                                                if($orderType == 'EORDER') {
                                                    ?>
                                                    <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">

                                                    </dt>
                                                    <dd class="col-md-8 col-sm-7 d-flex align-items-center mt-3">
                                                        <div class="form-check mb-0">
                                                            <input class="form-check-input" type="checkbox" id="addShipping">
                                                            <label class="form-check-label ms-2" for="addShipping">
                                                                Check the box to add shipping on sales order
                                                            </label>
                                                        </div>
                                                    </dd>
                                                    <?php
                                                }
                                                ?>
                                                <dt class="col-md-6 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                                                    <span class="fw-normal"><strong>Subtotal:</strong></span>
                                                </dt>
                                                <dd class="col-md-6 col-sm-7">
                                                    <span id="shippingSubTotal" class="fw-medium">SAR <?=$gdnDATA['shippingSubTotal']?></span>
                                                </dd>
                                            </dl>
                                        </div>
                                    </div>

                                    <!--Grey Header Section Ends-->
                                </div>

                                <div class="card-body pt-0 px-0">
                                    <form class="source-item">
                                        <div class="mb-4" data-repeater-list="line-item">
                                            <?php
                                            $itemCount = 0;

                                            while ($lineitemrow = mysqli_fetch_assoc($lineitemRes)) {
                                                ?>
                                                <div class="repeater-wrapper pt-0 pt-md-6" data-repeater-item>
                                                    <div class="d-flex border rounded position-relative pe-0">
                                                        <div class="row w-100 p-3">
                                                            <div class="col-md-2 col-12 mb-md-0 mb-4">
                                                                <p class="h6 mb-1 itemNumber">Item #<?= ++$itemCount ?></p>
                                                                <input name="sparepartpartnumber" type="text" class="form-control mb-5" readonly
                                                                       value="<?= getInternalReferenceNumberForSparepartID($lineitemrow['itemId']); ?>" />
                                                                <input name="sparepartID" type="hidden" class="form-control mb-5" readonly
                                                                       value="<?= $lineitemrow['itemId'] ?>" />
                                                            </div>
                                                            <div class="col-md-2 col-12 mb-md-0 mb-4">
                                                                <p class="h6 mb-1">Description</p>
                                                                <input name="sparepartdescription" type="text" class="form-control mb-5" readonly
                                                                       value="<?= getItemDescriptionForSparepartID($lineitemrow['itemId']) ?>" />
                                                            </div>
                                                            <div class="col-md-1 col-12 mb-md-0 mb-4">
                                                                <p class="h6 mb-1">Qty</p>
                                                                <input name="qty" type="text" class="form-control numbers-only calculation-trigger"
                                                                       readonly placeholder="Qty" min="1" required value="<?= $lineitemrow['quantity'] ?>" />
                                                            </div>
                                                            <div class="col-md-1 col-12 mb-md-0 mb-4">
                                                                <p class="h6 mb-1">Lot#</p>
                                                                <input name="lot" type="text" class="form-control" placeholder="Lot" min="1"
                                                                       required readonly value="<?= $lineitemrow['lotSerial'] ?>" />
                                                            </div>
                                                            <!--                                                            --><?php
                                                            //                                                            if($gdnDATA['transactionType'] != 'TRANSFER') {
                                                            //                                                                ?>
                                                            <!--                                                                <div class="col-md-2 col-12 mb-md-0 mb-4">-->
                                                            <!--                                                                    <p class="h6 mb-1">Status</p>-->
                                                            <!--                                                                    <input name="status" type="text" class="form-control" placeholder="Status" min="1"-->
                                                            <!--                                                                           required readonly value="--><?php //= $lineitemrow['orderWHStatus'] ?><!--" />-->
                                                            <!--                                                                </div>-->
                                                            <!--                                                                --><?php
                                                            //                                                            }
                                                            //                                                            ?>
                                                            <div class="col-md-2 col-12 mb-md-0 mb-4">
                                                                <p class="h6 mb-1">Shipment#</p>
                                                                <input name="shipment#" type="text" class="form-control" placeholder="Shipment#"
                                                                       required readonly value="<?= getShipmentNumberFromOrderAndIPickAndPackID($orderManagementID, $lineitemrow['pickAndPackLineItemId']) ?>" />
                                                            </div>
                                                            <!--                                                            --><?php
                                                            //                                                            if($gdnDATA['transactionType'] != 'TRANSFER') {
                                                            //                                                                ?>
                                                            <!--                                                                <div class="col-md-2 col-12 mb-md-0 mb-4">-->
                                                            <!--                                                                    <p class="h6 mb-1">WH</p>-->
                                                            <!--                                                                    <select name="wh" class="form-control mb-6" data-placeholder="Select WH">-->
                                                            <!--                                                                        <option value="">Select WH</option>-->
                                                            <!--                                                                        --><?php
                                                            //                                                                        $res = $db->query("SELECT * FROM warehouses WHERE active = 1");
                                                            //                                                                        while ($row = mysqli_fetch_assoc($res)) {
                                                            //                                                                            $stockQty = getStockOnHandQtyForSparePart($lineitemrow['itemId'], $row['warehouseId']) ?? 0;
                                                            //                                                                            ?>
                                                            <!--                                                                            <option --><?php //if($row['warehouseId'] == $lineitemrow['originWHId']) echo "selected"; ?>
                                                            <!--                                                                                value="--><?php //= $row['warehouseId'] ?><!--"> --><?php //= $row['warehouseName'] . ' | ' . $stockQty ?><!--</option>-->
                                                            <!--                                                                            --><?php
                                                            //                                                                        }
                                                            //                                                                        ?>
                                                            <!--                                                                    </select>-->
                                                            <!--                                                                </div>-->
                                                            <!--                                                                --><?php
                                                            //                                                            }
                                                            //                                                            ?>
                                                        </div>
                                                        <div class="row p-3 justify-content-end"></div>
                                                        <div class="d-flex flex-column align-items-center justify-content-between border-start p-2" style="display: none">
                                                            <i class="ti ti-x ti-lg cursor-pointer" data-repeater-delete style="display: none"></i>
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
                    <?php include_once __DIR__ . "/../../../includes/dashboard/dashboard_footer_section.php"; ?>
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
<!--  Cost Summary Modal-->
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
                                if ($gdnDATA['gdnStatus'] == GDN_STATUS_PROCUREMENT_MANAGER_APPROVAL) {
                                    ?>
                                    <button type="button" class="btn btn-danger" onclick="rejectGDN()">Reject</button>
                                    <?php
                                }
                                ?>
            </div>
        </div>
    </div>
</div>

<!-- / Layout wrapper -->
<?php include_once __DIR__ . "/../../../includes/dashboard/dashboard_footer_scripts.php"; ?>
<script>
  //const bolPresent = <?php //= $bolPresent ? 'true' : 'false' ?>//;
  //console.log('BOL Present: ',bolPresent);
  //
  //console.log('ShipmentId: ', <?php //=$shipmentID?>//);


  $(document).ready(function() {
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

      $('span#shippingSubTotal').text(`SAR ${total.toFixed(2)}`);
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
  });

  function printGDN() {

    let documentID = "<?= $gdnID ?>";
    const baseUrl = window.location.origin;
    const relativeUrl = '/ajax/ordermanagement/generate_pdf.php?gdnID=' + documentID;
    const fullUrl = baseUrl + relativeUrl;
    window.open(fullUrl, '_blank');

  }

  function printPP() {

    let documentID = "<?= $gdnID ?>";
    const baseUrl = window.location.origin;
    const relativeUrl = '/ajax/ordermanagement/generate_pp_pdf.php?gdnID=' + documentID;
    const fullUrl = baseUrl + relativeUrl;
    window.open(fullUrl, '_blank');

  }

  function createInvoice() {
      blockArea($('body')); // Optional: Show loading state while checking
      $.ajax({
        url: '/ajax/invoice/check_invoice_exists.php', // Backend script to check PO existence
        type: 'POST',
        data: { refDocID: '<?=$gdnID ?>' }, // Send the gdnID to check
        dataType: 'json',
        success: function(response) {
          unBlockArea($('body')); // Hide loading state
          if (response.status === 'success') {
            if (response.invoiceExists && response.invoiceId) {
              // Invoice exists, navigate to the edit page for that Invoice
              const baseUrl = window.location.origin;
              const editUrl = baseUrl + '/invoice/edit/' + response.invoiceId;
              window.open(editUrl, '_blank');
            } else {
              // No Invoice exists, navigate to the new Invoice creation page
              const baseUrl = window.location.origin;
              const params = new URLSearchParams();
              params.append('refDocID', '<?=htmlspecialchars($gdnDATA["documentId"], ENT_QUOTES) ?>');
              params.append('gdnID', '<?=htmlspecialchars($gdnID, ENT_QUOTES) ?>');
              const newUrl = baseUrl + '/invoice/new?' + params.toString();
              window.open(newUrl, '_blank');
            }
          } else {
            // Error in checking PO existence
            showErrorMessage('Failed to check Invoice existence: ' + response.message);
          }
        },
        error: function(xhr, status, error) {
          unBlockArea($('body')); // Hide loading state
          showErrorMessage('Error checking Invoice existence. Please try again.');
          console.error('Error:', error);
        }
      });
  }

  function extractNumbers(value) {
    if (!value) return value;
    // Extracts numbers (including decimals)
    return String(value).replace(/[^\d.-]/g, '');
  }

  function prepareGDNData() {
    let repeaterData = $('.source-item').repeaterVal();

    let requestData = {
      lineItems: repeaterData,
      gdnID: $('#gdnID').val(),
      orderManagementID: <?=$orderManagementID?>,
      documentID: <?=$gdnDATA['documentId']?>,
      orderType: '<?=$orderType?>',
      originWHID: $('#originWHID').val(),
      destinationWHID: $('#destinationWHID').val(),
      customerID: $('#customerID').val(),
      carrier: $('#carrier').val(),
      carrierWayBill: $('#carrierWayBillNo').val(),
      shippingDate: $('#shippingDate').val(),
      packageValue: $('#packageValue').val(),
      boxQty: $('#boxQty').val(),
      taxes: $('#taxes').val() || '0.00',
      shippingCost: $('#shippingCost').val() || '0.00',
      customduties: $('#customduties').val() || '0.00',
      foreigntransactionfees: $('#foreigntransactionfees').val() || '0.00',
      insurance: $('#insurance').val() || '0.00',
      surcharge: $('#surcharge').val() || '0.00',
      other: $('#other').val() || '0.00',
      shippingSubTotal: extractNumbers($('#shippingSubTotal').text()) || '0.00'
    };
    console.log('RequestData');
    console.log(requestData);

    return requestData;
  }
  //
  //function stripSAR(value) {
  //  if (value && value.includes('SAR')) {
  //    return value.replace('SAR ', '');
  //  }
  //  return value;
  //}
  //
  //
  //Save GDN
  function saveGDN() {

    if ($('#carrier').val().length > 0 && $('#carrierWayBillNo').val().length > 0) {
      blockArea($('body'));
      let requestData = prepareGDNData();

      $.ajax(
        {
          url: '/ajax/logistics/save_gdn.php', // Update with your PHP script URL
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
              showSuccessMessage(response.message, gotoPage, "/order-management/picknpack/edit/" + documentId);


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
      showErrorMessage("Please Enter the necessary fields");
    }
  }

  function sendForApproval() {
    saveGDNThread('<?=GDN_STATUS_PROCUREMENT_MANAGER_APPROVAL ?>');
  }

  function approveGDN() {
    let documentID = '<?=$gdnDATA['gdnId']?>';
    updateGDNStatus(documentID, "<?=GDN_STATUS_PROCUREMENT_MANAGER_APPROVED?>");
  }

  function rejectGDN() {
    let documentID = '<?=$gdnDATA['gdnId']?>';
    let rejectReason = $('#rejectReason').val();
    console.log(rejectReason);

    if (rejectReason.length) {
      updateGDNStatus(documentID, "<?=GDN_STATUS_PROCUREMENT_MANAGER_REJECTED ?>", true, rejectReason);
    } else {
      showErrorMessage('You must enter a reason for rejection');
    }
  }
  function createGDN() {
    let documentID = '<?=$gdnDATA['gdnId']?>';
    updateGDNStatus(documentID, "<?=GDN_CREATED?>");
  }

  function saveGDNThread(status) {

    if ($('#carrier').val().length > 0 && $('#carrierWayBillNo').val().length > 0) {
      // blockArea($('body'));
      let requestData = prepareGDNData();
      $.ajax({
        url: '/ajax/logistics/save_gdn.php', // Update with your PHP script URL
        method: 'POST',
        data: JSON.stringify(requestData), // Send the combined data as a JSON string
        contentType: 'application/json', // Indicate that the data is JSON
        dataType: 'json', // Expect a JSON response
        success: function(response) {
          console.log('Server Response after saving GDN:', response);

          if (typeof response === 'string') {
            response = JSON.parse(response);
          }

          console.log('Response: ' + response.status);
          // Check the response status
          if (response.status === 'success') {
            unBlockArea($('body'));
            let documentID = response.documentId;
            updateGDNStatus(documentID, status);
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
      showErrorMessage('Please Enter the necessary fields');
    }
  }

  function updateGDNStatus(documentID, status, showMessage = true, rejectReason = '') {

    console.log('Updating GDN Status: ' + documentID + ' - ' + status + ' - ' + showMessage + ' - ' + rejectReason);

    console.log(rejectReason);
    var formData = new FormData();
    formData.append('documentID', documentID);
    formData.append('status', status);
    formData.append('rejectReason', rejectReason);

    console.log(formData);


    $.ajax({
      url: '/ajax/logistics/update_gdn_status.php',
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
            showSuccessMessage(message, gotoPage, '/order-management/picknpack/edit/' + <?=$gdnID?>);
        }
      },

      error: function(error) {
        console.log(error);
      }
    });

  }

  function generateInvoice() {
    let itemIds = '<?=$itemIds?>';
    console.log(itemIds);
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
    console.log('Spare part Item Changed');
    const selectedData = e.params.data;

    try {

      const selectedSparepartID = selectedData.id;
      const selectedInternalReference = selectedData.internalReference;
      const selectedDesc = selectedData.desc;
      const selectedUOMID = selectedData.uomid;

      setTimeout(function() {

        var lastIndex = $('.source-item [data-repeater-item]').length - 1;
        console.log('Last Added Index:', lastIndex);

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
    totalAmountSpan.html('SAR ' + toTwoDecimal(totalAmount).toString());

  }


  // Initialize Select2 with AJAX
  function initializeSelect2(selectElement, preselectedValue = null) {
    console.log('preselectedValue: ' + preselectedValue);

    console.log(selectElement);
    // Initialize Select2
    $(selectElement).select2({
      placeholder: 'Select a part number',
      minimumInputLength: 3,
      ajax: {
        url: '/ajax/spareparts/get_spareparts.php',
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
</style>
</body>
</html>