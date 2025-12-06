<?php
$PAGE_ID = "SUPPLIER_VIEW";
include_once __DIR__ . "/../../includes/baseIncludes.php";
include_once __DIR__ . "/../../includes/auth_check.php";

$supplierID = "";

$supplierID = filter_input(INPUT_GET, 'supplierID', FILTER_VALIDATE_INT);

if ($supplierID === null || $supplierID === false || filter_var($supplierID, FILTER_VALIDATE_INT) === false) {
    header("location:/suppliers/list");
    exit();
}


?>
<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="../../assets/" data-template="vertical-menu-template" data-style="light">
<head>
    <?php include_once __DIR__ . "/../../includes/dashboard/dashboard_head_section.php"; ?>
    <title>GrandMaster ERP | Suppliers</title>
  <!-- Vendors CSS -->
  <link rel="stylesheet" href="/assets/vendor/libs/node-waves/node-waves.css"/>
  <link rel="stylesheet" href="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css"/>
  <link rel="stylesheet" href="/assets/vendor/libs/typeahead-js/typeahead.css"/>
  <link rel="stylesheet" href="/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css"/>
  <link rel="stylesheet" href="/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css"/>
  <link rel="stylesheet" href="/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css"/>
  <link rel="stylesheet" href="/assets/vendor/libs/select2/select2.css"/>
  <link rel="stylesheet" href="/assets/vendor/libs/@form-validation/form-validation.css"/>
  <link rel="stylesheet" href="/assets/vendor/libs/bootstrap-select/bootstrap-select.css"/>
  <link rel="stylesheet" href="/assets/vendor/libs/animate-css/animate.css"/>
  <link rel="stylesheet" href="/assets/vendor/libs/sweetalert2/sweetalert2.css"/>
  <!-- Page CSS -->
    <?php include_once __DIR__ . "/../../includes/dashboard/dashboard_head_section_post_vendor.php"; ?>
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
            <?php
            $supplier = new Supplier($db);
            $supplier->loadById($supplierID);
            ?>
          <div class="d-flex flex-column flex-sm-row align-items-center justify-content-sm-between mb-6 text-center text-sm-start gap-2">
            <div class="mb-2 mb-sm-0">
              <h4 class="mb-1">Supplier ID #<?= $supplier->supplierId ?></h4>
              <p class="mb-0"><?= formatDate($supplier->createdAt) ?></p>
            </div>
            <!--                        <button type="button" class="btn btn-label-danger delete-supplier">Delete Supplier</button>-->
          </div>
          <div class="row">
            <!-- Supplier-detail Sidebar -->
            <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
              <!-- Supplier-detail Card -->
              <div class="card mb-6">
                <div class="card-body pt-12">
                  <div class="supplier-avatar-section">
                    <div class="d-flex align-items-center flex-column">
                      <img class="img-fluid rounded mb-4" src="../../assets/img/avatars/1.png" height="120" width="120" alt="User avatar"/>
                      <div class="supplier-info text-center mb-6">
                        <h5 class="mb-0"><?= $supplier->companyName ?></h5>
                        <h5 class="mb-0"><?= $supplier->companyNameAr ?></h5>
                      </div>
                    </div>
                  </div>
                  <div class="row text-center mb-6">
                    <div class="col-6">
                      <span>VAT: <?= $supplier->vatNumber ?></span>
                    </div>
                    <div class="col-6">
                      <span>CR: <?= $supplier->companyCRNumber ?></span>
                    </div>
                  </div>
                  <div class="d-flex justify-content-around flex-wrap mb-6 gap-0 gap-md-3 gap-lg-4">
                    <div class="d-flex align-items-center gap-4 me-5">
                      <div class="avatar">
                        <div class="avatar-initial rounded bg-label-primary">
                          <i class="ti ti-shopping-cart ti-lg"></i>
                        </div>
                      </div>
                      <div>
                        <!--TODO: calculate total number orders for this supplier-->
                        <h5 class="mb-0"><?=getOrderCountForSupplier($supplier->supplierId)?></h5>
                        <span>Orders</span>
                      </div>
                    </div>
                    <div class="d-flex align-items-center gap-4">
                      <div class="avatar">
                        <div class="avatar-initial rounded bg-label-primary">
                          <i class="ti ti-currency-dollar ti-lg"></i>
                        </div>
                      </div>
                      <div>
                        <!--TODO: calculate total amount from orders for this supplier-->
                        <h5 class="mb-0"><?=fetchOutstandingBalanceForSupplierFromSupplierID($supplier->supplierId)['totalPaid']?></h5>
                        <span>Spent</span>
                      </div>
                    </div>
                  </div>
                  <div class="info-container">
                    <h5 class="pb-4 border-bottom text-capitalize mt-6 mb-4">Details</h5>
                    <ul class="list-unstyled mb-6">
                      <li class="mb-2">
                        <span class="h6 me-1">Phone:</span> <span><?= $supplier->phone ?></span>
                      </li>
                      <li class="mb-2">
                        <span class="h6 me-1">Email:</span> <span><?= $supplier->email ?></span>
                      </li>
                      <li class="mb-2">
                        <span class="h6 me-1">Status:</span>
                          <?php
                          if ($supplier->status == "Active") {
                              ?>
                            <span class="badge bg-label-success">Active</span>
                              <?php
                          } else { ?>
                            <span class="badge bg-label-danger">Inactive</span>
                              <?php
                          }
                          ?>
                      </li>
                      <li class="mb-2">
                        <span class="h6 me-1">Address:</span>
                        <span><?= getFormattedAddress($supplier) ?></span>
                      </li>
                      <li class="mb-2">
                        <span class="h6 me-1">Website:</span>
                        <span><?= $supplier->website ?></span>
                      </li>
                      <li class="mb-2">
                        <span class="h6 me-1">Total:</span>
                        <span>SAR <?= fetchOutstandingBalanceForSupplierFromSupplierID($supplier->supplierId)['totalPaid'] ?></span>
                      </li>
                    </ul>
                    <div class="d-flex justify-content-center">
                      <a href="javascript:;" class="btn btn-primary w-100" data-bs-target="#editSupplierModal" data-bs-toggle="modal">Edit
                        Details</a>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /Supplier-detail Card -->
              <!-- Plan Card -->
              <div class="row">
                <div class="col-4">
                  <div class="card h-100">
                    <div class="card-body d-flex justify-content-between align-items-center p-5">
                      <div class="card-icon">
                                                <span class="badge bg-label-success rounded p-2">
                                                  <i class="ti ti-cash ti-26px"></i>
                                                </span>
                      </div>
                      <div class="card-title mb-0 mx-3">
                        <h5 class="mb-1 me-2"><?= PaymentTerm::getPaymentTermById($supplier->salesPaymentTermId)->termName; ?></h5>
                        <p class="mb-0">Sales Payment Term</p>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-4">
                  <div class="card h-100">
                    <div class="card-body d-flex justify-content-between align-items-center p-5">
                      <div class="card-icon">
                                                <span class="badge bg-label-warning rounded p-2">
                                                  <i class="ti ti-credit-card-pay ti-26px"></i>
                                                </span>
                      </div>
                      <div class="card-title mb-0 mx-3">
                        <h5 class="mb-1 me-2"><?= PaymentTerm::getPaymentTermById($supplier->purchasePaymentTermId)->termName; ?></h5>
                        <p class="mb-0">Purchase Payment Term</p>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-4">
                  <div class="card h-100">
                    <div class="card-body d-flex justify-content-between align-items-center p-5">
                      <div class="card-icon">
                                                <span class="badge bg-label-info rounded p-2">
                                                  <i class="ti ti-currency-riyal ti-26px"></i>
                                                </span>
                      </div>
                      <div class="card-title mb-0 mx-3">
                        <h5 class="mb-1 me-2"><?= Currency::getCurrencyById($supplier->currencyId)->currencyName; ?></h5>
                        <p class="mb-0">Currency</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /Plan Card -->
            </div>
            <!--/ Supplier Sidebar -->
            <!-- Supplier Content -->
            <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
              <!-- Supplier Pills -->
              <div class="nav-align-top">
                <ul class="nav nav-pills flex-column flex-md-row mb-6 row-gap-2">
                  <li class="nav-item">
                    <a class="nav-link active" href="javascript:void(0);"><i class="ti ti-user ti-sm me-1_5"></i>Overview</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="app-ecommerce-supplier-details-security.html"><i class="ti ti-lock ti-sm me-1_5"></i>Security</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="app-ecommerce-supplier-details-billing.html"><i class="ti ti-map-pin ti-sm me-1_5"></i>Address
                      & Billing</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="app-ecommerce-supplier-details-notifications.html"><i class="ti ti-bell ti-sm me-1_5"></i>Notifications</a>
                  </li>
                </ul>
              </div>
              <!--/ Supplier Pills -->
              <!-- / Supplier cards -->
              <div class="row text-nowrap">
                <div class="col-md-6 mb-6">
                  <div class="card h-100">
                    <div class="card-body">
                      <div class="card-icon mb-2">
                        <div class="avatar">
                          <div class="avatar-initial rounded bg-label-primary">
                            <i class="ti ti-currency-dollar ti-lg"></i>
                          </div>
                        </div>
                      </div>
                      <div class="card-info">
                        <h5 class="card-title mb-2">Account Balance</h5>
                        <div class="d-flex align-items-baseline gap-1">
                          <h5 class="text-primary mb-0">$2345</h5>
                          <p class="mb-0">Credit Left</p>
                        </div>
                        <p class="mb-0 text-truncate">Account balance for next purchase</p>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 mb-6">
                  <div class="card">
                    <div class="card-body">
                      <div class="card-icon mb-2">
                        <div class="avatar">
                          <div class="avatar-initial rounded bg-label-success">
                            <i class="ti ti-gift ti-lg"></i>
                          </div>
                        </div>
                      </div>
                      <div class="card-info">
                        <h5 class="card-title mb-2">Loyalty Program</h5>
                        <span class="badge bg-label-success mb-2">Platinum member</span>
                        <p class="mb-0">3000 points to next tier</p>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 mb-6">
                  <div class="card">
                    <div class="card-body">
                      <div class="card-icon mb-2">
                        <div class="avatar">
                          <div class="avatar-initial rounded bg-label-warning">
                            <i class="ti ti-star ti-lg"></i>
                          </div>
                        </div>
                      </div>
                      <div class="card-info">
                        <h5 class="card-title mb-2">Wishlist</h5>
                        <div class="d-flex align-items-baseline gap-1">
                          <h5 class="text-warning mb-0">15</h5>
                          <p class="mb-0">Items in wishlist</p>
                        </div>
                        <p class="mb-0 text-truncate">Receive notification when items go on
                          sale</p>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 mb-6">
                  <div class="card">
                    <div class="card-body">
                      <div class="card-icon mb-2">
                        <div class="avatar">
                          <div class="avatar-initial rounded bg-label-info">
                            <i class="ti ti-crown ti-lg"></i>
                          </div>
                        </div>
                      </div>
                      <div class="card-info">
                        <h5 class="card-title mb-2">Coupons</h5>
                        <div class="d-flex align-items-baseline gap-1">
                          <h5 class="text-info mb-0">21</h5>
                          <p class="mb-0">Coupons you win</p>
                        </div>
                        <p class="mb-0 text-truncate">Use coupon on next purchase</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- / supplier cards -->
              <!-- Invoice table -->
              <div class="card mb-6">
                <div class="table-responsive mb-4">
                  <table class="table datatables-supplier-order border-top">
                    <thead>
                    <tr>
                      <th></th>
                      <th>Order</th>
                      <th>Date</th>
                      <th>Status</th>
                      <th>Spent</th>
                      <th class="text-md-center">Actions</th>
                    </tr>
                    </thead>
                  </table>
                </div>
              </div>
              <!-- /Invoice table -->
            </div>
            <!--/ Supplier Content -->
          </div>
          <!-- Modal -->
          <!-- Edit Supplier Modal -->
          <div class="modal fade" id="editSupplierModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-simple modal-edit-user">
              <div class="modal-content">
                <div class="modal-body">
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  <div class="text-center mb-6">
                    <h4 class="mb-2">Modify Supplier Information</h4>
                    <!--                                        <p>Updating user details will receive a privacy audit.</p>-->
                  </div>
                  <form class="ecommerce-supplier-add pt-0" id="supplierEditForm" method="POST" name="supplierEditForm" action="#" onsubmit="return false;">
                    <!--HIDDEN FIELDS-->
                    <input type="hidden" id="supplierId" name="supplierId" value="<?= $supplier->supplierId ?>"/>
                    <!--HIDDEN FIELDS END-->
                    <div class="ecommerce-supplier-add-basic mb-4">
                      <h5 class="mb-6">Basic Information</h5>
                      <div class="row">
                        <div class="mb-6  col-6">
                          <label class="form-label" for="supplierName">Name*</label>
                          <input type="text" class="form-control" id="supplierName" placeholder="Mohammed Faisal" name="supplierName" aria-label="Mohammed Faisal" value="<?= $supplier->companyName ?>"/>
                        </div>
                        <div class="mb-6 col-6">
                          <label class="form-label" for="supplierNameAR">Name in Arabic*
                          </label>
                          <input type="text" class="form-control" id="supplierNameAR" placeholder="محمد فيصل" name="supplierNameAR" aria-label="محمد فيصل" value="<?= $supplier->companyNameAr ?>"/>
                        </div>

                        <div class="mb-6 col-6">
                          <label class="form-label" for="supplierVATNumber">VAT Number</label>
                          <input type="text" id="supplierVATNumber" class="form-control" placeholder="123456789123456" aria-label="123456789123456" name="supplierVATNumber" value="<?= $supplier->vatNumber ?>"/>
                        </div>


                        <div class="mb-6 col-6">
                          <label class="form-label" for="companyCRNumber">CR Number</label>
                          <input type="text" id="companyCRNumber" class="form-control" placeholder="123456789123456" aria-label="123456789123456" name="companyCRNumber" value="<?= $supplier->companyCRNumber ?>"/>
                        </div>
                        <div class="mb-6 col-6">
                          <label class="form-label" for="ecommerce-supplier-add-email">
                            Email*
                          </label>
                          <input type="text" id="ecommerce-supplier-add-email" class="form-control" placeholder="john.doe@example.com" aria-label="john.doe@example.com" name="supplierEmail" value="<?= $supplier->email ?>"/>
                        </div>
                        <div class="mb-6 col-6">
                          <label class="form-label" for="ecommerce-supplier-add-contact">
                            Mobile*
                          </label>
                          <input type="text" id="ecommerce-supplier-add-contact" class="form-control phone-mask" placeholder="+(123) 456-7890" aria-label="+(123) 456-7890" name="supplierContact" value="<?= $supplier->phone ?>"/>
                        </div>
                      </div>
                    </div>
                    <div class="ecommerce-supplier-add-shiping mb-6">
                      <h6 class="mb-6">Address Information</h6>
                      <div class="mb-6">
                        <label class="form-label" for="supplierAddress1">Address Line 1</label>
                        <input type="text" id="supplierAddress1" class="form-control" placeholder="45 Roker Terrace" aria-label="45 Roker Terrace" name="supplierAddress1" value="<?= $supplier->addressLine1 ?>"/>
                      </div>
                      <div class="mb-6">
                        <label class="form-label" for="supplierAddress2">Address Line 2</label>
                        <input type="text" id="supplierAddress2" class="form-control" aria-label="address2" name="supplierAddress2" value="<?= $supplier->addressLine2 ?>"/>
                      </div>
                      <div class="row">
                        <div class=" mb-6 col-6">
                          <label class="form-label" for="supplierCountry">Country*</label>
                          <select id="supplierCountry" name="supplierCountry" class="selectpicker form-select w-100" data-style="btn-default" data-live-search="true" onchange="populateStateForSelectedCountry(this.value, 'supplierState', 'supplierCity')">
                            <option value="">Select a Country</option>
                              <?php
                              $res = $db->query("SELECT * FROM countries");
                              while ($row = mysqli_fetch_assoc($res)) {
                                  ?>
                                <option value="<?= $row['id'] ?>" <?php if ($row['id'] == $supplier->countryId) echo "selected" ?> ><?= $row['name'] ?></option>
                                  <?php
                              }
                              ?>
                          </select>
                        </div>
                        <div class=" mb-6 col-6">
                          <label class="form-label" for="supplierState">State / Province*
                          </label>
                          <select id="supplierState" name="supplierState" class="selectpicker w-100" data-style="btn-default" data-live-search="true" onchange="populateCityForSelectedState(this.value, 'supplierCity')">
                              <?php
                              $res = $db->query("SELECT * FROM states WHERE country_id = ?s", $supplier->countryId);
                              if ($res) {
                                  ?>
                                <option value="">Select a State</option> <?php
                              } else {
                                  ?>
                                <option value="">Select the Country first</option> <?php
                              }

                              while ($row = mysqli_fetch_assoc($res)) {
                                  ?>
                                <option value="<?= $row['id'] ?>" <?php if ($row['id'] == $supplier->stateId) echo "selected" ?> ><?= $row['name'] ?></option>
                                  <?php
                              }
                              ?>
                          </select>
                        </div>
                        <div class="mb-6 col-6">
                          <label class="form-label" for="supplierCity">City*</label>
                          <select id="supplierCity" name="supplierCity" class="selectpicker w-100" data-style="btn-default" data-live-search="true">
                              <?php
                              $res = $db->query("SELECT * FROM cities WHERE state_id = ?s", $supplier->stateId);
                              if ($res) {
                                  ?>
                                <option value="">Select a City</option> <?php
                              } else {
                                  ?>
                                <option value="">Select the State first</option> <?php
                              }

                              while ($row = mysqli_fetch_assoc($res)) {
                                  ?>
                                <option value="<?= $row['id'] ?>" <?php if ($row['id'] == $supplier->cityId) echo "selected" ?> ><?= $row['name'] ?></option>
                                  <?php
                              }
                              ?>
                          </select>
                        </div>
                        <div class="col-6 mb-6">
                          <label class="form-label" for="supplierPostalCode">Postal Code
                          </label>
                          <input type="text" id="supplierPostalCode" class="form-control" placeholder="734990" aria-label="734990" name="supplierPostalCode" maxlength="8" value="<?= $supplier->postalCode ?>"/>
                        </div>
                      </div>
                    </div>
                    <div class="ecommerce-supplier-add-shiping mb-6">
                      <h6 class="mb-6">Other Information</h6>
                      <div class="row">

                        <div class="mb-6 col-6">
                          <label class="form-label" for="supplierCurrency">Currency*</label>
                          <select id="supplierCurrency" name="supplierCurrency" class="selectpicker w-100" data-style="btn-default" data-live-search="true">
                            <option value="">Select</option>
                              <?php
                              $res = $db->query("SELECT * FROM currencies WHERE active = 1");
