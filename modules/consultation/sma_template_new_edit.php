<?php
$PAGE_ID = "SETTINGS_CONSULTATION_SMA_NEW_EDIT";
include_once __DIR__ . "/../../includes/baseIncludes.php";
include_once __DIR__ . "/../../includes/auth_check.php";

$isEditMode = false;
$isNewMode  = false;
$isListMode = false;
$templateID = 0;
if (isset($_GET['action']) && !empty($_GET['action'])) {
    $action = filter_var($_GET['action'], FILTER_SANITIZE_SPECIAL_CHARS);
    if ($action == "NEW") {
        $isNewMode = true;
    } else if ($action == "EDIT") {
        $isEditMode = true;
        $templateID = filter_var($_GET['templateID'], FILTER_SANITIZE_SPECIAL_CHARS);
    }
} else {
    $isListMode = true;
}


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
    <title>GrandMaster ERP | SMA Templates</title>
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
                                    <h4 class="my-0">SMA Templates</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row invoice-add">
                        <?php
                        if ($isListMode) {
                            //If LIST mode
                            ?>
                            <div class="col-lg-12 col-12 mb-lg-0 mb-6">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Available Templates</h5>
                                        <a href="/consultation/smatemplates?action=NEW" class="btn btn-primary"><i class="menu-icon tf-icons ti ti-plus"></i>Add</a>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th>Template Name</th>
                                                <th>Status</th>
                                                <th>Created</th>
                                                <th>Modified</th>
                                                <th>Actions</th>
                                            </tr>
                                            </thead>
                                            <tbody class="table-border-bottom-0">
                                            <?php
                                            $res     = $db->query("SELECT * FROM consultation_sma_templates");
                                            $numrows = $db->numRows($res);
                                            while ($row = mysqli_fetch_assoc($res)) {

                                                ?>
                                                <tr>
                                                    <td>
                                                        <span class="fw-medium"><?= $row['templateName'] ?></span>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        if ($row['active']) {
                                                            ?> <span class="badge bg-label-success me-1">Active</span> <?php
                                                        } else {
                                                            ?> <span class="badge bg-label-danger me-1">Inactive</span> <?php
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><?= formatDate($row['dateCreated'], false) ?></td>
                                                    <td>
                                                        <?= formatDate($row['dateModified'], false) ?>
                                                    </td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <button
                                                                    type="button"
                                                                    class="btn p-0 dropdown-toggle hide-arrow"
                                                                    data-bs-toggle="dropdown">
                                                                <i class="menu-icon tf-icons ti ti-dots-vertical"></i>
                                                            </button>
                                                            <div class="dropdown-menu">
                                                                <a class="dropdown-item" href="/consultation/smatemplates?action=EDIT&templateID=<?= $row['smaTemplateID'] ?>">
                                                                    <i class="menu-icon tf-icons ti ti-pencil"></i>Edit
                                                                </a>
                                                                <a class="dropdown-item" href="javascript:void(0);" onclick="deleteTemplate(<?= (int)$row['smaTemplateID'] ?>)">
                                                                    <i class="menu-icon tf-icons ti ti-trash"></i>Delete
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php
                                            }

                                            if ($numrows == 0) {
                                                ?>
                                                <tr>
                                                    <td colspan="5" class="text-center">
                                                        <span class="fw-light text-center">No templates yet. Click Add button to add new template</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="5" class="text-center">
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }

                        if (!$isListMode) {
                            //If NOT list mode
                            ?>
                            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" name="templateForm" id="templateForm">
                                <div class="col-lg-12 col-12 mb-lg-0 mb-6">
                                    <div class="card invoice-preview-card p-sm-6 p-6">
                                        <div class="card-body px-0">
                                            <?php

                                            if ($isEditMode) {
                                                $resTemplate = $db->query("SELECT * FROM consultation_sma_templates WHERE smaTemplateID = ?s", $templateID);
                                                $rowTemplate = mysqli_fetch_assoc($resTemplate);
                                            }
                                            ?>
                                            <div class="row">
                                                <div class="col-md-5 col-sm-5 col-12 mb-sm-0 mb-6">
                                                    <div class="mb-4">
                                                        <div>
                                                            <label for="defaultFormControlInput" class="form-label">
                                                                SMA Template Name
                                                            </label>
                                                            <input type="text" class="form-control" id="smaTemplateName" name="smaTemplateName" placeholder="Website Development Template" aria-describedby="smaTemplateNameHelp" required value="<?php if ($isEditMode) echo $rowTemplate['templateName'] ?>"/>
                                                            <div id="smaTemplateNameHelp" class="form-text">Give it a meaningful name</div>
                                                        </div>
                                                    </div>
                                                    <div class="form-check form-switch ">
                                                        <input class="form-check-input" type="checkbox" id="templateActive" <?php
                                                        if (isset($rowTemplate)) {
                                                            if ($rowTemplate['active'] == 1) echo 'checked';
                                                        } else {
                                                            echo 'checked';
                                                        } ?>/>
                                                        <label class="form-check-label" for="templateActive">Active</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-7 col-sm-7">
                                                    <div class="mt-6 d-flex gap-2 justify-content-end">
                                                        <?php
                                                        if ($isNewMode) {
                                                            ?>
                                                            <button type="submit" class="btn btn-primary mb-4">
                                                                <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-device-floppy ti-xs me-2"></i>Save</span>
                                                            </button>
                                                            <?php
                                                        }
                                                        if ($isEditMode) {
                                                            ?>
                                                            <button type="button" class="btn btn-success mb-4" onclick="updateTemplate()">
                                                                <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-device-floppy ti-xs me-2"></i>Update</span>
                                                            </button>
                                                            <?php
                                                        }
                                                        ?>
                                                        <a href="/consultation/smatemplates" class="btn btn-secondary mb-4">
                                                            <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="ti ti-circle-x ti-xs me-2"></i>Discard</span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 mt-6">
                                    <!--Introduction Starts-->
                                    <div class="card mb-2">
                                        <h5 class="card-header">SMA Content</h5>

                                        <div class="card-body mb-0">
                                            <strong>Available placeholders</strong>
                                            <p>{customer_company_name}</p>
                                        </div>

                                        <div class="card-body mt-0">
                                            <?php include __DIR__ . "/../../includes/snow_editor_toolbar.php"; ?>
                                            <div id="content" class="snow-editor" style="min-height: 600px;"><?php if ($isEditMode) echo $rowTemplate['content'] ?></div>
                                        </div>
                                    </div>
                                    <!--Introduction Ends-->

                                </div>
                            </form>
                            <?php
                        }
                        ?>
                    </div>
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
<!-- Page JS -->
<script>
    var quillEditors = {};

    $(document).ready(function (e) {

        $("#templateForm").submit(function (e) {
            e.preventDefault();
            blockArea($('body'));

            var dataToSend = {
                templateName: $("#smaTemplateName").val(),
                content: quillEditors['content'].root.innerHTML
            };

            $.ajax({
                url: '/ajax/consultation/save_sma_template.php',
                type: 'POST',
                data: JSON.stringify(dataToSend),
                contentType: 'application/json',
                dataType: 'json',
                success: function (response) {
                    console.log("Server Response: ", response);

                    unBlockArea($('body'));
                    if (response.status === "success") {
                        // Do something
                        showSuccessMessage(response.message, gotoPage, "/consultation/smatemplates");


                    } else if (response.status === "error") {
                        // Handle error
                        showErrorMessage(response.message);

                    }
                },

                error: function (xhr, status, error) {
                    console.error("AJAX error:", error);
                }
            });


        });


        //Initialize Editors
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


                quill.keyboard.addBinding({
                    key: 13,
                    shiftKey: true
                }, {
                    format: ['list']
                }, function (range, context) {
                    quill.insertText(range.index, '\n');
                    quill.setSelection(range.index + 1, Quill.sources.SILENT);
                });

                quillEditors[editorID] = quill;


            }
        });


    });

    function deleteTemplate(templateID) {

        Swal.fire({
            title: 'Are you sure?',
            text: "You are about to delete this template. This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {

                console.log("Deleting template ID:", templateID);

                $.ajax({
                    url: '/ajax/consultation/delete_template.php',
                    type: 'POST',
                    data: JSON.stringify({templateID: templateID}),
                    contentType: 'application/json',
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === "success") {
                            showSuccessMessage(response.message, gotoPage, "/consultation/smatemplates");
                        } else {
                            showErrorMessage(response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        showErrorMessage("Something went wrong. Please try again.");
                    }
                });

            } else {
                // ❌ Cancel clicked — no action needed
                console.log("Delete cancelled.");
            }
        });


    }


    function updateTemplate() {

        blockArea($('body'));

        var templateID = '<?=$templateID ?>';
        var dataToSend = {
            smaTemplateID: templateID,
            templateName: $("#smaTemplateName").val(),
            isActive: $('#templateActive').is(':checked') ? 1 : 0,
            content: quillEditors['content'].root.innerHTML
        };

        $.ajax({
            url: '/ajax/consultation/update_sma_template.php',
            type: 'POST',
            data: JSON.stringify(dataToSend),
            contentType: 'application/json',
            dataType: 'json',
            success: function (response) {
                console.log("Server Response: ", response);

                unBlockArea($('body'));
                if (response.status === "success") {
                    // Do something
                    showSuccessMessage(response.message, gotoPage, "/consultation/smatemplates");


                } else if (response.status === "error") {
                    // Handle error
                    showErrorMessage(response.message);

                }
            },

            error: function (xhr, status, error) {
                console.error("AJAX error:", error);
            }
        });


    }
</script>
</body>
</html>