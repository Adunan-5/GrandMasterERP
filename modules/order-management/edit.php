<?php
$PAGE_ID = "ORDER_MANAGEMENT_EDIT";
include_once __DIR__ . "/../../includes/baseIncludes.php";
include_once __DIR__ . "/../../includes/auth_check.php";

$orderID = "";
$orderID = filter_input(INPUT_GET, 'orderID', FILTER_VALIDATE_INT);
$filter = filter_input(INPUT_GET, 'filter', FILTER_SANITIZE_SPECIAL_CHARS);

$isShipmentFilter = ($filter === 'shipment') ? 1 : 0;
if ($orderID === null || $orderID === false || filter_var($orderID, FILTER_VALIDATE_INT) === false) {
    header("location:/order-management/list?filter=SALES_ORDER");
    exit();
}
if ($filter !== null && $filter !== 'shipment') {
    header("Location: /order-management/order/edit/$orderID");
    exit();
}

?>
<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
      data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template" data-style="light">
<head>
    <?php include_once __DIR__ . "/../../includes/dashboard/dashboard_head_section.php"; ?>
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
    <?php include_once __DIR__ . "/../../includes/dashboard/dashboard_head_section_post_vendor.php"; ?>
    <title>GrandMaster ERP | Order Management</title>
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
            <nav
                class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
                id="layout-navbar">
                <?php include_once __DIR__ . "/../../includes/dashboard/top_navbar.php"; ?>
            </nav>
            <!-- / Navbar -->
            <!-- Content wrapper -->
            <div class="content-wrapper">
                <?php
                $currentUserRole = getAuthenticatedUser()->getRoles()[0];

                $orderDATA = "";
                $res = $db->query("SELECT * FROM order_management_documents WHERE active = 1 and orderManagementId = ?s", $orderID);
                while ($row = mysqli_fetch_assoc($res)) {
                    $orderDATA = $row;
                }
                $orderType = $orderDATA['transactionType'];

                $documentID = $orderDATA['documentId'];

                if($orderType == "TRANSFER"){
                    $originWHID = $orderDATA['originWHId'];
                    $destinationWHID = $orderDATA['destinationWHId'];

                    $originWH = new Warehouse();
                    $originWH->loadById($originWHID);

                    $destinationWH = new Warehouse();
                    $destinationWH->loadById($destinationWHID);
                }

                $orderNumber = "";
                if($orderType == 'SALESORDER'){
                    $orderNumber = getSalesOrderNumberFromDocumentID($documentID);
                } else if($orderType == 'TRANSFER') {
                    $orderNumber = getTransferNumberFromDocumentID($documentID);
                }

                ?>
                <!-- Content -->
                <div class="container-fluid flex-grow-1 container-p-y">
                    <div class="card mb-6">
                        <div class="card-widget-separator-wrapper">
                            <div class="card-body card-widget-separator py-2">
                                <div class="row gy-1 gy-sm-1">
                                    <div class="col-6">
                                        <h4 class="my-0"><?= $orderDATA['orderNumber'] ?> </h4>
                                    </div>
                                    <div class="col-6 text-end">
