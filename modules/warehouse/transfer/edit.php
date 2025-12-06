<?php
$PAGE_ID = "TRANSFER_EDIT";
include_once __DIR__ . "/../../../includes/baseIncludes.php";
include_once __DIR__ . "/../../../includes/auth_check.php";

$transferID = "";
$transferID = filter_input(INPUT_GET, 'transferID', FILTER_VALIDATE_INT);
if ($transferID === null || $transferID === false || filter_var($transferID, FILTER_VALIDATE_INT) === false) {
    header("location:/transfer/list");
    exit();
}

?>
<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
<head>
    <?php include_once __DIR__ . "/../../../includes/dashboard/dashboard_head_section.php"; ?>
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
    <?php include_once __DIR__ . "/../../../includes/dashboard/dashboard_head_section_post_vendor.php"; ?>
    <title>GrandMaster ERP | Transfer</title>
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
            <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
                <?php include_once __DIR__ . "/../../../includes/dashboard/top_navbar.php"; ?>
            </nav>
            <!-- / Navbar -->
            <!-- Content wrapper -->
            <div class="content-wrapper">

                <?php

                $transferDATA = "";
                $res = $db->query("SELECT * FROM transfer_documents WHERE active = 1 and transferId = ?s", $transferID);
                while ($row = mysqli_fetch_assoc($res)) {
                    $transferDATA = $row;
                }


                $originWHID = $transferDATA['originWHId'];
                $destinationWHID = $transferDATA['destinationWHId'];

                $currentUserRole = getAuthenticatedUser()->getRoles()[0];
                $documentStatus = $transferDATA['transferStatus'];

                $orderStatus = $transferDATA['orderWHStatus'];

                $originWH = new Warehouse();
                $originWH->loadById($originWHID);

                $destinationWH = new Warehouse();
                $destinationWH->loadById($destinationWHID);

                $transferNumber = "DRAFT";
                if (!empty($transferDATA['transferNumberPrefix'])) {
                    $transferNumber = $transferDATA['transferNumberPrefix'] . $transferDATA['transferNumber'];
                }

                if($orderStatus == ORDER_WH_STATUS_RESERVED) {

//                  $orderRes = $db->query("SELECT * FROM order_management_documents WHERE transactionType = ?s AND documentId = ?s", 'TRANSFER', $transferID);
//                  $orderRow = mysqli_fetch_assoc($orderRes);
//                  $orderId = $orderRow['orderManagementId'];

//                  $res = $db->query("SELECT * FROM shipments WHERE documentType = ?s and documentId = ?s", 'TRANSFER', $orderId);
//                  $row = mysqli_fetch_assoc($res);
//                  $shipmentId = $row['shipmentId'];
                }

                ?>
                <!-- Content -->
                <div class="container-xxl flex-grow-1 container-p-y">
                    <div class="card mb-6">
                        <div class="card-widget-separator-wrapper">
                            <div class="card-body card-widget-separator py-2">
                                <div class="row gy-1 gy-sm-1">
                                  <div class="col-6">
                                    <h4 class="my-0"><?= getTransferNumberFromDocumentID($transferDATA['transferId']) ?> | Edit
                                      TRANSFER</h4>
                                  </div>
                                  <div class="col-6 text-end">
                                      <?php
                                      switch ($transferDATA['transferStatus']) {
                                          case TRANSFER_STATUS_NEW:
                                              ?>
                                            <button type="button"
                                                    class="btn btn-label-linkedin text-nowrap d-inline-flex position-relative me-4">
                                              DRAFT
                                            </button>
                                              <?php
                                              break;

                                          case TRANSFER_STATUS_PROCUREMENT_MANAGER_APPROVED:
                                              ?>
                                            <label
                                              class="no-hand btn btn-label-success text-nowrap d-inline-flex position-relative me-4">
                                              PROCUREMENT MANAGER APPROVED
                                            </label>
                                              <?php
                                              break;

                                          case TRANSFER_STATUS_PROCUREMENT_MANAGER_REJECTED:
                                              ?>
                                            <label
                                              class="no-hand btn btn-label-danger text-nowrap d-inline-flex position-relative me-4">
                                              PROCUREMENT MANAGER REJECTED
                                            </label>
                                              <?php
                                              break;

                                          default:
                                              ?>
                                            <button type="button"
                                                    class="btn btn-label-info text-nowrap d-inline-flex position-relative me-4">
                                                <?= $transferDATA['transferStatus'] ?>
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
                    $documentStatus = $transferDATA['transferStatus'];
                    if ($documentStatus == TRANSFER_STATUS_PROCUREMENT_MANAGER_REJECTED) {
                        $rejectReason = getRejectReasonForTransferID($transferID);
                        ?>
                      <div class="alert alert-solid-danger d-flex align-items-center" role="alert">
                        <span class="alert-icon rounded">
                          <i class="ti ti-ban"></i>
                        </span> Rejection Reason: <?= $rejectReason ?>
                      </div>
                        <?php
                    }
                    if(checkIfTransferHasEmptyReasons($transferID)) {

                    ?>

                    <div class="alert alert-solid-info" role="alert">
                      Please select Reason for all line items to proceed further
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
                                      <div class="col-12 mb-4">
                                        <div class="d-flex gap-2 justify-content-end">
                                          <button class="btn btn-primary mb-4" onclick="saveTransfer()">
                                            <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-device-floppy ti-xs me-2"></i>Save</span>
                                          </button>
                                          <?php
                                          if (($currentUserRole == ROLE_SUPERADMIN) && ($documentStatus == TRANSFER_STATUS_NEW || $documentStatus == TRANSFER_STATUS_PROCUREMENT_MANAGER_REJECTED) && !checkIfTransferHasEmptyReasons($transferID)) {
                                          ?>
                                          <button class="btn btn-primary mb-4" onclick="sendForApproval()">
                                            <span class="d-flex align-items-center justify-content-center text-nowrap">
                                              <i class="ti ti-device-floppy ti-xs me-2"></i>Send for Approval
                                            </span>
                                          </button>
                                            <?php
                                          }
                                          if (($currentUserRole == ROLE_SUPERADMIN) && $documentStatus == TRANSFER_STATUS_PROCUREMENT_MANAGER_APPROVAL) {
                                              ?>
                                            <button class="btn btn-success mb-4" onclick="approveTransfer()">
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
                                          if (($currentUserRole == ROLE_SUPERADMIN) && $documentStatus == TRANSFER_STATUS_PROCUREMENT_MANAGER_APPROVED) {
                                            ?>
                                          <button class="btn btn-primary mb-4" onclick="sendToPP()">
                                            <span class="d-flex align-items-center justify-content-center text-nowrap">
                                              <i class="ti ti-device-floppy ti-xs me-2"></i>Send to Pick & Pack
                                            </span>
                                          </button>
                                          <?php
                                          }
                                          if (($currentUserRole == ROLE_SUPERADMIN) && $documentStatus == TRANSFER_STATUS_SENT_TO_PICK_AND_PACK) {
                                          ?>
                                          <!--
                                          <button class="btn btn-primary mb-4" onclick="window.open('/shipment/order/edit/<?=$shipmentId?>', '_blank')">
                                            <span class="d-flex align-items-center justify-content-center text-nowrap">
                                              <i class="ti ti-thumb-up ti-xs me-2"></i>Confirm Sending
                                            </span>
                                          </button>
                                          -->

                                          <?php
                                          }
                                          ?>
                                        </div>
                                      </div>
                                        <div class="col-md-5 col-sm-5 col-12 mb-sm-0 mb-6" style="<?= $documentStatus == TRANSFER_STATUS_SENT_TO_PICK_AND_PACK ? 'display: none;' : '' ?>">
                                            <div class="mb-4">
                                                <label class="form-label" for="originWH">Origin WH:</label>
                                                <select name="originWH" id="originWH" class="form-select mb-4 w-50 selectpicker w-100" data-style="btn-default" data-live-search="true" tabindex="null">
                                                    <option value="">Select Origin</option>
                                                    <?php
                                                    $res = $db->query("SELECT * FROM warehouses WHERE active ='1'");
                                                    while ($row = mysqli_fetch_assoc($res)) {
                                                        ?>
                                                        <option <?php if($row['warehouseId'] == $transferDATA['originWHId']) echo "selected"; ?> value="<?= $row['warehouseId'] ?>"><?= $row['warehouseName'] . " | " . $row['warehouseCode'] ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-sm-5 col-12 mb-sm-0 mb-6" style="<?= $documentStatus == TRANSFER_STATUS_SENT_TO_PICK_AND_PACK ? 'display: none;' : '' ?>">
                                            <div class="mb-4">
                                                <label class="form-label" for="destinationWH">Destination WH:</label>
                                                <select name="destinationWH" id="destinationWH" class="form-select mb-4 w-50 selectpicker w-100" data-style="btn-default" data-live-search="true" tabindex="null">
                                                    <option value="">Select Destination</option>
                                                    <?php
                                                    $res = $db->query("SELECT * FROM warehouses WHERE active ='1'");
                                                    while ($row = mysqli_fetch_assoc($res)) {
                                                        ?>
                                                      <option <?php if($row['warehouseId'] == $transferDATA['destinationWHId']) echo "selected"; ?> value="<?= $row['warehouseId'] ?>"><?= $row['warehouseName'] . " | " . $row['warehouseCode'] ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--Grey Header Section-->
                                <div class="card-body invoice-preview-header rounded">
                                    <!--                  <div class="row" id="noWHInitialsDiv">-->
                                    <!--                    <div class="col-12 text-center">-->
                                    <!--                      Choose Origin and Destination WH-->
                                    <!--                    </div>-->
                                    <!--                  </div>-->

                                    <!-- Origin and Destination Warehouse Details Row -->
                                    <div class="row" id="rowHeader" style="<?= $documentStatus == TRANSFER_STATUS_SENT_TO_PICK_AND_PACK ? 'display: none;' : '' ?>">
                                        <!-- Origin Warehouse Details (Left) -->
                                        <div class="col-md-6 col-12 mb-4">
                                            <div class="svg-illustration mb-6 gap-2 align-items-center">
                                                <div>
                                                    <span class="app-brand-text fw-bold fs-4 ms-50" id="originWarehouseName"><?= $originWH->warehouseName ?></span>
                                                </div>
                                                <div>
                                                    <span class="app-brand-text fw-bold fs-5 ms-50" id="originWarehouseCode"><?= $originWH->warehouseCode ?></span>
                                                </div>
                                            </div>
                                            <p class="mb-2" id="originWarehouseAddressLine1"><?= $originWH->addressLine1 ?></p>
                                            <p class="mb-2" id="originWarehouseAddressLine2"><?= $originWH->addressLine2 ?></p>
                                            <p class="mb-2" id="originWarehouseCityPostalCode"><?= getCityFromID($originWH->cityId) . " " . $originWH->postalCode ?></p>
                                            <p class="mb-2" id="originWarehouseStateCountry"><?= getStateFromID($originWH->stateId) . " " . getCountryFromID($originWH->countryId) ?></p>
                                            <p class="mb-2" id="originWarehouseContact">Email: <?= $originWH->email ?></p>
                                            <p class="mb-2" id="originWarehousePhone">Contact: <?= $originWH->phone ?></p>
                                        </div>
                                        <!-- Destination Warehouse Details (Right) -->
                                        <div class="col-md-6 col-12 mb-4 text-md-end d-flex flex-column align-items-md-end">
                                            <div class="svg-illustration mb-6 gap-2 align-items-center">
                                                <div>
                                                    <span class="app-brand-text fw-bold fs-4 ms-50" id="destinationWarehouseName"><?= $destinationWH->warehouseName ?></span>
                                                </div>
                                                <div>
                                                    <span class="app-brand-text fw-bold fs-5 ms-50" id="destinationWarehouseCode"><?= $destinationWH->warehouseCode ?></span>
                                                </div>
                                            </div>
                                            <p class="mb-2" id="destinationWarehouseAddressLine1"><?= $destinationWH->addressLine1 ?></p>
                                            <p class="mb-2" id="destinationWarehouseAddressLine2"><?= $destinationWH->addressLine2 ?></p>
                                            <p class="mb-2" id="destinationWarehouseCityPostalCode"><?= getCityFromID($destinationWH->cityId) . " " . $destinationWH->postalCode ?></p>
                                            <p class="mb-2" id="destinationWarehouseStateCountry"><?= getStateFromID($destinationWH->stateId) . " " . getCountryFromID($destinationWH->countryId) ?></p>
                                            <p class="mb-2" id="destinationWarehouseContact">Email: <?= $destinationWH->email ?></p>
                                            <p class="mb-2" id="destinationWarehousePhone">Contact: <?= $destinationWH->phone ?></p>
                                        </div>
                                    </div>

                                    <!-- Input Fields in Two Rows -->
                                    <div class="row">
                                        <!-- First Row of Input Fields -->
                                        <div class="col-md-6 col-12 mb-4">
                                            <dl class="row mb-0">
                                                <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                                                    <span class="h5 text-capitalize mb-0 text-nowrap">Transfer</span>
                                                </dt>
                                                <dd class="col-md-8 col-sm-7">
                                                    <div class="input-group input-group-merge disabled">
                                                        <span class="input-group-text">#</span>
                                                        <input name="transferNumber" id="transferNumber" type="text" class="form-control" disabled placeholder="Will be generated once saved / confirmed" value="<?= $transferNumber ?>"/>
                                                    </div>
                                                    <input name="transferID" id="transferID" type="hidden" value="<?= $transferDATA['transferId'] ?>"/>
                                                </dd>
                                                <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                                                    <span class="fw-normal">Date Created:</span>
                                                </dt>
                                                <dd class="col-md-8 col-sm-7">
                                                    <input name="transferDate" id="transferDate" type="text" class="form-control gmm-date-format" placeholder="DD - MMM - YYYY" value="<?= $transferDATA['transferDateCreated']?>"/>
                                                </dd>
                                              <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center" style="<?= $documentStatus != TRANSFER_STATUS_SENT_TO_PICK_AND_PACK ? 'display: none;' : '' ?>">
                                                <span class="h5 text-capitalize mb-0 text-nowrap" style="<?= $documentStatus != TRANSFER_STATUS_SENT_TO_PICK_AND_PACK ? 'display: none;' : '' ?>">Additional Details</span>
                                              </dt>
                                              <dd class="col-md-8 col-sm-7" style="<?= $documentStatus != TRANSFER_STATUS_SENT_TO_PICK_AND_PACK ? 'display: none;' : '' ?>">
                                              </dd>
                                              <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center" style="<?= $documentStatus != TRANSFER_STATUS_SENT_TO_PICK_AND_PACK ? 'display: none;' : '' ?>">
                                                <span class="fw-normal" style="<?= $documentStatus != TRANSFER_STATUS_SENT_TO_PICK_AND_PACK ? 'display: none;' : '' ?>">Reference:</span>
                                              </dt>
                                              <dd class="col-md-8 col-sm-7" style="<?= $documentStatus != TRANSFER_STATUS_SENT_TO_PICK_AND_PACK ? 'display: none;' : '' ?>">
                                                <input type="text" id="reference" class="form-control" value="<?= $transferDATA['reference'] ?>"/>
                                              </dd>
                                              <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center" style="<?= $documentStatus != TRANSFER_STATUS_SENT_TO_PICK_AND_PACK ? 'display: none;' : '' ?>">
                                                <span class="fw-normal" style="<?= $documentStatus != TRANSFER_STATUS_SENT_TO_PICK_AND_PACK ? 'display: none;' : '' ?>">Notes:</span>
                                              </dt>
                                              <dd class="col-md-8 col-sm-7" style="<?= $documentStatus != TRANSFER_STATUS_SENT_TO_PICK_AND_PACK ? 'display: none;' : '' ?>">
                                                <input type="text" id="notes" class="form-control" value="<?= $transferDATA['notes'] ?>"/>
                                              </dd>
                                            </dl>
                                        </div>

                                        <div class="col-md-6 col-12 mb-4">
                                            <dl class="row mb-0">
                                                <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                                                    <span class="fw-normal">Sales Person:</span>
                                                </dt>
                                                <dd class="col-md-8 col-sm-7">
                                                    <input type="text" id="salesPerson" readonly class="form-control due-date" value="<?= getDisplayNameOfCurrentUser() ?>"/>
                                                </dd>
                                              <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                                                <span class="fw-normal">Reason for Transfer:</span>
                                              </dt>
                                              <dd class="col-md-8 col-sm-7">
                                                <input type="text" id="transferReason" class="form-control" value="<?= $transferDATA['transferReason'] ?>"/>
                                              </dd>
                                              <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                                                <span class="h5 text-capitalize mb-0 text-nowrap" style="<?= $documentStatus != TRANSFER_STATUS_SENT_TO_PICK_AND_PACK ? 'display: none;' : '' ?>">Shipping Details</span>
                                              </dt>
                                              <dd class="col-md-8 col-sm-7" style="<?= $documentStatus != TRANSFER_STATUS_SENT_TO_PICK_AND_PACK ? 'display: none;' : '' ?>">
                                              </dd>
                                              <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                                                <span class="fw-normal" style="<?= $documentStatus != TRANSFER_STATUS_SENT_TO_PICK_AND_PACK ? 'display: none;' : '' ?>">ETA:</span>
                                              </dt>
                                              <dd class="col-md-8 col-sm-7" style="<?= $documentStatus != TRANSFER_STATUS_SENT_TO_PICK_AND_PACK ? 'display: none;' : '' ?>">
                                                <input name="etaDate" id="etaDate" type="text" class="form-control gmm-date-format"
                                                       placeholder="DD - MMM - YYYY" value="<?= $transferDATA['etaDate'] ?>" />
                                              </dd>
                                                <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                                                    <span class="fw-normal" style="<?= $documentStatus != TRANSFER_STATUS_SENT_TO_PICK_AND_PACK ? 'display: none;' : '' ?>">Carrier:</span>
                                                </dt>
                                                <dd class="col-md-8 col-sm-7" style="<?= $documentStatus != TRANSFER_STATUS_SENT_TO_PICK_AND_PACK ? 'display: none;' : '' ?>">
                                                    <input type="text" id="carrier"  class="form-control due-date" value="<?= $transferDATA['shippingMethod'] ?>"/>
                                                </dd>
                                                <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                                                    <span class="fw-normal" style="<?= $documentStatus != TRANSFER_STATUS_SENT_TO_PICK_AND_PACK ? 'display: none;' : '' ?>">Tracking Number:</span>
                                                </dt>
                                                <dd class="col-md-8 col-sm-7" style="<?= $documentStatus != TRANSFER_STATUS_SENT_TO_PICK_AND_PACK ? 'display: none;' : '' ?>">
                                                    <input type="text" id="tracking" class="form-control due-date" value="<?= $transferDATA['trackingNumber'] ?>"/>
                                                </dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                                <!--Grey Header Section Ends-->
                                <hr class="mt-5 mb-6" />
                                <div class="row" style="<?= ($documentStatus == TRANSFER_STATUS_SENT_TO_PICK_AND_PACK || $documentStatus == TRANSFER_STATUS_PROCUREMENT_MANAGER_APPROVED) ? 'display: none;' : '' ?>">
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
                                    <div class="row" id="noSparePartsInitialDiv" style="display: none">
                                        <div class="col-12 text-center">
                                            Add spareparts from the above search box
                                        </div>
                                    </div>
                                    <form class="source-item">
                                        <div class="mb-4" data-repeater-list="line-item">
                                            <?php
                                            $itemCount = 0;
                                            $lineitemres = $db->query("SELECT * FROM transfer_line_items WHERE transferId = ?s", $transferID);

                                            while ($lineitemrow = mysqli_fetch_assoc($lineitemres)) {
                                            ?>
                                            <div class="repeater-wrapper pt-0 pt-md-6" data-repeater-item>
                                                <div class="d-flex border rounded position-relative pe-0">
                                                    <div class="row w-100 p-3">
                                                        <div class="col-md-2 col-12 mb-md-0 mb-4">
                                                            <p class="h6 mb-1 itemNumber">Item #<?= ++$itemCount ?></p>
                                                            <input name="sparepartinternalrefnumber" type="text" class="form-control mb-5" readonly value="<?= getInternalReferenceNumberForSparepartID($lineitemrow['itemId']); ?>"/>
                                                            <input name="sparepartID" type="hidden" class="form-control mb-5" readonly value="<?= $lineitemrow['itemId'] ?>"/>
                                                        </div>
                                                        <div class="col-md-2 col-12 mb-md-0 mb-4">
                                                            <p class="h6 mb-1">Description</p>
                                                            <input name="sparepartdescription" type="text" class="form-control mb-5" readonly value="<?= getItemDescriptionForSparepartID($lineitemrow['itemId']) ?>"/>
                                                        </div>
                                                        <div class="col-md-1 col-12 mb-md-0 mb-4">
                                                            <p class="h6 mb-1">Qty</p>
                                                            <input name="qty" type="text" class="form-control numbers-only calculation-trigger" placeholder="Qty" min="1" required value="<?= $lineitemrow['quantity'] ?>"/>
                                                        </div>
                                                        <div class="col-md-2 col-12 mb-md-0 mb-4">
                                                            <p class="h6 mb-1">Reason for Transfer</p>
                                                            <select name="transferReason" class="form-control mb-6" data-placeholder="Select Reason">
                                                                <option value="">Select the Reason</option>
                                                                <option value="0" <?php if ($lineitemrow['transferReason'] == '0') echo 'selected'; ?>> Shortage</option>
                                                                <?php
                                                                $res = $db->query("SELECT `documentId` 
                                                        FROM `key_documents` 
                                                        WHERE  `quotationStatus` = 'CONFIRMED' 
                                                          AND (`saleOrderStatus` IS NOT NULL OR `saleOrderStatus` <> '');");

                                                                while ($row = mysqli_fetch_assoc($res)) {
                                                                    ?>
                                                                    <option <?php if($lineitemrow['transferReason'] == $row['documentId']) echo 'SELECTED' ?> value="<?= $row['documentId'] ?>">Sales Order: <?= getSalesOrderNumberFromDocumentID($row['documentId']) ?></option>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-1 col-12 mb-md-0 mb-4">
                                                            <p class="h6 mb-1">Aisle</p>
                                                            <input name="aisle" type="text" class="form-control" placeholder="Aisle" min="1"  value="<?= $lineitemrow['aisle'] ?>"/>
                                                        </div>
                                                        <div class="col-md-1 col-12 mb-md-0 mb-4">
                                                            <p class="h6 mb-1">Bin</p>
                                                            <input name="bin" type="text" class="form-control" placeholder="Bin" min="1"  value="<?= $lineitemrow['bin'] ?>"/>
                                                        </div>
                                                        <div class="col-md-1 col-12 mb-md-0 mb-4">
                                                            <p class="h6 mb-1">Lot#</p>
                                                            <input name="lot" type="text" class="form-control" placeholder="Lot" min="1"  value="<?= $lineitemrow['lotSerial'] ?>"/>
                                                        </div>
                                                        <div class="col-md-2 col-12 mb-md-0 mb-4">
                                                            <p class="h6 mb-1">ETA</p>
                                                            <input name="eta" type="text" class="form-control gmmdatepicker-friendly" placeholder="Date" value="<?= empty($lineitemrow['eta']) ? "" : $lineitemrow['eta'] ?>"/>
                                                        </div>
                                                    </div>
                                                    <div class="row p-3 justify-content-end"></div>
                                                    <div class="d-flex flex-column align-items-center justify-content-between border-start p-2" >
                                                        <i class="ti ti-x ti-lg cursor-pointer" style="<?= ($documentStatus == TRANSFER_STATUS_SENT_TO_PICK_AND_PACK || $documentStatus == TRANSFER_STATUS_PROCUREMENT_MANAGER_APPROVED) ? 'display: none;' : '' ?>" data-repeater-delete></i>
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
                                <div class="card-body px-0" style="display:none">
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
          if ($transferDATA['transferStatus'] == TRANSFER_STATUS_PROCUREMENT_MANAGER_APPROVAL) {
              ?>
            <button type="button" class="btn btn-danger" onclick="rejectTransfer()">Reject</button>
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
  let sparepartsAutoCompleteData = [];

  $(document).ready(function (e) {

    $(document).on("change", "#originWH", function (e) {
      $("#noWHInitialsDiv").hide();

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

          $("#originWarehouseName").html(data.warehouseName);
          $("#originWarehouseCode").html(data.warehouseCode);
          $("#originWarehouseAddressLine1").html(data.addressLine1 === 'null' ? "" : data.addressLine1);
          $("#originWarehouseAddressLine2").html(data.addressLine2 === 'null' ? "" : data.addressLine2);
          $("#originWarehouseCityPostalCode").html((data.city ? data.city + ", " : "") + " " + (data.postalCode || ""));
          $("#originWarehouseStateCountry").html((data.state ? data.state + ", " : "") + data.country ? data.country : "");
          $("#originWarehousePhone").html("Phone: " + data.phone ? data.phone : "");
          $("#originWarehouseContact").html("Email: " + data.email ? data.email : "");

          $("#rowHeader").show();
          // Refresh Select2 to update stock quantities based on the new warehouse
          $('.select2-sparepart').each(function() {
            $(this).val(null).trigger('change'); // Clear current selection
            $(this).select2('destroy'); // Destroy the current Select2 instance
            initializeSelect2($(this)); // Reinitialize with the new warehouseID
          });
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

          $("#destinationWarehouseName").html(data.warehouseName);
          $("#destinationWarehouseCode").html(data.warehouseCode);
          $("#destinationWarehouseAddressLine1").html(data.addressLine1 === 'null' ? "" : data.addressLine1);
          $("#destinationWarehouseAddressLine2").html(data.addressLine2 === 'null' ? "" : data.addressLine2);
          $("#destinationWarehouseCityPostalCode").html((data.city ? data.city + ", " : "") + " " + (data.postalCode || ""));
          $("#destinationWarehouseStateCountry").html((data.state ? data.state + ", " : "") + data.country ? data.country : "");
          $("#destinationWarehousePhone").html("Phone: " + data.phone ? data.phone : "");
          $("#destinationWarehouseContact").html("Email: " + data.email ? data.email : "");

          $("#rowHeader").show();
        },

        error: function (error) {
          unBlockArea($('.invoice-preview-header'));
          console.log(error);
        }
      });

    });
  });

  function prepareTransferData() {
    console.log($('.source-item').repeaterVal());
    let repeaterData = $('.source-item').repeaterVal(); // Get the repeater data
    // Add additional data
    let requestData = {
      lineItems: repeaterData,
      salesPerson: $("#salesPerson").val(),
      transferID: $("#transferID").val(),
      originWHID: $('#originWH').val(),
      destinationWHID: $('#destinationWH').val(),
      transferDate: $('#transferDate').val(),
      transferReason: $('#transferReason').val(),
      transferNumber: $('#transferNumber').val(),
      carrier: $('#carrier').val(),
      tracking: $('#tracking').val(),
      etaDate: $('#etaDate').val(),
      notes: $('#notes').val(),
      reference: $('#reference').val()
    };

    console.log(requestData);
    return requestData;
  }


  function saveTransfer() {

    if ($('#originWH').val().length > 0 && $('#destinationWH').val().length > 0) {
      blockArea($('body'));
      let requestData = prepareTransferData();

      $.ajax(
        {
          url: '/ajax/transfers/save_transfer.php', // Update with your PHP script URL
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
              $("#transferID").val(documentId);
              $("#transferNumber").val("DRAFT");
              showSuccessMessage(response.message, gotoPage, "/warehouse/transfer/edit/" + documentId);


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
      showErrorMessage("Please choose Origin and Destination Warehouse");
    }
  }

  function sendForApproval() {
    saveTransferThread('<?=TRANSFER_STATUS_PROCUREMENT_MANAGER_APPROVAL ?>');
  }

  //function sendToPP() {
  //  let documentID = '<?php //=$transferID; ?>//';
  //  let transferData = prepareTransferData();
  //  console.log('Data:', transferData);
  //
  //  var formData = new FormData();
  //  formData.append('documentID', documentID);
  //  formData.append('status', '<?php //=TRANSFER_STATUS_SENT_TO_PICK_AND_PACK ?>//');
  //  formData.append('transferData', JSON.stringify(transferData)); // Add transfer data as JSON string
  //
  //  $.ajax({
  //    url: '/ajax/transfers/update_transfer_status.php',
  //    type: 'POST',
  //    data: formData,
  //    contentType: false,
  //    processData: false,
  //    success: function(data, status) {
  //      console.log('Data: ' + data);
  //
  //      var statusmessage = data.trim().split('|')[0];
  //      var message = data.trim().split('|')[1];
  //
  //      console.log('Status Message: ' + statusmessage);
  //      console.log('Message: ' + message);
  //
  //      if (statusmessage == 'SUCCESS') {
  //        showSuccessMessage(message, gotoPage, '/warehouse/transfer/edit/' + documentID);
  //      } else {
  //        showErrorMessage(message);
  //      }
  //    },
  //    error: function(error) {
  //      console.error('Error:', error);
  //      showErrorMessage('An error occurred while updating the transfer status');
  //    }
  //  });
  //}

  function sendToPP() {
    let documentID = '<?=$transferID; ?>';
    updateTransferStatus(documentID, "<?=TRANSFER_STATUS_SENT_TO_PICK_AND_PACK ?>");
  }
  function approveTransfer() {
    let documentID = '<?=$transferID; ?>';
    updateTransferStatus(documentID, "<?=TRANSFER_STATUS_PROCUREMENT_MANAGER_APPROVED ?>");
  }

  function rejectTransfer() {
    let documentID = '<?=$transferID; ?>';

    let rejectReason = $('#rejectReason').val();
    console.log(rejectReason);
    if (rejectReason.length) {
      updateTransferStatus(documentID, "<?=TRANSFER_STATUS_PROCUREMENT_MANAGER_REJECTED ?>", true, rejectReason);
    } else {
      showErrorMessage('You must enter a reason for rejection');
    }
  }

  function saveTransferThread(status) {

    //Then proceed with saving the quotaion.
    if ($('#originWH').val().length > 0 && $('#destinationWH').val().length > 0) {
      // blockArea($('body'));
      let requestData = prepareTransferData();
      $.ajax({
        url: '/ajax/transfers/save_transfer.php', // Update with your PHP script URL
        method: 'POST',
        data: JSON.stringify(requestData), // Send the combined data as a JSON string
        contentType: 'application/json', // Indicate that the data is JSON
        dataType: 'json', // Expect a JSON response
        success: function(response) {
          console.log('Server Response after saving Transfer:', response);

          if (typeof response === 'string') {
            response = JSON.parse(response);
          }

          console.log('Response: ' + response.status);
          // Check the response status
          if (response.status === 'success') {
            unBlockArea($('body'));
            let documentID = response.documentId;
            console.log('Transfer Status: ' + status);
            updateTransferStatus(documentID, status);
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
      showErrorMessage('Nothing to save.');
    }
  }

  function updateTransferStatus(documentID, status, showMessage = true, rejectReason = '') {

    console.log('Updating Transfer Status: ' + documentID + ' - ' + status + ' - ' + showMessage + ' - ' + rejectReason);

    console.log(rejectReason);
    var formData = new FormData();
    formData.append('documentID', documentID);
    formData.append('status', status);
    formData.append('rejectReason', rejectReason);

    console.log('DocumentID: ' + documentID);
    console.log('Status: ' + status);
    console.log('Show Message: ' + showMessage);


    $.ajax({
      url: '/ajax/transfers/update_transfer_status.php',
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
            showSuccessMessage(message, gotoPage, '/warehouse/transfer/edit/' + documentID);
        }
      },

      error: function(error) {
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
          initializeSelect2($('select[name="sparepartitem"]'), null);
          applyDatePicker();
          applySelectPicker();
        },
        defaultValues: {
          "eta": "<?=date('Y-m-d', strtotime('+14 days')) ?>"
        },

        show: function () {

          $(this).slideDown();
          updateItemNumbers();
          const index = $(this).index();

          //Initialize datepicker
          const etaDatePicker = $('[name="line-item[' + index + '][eta]"]');
          let fp = etaDatePicker.flatpickr({
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd - M - Y'
          });
          fp.clear();

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

  // function applyDatePicker() {
  //   const flatpickrFriendly = $('[name="line-item[0][eta]"]');
  //   flatpickrFriendly.flatpickr({
  //     dateFormat: 'Y-m-d',
  //     altInput: true,
  //     altFormat: 'd - M - Y'
  //   });
  // }


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
      const selectedleadtime = selectedData.leadtime;
      const selectedBin = selectedData.bin;
      const selectedAisle = selectedData.aisle;
      const selectedLot = selectedData.lot;

      console.log("Selected Lead time: " + selectedleadtime);

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

        const aisle = getElementByIndexAndName(index, 'aisle');
        aisle.val(selectedAisle);

        const bin = getElementByIndexAndName(index, 'bin');
        bin.val(selectedBin);

        const lot = getElementByIndexAndName(index, 'lot');
        lot.val(selectedLot);

        console.log("selectedleadtime: " + selectedleadtime);
        const eta = getElementByIndexAndName(index, 'eta');
        eta.val(selectedleadtime);
        eta.flatpickr({dateFormat: 'Y-m-d', altInput: true, altFormat: 'd - M - Y'});

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
        url: "/ajax/spareparts/get_spareparts_with_quantity.php",
        dataType: "json",
        delay: 250, // Delay AJAX requests to reduce load
        data: function (params) {
          return {q: params.term, warehouseID: $("#originWH").val() || ''}; // Send search query to PHP
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
                            <small>${data.desc}</small><br/>
                            <small> Total Available: ${data.totalAvailable}</small>
                          </div>`
        );
      },
      templateSelection: function (data) {
        return data.text || "Select a part number";
      }
    });
    if (preselectedValue) {
      $(selectElement).val(preselectedValue).trigger('change');
    }
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