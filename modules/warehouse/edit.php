<?php
$PAGE_ID = "WAREHOUSE_VIEW";
include_once __DIR__ . "/../../includes/baseIncludes.php";
include_once __DIR__ . "/../../includes/auth_check.php";

$warehouseID = "";

$warehouseID = filter_input(INPUT_GET, 'warehouseID', FILTER_VALIDATE_INT);

if ($warehouseID === null || $warehouseID === false || filter_var($warehouseID, FILTER_VALIDATE_INT) === false) {
    header("location:/warehouses/list");
    exit();
}
?>
<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="../../assets/" data-template="vertical-menu-template" data-style="light">
<head>
    <?php include_once __DIR__ . "/../../includes/dashboard/dashboard_head_section.php"; ?>
    <title>GrandMaster ERP | Warehouse</title>
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
                    $warehouse = new Warehouse($db);
                    $warehouse->loadById($warehouseID);
                    ?>
                    <div class="d-flex flex-column flex-sm-row align-items-center justify-content-sm-between mb-6 text-center text-sm-start gap-2">
                        <div class="mb-2 mb-sm-0">
                            <h4 class="mb-1">Warehouse Details</h4>
                        </div>
                    </div>
                    <form class="ecommerce-warehouse-add pt-0" id="warehouseEditForm" method="POST" name="warehouseEditForm" action="#" onsubmit="return false;">
                        <!-- HIDDEN FIELDS -->
                        <input type="hidden" id="warehouseId" name="warehouseId" value="<?= $warehouse->warehouseId ?>"/>
                        <!-- HIDDEN FIELDS END -->

                        <div class="ecommerce-warehouse-add-basic mb-4">
                            <div class="row">
                                <div class="mb-6 col-6">
                                    <label class="form-label" for="warehouseName">Warehouse Name*</label>
                                    <input type="text" class="form-control" id="warehouseName" placeholder="Warehouse Name" name="warehouseName" value="<?= $warehouse->warehouseName ?>"/>
                                </div>
                                <div class="mb-6 col-6">
                                    <label class="form-label" for="warehouseCode">Warehouse Code</label>
                                    <input type="text" class="form-control" id="warehouseCode" placeholder="Warehouse Code" name="warehouseCode" value="<?= $warehouse->warehouseCode ?>"/>
                                </div>
<!--                              <div class="ecommerce-supplier-add-shiping mb-6">-->
                                <h6 class="mb-6">Address Information</h6>
                                <div class="mb-6 col-6">
                                  <label class="form-label" for="warehouseAddress1">Address Line 1</label>
                                  <input type="text" id="warehouseAddress1" class="form-control" placeholder="45 Roker Terrace" aria-label="45 Roker Terrace" name="warehouseAddress1" value="<?= $warehouse->addressLine1 ?>"/>
                                </div>
                                <div class="mb-6 col-6">
                                  <label class="form-label" for="warehouseAddress2">Address Line 2</label>
                                  <input type="text" id="warehouseAddress2" class="form-control" aria-label="address2" name="warehouseAddress2" value="<?= $warehouse->addressLine2 ?>"/>
                                </div>