<!--                                      <button type="button" class="btn btn-label-linkedin text-nowrap d-inline-flex position-relative me-4">-->
<!--                                        --><?php //= $orderDATA['orderWHStatus']?>
<!--                                      </button>-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
<!--                    --><?php
//                    $rejectReason = "";
//                    $documentStatus = $rfpDATA['rfpStatus'];
//                    if ($documentStatus == RFP_STATUS_PROCUREMENT_MANAGER_REJECTED) {
//                        $rejectReason = getRejectReasonForRFPID($rfpID);
//                        ?>
<!--                        <div class="alert alert-solid-danger d-flex align-items-center" role="alert">-->
<!--                        <span class="alert-icon rounded">-->
<!--                          <i class="ti ti-ban"></i>-->
<!--                        </span> Rejection Reason: --><?php //= $rejectReason ?>
<!--                        </div>-->
<!--                        --><?php
//                    }
//
//                    if ($rfpDATA['rfpStatus'] == RFP_STATUS_SENT_TO_SUPPLIER && (!checkIfRFPEligibleForPO($rfpID) || empty($rfpDATA['supplierQuotationAttachment']) || empty($rfpDATA['supplierQuotationNumber']))) {
//                        ?>
<!--                        <div class="alert alert-solid-info" role="alert">-->
<!--                            To create Purchase Order, Please Enter the Supplier Quotation No., Supplier Quotation Attachment File,-->
<!--                            Cost for Line Items and Shipping Cost.-->
<!--                        </div>-->
<!---->
<!--                        --><?php
//                    }
//
//                    if ($rfpDATA['rfpStatus'] == RFP_STATUS_PROCUREMENT_MANAGER_APPROVED) {
//                        ?>
<!--                        <div class="alert alert-solid-info" role="alert">-->
<!--                            In order to proceed further, please print and send it to the Supplier-->
<!--                        </div>-->
<!--                        --><?php
//                    }
//                    ?>
                    <div class="row invoice-add">
                        <!-- Invoice Add-->
                        <div class="col-lg-12 col-12 mb-lg-0 mb-6">
                            <div class="card invoice-preview-card p-sm-6 p-6">
                                <div class="card-body px-0">
                                    <div class="row">
                                        <!-- Buttons Row (Left-Aligned) -->
                                        <div class="col-12 mb-4">
                                            <div class="d-flex gap-2 justify-content-end">
                                                <?php
                                                if($orderType == "SALESORDER" && $orderDATA['orderWHStatus'] == ORDER_WH_STATUS_ALLOCATED){
                                                ?>
                                                <button class="btn btn-primary" onclick="window.location.href='/rfp/new?refDocID=<?=$orderDATA['documentId']?>'">
                                                  <span class="d-flex align-items-center justify-content-center text-nowrap">
                                                    <i class="ti ti-plus ti-xs me-2"></i>Create RFP
                                                  </span>
                                                </button>

                                              <button class="btn btn-primary" onclick="window.location.href='/warehouse/transfer/new'">
                                                  <span class="d-flex align-items-center justify-content-center text-nowrap">
                                                    <i class="ti ti-plus ti-xs me-2"></i>Create Transfer
                                                  </span>
                                                </button>
                                                <?php
                                                } if($orderDATA['orderWHStatus'] == ORDER_WH_STATUS_RESERVED) {

                                                ?>
<!--                                              <button class="btn btn-primary" onclick="confirmShipment()">-->
<!--                                                  <span class="d-flex align-items-center justify-content-center text-nowrap">-->
<!--                                                    <i class="ti ti-cube-send ti-xs me-2"></i>Confirm Shipment-->
<!--                                                  </span>-->
                                                  <?php
                                                  }
                                                if($orderDATA['orderWHStatus'] == ORDER_WH_STATUS_RESERVED || $orderDATA['orderWHStatus'] == ORDER_WH_STATUS_READY) {
                                                  ?>
<!--                                              </button>-->
<!--                                                  <button class="btn btn-primary" onclick="">-->
<!--                                                  <span class="d-flex align-items-center justify-content-center text-nowrap">-->
<!--                                                    <i class="ti ti-printer ti-xs me-2"></i>Print-->
<!--                                                  </span>-->
<!--                                                  </button>-->
                                                <?php
                                                } if($orderDATA['orderWHStatus'] != ORDER_WH_STATUS_RESERVED&& $orderDATA['orderWHStatus'] != ORDER_WH_STATUS_READY) {
                                                ?>
                                              <button class="btn btn-primary" onclick="saveOrder()">
                                                  <span class="d-flex align-items-center justify-content-center text-nowrap">
                                                    <i class="ti ti-device-floppy ti-xs me-2"></i>Save
                                                  </span>
                                              </button>
                                                <?php
                                                }
                                                ?>
