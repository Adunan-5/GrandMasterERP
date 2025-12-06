<?php
$PAGE_ID = "ECOMMERCE_ORDER_EDIT";
include_once __DIR__ . "/../../includes/baseIncludes.php";
include_once __DIR__ . "/../../includes/auth_check.php";

$documentID = "";
$documentID = filter_input(INPUT_GET, 'documentID', FILTER_VALIDATE_INT);
if ($documentID === null || $documentID === false || filter_var($documentID, FILTER_VALIDATE_INT) === false) {
    header("location:/ecommerce/list");
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
  <title>GrandMaster ERP | E-Commerce</title>
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

                      $keyDocumentHistory = new KeyDocumentHistory();
                      $keyDocumentHistory->loadByDocumentId($documentID);


                      $documentInfo = '';
                      $lineItems    = array();
                      $res          = $db->query("SELECT * FROM key_documents WHERE documentId = ?s", $documentID);
                      $documentInfo = mysqli_fetch_assoc($res);

                      $res = $db->query("SELECT * FROM line_items WHERE documentId = ?s", $documentID);
                      while ($row = mysqli_fetch_assoc($res)) {
                          $lineItems[] = $row;
                      }

                      $orderStatus = $keyDocument->orderStatus;

                      ?>
                    <h4 class="my-0"><?= $keyDocument->orderId ?> | E-Commerce Order </h4>
                  </div>
                  <div class="col-6 text-end">
                      <?php
                      switch ($orderStatus) {
                          case ORDER_STATUS_NEW:
                              ?>
                            <button type="button" class="btn btn-label-linkedin text-nowrap d-inline-flex position-relative me-4">
                              <?=ORDER_STATUS_NEW?>
                            </button>
                              <?php
                              break;

                          case ORDER_STATUS_REJECTED:
                              ?>
                            <label class="no-hand btn btn-label-danger text-nowrap d-inline-flex position-relative me-4">
                              REJECTED
                            </label>
                              <?php
                              break;

                          case ORDER_STATUS_APPROVED:
                              ?>
                            <label class="no-hand btn btn-label-success text-nowrap d-inline-flex position-relative me-4">
                              APPROVED
                            </label>
                              <?php
                              break;
                          case ORDER_STATUS_ACCOUNTANT_REJECTED:
                              ?>
                            <label class="no-hand btn btn-label-danger text-nowrap d-inline-flex position-relative me-4">
                              ACCOUNTANT REJECTED
                            </label>
                              <?php
                              break;

                          case ORDER_STATUS_ACCOUNTANT_APPROVED:
                              ?>
                            <label class="no-hand btn btn-label-success text-nowrap d-inline-flex position-relative me-4">
                              ACCOUNTANT APPROVED
                            </label>
                              <?php
                              break;

                          case ORDER_STATUS_CUSTOMER_REJECTED:
                              ?>
                            <label class="no-hand btn btn-label-danger text-nowrap d-inline-flex position-relative me-4">
                              CUSTOMER REJECTED
                            </label>
                              <?php
                              break;
                          case ORDER_STATUS_CUSTOMER_ACCEPTED:
                              ?>
                            <a href="/salesorders/edit/<?= $keyDocument->documentId ?>" target="_blank" class="btn btn-label-info"><?= $keyDocument->getSalesOrderNumberWithPrefix() ?></a>
                            <label class="no-hand btn btn-label-success text-nowrap d-inline-flex position-relative me-4">
                              CUSTOMER ACCEPTED
                            </label>
                              <?php
                              break;
                          default:
                              ?>
                            <button type="button" class="btn btn-label-info text-nowrap d-inline-flex position-relative me-4">
                                <?= $orderStatus ?>
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
            if ($orderStatus == QUOTATION_STATUS_CUSTOMER_REJECTED) {
                $rejectReason = getEcommerceCustomerRejectReasonForDocumentID($documentID);
                ?>
              <div class="alert alert-solid-danger d-flex align-items-center" role="alert">
                        <span class="alert-icon rounded">
                          <i class="ti ti-ban"></i>
                        </span> Rejection Reason: <?= $rejectReason ?>
              </div>
                <?php
            }
            if ($orderStatus == ORDER_STATUS_REJECTED) {
              $rejectReason = getEcommerceOrderRejectReasonForQuotationID($documentID);
              ?>
              <div class="alert alert-solid-danger d-flex align-items-center" role="alert">
                        <span class="alert-icon rounded">
                          <i class="ti ti-ban"></i>
                        </span> Rejection Reason: <?= $rejectReason ?>
              </div>
                <?php
            }
            if ($orderStatus == ORDER_STATUS_ACCOUNTANT_REJECTED) {
                $rejectReason = getEcommerceOrderAccountantRejectReasonForQuotationID($documentID);
                ?>
              <div class="alert alert-solid-danger d-flex align-items-center" role="alert">
                        <span class="alert-icon rounded">
                          <i class="ti ti-ban"></i>
                        </span> Rejection Reason: <?= $rejectReason ?>
              </div>
                <?php
            }
            //Temporary for Faisal
            if ($orderStatus == ORDER_STATUS_SENT_TO_CUSTOMER) {
                ?>
              <div class="alert alert-outline-warning" role="alert">
                This is a temporary way to simulate customer acceptance of the order.
                <a href="/order/view/<?= $documentID ?>" target="_blank">Click here to open</a>.
                Password: <?= $keyDocument->customerPassword ?>
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
                    <div class="col-md-5 col-sm-5 col-12 mb-sm-0 mb-6">
                      <div class="mb-4" style="display: none">
                        <label class="form-label" for="customerCountry">Quotation For:</label>
                        <select name="customer" id="customer" class="form-select mb-4 w-50 selectpicker w-100" data-style="btn-default" data-live-search="true" tabindex="null">
                          <option value="">Select Customer</option>
                            <?php
                            $res        = $db->query("SELECT * FROM customers WHERE status ='Active'");
                            $customerID = $documentInfo['customerId'];
                            while ($row = mysqli_fetch_assoc($res)) {
                                $selected = "";
                                if ($customerID == $row['customerId']) $selected = "selected";
                                ?>
                              <option value="<?= $row['customerId'] ?>" <?= $selected ?>><?= "GMM" . $row['customerCode'] . " | " . $row['companyName'] . " | " . $row['companyNameAr']  ?></option>
                                <?php
                            }
                            ?>
                        </select>
                      </div>
                    </div>
                    <!--Document ACTION BUTTONS-->
                    <div class="col-md-7 col-sm-7">
                      <div class="mt-6 d-flex gap-2 justify-content-end">
                          <?php
                          $documentStatus  = $documentInfo['quotationStatus'];
                          $currentUserRole = getAuthenticatedUser()->getRoles()[0];
                              ?>

                        <button class="btn btn-primary mb-4" onclick="saveOrder()">
                          <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-device-floppy ti-xs me-2"></i>Save</span>
                        </button>
                          <?php
                          if (($currentUserRole == ROLE_SUPERADMIN || $currentUserRole == ROLE_SALES_MANAGER) && ($orderStatus == ORDER_STATUS_NEW || $orderStatus == ORDER_STATUS_REJECTED)) {
                          ?>

                            <button class="btn btn-primary mb-4" onclick="sendForApproval()">
                              <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-file-like ti-xs me-2"></i>Send for Approval</span>
                            </button>
                          <?php
                          }
                          if (($currentUserRole == ROLE_SUPERADMIN || $currentUserRole == ROLE_ACCOUNTS_MANAGER) && ($orderStatus == ORDER_STATUS_ACCOUNTANT_APPROVED)) {
                              ?>
                            <button class="btn btn-primary mb-4" data-bs-toggle="offcanvas" data-bs-target="#sendQuotationOffcanvas">
                              <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-send ti-xs me-2"></i>Send To Customer</span>
                            </button>
                        <?php
                        }
                          if (($currentUserRole == ROLE_SUPERADMIN || $currentUserRole == ROLE_SALES_MANAGER)
                              && ($orderStatus == ORDER_STATUS_AWAITING_APPROVAL || $orderStatus == ORDER_STATUS_ACCOUNTANT_REJECTED)) {
                              ?>
                            <button class="btn btn-primary mb-4" onclick="approveOrder()">
                              <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-eye-check ti-xs me-2"></i>Approve</span>
                            </button>
                            <button class="btn btn-danger mb-4" data-bs-toggle="modal" data-bs-target="#rejectReasonModal">
                              <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-circle-dashed-x ti-xs me-2"></i>Reject</span>
                            </button>
                              <?php
                          }
                          if(($currentUserRole == ROLE_SUPERADMIN || $currentUserRole == ROLE_ACCOUNTS_MANAGER) && $orderStatus == ORDER_STATUS_APPROVED) {
                            ?>
                            <button class="btn btn-primary mb-4" onclick="approveOrderAccountant()">
                              <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-eye-check ti-xs me-2"></i>Approve(Accounts)</span>
                            </button>
                            <button class="btn btn-danger mb-4" data-bs-toggle="modal" data-bs-target="#rejectReasonModal">
                              <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-circle-dashed-x ti-xs me-2"></i>Reject</span>
                            </button>
                            <?php
                            }
                          if ($currentUserRole == ROLE_SUPERADMIN && $orderStatus == ORDER_STATUS_SENT_TO_CUSTOMER) {
                          ?>

                            <button class="btn btn-warning mb-4" data-bs-toggle="offcanvas" data-bs-target="#sendQuotationOffcanvas">
                              <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-reload ti-xs me-2"></i>Re-send To Customer</span>
                            </button>
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

                        $ecommerceAddress = $customer->ecommerceAddress[0];
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
                      <p class="mb-2" id="customerAddressLine1"><?= $ecommerceAddress['addressLine1']; ?></p>
                      <p class="mb-2" id="customerAddressLine2"><?= $ecommerceAddress['addressLine2']; ?></p>
                      <p class="mb-2" id="customerCityPostalCode"><?= $ecommerceAddress['city'] . ", " . $ecommerceAddress['postalCode'] ?></p>
                      <p class="mb-2" id="customerStateCountry"><?= $ecommerceAddress['state'] . ", " . $ecommerceAddress['country'] ?></p>
<!--                      <p class="mb-2" id="customerVAT">VAT: --><?php //= $customer->vatNumber ?><!--</p>-->
<!--                      <p class="mb-2" id="customerCR">CR: --><?php //= $customer->companyCRNumber ?><!--</p>-->
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
                          <span class="h5 text-capitalize mb-0 text-nowrap">Order</span>
                        </dt>
                        <dd class="col-sm-7">
                          <div class="input-group input-group-merge disabled">
                            <span class="input-group-text">#</span>
                            <input name="quotationNumber" id="quotationNumber" type="text" class="form-control" disabled placeholder="Will be generated once saved / confirmed" value="<?= $documentInfo['orderId'] ?>"/>
                          </div>
                          <input name="documentId" id="documentId" type="hidden" value="<?= $documentInfo['documentId'] ?>"/>
                        </dd>
                        <dt class="col-sm-5 mb-2 d-md-flex align-items-center justify-content-end">
                          <span class="fw-normal">Date Issued:</span>
                        </dt>
                        <dd class="col-sm-7">
                          <input name="orderDate" id="orderDate" type="text" class="form-control gmm-date-format" placeholder="DD - MMM - YYYY" value="<?= $documentInfo['orderDateIssued'] ?>"/>
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
                          <span class="fw-normal">Carrier:</span>
                        </dt>
                        <dd class="col-sm-7">
                          <input type="text" name="carrier" id="carrier" class="form-control" value="<?=!empty($documentInfo['carrier']) ? $documentInfo['carrier'] : ''?>" placeholder="Carrier Name"/>
                        </dd>
                        <dt class="col-sm-5 mb-2 d-md-flex align-items-center justify-content-end">
                          <span class="fw-normal">Tracking Number:</span>
                        </dt>
                        <dd class="col-sm-7">
                          <input type="text" name="trackingNumber" id="trackingNumber" class="form-control" value="<?=!empty($documentInfo['trackingNumber']) ? $documentInfo['trackingNumber'] : ''?>" placeholder="Tracking Number"/>
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
                    <div class="mb-4" data-repeater-list="line-item">
                        <?php
                        $itemCount = 0;
                        foreach ($lineItems as $item) {
                            ?>
                          <div class="repeater-wrapper pt-0 pt-md-6" data-repeater-item>
                            <div class="d-flex border rounded position-relative pe-0">
                              <div class="row w-100 p-3">
                                <div class="col-md-2 col-12 mb-md-0 mb-4">
                                  <p class="h6 mb-1 itemNumber">Item
                                    #<?= ++$itemCount; ?></p>
                                  <select name="sparepartitem" class="sparepartitem form-control mb-5" data-selected="<?= $item['itemId'] ?>">
                                    <option value="">Select a part number</option>
                                      <?php
                                      $res = $db->query("SELECT * FROM spareparts WHERE active = 1 and sparepartId = ?s", $item['itemId']);
                                      while ($row = mysqli_fetch_assoc($res)) {
                                          ?>
                                        <option selected value="<?= $row['sparepartId'] ?>"
                                                data-desc="<?= $row['description'] ?>"
                                                data-leadtime="<?= $row['leadTime'] ?>"
                                                data-salesprice="<?= $row['salesPrice'] ?>"
                                                data-hscode="<?= $row['hsCode'] ?>"
                                                data-hspercentage="<?= $row['hsPercentage'] ?>"
                                                data-uom="<?= getUOMNameFromID($row['uomId']) ?>"
                                                data-uomid="<?= $row['uomId'] ?>"><?= $row['partNumber'] ?></option>
                                          <?php
                                      }
                                      ?>
                                  </select>
                                </div>
                                <div class="col-md-2 col-12 mb-md-0 mb-4">
                                  <p class="h6 mb-1">Description</p>
                                  <input name="sparepartdescription" type="text" class="form-control mb-5" value="<?= getItemDescriptionForSparepartID($item['itemId']) ?>" readonly/>
                                  <div class="text-heading" style="display: none">
                                    <div class="mb-1">Discount:</div>
                                    <span class="discount me-2">0%</span>
                                    <span class="tax-1 me-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Tax 1">0%</span>
                                    <span class="tax-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Tax 2">0%</span>
                                  </div>
                                </div>
                                <div class="col-md-1 col-12 mb-md-0 mb-4">
                                  <p class="h6 mb-1">Qty</p>
                                  <input name="qty" type="text" class="form-control numbers-only calculation-trigger" placeholder="Qty" min="1" required readonly value="<?= $item['quantity'] ?>"/>
                                </div>
                                <div class="col-md-1 col-12 mb-md-0 mb-4">
                                  <p class="h6 mb-1">ETA</p>
                                  <!--                                                            <p class="mb-0 text-heading">$24.00</p>-->
                                  <input name="eta" type="text" class="form-control gmmdatepicker-friendly" placeholder="Date" readonly value="<?= empty($item['eta']) ? "" : $item['eta'] ?>"/>
                                </div>
                                <div class="col-md-1 col-12 mb-md-0 mb-4">
                                  <p class="h6 mb-1">UOM</p>
                                  <select name="uom" class="form-control mb-6 calculation-trigger" data-placeholder="Select UOM">
                                    <option disabled value="">Select UOM</option>
                                      <?php
                                      $res = $db->query("SELECT * FROM uoms WHERE active = 1");

                                      while ($row = mysqli_fetch_assoc($res)) {
                                          $selected = "";
                                          if ($item['UOM'] == $row['uomId']) $selected = "selected";

                                          ?>
                                        <option <?= $selected ?> value="<?= $row['uomId'] ?>" data-type="<?= $row['type'] ?>" data-ratio="<?= $row['ratio'] ?>"><?= $row['uomName'] ?></option>
                                          <?php
                                      }
                                      ?>
                                  </select>
                                </div>
                                <div class="col-md-1 col-12 mb-md-0 mb-4">
                                  <p class="h6 mb-1">Unit Price</p>
                                  <input name="unitPrice" type="text" class="form-control numbers-only calculation-trigger" placeholder="" value="<?= $item['unitPrice'] ?>" readonly/>
                                </div>
                                <div class="col-md-1 col-12 mb-md-0 mb-4">
                                  <p class="h6 mb-1">Total</p>
                                  <input name="total" type="text" class="form-control" placeholder="" min="1" readonly/>
                                </div>
                                <div class="col-md-1 col-12 mb-md-0 mb-4">
                                  <p class="h6 mb-1">VAT %</p>
                                  <input name="vatPercentage" type="text" class="form-control numbers-only calculation-trigger" placeholder="" value="<?= $item['vatPercentage'] ?>" readonly/>
                                </div>
                                <div class="col-md-1 col-12 mb-md-0 mb-4">
                                  <p class="h6 mb-1">VAT Amt</p>
                                  <input name="vatAmount" type="text" class="form-control" placeholder="" readonly/>
                                </div>
                                <div class="col-md-1 col-12 mb-md-0 mb-4">
                                  <p class="h6 mb-1">HS Code</p>
                                  <input name="hsCode" type="text" class="form-control" placeholder="" value="<?= $item['hsCode'] ?>"/>
                                </div>
                                <div class="col-md-1 col-12 mb-md-0 mb-4">
                                  <p class="h6 mb-1">HS %</p>
                                  <input name="hsPercentage" type="text" class="form-control numbers-only calculation-trigger" placeholder="" value="<?= $item['hsPercentage'] ?>"/>
                                </div>
                                <div class="col-md-1 col-12 mb-md-0 mb-4">
                                  <p class="h6 mb-1">Discount %</p>
                                  <input name="discountPercentage" type="text" class="form-control numbers-only calculation-trigger" placeholder="" readonly value="<?= $item['discountPercentage'] ?>"/>
                                </div>
                                <div class="col-md-1 col-12 mb-md-0 mb-4">
                                  <p class="h6 mb-1 text-nowrap">Discount Amt</p>
                                  <input name="discountAmount" type="text" class="form-control" placeholder="" readonly/>
                                </div>
                                <div class="col-md-1 col-12 mb-md-0 mb-4">
                                  <p class="h6 mb-1 text-nowrap">Shipping Amt</p>
                                  <input name="shippingAmount" type="text" class="form-control numbers-only calculation-trigger" placeholder="" value="<?= $item['shippingAmount']?>"/>
                                </div>
                                <div class="col-md-2 col-12 mb-md-0 mb-4">
                                  <p class="h6 mb-1">Shipping VAT %</p>
                                  <input name="shippingVatPercentage" type="text" class="form-control numbers-only calculation-trigger" placeholder="" value="<?= $item['shippingVatPercentage'] ?>"/>
                                </div>
                                <div class="col-md-2 col-12 mb-md-0 mb-4">
                                  <p class="h6 mb-1">Shipping Sub Total</p>
                                  <input name="shippingSubTotal" type="text" class="form-control" placeholder="" min="1" readonly/>
                                </div>
                                <div class="col-md-2 col-12 mb-md-0 mb-4">
                                  <p class="h6 mb-1">Sub Total</p>
                                  <input name="subTotal" type="text" class="form-control" placeholder="" min="1" readonly/>
                                </div>
                              </div>
                              <div class="row p-3 justify-content-end"></div>
                              <div class="d-flex flex-column align-items-center justify-content-between border-start p-2 deleteItemCrossButton">
                                <i class="ti ti-x ti-lg cursor-pointer" data-repeater-delete style="display: none"></i>
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
                                  <!--                                                            <input name="sparepartitem" type="text" class="sparepartitem form-control mb-5" placeholder="Start typing..." onchange="onSparePartItemChanged(this)"/>-->
                                  <select name="sparepartitem" class="sparepartitem form-control mb-5" data-live-search="true">
                                    <option value="">Select a part number</option>
                                      <?php
                                      $res = $db->query("SELECT * FROM spareparts WHERE active = 1 limit 1");
                                      while ($row = mysqli_fetch_assoc($res)) {
                                          ?>
                                        <option value="<?= $row['sparepartId'] ?>" data-desc="<?= $row['description'] ?>" data-leadtime="<?= $row['leadTime'] ?>" data-salesprice="<?= $row['salesPrice'] ?>" data-hscode="<?= $row['hsCode'] ?>" data-hspercentage="<?= $row['hsPercentage'] ?>" data-uom="<?= getUOMNameFromID($row['uomId']) ?>" data-uomid="<?= $row['uomId'] ?>"><?= $row['partNumber'] ?></option>
                                          <?php
                                      }
                                      ?>
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
                              <div class="d-flex flex-column align-items-center justify-content-between border-start p-2 deleteItemCrossButton">
                                <i class="ti ti-x ti-lg cursor-pointer" data-repeater-delete style="display: none"></i>
                              </div>
                            </div>
                          </div>
                            <?php
                        }
                        ?>
                    </div>
                    <div class="row" style="display: none">
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
                          <span class="px-5">Total Amount before VAT & Shipping: </span>
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
                        <div class="d-flex justify-content-between mb-2">
                          <span class="px-5">Total Shipping Amount: </span>
                          <span id="totalShippingAmountSpan" class="fw-medium text-heading">SAR 0.00</span>
                        </div>
                        <hr/>
                        <div class="d-flex justify-content-between">
                          <span class="px-5">Total Amount After VAT & Shipping: </span>
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
          <!--Document History Canvas starts-->
          <!--Document History Canvas ENDS-->
          <!-- Send Quotation Sidebar -->
            <?php
            if (($currentUserRole == ROLE_SALES_EXECUTIVE || $currentUserRole == ROLE_SUPERADMIN) ) {
                ?>
              <div class="offcanvas offcanvas-end" id="sendQuotationOffcanvas" aria-hidden="true">
                <div class="offcanvas-header mb-6 border-bottom">
                  <h5 class="offcanvas-title">Send Quotation</h5>
                  <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body pt-0 flex-grow-1">
                  <form id="sendQuotationEmailForm" name="sendQuotationEmailForm" method="POST">
                    <div class="mb-6">
                      <label for="quotation-to" class="form-label">To</label>
                      <input type="text" class="form-control" id="quotation-to" name="quotation-to" value="<?= $customer->email ?>" placeholder="customer@email.com"/>
                    </div>
                    <div class="mb-6">
                      <label for="quotation-subject" class="form-label">Subject</label>
                      <input type="text" class="form-control" id="quotation-subject" name="quotation-subject" value="GrandMaster E-commerce Order #<?= $keyDocument->orderId ?>" placeholder="Invoice regarding goods"/>
                    </div>
                    <div class="mb-6" style="display: none">
                      <label for="quotation-message" class="form-label">Message</label>
                      <textarea class="form-control" name="quotation-message" id="quotation-message" cols="3" rows="8">Messrs. <?= $customer->companyName ?>,

Thank you for the opportunity to assist your business.

Please find attached the quotation <?= getQuotationNumberFromDocumentID($keyDocument->documentId) ?> for your review. The total amount is SAR <?= getTotalAmountByDocumentID($keyDocument->documentId)['totalAmountAfterVAT'] ?>, and this quotation is valid until <?= formatDate($keyDocument->quotationDateExpiry, false) ?>.

Should you have any questions or require adjustments to this quotation, please do not hesitate to contact us. We look forward to your feedback and are happy to assist further.

Thank you for considering our services.</textarea>
                    </div>
                    <div class="mb-6" style="display: none">
                                          <span class="badge bg-label-primary">
                                            <i class="ti ti-link ti-xs"></i>
                                            <span class="align-middle">Quotation Attached</span>
                                          </span>
                    </div>
                    <div class="mb-6 d-flex flex-wrap">
                      <button type="button" class="btn btn-primary me-4" onclick="sendQuotationEmail()">
                        Send
                      </button>
                      <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">
                        Cancel
                      </button>
                    </div>
                  </form>
                </div>
              </div>
                <?php
            }
            ?>
          <!-- /Send Quotation Sidebar -->
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
<!--RejectReasonModal-->
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
          <?php
          if ($orderStatus == ORDER_STATUS_AWAITING_APPROVAL) {
              ?>
            <button type="button" class="btn btn-danger" onclick="rejectOrder()">Reject</button>
              <?php
          }
          if ($orderStatus == ORDER_STATUS_APPROVED) {
              ?>
            <button type="button" class="btn btn-danger" onclick="rejectOrderAccountant()">Reject</button>
              <?php
          }

          ?>
      </div>
    </div>
  </div>
</div>
<!-- / Layout wrapper -->
<?php include_once __DIR__ . "/../../includes/dashboard/dashboard_footer_scripts.php"; ?>
<script>
  let sparepartsAutoCompleteData = [];
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

  function prepareOrderData() {
    console.log($('.source-item').repeaterVal());

    let repeaterData = $('.source-item').repeaterVal(); // Get the repeater data

    // Add additional data
    let requestData = {
      lineItems: repeaterData,
      orderDate: $('#orderDate').val(), 
      paymentTerms: $('#paymentTerms').val(), 
      documentId: $('#documentId').val(),
      carrier: $('#carrier').val(), 
      trackingNumber: $('#trackingNumber').val(), 
    };
    console.log("requestData");
    console.log(requestData);

    return requestData;
  }

  function saveOrder() {

    blockArea($('body'));

    let requestData = prepareOrderData();
    console.log(requestData);

    $.ajax({
      url: '/ajax/documents/save_ecommerce_order.php', // Update with your PHP script URL
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
    saveOrderThread('<?=ORDER_STATUS_AWAITING_APPROVAL ?>');
  }


  function sendForSOApproval() {
    let customerPO = $("#customerPO").val();

    if (customerPO.length > 3) {


      if ($("#poAttachmentFileName").val().length > 0) {
        //PO Was already attached. so Proceed with SO Approval
        saveOrderThread('<?=QUOTATION_STATUS_AWAITING_SALES_MANAGER_SO_APPROVAL ?>');
      } else {
        //PO is not attached. so prompt or upload the new PO.
        var fileInput = document.getElementById('poAttachmentFile');
        if (fileInput.files.length === 0) {
          showErrorMessage("Customer PO is not attached");
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
                saveOrderThread('<?=QUOTATION_STATUS_AWAITING_SALES_MANAGER_SO_APPROVAL ?>');
              }
              console.log(response); // Server response
            },
            error: function (xhr, status, error) {
              console.error(error);
            }
          });
        }
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

  function approveOrder() {
    let documentID = '<?=$documentID; ?>';
    updateOrderStatus(documentID, "<?=ORDER_STATUS_APPROVED ?>");
  }

  function approveOrderAccountant() {
      let documentID = '<?=$documentID; ?>';
      updateOrderStatus(documentID, "<?=ORDER_STATUS_ACCOUNTANT_APPROVED ?>");
    }

  function approveQuotationSOSM() {
    let documentID = '<?=$documentID; ?>';
    updateOrderStatus(documentID, "<?=QUOTATION_STATUS_AWAITING_ACCOUNTANT_SO_APPROVAL ?>");
  }

  function approveQuotationSOAccountant() {
    let documentID = '<?=$documentID; ?>';
    updateOrderStatus(documentID, "<?=QUOTATION_STATUS_SO_APPROVED ?>");
  }

  function confirmQuotationAndGenerateSalesOrder() {
    let documentID = '<?=$documentID; ?>';
    updateOrderStatus(documentID, "<?=QUOTATION_STATUS_CONFIRMED ?>");
  }

  function rejectOrder() {
    let documentID = '<?=$documentID; ?>';

    let rejectReason = $("#rejectReason").val();
    if (rejectReason.length) {
      updateOrderStatus(documentID, "<?=ORDER_STATUS_REJECTED ?>", true, rejectReason);
    } else {
      showErrorMessage("You must enter a reason for rejection");
    }
  }

  function rejectOrderAccountant() {
    let documentID = '<?=$documentID; ?>';

    let rejectReason = $("#rejectReason").val();
    if (rejectReason.length) {
      updateOrderStatus(documentID, "<?=ORDER_STATUS_ACCOUNTANT_REJECTED ?>", true, rejectReason);
    } else {
      showErrorMessage("You must enter a reason for rejection");
    }
  }

  function rejectQuotationSMSO() {
    let documentID = '<?=$documentID; ?>';
    let rejectReason = $("#rejectReason").val();
    if (rejectReason.length) {
      updateOrderStatus(documentID, "<?=QUOTATION_STATUS_SALES_MANAGER_SO_REJECTED ?>", true, rejectReason);
    } else {
      showErrorMessage("You must enter a reason for rejection");
    }
  }

  function rejectQuotationAccountantSO() {
    let documentID = '<?=$documentID; ?>';
    let rejectReason = $("#rejectReason").val();
    if (rejectReason.length) {
      updateOrderStatus(documentID, "<?=QUOTATION_STATUS_ACCOUNTANT_SO_REJECTED ?>", true, rejectReason);
    } else {
      showErrorMessage("You must enter a reason for rejection");
    }
  }


  function saveOrderThread(status) {

    //Then proceed with saving the quotaion.
    if ($('#customer').val().length > 0) {
      // blockArea($('body'));
      let requestData = prepareOrderData();
      $.ajax({
        url: '/ajax/documents/save_ecommerce_order.php', // Update with your PHP script URL
        method: 'POST',
        data: JSON.stringify(requestData), // Send the combined data as a JSON string
        contentType: 'application/json', // Indicate that the data is JSON
        dataType: 'json', // Expect a JSON response
        success: function (response) {
          console.log('Server Response after saving order:', response);

          if (typeof response === 'string') {
            response = JSON.parse(response);
          }

          console.log("REsponse: " + response.status);
          // Check the response status
          if (response.status === 'success') {
            unBlockArea($('body'));
            let documentID = response.documentId;
            updateOrderStatus(documentID, status);
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

  function updateOrderStatus(documentID, status, showMessage = true, rejectReason = "") {

    console.log("Updating QuotationStatus: " + documentID + " - " + status + " - " + showMessage + " - " + rejectReason);

    var formData = new FormData();
    formData.append("documentID", documentID);
    formData.append("status", status);
    formData.append("rejectReason", rejectReason);


    $.ajax({
      url: '/ajax/documents/update_ecommerce_order_status.php',
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
            showSuccessMessage(message, gotoPage, "/ecommerce/edit/" + documentID);
        }
      },

      error: function (error) {
        console.log(error);
      }
    });

  }


  function sendQuotationEmail() {

    blockArea($('body'));
    let documentID = '<?=$documentID ?>';

    var form = $('#sendQuotationEmailForm')[0]; // You need to use standard javascript object here
    var formData = new FormData(form);
    formData.append("documentID", documentID);

    $.ajax({
      url: '/ajax/documents/send_email_ecommerce_customer.php',
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
          // showSuccessMessage(message, gotoPage, location.href = "/quotation/edit/" + documentID);
          updateOrderStatus(documentID, '<?=ORDER_STATUS_SENT_TO_CUSTOMER ?>', false);
          showSuccessMessage("Email sent to customer.", gotoPage, "/ecommerce/edit/" + documentID);
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
    const relativeUrl = '/ajax/documents/generate_pdf.php?documentID=' + '<?=$documentID ?>';
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
          fp.clear();

          //Initialize selectpicker
          const sparepartitem = $('[name="line-item[' + index + '][sparepartitem]"]');
          initializeSelect2(sparepartitem, null);

        },
        hide: function (remove) {
          $(this).slideUp(500, function () {
            remove(); // Ensures the element is removed
            doCalculation(); // Call after removal from DOM
          });
        },


      });
    }

    // // Item details select onchange
    // $(document).on('change', '.item-details', function () {
    //     var $this = $(this),
    //         value = adminDetails[$this.val()];
    //     if ($this.next('textarea').length) {
    //         $this.next('textarea').val(value);
    //     } else {
    //         $this.after('<textarea class="form-control" rows="2">' + value + '</textarea>');
    //     }
    // });
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
    // sparepartitem = $('[name="line-item[0][sparepartitem]"]');
    // sparepartitem.selectpicker();

    sparepartitem = $('[name="line-item[0][sparepartitem]"]');
    preselectedValue = sparepartitem.data('selected') || null;
    initializeSelect2(sparepartitem, preselectedValue);


      <?php
      for($itemIndex = 1; $itemIndex <= $itemCount - 1; $itemIndex++  )
      {
      ?>
    sparepartitem = $('[name="line-item[<?=$itemIndex ?>][sparepartitem]"]');
    preselectedValue = sparepartitem.data('selected') || null;
    initializeSelect2(sparepartitem, preselectedValue);
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
  // $(document).on('change', '.sparepartitem', function (e) {
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

  function doCalculation() {
    const totalItemRows = getRepeaterRowCount();

    let rowSubTotal = 0;

    let totalAmountBeforeVAT = 0.00;
    let totalDiscountAmount = 0.00;
    let totalShippingAmount = 0.00;
    let totalVATAmount = 0.00;
    let totalAmountAfterVAT = 0.00;

    for (let index = 0; index < totalItemRows; index++) {

      let qty = parseFloat(getElementByIndexAndName(index, 'qty').val()) || 0;
      let unitPrice = parseFloat(getElementByIndexAndName(index, 'unitPrice').val()) || 0;
      let vatPercentage = parseFloat(getElementByIndexAndName(index, 'vatPercentage').val()) || 0;
      let discountPercentage = parseFloat(getElementByIndexAndName(index, 'discountPercentage').val()) || 0;
      let uomRatio = parseFloat(getElementByIndexAndName(index, 'uom').find(':selected').data("ratio")) || 1;
      let shippingVatPercentage = parseFloat(getElementByIndexAndName(index, 'shippingVatPercentage').val()) || 0;
      let shippingAmount = parseFloat(getElementByIndexAndName(index, 'shippingAmount').val()) || 0;


      // Calculate base subtotal
      let baseSubtotal = qty * uomRatio * unitPrice;

      // Apply discount
      let discountAmount = baseSubtotal * (discountPercentage / 100);
      let discountedSubtotal = baseSubtotal - (baseSubtotal * (discountPercentage / 100));


      // Calculate VAT amount (based on the discounted subtotal)
      let vatAmount = discountedSubtotal * (vatPercentage / 100);


      //Calculate Shipping Subtotal
      let shippingSubTotal = shippingAmount + (shippingAmount * (shippingVatPercentage/100));

      let subTotal = discountedSubtotal + vatAmount + shippingSubTotal;


      //Add totals
      totalAmountBeforeVAT += discountedSubtotal;
      totalDiscountAmount += discountAmount;
      totalVATAmount += vatAmount;
      totalShippingAmount += shippingSubTotal;
      totalAmountAfterVAT += (parseFloat(vatAmount) + parseFloat(discountedSubtotal) + parseFloat(shippingSubTotal));


      // Set values to respective fields
      getElementByIndexAndName(index, 'subTotal').val(toTwoDecimal(subTotal)); // Set the discounted subtotal
      getElementByIndexAndName(index, 'total').val(toTwoDecimal(discountedSubtotal)); // Set the discounted subtotal
      getElementByIndexAndName(index, 'vatAmount').val(toTwoDecimal(vatAmount));         // Set the VAT amount
      getElementByIndexAndName(index, 'discountAmount').val(toTwoDecimal(discountAmount));         // Set the Discount amount
      getElementByIndexAndName(index, 'shippingSubTotal').val(toTwoDecimal(shippingSubTotal));


    }


    const totalAmountBeforeVATSpan = $("#totalAmountBeforeVATSpan");
    const totalDiscountAmountSpan = $("#totalDiscountAmountSpan");
    const totalVATAmountSpan = $("#totalVATAmountSpan");
    const totalShippingAmountSpan = $("#totalShippingAmountSpan");
    const totalAmountAfterVATSpan = $("#totalAmountAfterVATSpan");

    totalAmountBeforeVATSpan.html("SAR " + toTwoDecimal(totalAmountBeforeVAT).toString());
    totalDiscountAmountSpan.html("SAR " + toTwoDecimal(totalDiscountAmount).toString());
    totalVATAmountSpan.html("SAR " + toTwoDecimal(totalVATAmount).toString());
    totalShippingAmountSpan.html("SAR " + toTwoDecimal(totalShippingAmount).toString());
    totalAmountAfterVATSpan.html("SAR " + toTwoDecimal(totalAmountAfterVAT).toString());

  }

  //const sparePartsOptions = [
  //    <?php
  //    $res = $db->query("SELECT * FROM spareparts WHERE active = 1");
  //    while ($row = mysqli_fetch_assoc($res)) {
  //    // Safely encode values to prevent JS errors
  //    $sparepartId = json_encode($row['sparepartId']);
  //    $partNumber = json_encode($row['partNumber']);
  //    $description = json_encode($row['description']);
  //    $leadTime = $row['leadTime'];
  //    if (is_null($leadTime)) $leadTime = 14;
  //    $isValidDate = !empty($leadTime) && DateTime::createFromFormat('Y-m-d', $leadTime) && (strtotime($leadTime) !== false);
  //    //        $isValidDate = DateTime::createFromFormat('Y-m-d', $leadTime) && (strtotime($leadTime) !== false);
  //    // If `leadTime` is invalid, set it to 14 days from today
  //    if (!$isValidDate) {
  //        $leadTime = date('Y-m-d', strtotime('+' . (int)$leadTime . ' days'));
  //    }
  //    $leadTime = json_encode($leadTime);
  //    $salesPrice = json_encode($row['salesPrice']);
  //    $hsCode = json_encode($row['hsCode']);
  //    $hsPercentage = json_encode($row['hsPercentage']);
  //    $uomName = json_encode(getUOMNameFromID($row['uomId']));
  //    $uomId = json_encode($row['uomId']);
  //    ?>
  //    {
  //        id: <?php //= $sparepartId ?>//,
  //        text: <?php //= $partNumber ?>//,
  //        desc: <?php //= $description ?>//,
  //        leadtime: <?php //= $leadTime ?>//,
  //        salesprice: <?php //= $salesPrice ?>//,
  //        hscode: <?php //= $hsCode ?>//,
  //        hspercentage: <?php //= $hsPercentage ?>//,
  //        uom: <?php //= $uomName ?>//,
  //        uomid: <?php //= $uomId ?>
  //    },
  //    <?php
  //    }
  //    ?>
  //];


  // Function to fetch filtered results
  // function fetchFilteredResults(query) {
  //     const filtered = sparePartsOptions.filter(option => {
  //         const textMatch = option.text.toLowerCase().includes(query.toLowerCase());
  //         const descMatch = option.desc.toLowerCase().includes(query.toLowerCase());
  //         return textMatch || descMatch;
  //     });
  //     return filtered.slice(0, 50); // Limit results to the first 10 matches
  // }


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

    // If a preselected value is provided, add it to the dropdown
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
</script>
<style>
    .no-hand {
        cursor: default !important;
    }
</style>
</body>
</html>