//                              $selected = "";
//                              while ($row = mysqli_fetch_assoc($res)) {
//                                  if ($row['currencyId'] == $supplier->currencyId) $selected = 'selected';
//                                  echo '<option value="' . $row['currencyId'] . '" ' . $selected . '>' . $row['currency'] . " - " . $row['currencyName'] . '</option>';
//                              }
                              while ($row = mysqli_fetch_assoc($res)) {
                                  ?>
                                <option value="<?= $row['currencyId'] ?>" <?php if ($row['currencyId'] == $supplier->currencyId) echo "selected" ?> ><?= $row['currency'] . " - " . $row['currencyName'] ?></option>
                                  <?php
                              }
                              ?>
                              ?>
                          </select>
                        </div>
                        <div class=" mb-6 col-6">
                          <label class="form-label" for="supplierPurchasePaymentTerm">Purchase
                            Payment Term*
                          </label>
                          <select id="supplierPurchasePaymentTerm" name="supplierPurchasePaymentTerm" class="selectpicker form-select w-100" data-style="btn-default" data-live-search="true">
                            <option value="">Select</option>
                              <?php
                              $res = $db->query("SELECT * FROM payment_terms WHERE active = 1");
//                              $selected = "";
//                              while ($row = mysqli_fetch_assoc($res)) {
//                                  if ($row['termId'] == $supplier->purchasePaymentTermId) $selected = 'selected';
//                                  echo '<option value="' . $row['termId'] . '" ' . $selected . '>' . $row['termName'] . '</option>';
//                              }

                              while ($row = mysqli_fetch_assoc($res)) {
                                  ?>
                                <option value="<?= $row['termId'] ?>" <?php if ($row['termId'] == $supplier->purchasePaymentTermId) echo "selected" ?> ><?= $row['termName'] ?></option>
                                  <?php
                              }
                              ?>
                          </select>
                        </div>
                        <div class="col-6 mb-6">
                          <label class="form-label" for="supplierSalesPaymentTerm">Sales
                            Payment Term*
                          </label>
                          <select id="supplierSalesPaymentTerm" name="supplierSalesPaymentTerm" class="selectpicker w-100" data-style="btn-default" data-live-search="true">
                            <option value="">Select</option>
                              <?php
                              $res = $db->query("SELECT * FROM payment_terms WHERE active = 1");