<!--                                                --><?php
//                                                if (($currentUserRole == ROLE_SUPERADMIN) && ($documentStatus == RFP_STATUS_NEW || $documentStatus == RFP_STATUS_PROCUREMENT_MANAGER_REJECTED)) {
//                                                    ?>
<!--                                                    <button class="btn btn-primary" onclick="sendForApproval()">-->
<!--                          <span class="d-flex align-items-center justify-content-center text-nowrap">-->
<!--                            <i class="ti ti-device-floppy ti-xs me-2"></i>Send for Approval-->
<!--                          </span>-->
<!--                                                    </button>-->
<!--                                                    --><?php
//                                                }
//                                                if (($currentUserRole == ROLE_SUPERADMIN) && $documentStatus == RFP_STATUS_PROCUREMENT_MANAGER_APPROVAL) {
//                                                    ?>
<!--                                                    <button class="btn btn-success" onclick="approveRFP()">-->
<!--                          <span class="d-flex align-items-center justify-content-center text-nowrap">-->
<!--                            <i class="ti ti-device-floppy ti-xs me-2"></i>Approve-->
<!--                          </span>-->
<!--                                                    </button>-->
<!--                                                    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectReasonModal">-->
<!--                          <span class="d-flex align-items-center justify-content-center text-nowrap">-->
<!--                            <i class="ti ti-circle-dashed-x ti-xs me-2"></i>Reject-->
<!--                          </span>-->
<!--                                                    </button>-->
<!--                                                    --><?php
//                                                }
//                                                if (($currentUserRole == ROLE_SUPERADMIN) && ($documentStatus == RFP_STATUS_PROCUREMENT_MANAGER_APPROVED || $documentStatus == RFP_STATUS_SENT_TO_SUPPLIER)) {
//                                                    ?>
<!--                                                    <button class="btn btn-info" onclick="printRFP()">-->
<!--                          <span class="d-flex align-items-center justify-content-center text-nowrap">-->
<!--                            <i class="ti ti-printer ti-xs me-2"></i>Print-->
<!--                          </span>-->
<!--                                                    </button>-->
<!--                                                    --><?php
//                                                }
//                                                //Check if the document can be converted to PO
//                                                if (checkIfRFPEligibleForPO($rfpID) && $documentStatus == RFP_STATUS_SENT_TO_SUPPLIER && !empty($rfpDATA['supplierQuotationAttachment']) && !empty($rfpDATA['supplierQuotationNumber'])) {
//                                                    ?>
<!--                                                    <button class="btn btn-success" onclick="createPO()">-->
<!--                          <span class="d-flex align-items-center justify-content-center text-nowrap">-->
<!--                            <i class="ti ti-file-check ti-xs me-2"></i>Create PO-->
<!--                          </span>-->
<!--                                                    </button>-->
<!--                                                    --><?php
//                                                }
//
//                                                ?>
                                            </div>
                                        </div>
                                    </div>
<!--                                    <div class="row">-->
<!--                                        <div class="col-md-5 col-sm-5 col-12 mb-sm-0 mb-6">-->
<!--                                            <div class="mb-4">-->
<!--                                                <label class="form-label" for="customerCountry">Supplier:</label>-->
<!--                                                <select name="supplier" id="supplier" class="form-select mb-4 w-50 selectpicker w-100"-->
<!--                                                        data-style="btn-default" data-live-search="true" tabindex="null">-->
<!--                                                    <option value="">Select supplier</option>-->
<!--                                                    --><?php
//                                                    $res = $db->query("SELECT * FROM suppliers WHERE status ='Active'");
//                                                    while ($row = mysqli_fetch_assoc($res)) {
//                                                        ?>
<!--                                                        <option --><?php //if ($row['supplierId'] == $rfpDATA['supplierId']) echo "selected"; ?>
<!--                                                            value="--><?php //= $row['supplierId'] ?><!--">--><?php //= $row['companyName'] . " | " . $row['companyNameAr'] ?><!--</option>-->
<!--                                                        --><?php
//                                                    }
//                                                    ?>
<!--                                                </select>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                        <div class="col-md-5 col-sm-5 col-12 mb-sm-0 mb-6">-->
<!--                                            <div class="mb-4">-->
<!--                                                <label class="form-label" for="destinationWH">Destination WH:-->
<!--                                                </label>-->
<!--                                                <select name="destinationWH" id="destinationWH"-->
<!--                                                        class="form-select mb-4 w-50 selectpicker w-100" data-style="btn-default"-->
<!--                                                        data-live-search="true" tabindex="null">-->
<!--                                                    <option value="">Select Warehouse</option>-->
<!--                                                    --><?php
//                                                    $res = $db->query("SELECT * FROM warehouses WHERE active ='1'");
//                                                    while ($row = mysqli_fetch_assoc($res)) {
//                                                        ?>
<!--                                                        <option --><?php //if ($row['warehouseId'] == $rfpDATA['warehouseId']) echo "selected"; ?>
<!--                                                            value="--><?php //= $row['warehouseId'] ?><!--">--><?php //= $row['warehouseName'] . " | " . $row['warehouseCode'] ?><!--</option>-->
<!--                                                        --><?php
//                                                    }
//                                                    ?>
<!--                                                </select>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                    </div>-->
                                </div>
                                <!--Grey Header Section-->
                                <div class="card-body invoice-preview-header rounded">
                                  <div class="row">
                                    <!-- First Row of Input Fields -->
                                    <div class="col-md-6 col-12 mb-4">
                                      <dl class="row mb-0">
                                        <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                                          <span class="h5 text-capitalize mb-0 text-nowrap"><?= getDisplayTransactionType($orderDATA['transactionType']) ?></span>
                                        </dt>
                                        <dd class="col-md-8 col-sm-7">
                                          <div class="input-group input-group-merge disabled">
                                            <span class="input-group-text">#</span>
                                            <input name="orderNumber" id="orderNumber" type="text" class="form-control" disabled placeholder="Will be generated once saved / confirmed" value="<?= $orderNumber ?>"/>
                                          </div>
                                          <input name="orderID" id="orderID" type="hidden" value="<?= $orderDATA['orderManagementId'] ?>"/>
                                        </dd>
