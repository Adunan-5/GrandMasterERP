<?php
$PAGE_ID = "DOCUMENT_SETTINGS_EDIT";
include_once __DIR__ . "/../../../includes/baseIncludes.php";
include_once __DIR__ . "/../../../includes/auth_check.php";

$documentID = "";

$documentID = filter_input(INPUT_GET, 'documentID', FILTER_VALIDATE_INT);

if ($documentID === null || $documentID === false || filter_var($documentID, FILTER_VALIDATE_INT) === false) {
    header("location:/dashboard");
    exit();
}


?>
<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="../../assets/" data-template="vertical-menu-template" data-style="light">
<head>
    <?php include_once __DIR__ . "/../../../includes/dashboard/dashboard_head_section.php"; ?>
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
    <?php include_once __DIR__ . "/../../../includes/dashboard/dashboard_head_section_post_vendor.php"; ?>
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
                <!-- Content -->
                <div class="container-xxl flex-grow-1 container-p-y">
                    <?php
                    $settingsDocument = new SettingsDocument($db);
                    $settingsDocument->loadById($documentID);
                    ?>
                    <form class="settings-document-form pt-0" id="settingsDocumentForm" method="POST" name="settingsDocumentForm" enctype="multipart/form-data" action="#" onsubmit="return false;">
                        <input type="hidden" id="documentSettingId" name="documentSettingId" value="<?= $settingsDocument->documentSettingId ?>"/>

                        <div class="settings-document-section mb-4">
                            <div class="row">
                                <div class="mb-6 col-6">
                                    <label class="form-label" for="titleFontSize">Title Font Size</label>
                                    <input type="number" class="form-control" id="titleFontSize" name="titleFontSize" value="<?= $settingsDocument->titleFontSize ?>"/>
                                </div>
                                <div class="mb-6 col-6">
                                    <label class="form-label" for="titleFontSize_ar">Title Font Size (AR)</label>
                                    <input type="number" class="form-control" id="titleFontSize_ar" name="titleFontSize_ar" value="<?= $settingsDocument->titleFontSize_ar ?>"/>
                                </div>
                                <div class="mb-6 col-6">
                                    <label class="form-label" for="titleFontFamily">Title Font Family</label>
                                    <select id="titleFontFamily" name="titleFontFamily" class="selectpicker form-select w-100" data-style="btn-default" data-live-search="true">
                                        <option value="">Select a Font Family</option>
                                        <?php
                                        $res = $db->query("SELECT * FROM document_fontFamily");
                                        while ($row = mysqli_fetch_assoc($res)) {
                                            ?>
                                            <option value="<?= $row['fontFamilyId'] ?>" <?php if ($row['fontFamilyId'] == $settingsDocument->titleFontFamilyId) echo "selected" ?> ><?= $row['fontFamilyName'] ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-6 col-6">
                                    <label class="form-label" for="titleFontFamily_ar">Title Font Family (AR)</label>
                                    <select id="titleFontFamily_ar" name="titleFontFamily_ar" class="selectpicker form-select w-100" data-style="btn-default" data-live-search="true">
                                        <option value="">Select a Font Family</option>
                                        <?php
                                        $res = $db->query("SELECT * FROM document_fontFamily");
                                        while ($row = mysqli_fetch_assoc($res)) {
                                            ?>
                                            <option value="<?= $row['fontFamilyId'] ?>" <?php if ($row['fontFamilyId'] == $settingsDocument->titleFontFamilyId_ar) echo "selected" ?> ><?= $row['fontFamilyName'] ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="mb-6 col-6">
                                    <label class="form-label" for="headerPrimaryFontSize">Header Primary Font Size</label>
                                    <input type="number" class="form-control" id="headerPrimaryFontSize" name="headerPrimaryFontSize" value="<?= $settingsDocument->headerPrimaryFontSize ?>"/>
                                </div>
                                <div class="mb-6 col-6">
                                    <label class="form-label" for="headerPrimaryFontSize_ar">Header Primary Font Size (AR)</label>
                                    <input type="number" class="form-control" id="headerPrimaryFontSize_ar" name="headerPrimaryFontSize_ar" value="<?= $settingsDocument->headerPrimaryFontSize_ar ?>"/>
                                </div>
                                <div class="mb-6 col-6">
                                    <label class="form-label" for="headerPrimaryFontFamily">Header Primary Font Family</label>
                                    <select id="headerPrimaryFontFamily" name="headerPrimaryFontFamily" class="selectpicker form-select w-100" data-style="btn-default" data-live-search="true">
                                        <option value="">Select a Font Family</option>
                                        <?php
                                        $res = $db->query("SELECT * FROM document_fontFamily");
                                        while ($row = mysqli_fetch_assoc($res)) {
                                            ?>
                                            <option value="<?= $row['fontFamilyId'] ?>" <?php if ($row['fontFamilyId'] == $settingsDocument->headerPrimaryFontFamilyId) echo "selected" ?> ><?= $row['fontFamilyName'] ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-6 col-6">
                                    <label class="form-label" for="headerPrimaryFontFamily_ar">Header Primary Font Family (AR)</label>
                                    <select id="headerPrimaryFontFamily_ar" name="headerPrimaryFontFamily_ar" class="selectpicker form-select w-100" data-style="btn-default" data-live-search="true">
                                        <option value="">Select a Font Family</option>
                                        <?php
                                        $res = $db->query("SELECT * FROM document_fontFamily");
                                        while ($row = mysqli_fetch_assoc($res)) {
                                            ?>
                                            <option value="<?= $row['fontFamilyId'] ?>" <?php if ($row['fontFamilyId'] == $settingsDocument->headerPrimaryFontFamilyId_ar) echo "selected" ?> ><?= $row['fontFamilyName'] ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="mb-6 col-6">
                                    <label class="form-label" for="headerSecondaryFontSize">Header Secondary Font Size</label>
                                    <input type="number" class="form-control" id="headerSecondaryFontSize" name="headerSecondaryFontSize" value="<?= $settingsDocument->headerSecondaryFontSize ?>"/>
                                </div>
                                <div class="mb-6 col-6">
                                    <label class="form-label" for="headerSecondaryFontSize_ar">Header Secondary Font Size (AR)</label>
                                    <input type="number" class="form-control" id="headerSecondaryFontSize_ar" name="headerSecondaryFontSize_ar" value="<?= $settingsDocument->headerSecondaryFontSize_ar ?>"/>
                                </div>
                                <div class="mb-6 col-6">
                                    <label class="form-label" for="headerSecondaryFontFamily">Header Secondary Font Family</label>
                                    <select id="headerSecondaryFontFamily" name="headerSecondaryFontFamily" class="selectpicker form-select w-100" data-style="btn-default" data-live-search="true">
                                        <option value="">Select a Font Family</option>
                                        <?php
                                        $res = $db->query("SELECT * FROM document_fontFamily");
                                        while ($row = mysqli_fetch_assoc($res)) {
                                            ?>
                                            <option value="<?= $row['fontFamilyId'] ?>" <?php if ($row['fontFamilyId'] == $settingsDocument->headerSecondaryFontFamilyId) echo "selected" ?> ><?= $row['fontFamilyName'] ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-6 col-6">
                                    <label class="form-label" for="headerSecondaryFontFamily_ar">Header Secondary Font Family (AR)</label>
                                    <select id="headerSecondaryFontFamily_ar" name="headerSecondaryFontFamily_ar" class="selectpicker form-select w-100" data-style="btn-default" data-live-search="true">
                                        <option value="">Select a Font Family</option>
                                        <?php
                                        $res = $db->query("SELECT * FROM document_fontFamily");
                                        while ($row = mysqli_fetch_assoc($res)) {
                                            ?>
                                            <option value="<?= $row['fontFamilyId'] ?>" <?php if ($row['fontFamilyId'] == $settingsDocument->headerSecondaryFontFamilyId_ar) echo "selected" ?> ><?= $row['fontFamilyName'] ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-6 col-6">
                                    <label class="form-label" for="bodyFontSize">Body Font Size</label>
                                    <input type="number" class="form-control" id="bodyFontSize" name="bodyFontSize" value="<?= $settingsDocument->bodyFontSize ?>"/>
                                </div>
                                <div class="mb-6 col-6">
                                    <label class="form-label" for="bodyFontSize_ar">Body Font Size (AR)</label>
                                    <input type="number" class="form-control" id="bodyFontSize_ar" name="bodyFontSize_ar" value="<?= $settingsDocument->bodyFontSize_ar ?>"/>
                                </div>
                                <div class="mb-6 col-6">
                                    <label class="form-label" for="bodyFontFamily">Body Font Family</label>
                                    <select id="bodyFontFamily" name="bodyFontFamily" class="selectpicker form-select w-100" data-style="btn-default" data-live-search="true">
                                        <option value="">Select a Font Family</option>
                                        <?php
                                        $res = $db->query("SELECT * FROM document_fontFamily");
                                        while ($row = mysqli_fetch_assoc($res)) {
                                            ?>
                                            <option value="<?= $row['fontFamilyId'] ?>" <?php if ($row['fontFamilyId'] == $settingsDocument->bodyFontFamilyId) echo "selected" ?> ><?= $row['fontFamilyName'] ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-6 col-6">
                                    <label class="form-label" for="bodyFontFamily_ar">Body Font Family (AR)</label>
                                    <select id="bodyFontFamily_ar" name="bodyFontFamily_ar" class="selectpicker form-select w-100" data-style="btn-default" data-live-search="true">
                                        <option value="">Select a Font Family</option>
                                        <?php
                                        $res = $db->query("SELECT * FROM document_fontFamily");
                                        while ($row = mysqli_fetch_assoc($res)) {
                                            ?>
                                            <option value="<?= $row['fontFamilyId'] ?>" <?php if ($row['fontFamilyId'] == $settingsDocument->bodyFontFamilyId_ar) echo "selected" ?> ><?= $row['fontFamilyName'] ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-6 col-6">
                                    <label class="form-label" for="tableHeaderFontSize">Table Header Font Size</label>
                                    <input type="number" class="form-control" id="tableHeaderFontSize" name="tableHeaderFontSize" value="<?= $settingsDocument->tableHeaderFontSize ?>"/>
                                </div>
                                <div class="mb-6 col-6">
                                    <label class="form-label" for="tableHeaderFontSize_ar">Table Header Font Size (AR)</label>
                                    <input type="number" class="form-control" id="tableHeaderFontSize_ar" name="tableHeaderFontSize_ar" value="<?= $settingsDocument->tableHeaderFontSize_ar ?>"/>
                                </div>
                                <div class="mb-6 col-6">
                                    <label class="form-label" for="tableHeaderFontFamily">Table Header Font Family</label>
                                    <select id="tableHeaderFontFamily" name="tableHeaderFontFamily" class="selectpicker form-select w-100" data-style="btn-default" data-live-search="true">
                                        <option value="">Select a Font Family</option>
                                        <?php
                                        $res = $db->query("SELECT * FROM document_fontFamily");
                                        while ($row = mysqli_fetch_assoc($res)) {
                                            ?>
                                            <option value="<?= $row['fontFamilyId'] ?>" <?php if ($row['fontFamilyId'] == $settingsDocument->tableHeaderFontFamilyId) echo "selected" ?> ><?= $row['fontFamilyName'] ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-6 col-6">
                                    <label class="form-label" for="tableHeaderFontFamily_ar">Table Header Font Family (AR)</label>
                                    <select id="tableHeaderFontFamily_ar" name="tableHeaderFontFamily_ar" class="selectpicker form-select w-100" data-style="btn-default" data-live-search="true">
                                        <option value="">Select a Font Family</option>
                                        <?php
                                        $res = $db->query("SELECT * FROM document_fontFamily");
                                        while ($row = mysqli_fetch_assoc($res)) {
                                            ?>
                                            <option value="<?= $row['fontFamilyId'] ?>" <?php if ($row['fontFamilyId'] == $settingsDocument->tableHeaderFontFamilyId_ar) echo "selected" ?> ><?= $row['fontFamilyName'] ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-6 col-6">
                                    <label class="form-label" for="tableRowsFontSize">Table Rows Font Size</label>
                                    <input type="number" class="form-control" id="tableRowsFontSize" name="tableRowsFontSize" value="<?= $settingsDocument->tableRowsFontSize ?>"/>
                                </div>
                                <div class="mb-6 col-6">
                                    <label class="form-label" for="tableRowsFontSize_ar">Table Rows Font Size (AR)</label>
                                    <input type="number" class="form-control" id="tableRowsFontSize_ar" name="tableRowsFontSize_ar" value="<?= $settingsDocument->tableRowsFontSize_ar ?>"/>
                                </div>
                                <div class="mb-6 col-6">
                                    <label class="form-label" for="tableRowsFontFamily">Table Rows Font Family</label>
                                    <select id="tableRowsFontFamily" name="tableRowsFontFamily" class="selectpicker form-select w-100" data-style="btn-default" data-live-search="true">
                                        <option value="">Select a Font Family</option>
                                        <?php
                                        $res = $db->query("SELECT * FROM document_fontFamily");
                                        while ($row = mysqli_fetch_assoc($res)) {
                                            ?>
                                            <option value="<?= $row['fontFamilyId'] ?>" <?php if ($row['fontFamilyId'] == $settingsDocument->tableRowsFontFamilyId) echo "selected" ?> ><?= $row['fontFamilyName'] ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-6 col-6">
                                    <label class="form-label" for="tableRowsFontFamily_ar">Table Rows Font Family (AR)</label>
                                    <select id="tableRowsFontFamily_ar" name="tableRowsFontFamily_ar" class="selectpicker form-select w-100" data-style="btn-default" data-live-search="true">
                                        <option value="">Select a Font Family</option>
                                        <?php
                                        $res = $db->query("SELECT * FROM document_fontFamily");
                                        while ($row = mysqli_fetch_assoc($res)) {
                                            ?>
                                            <option value="<?= $row['fontFamilyId'] ?>" <?php if ($row['fontFamilyId'] == $settingsDocument->tableRowsFontFamilyId_ar) echo "selected" ?> ><?= $row['fontFamilyName'] ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-6 col-6">
                                    <label class="form-label" for="footerFontSize">Footer Font Size</label>
                                    <input type="number" class="form-control" id="footerFontSize" name="footerFontSize" value="<?= $settingsDocument->footerFontSize ?>"/>
                                </div>
                                <div class="mb-6 col-6">
                                    <label class="form-label" for="footerFontSize_ar">Footer Font Size (AR)</label>
                                    <input type="number" class="form-control" id="footerFontSize_ar" name="footerFontSize_ar" value="<?= $settingsDocument->footerFontSize_ar ?>"/>
                                </div>
                                <div class="mb-6 col-6">
                                    <label class="form-label" for="footerFontFamily">Footer Font Family</label>
                                    <select id="footerFontFamily" name="footerFontFamily" class="selectpicker form-select w-100" data-style="btn-default" data-live-search="true">
                                        <option value="">Select a Font Family</option>
                                        <?php
                                        $res = $db->query("SELECT * FROM document_fontFamily");
                                        while ($row = mysqli_fetch_assoc($res)) {
                                            ?>
                                            <option value="<?= $row['fontFamilyId'] ?>" <?php if ($row['fontFamilyId'] == $settingsDocument->footerFontFamilyId) echo "selected" ?> ><?= $row['fontFamilyName'] ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-6 col-6">
                                    <label class="form-label" for="footerFontFamily_ar">Footer Font Family (AR)</label>
                                    <select id="footerFontFamily_ar" name="footerFontFamily_ar" class="selectpicker form-select w-100" data-style="btn-default" data-live-search="true">
                                        <option value="">Select a Font Family</option>
                                        <?php
                                        $res = $db->query("SELECT * FROM document_fontFamily");
                                        while ($row = mysqli_fetch_assoc($res)) {
                                            ?>
                                            <option value="<?= $row['fontFamilyId'] ?>" <?php if ($row['fontFamilyId'] == $settingsDocument->footerFontFamilyId_ar) echo "selected" ?> ><?= $row['fontFamilyName'] ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div>
                            <button class="btn btn-primary me-sm-4 data-submit">Update</button>
                            <button type="reset" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal" aria-label="Close">Discard</button>
                        </div>
                    </form>

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
    <!-- / Layout wrapper -->
    <?php include_once __DIR__ . "/../../../includes/dashboard/dashboard_footer_scripts.php"; ?>

    <script>
      $(document).ready(function () {
        // Form Validation
        const settingsDocumentForm = document.getElementById('settingsDocumentForm');

        // Add New sparepart Form Validation
        const fv = FormValidation.formValidation(settingsDocumentForm, {
          fields: {
            titleFontSize: {
              validators: {
                notEmpty: {
                  message: 'Please enter Title font value '
                }
              }
            },
            titleFontSize_ar: {
              validators: {
                notEmpty: {
                  message: 'Please enter Title font value for AR '
                }
              }
            },
            titleFontFamily: {
              validators: {
                notEmpty: {
                  message: 'Please choose Title font family '
                }
              }
            },
            titleFontFamily_ar: {
              validators: {
                notEmpty: {
                  message: 'Please choose Title font family for AR '
                }
              }
            },
            headerPrimaryFontSize: {
              validators: {
                notEmpty: {
                  message: 'Please enter Header Primary font value '
                }
              }
            },
            headerPrimaryFontSize_ar: {
              validators: {
                notEmpty: {
                  message: 'Please enter Header Primary font value for AR '
                }
              }
            },
            headerPrimaryFontFamily: {
              validators: {
                notEmpty: {
                  message: 'Please choose Header Primary font family '
                }
              }
            },
            headerPrimaryFontFamily_ar: {
              validators: {
                notEmpty: {
                  message: 'Please choose Header Primary font family for AR '
                }
              }
            },
            headerSecondaryFontSize: {
              validators: {
                notEmpty: {
                  message: 'Please enter Header Secondary font value '
                }
              }
            },
            headerSecondaryFontSize_ar: {
              validators: {
                notEmpty: {
                  message: 'Please enter Header Secondary font value for AR '
                }
              }
            },
            headerSecondaryFontFamily: {
              validators: {
                notEmpty: {
                  message: 'Please choose Header Secondary font family '
                }
              }
            },
            headerSecondaryFontFamily_ar: {
              validators: {
                notEmpty: {
                  message: 'Please choose Header Secondary font family for AR '
                }
              }
            },
            bodyFontSize: {
              validators: {
                notEmpty: {
                  message: 'Please enter Body font value '
                }
              }
            },
            bodyFontSize_ar: {
              validators: {
                notEmpty: {
                  message: 'Please enter Body font value for AR '
                }
              }
            },
            bodyFontFamily: {
              validators: {
                notEmpty: {
                  message: 'Please choose Body font family '
                }
              }
            },
            bodyFontFamily_ar: {
              validators: {
                notEmpty: {
                  message: 'Please choose Body font family for AR '
                }
              }
            },
            tableHeaderFontSize: {
              validators: {
                notEmpty: {
                  message: 'Please enter Table Header font value '
                }
              }
            },
            tableHeaderFontSize_ar: {
              validators: {
                notEmpty: {
                  message: 'Please enter Table Header font value for AR '
                }
              }
            },
            tableHeaderFontFamily: {
              validators: {
                notEmpty: {
                  message: 'Please choose Table Header font family '
                }
              }
            },
            tableHeaderFontFamily_ar: {
              validators: {
                notEmpty: {
                  message: 'Please choose Table Header font family for AR '
                }
              }
            },
            tableRowsFontSize: {
              validators: {
                notEmpty: {
                  message: 'Please enter Table Rows font value '
                }
              }
            },
            tableRowsFontSize_ar: {
              validators: {
                notEmpty: {
                  message: 'Please enter Table Rows font value for AR '
                }
              }
            },
            tableRowsFontFamily: {
              validators: {
                notEmpty: {
                  message: 'Please choose Table Rows font family '
                }
              }
            },
            tableRowsFontFamily_ar: {
              validators: {
                notEmpty: {
                  message: 'Please choose Table Rows font family for AR '
                }
              }
            },
            footerFontSize: {
              validators: {
                notEmpty: {
                  message: 'Please enter Table Rows font value '
                }
              }
            },
            footerFontSize_ar: {
              validators: {
                notEmpty: {
                  message: 'Please enter Table Rows font value for AR '
                }
              }
            },
            footerFontFamily: {
              validators: {
                notEmpty: {
                  message: 'Please choose Table Rows font family '
                }
              }
            },
            footerFontFamily_ar: {
              validators: {
                notEmpty: {
                  message: 'Please choose Table Rows font family for AR '
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


        $("#settingsDocumentForm").submit(function (e) {
          e.preventDefault();

          fv.validate().then(function (status) {
            if (status === "Valid") {
              blockArea($(".form-block"));

              var form = $("#settingsDocumentForm")[0]; // Targeting the specific form
              var formData = new FormData(form);

              $.ajax({
                url: "/ajax/settings/documentsettings/update_document_setting.php",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                  console.log(data);
                  unBlockArea($(".form-block"));

                  var response = data.trim().split("|");
                  var statusMessage = response[0];
                  var message = response[1];

                  if (statusMessage === "SUCCESS") {
                    Swal.fire({
                      title: "Success!",
                      icon: "success",
                      text: message,
                      customClass: {
                        confirmButton: "btn btn-primary",
                      },
                      showClass: {
                        popup: "animate__animated animate__bounce",
                      },
                      buttonsStyling: false,
                    }).then(function () {
                      blockArea($("body"));
                      location.reload(); // Reload page after success
                    });
                  } else if (statusMessage === "ERROR") {
                    Swal.fire({
                      title: "Oops!",
                      icon: "error",
                      text: message,
                      customClass: {
                        confirmButton: "btn btn-primary",
                      },
                      showClass: {
                        popup: "animate__animated animate__shakeX",
                      },
                      buttonsStyling: false,
                    });
                  }
                },
                error: function (error) {
                  console.log(error);
                },
              });
            }
          });

          return false;
        });

      });
    </script>
    <style>
        #editSparepartModal {
            /*width: 400px !important;*/
        }
    </style>
</body>
</html>