<?php
$PAGE_ID = "MACHINE_VIEW";
include_once __DIR__ . "/../../includes/baseIncludes.php";
include_once __DIR__ . "/../../includes/auth_check.php";

$machineID = "";

$machineID = filter_input(INPUT_GET, 'machineID', FILTER_VALIDATE_INT);

if ($machineID === null || $machineID === false || filter_var($machineID, FILTER_VALIDATE_INT) === false) {
    header("location:/machines/list");
    exit();
}


?>
<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="../../assets/" data-template="vertical-menu-template" data-style="light">
<head>
    <?php include_once __DIR__ . "/../../includes/dashboard/dashboard_head_section.php"; ?>
    <title>GrandMaster ERP | Machines</title>
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
                    $sparepart = new Sparepart($db);
                    $sparepart->loadById($machineID);
                    ?>
                    <div class="d-flex flex-column flex-sm-row align-items-center justify-content-sm-between mb-6 text-center text-sm-start gap-2">
                        <div class="mb-2 mb-sm-0">
                            <h4 class="mb-1">Serial Number #<?= $sparepart->serialNumber ?></h4>
                        </div>
                        <!--                        <button type="button" class="btn btn-label-danger delete-sparepart">Delete sparepart</button>-->
                    </div>
                    <div class="row">
                        <!-- sparepart-detail Sidebar -->
                        <div class="col-12 col-lg-6 col-md-6 order-1 order-md-0">
                            <!-- sparepart-detail Card -->
                            <div class="card mt-6">
                                <div class="info-container m-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <!-- Left side: History button -->
                                        <div>
                                        <a href="javascript:;" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#showHistoryOffcanvas">
                                            History
                                        </a>
                                        </div>
                                        <!-- Right side: Edit/Create buttons -->
                                        <div>
                                        <a href="javascript:;" class="btn btn-primary me-2" data-bs-target="#editMachineModal" data-bs-toggle="modal">
                                            Edit Details
                                        </a>
                                        <a href="javascript:;" class="btn btn-primary" data-bs-target="#updateQuantityModal" data-bs-toggle="modal">
                                            Update Quantity
                                        </a>
                                        </div>
                                    </div>
                                    </div>
                                <div class="card-body pt-0">
                                    <div class="sparepart-avatar-section">
                                        <div class="d-flex align-items-center flex-column">
                                            <?php
                                            $sparePartImage = NO_IMAGE_SPARE_PART;
                                            if (!empty($sparepart->image))
                                                $sparePartImage = $sparepart->image;
                                            ?>
                                            <img class="img-fluid rounded mb-4" src="/uploads/sparepart-images/<?= $sparePartImage ?>" height="120" width="120" alt="Spare Part Image"/>
                                            <div class="sparepart-info text-center mb-6">
                                                <table class="table">
                                                    <tbody>
                                                    <tr>
                                                        <td class="text-end">Serial Number:</td>
                                                        <td class="text-start"><?= htmlspecialchars($sparepart->serialNumber) ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-end">Internal Reference:</td>
                                                        <td class="text-start"><?= htmlspecialchars($sparepart->internalReference) ?></td>
                                                    </tr>
                                                    </tbody>
                                                    <tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row text-left mb-3">
                                        <div class="col-6">
                                            <span>Description: </span><br>
                                            <strong><?= $sparepart->description ?></strong>
                                        </div>
                                        <div class="col-6">
                                            <span>Machine Name: </span><br><strong><?= $sparepart->name ?></strong>
                                        </div>
                                    </div>
                                    <div class="row text-left mb-3">
                                        <div class="col-6">
                                            <span>Brand:</span><br><strong><?= $sparepart->brandName ?></strong>
                                        </div>
                                        <div class="col-6">
                                            <span>Lead Time: </span><br> <strong><?= $sparepart->leadTime ?></strong>
                                        </div>
                                    </div>
                                    <div class="row text-left mb-3">
                                        <div class="col-6">
                                            <span>Sale Price: </span> <br>
                                            <strong><?= $sparepart->salesPrice ?></strong>
                                        </div>
                                        <div class="col-6">
                                            <span>Cost: </span><br> <strong><?= $sparepart->cost ?></strong>
                                        </div>
                                    </div>
                                    <div class="row text-left mb-3">
                                        <div class="col-6">
                                            <?php
                                            $margin = null;
                                            if (!empty($sparepart->salesPrice) && $sparepart->salesPrice > 0) {
                                                $margin = round((($sparepart->salesPrice - $sparepart->cost) / $sparepart->salesPrice) * 100, 2);
                                            }
                                            ?>
                                            <span>Margin %:</span><br>
                                            <strong><?= $margin !== null ? $margin . '%' : 'N/A' ?></strong>
                                        </div>
                                    </div>
                                    <div class="row text-left mb-3">
                                        <div class="col-6">
                                            <span>Origin:</span><br><strong><?= $sparepart->origin ?></strong>
                                        </div>
                                        <div class="col-6">
                                            <span>UOM:</span><br><strong><?= $sparepart->uomName ?></strong>
                                        </div>
                                    </div>
                                    <div class="row text-left mb-3">
                                        <div class="col-6">
                                            <span>HS Code:</span><br><strong><?= $sparepart->hsCode ?></strong>
                                        </div>
                                        <div class="col-6">
                                            <span>HS Percentage:</span><br><strong><?= $sparepart->hsPercentage ?></strong>
                                        </div>
                                    </div>
                                    <div class="row text-left mb-3">
                                        <div class="col-6">
                                            <span>Notes:</span><br><strong><?= $sparepart->notes ?></strong>
                                        </div>
                                        <div class="col-6">
                                            <span>Published:</span><br><strong><?= $sparepart->publishedOnEcommerce == 1 ? 'Yes' : 'No' ?></strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /Plan Card -->
                        </div>
                        <!--/ sparepart Content -->
                        <div class="col-12 col-lg-6 col-md-6 order-1 order-md-0">
                            <div class="card mt-6">
                                <div class="card-body">
                                    <h4>Stock</h4>
                                    <?php
                                    $hasStock = false;

                                    $res        = $db->query("SELECT sum(quantity) as totalQuantity FROM inventory_stock WHERE sparepartId = ?s", $machineID);
                                    $totalStock = $row = mysqli_fetch_assoc($res)['totalQuantity'];
                                    if ($totalStock > 0) $hasStock = true;
                                    ?>
                                    Total On Hand: <?=$totalStock ?>



                                    <?php if ($hasStock == false) {
                                        ?>
                                        <p>No Stock available</p>
                                        <?php
                                    }else{
                                        ?>
                                        <table class="table mt-4" >
                                            <thead >
                                            <tr>
                                                <td>
                                                    <strong>Warehouse</strong>
                                                </td>
                                                <td>
                                                    <strong>Quantity</strong>
                                                </td>
                                              <td>
                                                <strong>Aisle</strong>
                                              </td>
                                              <td>
                                                <strong>Bin</strong>
                                              </td>
                                              <td>
                                                <strong>Lot#</strong>
                                              </td>
                                              <td>
                                                <strong>Last Updated</strong>
                                              </td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php


                                            $warehouseRes = $db->query("SELECT * FROM warehouses WHERE active = 1");

                                            while($warehouseRow=mysqli_fetch_assoc($warehouseRes))
                                            {

                                                $stockRes = $db->query("SELECT * FROM inventory_stock WHERE sparepartId = ?s and warehouseId = ?s", $machineID, $warehouseRow['warehouseId']);

                                                while($stockRow=mysqli_fetch_assoc($stockRes))
                                                {
                                                	?>
                                                    <tr>
                                                        <td><?=$warehouseRow['warehouseCode'] ?></td>
                                                        <td><?=$stockRow['quantity'] ?></td>
                                                      <td><?=$stockRow['aisle'] ?></td>
                                                      <td><?=$stockRow['bin'] ?></td>
                                                      <td><?=$stockRow['lotSerial'] ?></td>
                                                      <td><?=formatDate($stockRow['lastStockUpdate']) ?></td>
                                                    </tr>
                                                    <?php
                                                }
                                            	?>

                                                <?php
                                            }


                                            ?>


                                            </tbody>

                                        </table>

                                        <?php
                                    } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal -->
                    <!-- Edit Machine Modal -->
                    <div class="modal fade" id="editMachineModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-simple modal-edit-user">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    <div class="text-center mb-6">
                                        <h4 class="mb-2">Modify Machine Information</h4>
                                        <!--                                        <p>Updating user details will receive a privacy audit.</p>-->
                                    </div>
                                    <form class="ecommerce-sparepart-add pt-0" id="machineEditForm" method="POST" name="machineEditForm" enctype="multipart/form-data" action="#" onsubmit="return false;">
                                        <!--HIDDEN FIELDS-->
                                        <input type="hidden" id="sparepartId" name="sparepartId" value="<?= $sparepart->sparepartId ?>"/>
                                        <input type="hidden" name="itemType" value="MACHINE">
                                        <!--HIDDEN FIELDS END-->
                                        <div class="ecommerce-sparepart-add-basic mb-4">
                                            <div class="row">
                                                <div class="mb-6  col-6">
                                                    <label class="form-label" for="partNumber">Serial Number*</label>
                                                    <input type="text" class="form-control" id="partNumber" placeholder="Machine Serial Number" name="partNumber" aria-label="Mohammed Faisal" value="<?= $sparepart->serialNumber ?>"/>
                                                </div>
                                                <div class="mb-6 col-6">
                                                    <label class="form-label" for="internalReference">Internal
                                                        Reference*
                                                    </label>
                                                    <input type="text" class="form-control" id="internalReference" placeholder="محمد فيصل" name="internalReference" aria-label="محمد فيصل" value="<?= $sparepart->internalReference ?>"/>
                                                </div>
                                                <div class="mb-6 col-6">
                                                    <label class="form-label" for="name">Name</label>
                                                    <input type="text" id="name" class="form-control" placeholder="123456789123456" aria-label="123456789123456" name="name" value="<?= $sparepart->name ?>"/>
                                                </div>
                                                <div class="mb-6 col-6">
                                                    <label class="form-label" for="description">Description</label>
                                                    <input type="text" id="description" class="form-control" placeholder="123456789123456" aria-label="123456789123456" name="description" value="<?= $sparepart->description ?>"/>
                                                </div>
                                                <div class=" mb-6 col-6">
                                                    <label class="form-label" for="sparepartBrand">Brand*</label>
                                                    <select id="sparepartBrand" name="sparepartBrand" class="selectpicker form-select w-100" data-style="btn-default" data-live-search="true">
                                                        <option value="">Select a Brand</option>
                                                        <?php
                                                        $res = $db->query("SELECT * FROM brands");
                                                        while ($row = mysqli_fetch_assoc($res)) {
                                                            ?>
                                                            <option value="<?= $row['brandId'] ?>" <?php if ($row['brandId'] == $sparepart->brandId) echo "selected" ?> ><?= $row['brandName'] ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="mb-6 col-6">
                                                    <label class="form-label" for="sparepartImage">Image</label>
                                                    <input type="file" id="sparepartImage" class="form-control" name="image" accept="image/*"/>
                                                    <!-- Check if the image exists and is not one of the default images -->
                                                    <?php
                                                    $sparePartImage = NO_IMAGE_SPARE_PART;
                                                    if (!empty($sparepart->image))
                                                        $sparePartImage = $sparepart->image;
                                                    ?>
                                                    <div class="mb-2">
                                                        <a href="/uploads/sparepart-images/<?= $sparePartImage ?>" target="_blank">
                                                            View Machine Image </a>
                                                    </div>
                                                </div>
                                                <div class="mb-6 col-6">
                                                    <label class="form-label" for="leadTime">
                                                        Lead Time
                                                    </label>
                                                    <input type="text" id="leadTime" class="form-control phone-mask" placeholder="5" name="leadTime" value="<?= $sparepart->leadTime ?>"/>
                                                </div>
                                                <div class="mb-6 col-6">
                                                    <label class="form-label" for="origin">
                                                        Origin
                                                    </label>
                                                    <input type="text" id="origin" class="form-control phone-mask" placeholder="Country of Origin" name="origin" value="<?= $sparepart->origin ?>"/>
                                                </div>
                                                <div class="mb-6 col-6">
                                                    <label class="form-label" for="salesPrice">
                                                        Sales Price*
                                                    </label>
                                                    <input type="text" id="salesPrice" class="form-control phone-mask" placeholder="0" name="salesPrice" value="<?= $sparepart->salesPrice ?>"/>
                                                </div>
                                                <div class=" mb-6 col-6">
                                                    <label class="form-label" for="uomId">UOM*</label>
                                                    <select id="uomId" name="uomId" class="selectpicker form-select w-100" data-style="btn-default" data-live-search="true">
                                                        <option value="">Select an UOM</option>
                                                        <?php
                                                        $res = $db->query("SELECT * FROM uoms");
                                                        while ($row = mysqli_fetch_assoc($res)) {
                                                            ?>
                                                            <option value="<?= $row['uomId'] ?>" <?php if ($row['uomId'] == $sparepart->uomId) echo "selected" ?> ><?= $row['uomName'] ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="mb-6 col-6">
                                                    <label class="form-label" for="cost">
                                                        Cost*
                                                    </label>
                                                    <input type="text" id="cost" class="form-control phone-mask" placeholder="0" name="cost" value="<?= $sparepart->cost ?>"/>
                                                </div>
                                                <div class="mb-6 col-6">
                                                    <label class="form-label" for="notes">
                                                        Notes
                                                    </label>
                                                    <input type="text" id="notes" class="form-control phone-mask" placeholder="Additional Note" name="notes" value="<?= $sparepart->notes ?>"/>
                                                </div>
                                                <div class="mb-6 col-6">
                                                    <label class="form-label" for="hsCode">
                                                        HS Code
                                                    </label>
                                                    <input type="text" id="hsCode" class="form-control phone-mask" placeholder="HS Code" name="hsCode" value="<?= $sparepart->hsCode ?>"/>
                                                </div>
                                                <div class="mb-6 col-6">
                                                    <label class="form-label" for="hsPercentage">
                                                        HS Percentage
                                                    </label>
                                                    <input type="text" id="hsPercentage" class="form-control phone-mask" placeholder="%" name="hsPercentage" value="<?= $sparepart->hsPercentage ?>"/>
                                                </div>
                                                <div class=" mb-6 col-6">
                                                    <label class="form-label" for="published">Published on E-commerce
                                                    </label>
                                                    <select id="published" name="published" class="selectpicker form-select w-100" data-style="btn-default" data-live-search="true">
                                                        <option value="">Select an UOM</option>
                                                        <option value="1" <?php echo ($sparepart->publishedOnEcommerce == 1) ? 'selected' : ''; ?>>
                                                            Yes
                                                        </option>
                                                        <option value="0" <?php echo ($sparepart->publishedOnEcommerce == 0) ? 'selected' : ''; ?>>
                                                            No
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <?php if (is_ceo() || has_permission('itemsOrProducts', 'edit')) { ?>
                                            <button class="btn btn-primary me-sm-4 data-submit">Update</button>
                                            <?php } ?>
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
                    <!-- Update Quantity Modal -->
                    <div class="modal fade" id="updateQuantityModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Update Quantity</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-6 mb-4">
                                            <label for="newQuantityInput" class="form-label">New quantity on hand
                                            </label>
                                            <input type="text" id="newQuantityInput" class="form-control" placeholder="0"/>
                                        </div>
                                        <div class="col-6 mb-4">
                                            <label for="warehouseSelect" class="form-label">Warehouse</label>
                                            <select name="warehouseSelect" id="warehouseSelect" class="select2 form-select" data-allow-clear="true">
                                                <option value="">Select warehouse</option>
                                                <?php
                                                $res = $db->query("select * From warehouses WHERE active =1");

                                                while ($row = mysqli_fetch_assoc($res)) {
                                                    ?>
                                                    <option value="<?= $row["warehouseId"] ?>"><?= $row['warehouseCode'] . " | " . $row['warehouseName'] ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                  <div class="row">
                                    <div class="col-6 mb-4">
                                      <label for="aisle" class="form-label">Aisle
                                      </label>
                                      <input type="text" id="aisle" class="form-control" placeholder="Aisle"/>
                                    </div>
                                    <div class="col-6 mb-4">
                                      <label for="bin" class="form-label">Bin
                                      </label>
                                      <input type="text" id="bin" class="form-control" placeholder="Bin"/>
                                    </div>
                                    <div class="col-6 mb-4">
                                      <label for="lotSerial" class="form-label">Lot#
                                      </label>
                                      <input type="text" id="lotSerial" class="form-control" placeholder="Lot #"/>
                                    </div>
                                  </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                                        Close
                                    </button>
                                    <button type="button" id="updateNewQuantityButton" class="btn btn-primary">Update
                                        Quantity
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--/ Update Quantity Modal -->
                    <!-- /Modal -->
                </div>

                <!-- Offcanvas -->
                    <!--Sparepart History Canvas starts-->
                    <div class="offcanvas offcanvas-end" id="showHistoryOffcanvas" aria-hidden="true">
                        <div class="offcanvas-header mb-6 border-bottom">
                            <h5 class="offcanvas-title">History</h5>
                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body pt-0 flex-grow-1">
                            <!-- Timeline Basic-->
                            <div class="mt-5">
                                <ul class="timeline mb-0">
                                    <?php

                                    $sparepartHistory = new SparepartHistory();
                                    $sparepartHistory->loadBySparepartId($machineID);

                                    foreach ($sparepartHistory->timeline as $historyItem) {
                                        ?>
                                        <li class="timeline-item timeline-item-transparent">
                                          <span class="timeline-point timeline-point-<?= $historyItem->stickerPottu ?>"></span>
                                            <div class="timeline-event">
                                                <div class="timeline-header mb-3">
                                                    <h6 class="mb-0"><?= $historyItem->title ?></h6>
                                                    <small class="text-muted"><?= $historyItem->updatedAt ?></small>
                                                </div>
                                              <div class="d-flex justify-content-between flex-wrap gap-2 mb-2">
                                                <div class="d-flex flex-wrap align-items-center mb-50">
                                                  <div class="avatar avatar-sm me-2">
                                                      <?php if ($historyItem->updatedBy > 0) { ?>
                                                        <img src="<?= getProfilePicFromUserID($historyItem->updatedBy) ?>" alt="Avatar" class="rounded-circle">
                                                      <?php } ?>
                                                  </div>
                                                  <div>
                                                    <p class="mb-0 small fw-medium"><?= getDisplayNameFromUserID($historyItem->updatedBy) ?></p>
                                                  </div>
                                                </div>
                                              </div>
                                                <?php if (!empty($historyItem->remark)) {
                                                    // Check if the title contains 'rejected'
                                                    $remarkClass = 'bg-info'; // Default class
                                                    if (strpos(strtolower($historyItem->title), 'rejected') !== false) {
                                                        $remarkClass = 'bg-danger text-white'; // Apply danger class if title contains 'rejected'
                                                    }
                                                    ?>
                                                  <p class="badge <?= $remarkClass ?> rounded d-flex align-items-center">
                                                      <?= nl2br(htmlspecialchars($historyItem->remark)) ?>
                                                  </p>
                                                <?php } ?>
                                            </div>
                                        </li>
                                        <?php
                                    }

                                    ?>

                                    <?php

                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!--Sparepart History Canvas ENDS-->
                    <!-- Offcanvas Ends -->
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
        // Form Validation
        const machineEditForm = document.getElementById('machineEditForm');

        // Add New sparepart Form Validation
        const fv = FormValidation.formValidation(machineEditForm, {
            fields: {
                partNumber: {
                    validators: {
                        notEmpty: {
                            message: 'Please enter Serial number '
                        }
                    }
                },
                internalReference: {
                    validators: {
                        notEmpty: {
                            message: 'Please enter internal reference '
                        }
                    }
                },
                sparepartBrand: {
                    validators: {
                        notEmpty: {
                            message: 'Please choose brand '
                        }
                    }
                },
                salesPrice: {
                    validators: {
                        notEmpty: {
                            message: 'Please enter Sales Price'
                        },
                        numeric: {
                            message: 'Please enter a valid Sales Price',
                            decimalSeparator: '.'
                        }
                    }
                },
                uomId: {
                    validators: {
                        notEmpty: {
                            message: 'Please choose unit of measurement '
                        }
                    }
                },
                cost: {
                    validators: {
                        notEmpty: {
                            message: 'Please enter Cost Price'
                        },
                        numeric: {
                            message: 'Please enter a valid Cost',
                            decimalSeparator: '.'
                        }
                    }
                },
                // hsPercentage: {
                //     validators: {
                //         numeric: {
                //             message: 'Please enter a valid HS Percentage',
                //             decimalSeparator: '.'
                //         }
                //     }
                // },
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap5: new FormValidation.plugins.Bootstrap5({
                    eleValidClass: '',
                    rowSelector: function (field, ele) {
                        return '.mb-6';
                    }
                }),
                submitButton: new FormValidation.plugins.SubmitButton(),
                autoFocus: new FormValidation.plugins.AutoFocus()
            }
        });


        $("#machineEditForm").submit(function (e) {
            e.preventDefault();

            fv.validate().then(function (status) {
                if (status === 'Valid') {

                    blockArea($('.form-block'));

                    var form = $('form')[0]; // You need to use standard javascript object here
                    var formData = new FormData(form);

                    $.ajax({
                        url: '/ajax/spareparts/update_spare_part.php',
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
                                        $('#editMachineModal').modal('hide');
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

        $("#updateNewQuantityButton").click(function (e) {

            let newQuantity = $("#newQuantityInput").val();

            let selectedWarehouseID = $('#warehouseSelect').val();
            let aisle = $('#aisle').val();
            let bin = $('#bin').val();
            let lotSerial = $('#lotSerial').val();

            if (!selectedWarehouseID || selectedWarehouseID === '') {
                showErrorMessage("Warehouse must be selected.");
                return false; // Indicate validation failed
            }
            if (!aisle || aisle === '') {
              showErrorMessage("Please enter Aisle value.");
              return false; // Indicate validation failed
            }
            if (!bin || bin === '') {
              showErrorMessage("Please enter Bin value.");
              return false; // Indicate validation failed
            }
            if (!lotSerial || lotSerial === '') {
              showErrorMessage("Please enter Lot# value.");
              return false; // Indicate validation failed
            }

            if (newQuantity === null || newQuantity === "") {
                showErrorMessage("Quantity field cannot be empty");
            } else {
                const data = {
                    sparepartID: <?= json_encode($machineID); ?>,
                    newQuantity: newQuantity,
                    warehouseID: selectedWarehouseID,
                    aisle: aisle,
                    bin: bin,
                    lotSerial: lotSerial
                }

                //Ajax call
                $.ajax({
                    url: '/ajax/spareparts/updateSparepartQuantity.php',
                    type: 'POST',
                    dataType: 'json',
                    contentType: 'application/json',  // Important for JSON data
                    data: JSON.stringify(data),       // Convert to JSON string
                    success: function (response) {

                        if (response.status === "SUCCESS") {
                            showSuccessMessage("Quantity successfully updated", gotoPage, '/spareparts/edit/' + '<?=$machineID?>');

                        } else {
                            showErrorMessage('Something went wrong. Please try again' + response.message);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {

                        console.log(jqXHR.status + ' - ' + errorThrown);
                        showErrorMessage("Something went wrong. Please try again.");
                    }
                });
            }

        });

    });
</script>
<style>
    #editMachineModal {
        /*width: 400px !important;*/
    }

    #showHistoryOffcanvas {
        width: 650px !important;
    }
</style>
</body>
</html>