<!--                                          --><?php
//                                          if($orderDATA['orderWHStatus'] != ORDER_WH_STATUS_RESERVED && $orderDATA['orderWHStatus'] != ORDER_WH_STATUS_READY) {
//                                          ?>
                                        <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                                          <span class="fw-normal">Origin:</span>
                                        </dt>
                                        <dd class="col-md-8 col-sm-7">
                                          <input type="text" id="originWH" readonly class="form-control" value="<?= !empty(getWarehouseNameFromID($orderDATA['originWHId'])) ? getWarehouseNameFromID($orderDATA['originWHId']) : '' ?>"/>
                                          <input type="hidden" id="originWHId" class="form-control" value="<?= !empty($orderDATA['originWHId']) ? $orderDATA['originWHId'] : '' ?>"/>
                                        </dd>
<!--                                              --><?php
//                                          }
//                                          ?>
                                      </dl>
                                    </div>

                                    <div class="col-md-6 col-12 mb-4">
                                      <dl class="row mb-0">
<!--                                          --><?php
//                                          if($orderDATA['orderWHStatus'] != ORDER_WH_STATUS_RESERVED && $orderDATA['orderWHStatus'] != ORDER_WH_STATUS_READY) {
//                                          ?>
                                        <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                                          <span class="fw-normal">Sales Person:</span>
                                        </dt>
                                        <dd class="col-md-8 col-sm-7">
                                          <input type="text" id="salesPerson" readonly class="form-control due-date" value="<?= getDisplayNameOfCurrentUser() ?>"/>
                                        </dd>
                                        <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">
                                          <span class="fw-normal">Transaction Type:</span>
                                        </dt>
                                        <dd class="col-md-8 col-sm-7">
                                          <input type="text" id="transactionType" class="form-control" readonly value="<?= getDisplayTransactionType($orderDATA['transactionType']) ?>"/>
                                        </dd>
<!--                                              --><?php
//                                          }
//                                          if($orderDATA['orderWHStatus'] == ORDER_WH_STATUS_RESERVED || $orderDATA['orderWHStatus'] == ORDER_WH_STATUS_READY) {
//
//                                          ?>
<!--                                        <dt class="col-md-4 col-sm-5 mb-2 text-md-end d-flex align-items-center">-->
<!--                                          <span class="fw-normal">Shipment#:</span>-->
<!--                                        </dt>-->
<!--                                        <dd class="col-md-8 col-sm-7">-->
<!--                                          <input type="text" id="shipment#" class="form-control" readonly value="--><?php //= getShipmentNumberFromDocumentID($orderDATA['documentId'], $orderType) ?><!--"/>-->
<!--                                        </dd>-->
<!--                                          --><?php
//                                          }
//                                          ?>
                                      </dl>
                                    </div>
                                  </div>
                                    <!--Grey Header Section Ends-->
                                </div>

                              <div class="card-body pt-0 px-0">
                                <form class="source-item">
                                  <button type="button" class="btn <?= ($filter !== 'shipment') ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm me-2 mt-4 active" id="outbound-btn" onclick="window.location.href='/order-management/order/edit/<?=$orderID?>'">Outbound</button>
                                  <button type="button" class="btn <?= ($filter === 'shipment') ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm mt-4" id="shipmentLine-btn" onclick="window.location.href='/order-management/order/edit/<?=$orderID?>?filter=shipment'">Shipment Line</button>
                                  <!-- Add Select All checkbox and bulk action buttons -->