//                              $selected = "";
//                              while ($row = mysqli_fetch_assoc($res)) {
//                                  if ($row['termId'] == $supplier->salesPaymentTermId) $selected = 'selected';
//                                  echo '<option value="' . $row['termId'] . '" ' . $selected . '>' . $row['termName'] . '</option>';
//                              }
                              while ($row = mysqli_fetch_assoc($res)) {
                                  ?>
                                <option value="<?= $row['termId'] ?>" <?php if ($row['termId'] == $supplier->salesPaymentTermId) echo "selected" ?> ><?= $row['termName'] ?></option>
                                  <?php
                              }
                              ?>
                          </select>
                        </div>
                        <div class="col-6 mb-6">
                          <label class="form-label" for="supplierWebsite">Website</label>
                          <input type="text" id="supplierWebsite" class="form-control" placeholder="https://example.com" aria-label="https://example.com" name="supplierWebsite" value="<?= $supplier->website ?>"/>
                        </div>
                        <div class="mb-6 col-6">
                          <label class="form-label" for="supplierStatus">Status</label>
                          <select id="supplierStatus" name="supplierStatus" class="selectpicker w-100" data-style="btn-default" data-live-search="true">
                            <option data-tokens="active" <?php if ($supplier->status == "Active") echo 'selected' ?> value="Active">
                              Active
                            </option>
                            <option data-tokens="inactive" <?php if ($supplier->status == "Inactive") echo 'selected' ?> value="Inactive">
                              Inactive
                            </option>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div>
                      <button class="btn btn-primary me-sm-4 data-submit">Update</button>
                      <button type="reset" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal" aria-label="Close">
                        Discard
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <!--/ Edit User Modal -->
          <!-- Add New Credit Card Modal -->
          <div class="modal fade" id="upgradePlanModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-simple modal-upgrade-plan">
              <div class="modal-content">
                <div class="modal-body p-4">
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  <div class="text-center mb-6">
                    <h4 class="mb-2">Upgrade Plan</h4>
                    <p>Choose the best plan for user.</p>
                  </div>
                  <form id="upgradePlanForm" class="row g-4" onsubmit="return false">
                    <div class="col-sm-9">
                      <label class="form-label" for="choosePlan">Choose Plan</label>
                      <select id="choosePlan" name="choosePlan" class="form-select" aria-label="Choose Plan">
                        <option selected>Choose Plan</option>
                        <option value="standard">Standard - $99/month</option>
                        <option value="exclusive">Exclusive - $249/month</option>
                        <option value="Enterprise">Enterprise - $499/month</option>
                      </select>
                    </div>
                    <div class="col-sm-3 d-flex align-items-end">
                      <button type="submit" class="btn btn-primary">Upgrade</button>
                    </div>
                  </form>
                </div>
                <hr class="mx-4 my-2"/>
                <div class="modal-body p-4">
                  <p class="mb-0">User current plan is standard plan</p>
                  <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="d-flex justify-content-center me-2 mt-1">
                      <sup class="h6 pricing-currency pt-1 mt-2 mb-0 me-1 text-primary">$</sup>
                      <h1 class="mb-0 text-primary">99</h1>
                      <sub class="pricing-duration mt-auto mb-5 pb-1 small text-body">/month</sub>
                    </div>
                    <button class="btn btn-label-danger cancel-subscription">Cancel Subscription
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!--/ Add New Credit Card Modal -->
          <!-- /Modal -->
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