<!--                                <div class="row">-->
                                  <div class=" mb-6 col-6">
                                    <label class="form-label" for="warehouseCountry">Country*</label>
                                    <select id="warehouseCountry" name="warehouseCountry" class="selectpicker form-select w-100" data-style="btn-default" data-live-search="true" onchange="populateStateForSelectedCountry(this.value, 'warehouseState', 'warehouseCity')">
                                      <option value="">Select a Country</option>
                                        <?php
                                        $res = $db->query("SELECT * FROM countries");
                                        while ($row = mysqli_fetch_assoc($res)) {
                                            ?>
                                          <option value="<?= $row['id'] ?>" <?php if ($row['id'] == $warehouse->countryId) echo "selected" ?> ><?= $row['name'] ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                  </div>
                                  <div class=" mb-6 col-6">
                                    <label class="form-label" for="warehouseState">State / Province*
                                    </label>
                                    <select id="warehouseState" name="warehouseState" class="selectpicker w-100" data-style="btn-default" data-live-search="true" onchange="populateCityForSelectedState(this.value, 'warehouseCity')">
                                        <?php
                                        $res = $db->query("SELECT * FROM states WHERE country_id = ?s", $warehouse->countryId);
                                        if ($res) {
                                            ?>
                                          <option value="">Select a State</option> <?php
                                        } else {
                                            ?>
                                          <option value="">Select the Country first</option> <?php
                                        }

                                        while ($row = mysqli_fetch_assoc($res)) {
                                            ?>
                                          <option value="<?= $row['id'] ?>" <?php if ($row['id'] == $warehouse->stateId) echo "selected" ?> ><?= $row['name'] ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                  </div>
                                  <div class="mb-6 col-6">
                                    <label class="form-label" for="warehouseCity">City*</label>
                                    <select id="warehouseCity" name="warehouseCity" class="selectpicker w-100" data-style="btn-default" data-live-search="true">
                                        <?php
                                        $res = $db->query("SELECT * FROM cities WHERE state_id = ?s", $warehouse->stateId);
                                        if ($res) {
                                            ?>
                                          <option value="">Select a City</option> <?php
                                        } else {
                                            ?>
                                          <option value="">Select the State first</option> <?php
                                        }

                                        while ($row = mysqli_fetch_assoc($res)) {
                                            ?>
                                          <option value="<?= $row['id'] ?>" <?php if ($row['id'] == $warehouse->cityId) echo "selected" ?> ><?= $row['name'] ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                  </div>
                                  <div class="col-6 mb-6">
                                    <label class="form-label" for="warehousePostalCode">Postal Code
                                    </label>
                                    <input type="text" id="warehousePostalCode" class="form-control" placeholder="734990" aria-label="734990" name="warehousePostalCode" maxlength="8" value="<?= $warehouse->postalCode ?>"/>
                                  </div>
                                  <div class="col-6 mb-6">
                                    <label class="form-label" for="warehouseEmail">Email</label>
                                    <input type="text" id="warehouseEmail" class="form-control" placeholder="warehouse@ggm.com.co" aria-label="warehouse@ggm.com.co" name="warehouseEmail" value="<?= $warehouse->email ?>"/>
                                  </div>
                                  <div class="col-6 mb-6">
                                    <label class="form-label" for="ecommerce-warehouse-add-contact">Mobile
                                    </label>
                                    <input type="text" id="ecommerce-warehouse-add-contact" class="form-control phone-mask" placeholder="+(123) 456-7890" aria-label="+(123) 456-7890" name="warehouseContact" value="<?= $warehouse->phone ?>"/>
                                  </div>
<!--                                </div>-->
<!--                              </div>-->
                            </div>
                        </div>

                        <div>
                            <button class="btn btn-primary me-sm-4 data-submit">Update</button>
                            <button type="reset" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal" aria-label="Close" onclick="window.location.href='/warehouse/list'">Discard</button>
                        </div>
                    </form>
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
    // Form Validation
    const warehouseEditForm = document.getElementById('warehouseEditForm');

    // Add New warehouse Form Validation
    const fv = FormValidation.formValidation(warehouseEditForm, {
        fields: {
            warehouseName: {
                validators: {
                    notEmpty: {
                        message: 'Please enter the warehouse name'
                    }
                }
            },
          warehouseCountry: {
            validators: {
              notEmpty: {
                message: 'Please select the Country'
              }
            }
          },
          warehouseState: {
            validators: {
              notEmpty: {
                message: 'Please select the State'
              }
            }
          },
          warehouseCity: {
            validators: {
              notEmpty: {
                message: 'Please select the City'
              }
            }
          },
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

    // Submit the form via AJAX
    $("#warehouseEditForm").submit(function (e) {
        e.preventDefault();

        fv.validate().then(function (status) {
            if (status === 'Valid') {
                blockArea($('.form-block'));

                var form = $('#warehouseEditForm')[0]; // Correct form reference
                var formData = new FormData(form);

                $.ajax({
                    url: '/ajax/warehouses/update_warehouse.php',
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
                            Swal.fire({
                                title: 'Success!',
                                icon: 'success',
                                text: message,
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                },
                                showClass: {
                                    popup: 'animate__animated animate__bounce'
                                },
                                buttonsStyling: false
                            }).then(function () {
                                window.location.href = '/warehouse/list';
                            });
                        }

                        if (statusmessage == "ERROR") {
                            Swal.fire({
                                title: 'Oops!',
                                icon: 'error',
                                text: message,
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

</body>
</html>