<!--                                    --><?php
//                                    if(checkIfAllLineItemsStaged($orderID) && !checkIfAllLineItemsProcessed($orderID)) {
//                                    ?>
                                  <div class="d-flex justify-content-between mt-4">
                                    <div class="form-check" id="selectAll">
                                        <?php
                                        if($filter=== 'shipment') {
//                                        if($filter=== 'shipment' && $orderDATA['orderWHStatus'] != ORDER_WH_STATUS_RESERVED && $orderDATA['orderWHStatus'] != ORDER_WH_STATUS_READY) {
                                        ?>
                                      <input type="checkbox" class="form-check-input" id="select-all">
                                      <label class="form-check-label" for="select-all">Select All</label>
                                    </div>
                                    <div id="actionButtons">
                                      <button type="button" class="btn btn-warning btn-sm me-2" id="return-btn" onclick="returnItem()">Return</button>
                                      <button type="button" class="btn btn-primary btn-sm" id="release-btn" onclick="releaseItem()">Release</button>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                  </div>
<!--                                    --><?php
//                                    }
//                                    ?>

                                  <div class="mb-4" data-repeater-list="line-item">
                                      <?php
                                      $itemCount = 0;
                                      $filterQuery = "";
                                      if($filter === 'shipment') {
                                        $filterQuery = "AND orderWHStatus != 'ALLOCATED'";
                                      }
                                      $lineitemres = $db->query("SELECT * FROM order_management_line_items WHERE orderManagementId = ?s $filterQuery", $orderID);
                                      $pickAndPackItems = [];
                                      $pickRes = $db->query("SELECT * FROM pick_and_pack_line_items WHERE orderManagementId = ?s", $orderID);
                                      while ($row = mysqli_fetch_assoc($pickRes)) {
                                          $itemId = $row['itemId'];
                                          if (!isset($pickAndPackItems[$itemId])) {
                                              $pickAndPackItems[$itemId] = [
                                                  'totalPickedQty' => 0,
                                                  'shipmentNumbers' => [],
                                              ];
                                          }
                                          $pickAndPackItems[$itemId]['totalPickedQty'] += $row['quantity'];
                                          $pickAndPackItems[$itemId]['shipmentNumbers'][] = $row['shipmentNumberPrefix'] . $row['shipmentNumber'];
                                      }
                                      while ($lineitemrow = mysqli_fetch_assoc($lineitemres)) {
                                          $itemId = $lineitemrow['itemId'];
                                          $totalQty = $lineitemrow['quantity']; // from order_management_line_items
                                          $releasedQty = isset($pickAndPackItems[$itemId]) ? $pickAndPackItems[$itemId]['totalPickedQty'] : 0;
                                          $pendingQty = $totalQty - $releasedQty;
                                          $shipmentNumbers = isset($pickAndPackItems[$itemId]) ? implode(', ', $pickAndPackItems[$itemId]['shipmentNumbers']) : '-';
                                          ?>
                                        <div class="repeater-wrapper pt-0 pt-md-6" data-repeater-item>
                                          <div class="d-flex border rounded position-relative pe-0">
                                            <div class="row w-100 p-3">
                                              <!-- Add checkbox for each line item -->
<!--                                                --><?php
//                                                if(checkIfAllLineItemsStaged($orderID) && !checkIfAllLineItemsProcessed($orderID)) {
//                                                ?>
                                              <div class="d-flex justify-content-start mb-2">
                                                <div class="form-check me-3">
                                                    <?php
//                                                    if($lineitemrow['orderWHStatus'] != 'RESERVED' && checkIfAllLineItemsStaged($orderID) && $filter=== 'shipment')
                                                    if($filter=== 'shipment' && $pendingQty >0)
                                                    {
                                                      ?>
                                                  <input type="checkbox" class="form-check-input line-item-checkbox"
                                                         data-item-id="<?= $lineitemrow['itemId'] ?>" name="line-item-checkbox">
                                                  <label class="form-check-label">Select</label>
                                                    <?php
                                                    }
                                                    ?>
                                                </div>
                                              </div>
<!--                                                    --><?php
//                                                }
//                                                ?>
                                              <div class="col-md-2 col-12 mb-md-0 mb-4">
                                                <p class="h6 mb-1 itemNumber">Item #<?= ++$itemCount ?></p>
                                                <input name="sparepartpartnumber" type="text" class="form-control mb-5" readonly
                                                       value="<?= getPartNumberForSparepartID($lineitemrow['itemId']); ?>" />
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
                                              <?php
                                              if($filter=== 'shipment') {
                                              ?>
                                              <div class="col-md-1 col-12 mb-md-0 mb-4">
                                                <p class="h6 mb-1">Release Qty</p>
                                                <input name="releasedQty" type="text" class="form-control numbers-only releasedQty calculation-trigger"
                                                        placeholder="Qty" min="1" required value="<?= $releasedQty ?>" />
                                              </div>
                                              <div class="col-md-1 col-12 mb-md-0 mb-4">
                                                <p class="h6 mb-1">Pending Qty</p>
                                                  <input name="pendingQty" type="text" class="form-control numbers-only releasedQty calculation-trigger"
                                                         placeholder="Qty" min="1" required value="<?= $pendingQty ?>" />
                                              </div>
                                              <?php
                                              }
                                              ?>
                                              <div class="col-md-1 col-12 mb-md-0 mb-4">
                                                <p class="h6 mb-1">Lot#</p>
                                                <input name="lot" type="text" class="form-control" placeholder="Lot" min="1"
                                                       required readonly value="<?= $lineitemrow['lotSerial'] ?>" />
                                              </div>
                                                <?php
                                                if($orderDATA['transactionType'] != 'TRANSFER') {
                                                ?>
                                              <div class="col-md-2 col-12 mb-md-0 mb-4">
                                                <p class="h6 mb-1">Status</p>
                                                <input name="status" type="text" class="form-control" placeholder="Status" min="1"
                                                       required readonly value="<?= $lineitemrow['orderWHStatus'] ?>" />
                                              </div>
                                              <?php
                                                }
                                              ?>
                                              <div class="col-md-2 col-12 mb-md-0 mb-4">
                                                <p class="h6 mb-1">Shipment#</p>
                                                <input name="shipment#" type="text" class="form-control" placeholder="Shipment#" min="1"
                                                       required readonly value="<?= $shipmentNumbers ?>" />
                                              </div>
                                                <?php
                                                if($orderDATA['transactionType'] != 'TRANSFER') {
                                                ?>
                                              <div class="col-md-2 col-12 mb-md-0 mb-4">
                                                <p class="h6 mb-1">WH</p>
                                                <select name="wh" class="form-control mb-6" data-placeholder="Select WH">
                                                  <option value="">Select WH</option>
                                                    <?php
                                                    $res = $db->query("SELECT * FROM warehouses WHERE active = 1");
                                                    while ($row = mysqli_fetch_assoc($res)) {
                                                        $stockQty = getStockOnHandQtyForSparePart($lineitemrow['itemId'], $row['warehouseId']) ?? 0;
                                                        ?>
                                                      <option <?php if($row['warehouseId'] == $lineitemrow['originWHId']) echo "selected"; ?>
                                                        value="<?= $row['warehouseId'] ?>"> <?= $row['warehouseName'] . ' | ' . $stockQty ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                              </div>
                                                    <?php
                                                }
                                                ?>
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
<!--  Reject Reason Modal-->
<!-- / Layout wrapper -->
<?php include_once __DIR__ . "/../../includes/dashboard/dashboard_footer_scripts.php"; ?>
<script>

  console.log('Script loaded');

  $(document).ready(function($) {

    // $('#outbound-btn').click(function() {
    //   $('#outbound').show();
    //   $('#selectAll').show();
    //   $('#actionButtons').show();
    //   $(this).addClass('btn-primary').removeClass('btn-outline-primary');
    //   $('#shipmentLine-btn').removeClass('btn-primary').addClass('btn-outline-primary');
    //   $('#shipmentLine').hide();
    // });
    //
    // $('#shipmentLine-btn').click(function() {
    //   $('#outbound').hide();
    //   $('#selectAll').hide();
    //   $('#actionButtons').hide();
    //   $(this).addClass('btn-primary').removeClass('btn-outline-primary');
    //   $('#outbound-btn').removeClass('btn-primary').addClass('btn-outline-primary');
    //   $('#shipmentLine').show();
    // })

    // Existing WH change handler
    $(document).on('change', 'select[name$="[wh]"]', function(e) {
      console.log('WH select changed:', $(this).val());

      let warehouseId = $(this).val();
      let $repeaterItem = $(this).closest('.repeater-wrapper');
      let sparepartId = $repeaterItem.find('input[name$="[sparepartID]"]').val();
      let $lotField = $repeaterItem.find('input[name$="[lot]"]');
      let $statusField = $repeaterItem.find('input[name$="[status]"]');

      console.log('Line Item Sparepart ID:', sparepartId);
      console.log('Lot Field Exists:', $lotField.length > 0);
      console.log('Status Field Exists:', $statusField.length > 0);

      if (warehouseId && $statusField.length > 0) {
        // console.log('Warehouse selected, setting Status to STAGED');
        $statusField.val('STAGED');
      } else {
        console.log('No warehouse selected, clearing Status field');
        $statusField.val('');
      }

      if (warehouseId && sparepartId && $lotField.length > 0) {
        let formData = new FormData();
        formData.append('sparepartId', sparepartId);
        formData.append('warehouseId', warehouseId);

        $.ajax({
          url: '/ajax/inventory/fetch_lotSerial.php',
          type: 'POST',
          data: formData,
          contentType: false,
          processData: false,
          dataType: 'json',
          success: function(data) {
            console.log('AJAX Success - Lot Serial Response:', data);
            if (data.success && data.lotSerial) {
              console.log('Setting Lot to:', data.lotSerial);
              $lotField.val(data.lotSerial);
            } else {
              console.log('No lotSerial found, clearing Lot field');
              $lotField.val('');
            }
          },
          error: function(error) {
            console.log('AJAX Error:', error);
            $lotField.val('');
          }
        });
      } else {
        console.log('No warehouse selected or missing sparepartId/lot field, clearing Lot');
        $lotField.val('');
      }

      let selectedWarehouses = [];
      $('select[name$="[wh]"]').each(function() {
        let whId = $(this).val();
        if (whId) {
          selectedWarehouses.push(whId);
        }
      });
      console.log('Selected Warehouses:', selectedWarehouses);

      let allSame = selectedWarehouses.length > 0 && selectedWarehouses.every((val, i, arr) => val === arr[0]);
      console.log('All Same:', allSame);

      let originField = $('#originWH');
      let originWHIdField = $('#originWHId');
      console.log('Origin Field Exists:', originField.length > 0);
      console.log('Origin WH ID Field Exists:', originWHIdField.length > 0);

      if (allSame && selectedWarehouses.length > 0) {
        let warehouseId = selectedWarehouses[0];
        console.log('Fetching details for Warehouse ID:', warehouseId);

        let formData = new FormData();
        formData.append('warehouseID', warehouseId);

        $.ajax({
          url: '/ajax/warehouses/get_warehouse_detail.php',
          type: 'POST',
          data: formData,
          contentType: false,
          processData: false,
          dataType: 'json',
          success: function(data, status) {
            console.log('AJAX Success - Response:', data);

            if (data && data.warehouseName) {
              console.log('Setting Origin to:', data.warehouseName);
              console.log('Setting Origin WH ID to:', warehouseId);
              originField.val(data.warehouseName);
              originWHIdField.val(warehouseId);
            } else {
              console.log('No warehouseName in response, clearing Origin and WH ID');
              originField.val('');
              originWHIdField.val('');
            }
          },
          error: function(error) {
            console.log('AJAX Error:', error);
            originField.val('');
            originWHIdField.val('');
          }
        });
      } else {
        console.log('Different warehouses or no selection, clearing Origin and WH ID');
        originField.val('');
        originWHIdField.val('');
      }
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

    // Trigger the change event on page load for pre-populated dropdowns
    $('select[name$="[wh]"]').trigger('change');
  });

  function printPP() {

    let documentID = "<?= $orderID ?>";
    const baseUrl = window.location.origin;
    const relativeUrl = '/ajax/ordermanagement/generate_pdf.php?orderID=' + documentID;
    const fullUrl = baseUrl + relativeUrl;
    window.open(fullUrl, '_blank');

  }

  function prepareOrderData() {
    console.log($('.source-item').repeaterVal());
    let repeaterData = $('.source-item').repeaterVal(); // Get the repeater data
    // Add additional data
    let requestData = {
      lineItems: repeaterData,
      salesPerson: $("#salesPerson").val(),
      orderID: $("#orderID").val(),
      originWHID: $('#originWHId').val(),
      orderNumber: $('#orderNumber').val(),
      isShipmentFilter: <?= $isShipmentFilter ?>,
    };

    console.log(requestData);
    return requestData;
  }

  function saveOrder() {
    if($('#originWH').val().length > 0) {
      blockArea($('body'));
      let requestData = prepareOrderData();

      $.ajax(
        {
          url: '/ajax/ordermanagement/save_order.php',
          method: 'POST',
          data: JSON.stringify(requestData),
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
              $("#orderID").val(documentId);
              showSuccessMessage(response.message, gotoPage, "/order-management/order/edit/" + documentId);


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
      showErrorMessage("Origin is not selected. Either Transfer everything to one place or create a RFP to proceed further");
    }
  }

  function releaseItem() {
    saveItemsThread("<?= ORDER_WH_STATUS_RESERVED ?>");
  }

  function returnItem() {
    saveItemsThread("<?= ORDER_WH_STATUS_RETURNED ?>");
  }

  function saveItemsThread(status) {
    // Collect selected item IDs and their corresponding releasedQty
    let selectedItems = $('.line-item-checkbox:checked').map(function() {
      let itemId = $(this).data('item-id');
      let releasedQty = $(this).closest('.repeater-wrapper').find('.releasedQty').val();

      console.log(`Item ID: ${itemId}, Released Qty: ${releasedQty}`);

      return {
        itemId: itemId,
        releasedQty: releasedQty
      };
    }).get();

    let hasZeroQuantity = selectedItems.some(item => item.releasedQty <= 0);
    if (hasZeroQuantity) {
      showErrorMessage('Released quantity cannot be 0 or less');
      return;
    }

    let orderManagementId = <?= $orderID ?>;
    console.log('Order Management ID:', orderManagementId);

    if (selectedItems.length === 0) {
      console.log('No items selected');
      showErrorMessage('No items selected');
      return;
    }

    if (!orderManagementId) {
      console.log('Missing orderManagementId');
      return;
    }

    let formData = new FormData();
    formData.append('items', JSON.stringify(selectedItems)); // Includes both itemId and releasedQty
    formData.append('orderManagementId', orderManagementId);
    formData.append('status', status);

    $.ajax({
      url: '/ajax/ordermanagement/process_line_items.php',
      type: 'POST',
      data: formData,
      contentType: false,
      processData: false,
      dataType: 'json',
      success: function(response) {
        console.log('AJAX Success - Bulk Release Response:', response);
        if (typeof response === 'string') {
          response = JSON.parse(response);
        }

        if (response.status === 'success' && status === "<?= ORDER_WH_STATUS_RETURNED ?>") {
          console.log(response.message);
          unBlockArea($('body'));
          showSuccessMessage(response.message, gotoPage, "/order-management/order/edit/" + orderManagementId);
        } else if (response.status === 'SUCCESS' && status === "<?= ORDER_WH_STATUS_RESERVED ?>") {
          console.log(response.message);
          unBlockArea($('body'));
          showSuccessMessage('Items released successfully', gotoPage, "/order-management/list?filter=ALL");
        } else {
          console.error('Error:', response.message);
          alert(`Error: ${response.message}`);
        }
      },
      error: function(error) {
        console.log('AJAX Error:', error);
      }
    });
  }

  function confirmShipment() {
    let documentID = <?= $orderID?>;
    updateOrderStatus(documentID, '<?=ORDER_WH_STATUS_READY?>');
  }

  function updateOrderStatus(documentID, status, showMessage = true, rejectReason = '') {

    console.log('Updating Order Status: ' + documentID + ' - ' + status + ' - ' + showMessage + ' - ' + rejectReason);

    console.log(rejectReason);
    var formData = new FormData();
    formData.append('documentID', documentID);
    formData.append('status', status);
    formData.append('rejectReason', rejectReason);

    console.log(formData);


    $.ajax({
      url: '/ajax/ordermanagement/update_order_status.php',
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
            showSuccessMessage(message, gotoPage, '/order-management/order/edit/' + documentID);
        }
      },

      error: function(error) {
        console.log(error);
      }
    });

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