<script>
  $(document).ready(function () {

    //Form Validation
    const supplierEditForm = document.getElementById('supplierEditForm');

    // Add New supplier Form Validation
    const fv = FormValidation.formValidation(supplierEditForm, {
      fields: {
        supplierName: {
          validators: {
            notEmpty: {
              message: 'Please enter supplier or company name '
            }
          }
        },
        supplierNameAR: {
          validators: {
            notEmpty: {
              message: 'Please enter supplier or company name in arabic '
            }
          }
        },
        supplierEmail: {
          validators: {
            notEmpty: {
              message: 'Please enter your email'
            },
            emailAddress: {
              message: 'The value is not a valid email address'
            }
          }
        },
        supplierCountry: {
          validators: {
            notEmpty: {
              message: 'Please select the Country'
            }
          }
        },
        supplierState: {
          validators: {
            notEmpty: {
              message: 'Please select the State'
            }
          }
        },
        supplierCity: {
          validators: {
            notEmpty: {
              message: 'Please select the City'
            }
          }
        },
        supplierSalesPaymentTerm: {
          validators: {
            notEmpty: {
              message: 'Please select the Sales Payment Terms'
            }
          }
        },
        supplierPurchasePaymentTerm: {
          validators: {
            notEmpty: {
              message: 'Please select the Purchase Payment Terms'
            }
          }
        },
        supplierCurrency: {
          validators: {
            notEmpty: {
              message: 'Please select the Currency'
            }
          }
        },
        supplierContact: {
          validators: {
            notEmpty: {
              message: 'Please enter phone number'
            },
            regexp: {
              regexp: /^\+?[0-9\s]+$/,
              message: 'The phone number can only contain numbers, spaces, and an optional "+"'
            }
          }
        }
      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({
          // Use this for enabling/changing valid/invalid class
          eleValidClass: '',
          rowSelector: function (field, ele) {
            // field is the field name & ele is the field element
            return '.mb-6';
          }
        }),
        submitButton: new FormValidation.plugins.SubmitButton(),
        // Submit the form when all fields are valid
        // defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
        autoFocus: new FormValidation.plugins.AutoFocus()
      }
    });

    $("#supplierEditForm").submit(function (e) {
      e.preventDefault();

      fv.validate().then(function (status) {
        if (status === 'Valid') {

          blockArea($('.form-block'));

          var form = $('form')[0]; // You need to use standard javascript object here
          var formData = new FormData(form);

          $.ajax({
            url: '/ajax/supplier/update_supplier.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (data, status) {
              console.log(data);
              console.log(status);
              unBlockArea($('.form-block'));
              var statusmessage = data.trim().split("|")[0];
              var message = data.trim().split("|")[1];

              if (statusmessage == "SUCCESS") {
                // window.location = "/dashboard";
                Swal.fire({
                  title: 'Success!',
                  icon: 'success',
                  text: message,
                  type: 'success',
                  customClass: {
                    confirmButton: 'btn btn-primary'
                  },
                  showClass: {
                    popup: 'animate__animated animate__bounce'
                  },
                  buttonsStyling: false
                }).then(
                  function (result) {
                    $('#editSupplierModal').modal('hide');
                    blockArea($('body'));
                    location.reload();
                  }
                );
              }

              if (statusmessage == "ERROR") {

                Swal.fire({
                  title: 'Oops!',
                  icon: 'error',
                  text: message,
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
            },

            error: function (error) {

              console.log(error);
            }
          });


        }

      });


      return false;
    });
  });
</script>
<style>
    #editSupplierModal {
        /*width: 400px !important;*/
    }
</style>
</body>
</html>