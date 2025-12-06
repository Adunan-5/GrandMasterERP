<?php
$PAGE_ID = "BRAND_VIEW";
include_once __DIR__ . "/../../includes/baseIncludes.php";
include_once __DIR__ . "/../../includes/auth_check.php";

$brandID = "";

$brandID = filter_input(INPUT_GET, 'brandID', FILTER_VALIDATE_INT);

if ($brandID === null || $brandID === false || filter_var($brandID, FILTER_VALIDATE_INT) === false) {
    header("location:/brands/list");
    exit();
}


?>
<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="../../assets/" data-template="vertical-menu-template" data-style="light">
<head>
    <?php include_once __DIR__ . "/../../includes/dashboard/dashboard_head_section.php"; ?>
    <title>GrandMaster ERP | Brands</title>
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
                    $brand = new Brand($db);
                    $brand->loadById($brandID);
                    ?>
                    <div class="d-flex flex-column flex-sm-row align-items-center justify-content-sm-between mb-6 text-center text-sm-start gap-2">
                        <div class="mb-2 mb-sm-0">
                            <h4 class="mb-1">Brand ID #<?= $brand->brandId ?></h4>
                        </div>
                        <!--                        <button type="button" class="btn btn-label-danger delete-customer">Delete Customer</button>-->
                    </div>
                    <div class="row">
                        <!-- Customer-detail Sidebar -->
                        <div class="col-6 col-lg-5 col-md-5 order-1 order-md-0">
                            <!-- Customer-detail Card -->
                            <div class="card mb-6">
                                <div class="card-body pt-12">
                                    <div class="customer-avatar-section">
                                        <div class="d-flex align-items-center flex-column">
                                            <?php 
                                            $defaultImage = '/uploads/brand-images/no-image-available-placeholder.png'; // Path to the default image
                                            $imagePath = !empty($brand->image) ? '/uploads/brand-images/' . htmlspecialchars($brand->image) : $defaultImage;
                                            ?>
                                            <img class="img-fluid rounded mb-4" src="<?= $imagePath ?>" height="120" width="120" alt="Brand Image"/>
                                            <div class="customer-info text-center mb-6">
                                                <h5 class="mb-0">Brand Name: <?= $brand->brandName ?></h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="info-container">
                                        <div class="d-flex justify-content-center">
                                            <a href="javascript:;" class="btn btn-primary w-100" data-bs-target="#editBrandModal" data-bs-toggle="modal">Edit
                                                Details</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /Plan Card -->
                        </div>
                        <!--/ Customer Content -->
                    </div>
                    <!-- Modal -->
                    <!-- Edit Customer Modal -->
                    <div class="modal fade" id="editBrandModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-simple modal-edit-user">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    <div class="text-center mb-6">
                                        <h4 class="mb-2">Modify Brand Information</h4>
                                        <!--                                        <p>Updating user details will receive a privacy audit.</p>-->
                                    </div>
                                    <form class="ecommerce-brand-add pt-0" id="brandEditForm" method="POST" name="brandEditForm" enctype="multipart/form-data" action="#" onsubmit="return false;">
                                        <!--HIDDEN FIELDS-->
                                        <input type="hidden" id="brandId" name="brandId" value="<?= $brand->brandId ?>"/>
                                        <!--HIDDEN FIELDS END-->
                                        <div class="ecommerce-brand-add-basic mb-4">
                                            <div class="row">
                                                <div class="mb-6  col-6">
                                                    <label class="form-label" for="brandName">Brand Name*</label>
                                                    <input type="text" class="form-control" id="brandName" placeholder="Brand Name" name="brandName" aria-label="Mohammed Faisal" value="<?= $brand->brandName ?>"/>
                                                </div>
                                                <div class="mb-6 col-6">
                                                    <label class="form-label" for="brandImage">Image</label>
                                                    <input type="file" id="brandImage" class="form-control" name="image" accept="image/*" />
                                                    <!-- Display existing image URL as a clickable link if available, and not the default placeholder image -->
                                                    <?php if (!empty($brand->image) && $brand->image !== 'no-image-available-placeholder.png') : ?>
                                                        <div class="mb-2">
                                                            <a href="/uploads/brand-images/<?= htmlspecialchars($brand->image) ?>" target="_blank">
                                                                View Brand Image
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>
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
    // Form Validation
    const brandEditForm = document.getElementById('brandEditForm');

    // Add New customer Form Validation
    const fv = FormValidation.formValidation(brandEditForm, {
        fields: {
            brandName: {
                validators: {
                    notEmpty: {
                        message: 'Please enter brand name '
                    }
                }
            }
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


        $("#brandEditForm").submit(function (e) {
            e.preventDefault();

            fv.validate().then(function (status) {
                if (status === 'Valid') {

                    blockArea($('.form-block'));

                    var form = $('form')[0]; // You need to use standard javascript object here
                    var formData = new FormData(form);

                    $.ajax({
                        url: '/ajax/brands/update_brand.php',
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
                                        $('#editBrandModal').modal('hide');
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
    #editBrandModal {
        /*width: 400px !important;*/
    }
</style>
</body>
</html>