<?php 
$PAGE_ID = "HR_SALARY_PAYROLL";
include_once __DIR__ . "/../../includes/baseIncludes.php";
include_once __DIR__ . "/../../includes/auth_check.php";
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
    <link rel="stylesheet" href="/assets/vendor/css/pages/app-invoice.css"/>
    <link rel="stylesheet" href="/assets/vendor/css/jquery-ui.css"/>
    <link rel="stylesheet" href="/assets/vendor/libs/select2/select2.css"/>
    <!-- Handsontable CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable/styles/handsontable.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable/styles/ht-theme-main.min.css" />

    <?php include_once __DIR__ . "/../../includes/dashboard/dashboard_head_section_post_vendor.php"; ?>

    <style>
        #salaryTable {
            max-width: 100%;
            margin: auto;
        }
        /* Ensure Handsontable fits within the card */
        .handsontable {
            font-size: 14px;
        }
        /* Align with your dashboard styling */
        .ht_master .wtHider {
            width: 100% !important;
        }

        .hr-color {
            border-top: 1px solid #e0e0e0; /* Adjust color to match your theme */
            margin: 20px 0; /* Space above and below the line */
        }
    </style>

    <title>GrandMaster ERP | Salary Payroll</title>
</head>

<?php
$companyId = intval($_SESSION['SES_SELECTED_COMPANY']);
$employees = [];
$query = "SELECT u.userID, u.firstName, u.lastName, u.basicSalary, u.employerGOSI, u.employeeGOSI 
          FROM users u
          WHERE u.active = 1 AND u.userID != 1
          ORDER BY u.firstName ASC";
$res = $db->query($query);
while ($row = mysqli_fetch_assoc($res)) {
    $employeePercent = 0.10; // default
    $employerPercent = 0.12; // default
    $emp = isset($row['employeeGOSI']) && $row['employeeGOSI'] > 0 ? (float)$row['employeeGOSI'] / 100 : 0;
    $empl = isset($row['employerGOSI']) && $row['employerGOSI'] > 0 ? (float)$row['employerGOSI'] / 100 : 0;
    if ($emp > 0) $employeePercent = $emp;
    if ($empl > 0) $employerPercent = $empl;
    $employees[] = [
        'id' => (int)$row['userID'],
        'name' => $row['firstName'] . ' ' . $row['lastName'],
        'basicSalary' => $row['basicSalary'] ? (float)$row['basicSalary'] : null,
        'ha' => null,
        'ta' => null,
        'oa' => null,
        'totalBeforeGosi' => null,
        'totalSalary' => null,
        'deductionEmployee10' => null,
        'deductionCompany12' => null,
        'totalGosi' => null,
        'totalPaid' => null,
        'employeeGosiPercent' => $employeePercent,
        'employerGosiPercent' => $employerPercent
    ];
}
?>

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
                                    <h4 class="my-0">Salary Payroll</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row invoice-add">
                        <!-- Salary Table -->
                        <div class="col-lg-12 col-12 mb-lg-0 mb-6">
                            <div class="card invoice-preview-card p-sm-6 p-6">
                                <div class="card-body px-0">
                                     <!-- Header controls -->
                                    <div class="header-controls d-flex justify-content-between align-items-center mb-5">
                                        <div class="d-flex align-items-center">
                                            <label for="monthSelect" class="me-2 mb-0">Select Month: </label>
                                            <input type="month" id="monthSelect" class="form-control" style="width: auto;">
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <label for="filterEmployee" class="me-2 mb-0">Select Employee: </label>
                                            <select id="filterEmployee" class="form-select selectpicker" data-style="btn-default" data-live-search="true" data-width="500px" multiple tabindex="null">
                                                <option value="">All Employees</option>
                                                <?php
                                                foreach ($employees as $employee) {
                                                    ?>
                                                    <option value="<?= $employee['id'] ?>"><?= htmlspecialchars($employee['name']) ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <button id="saveButton" class="btn btn-primary">
                                            <i class="ti ti-device-floppy me-2"></i>Save
                                        </button>
                                    </div>
                                    <hr class="hr-color">
                                    <!-- Instructional Text and Handsontable -->
                                    <div class="row">
                                        <small class="text-muted mb-2 d-block">Shift + Mouse scroll to scroll horizontally</small>
                                        <div id="salaryTableWrapper" style="width: 100%; height: 500px; overflow: auto;">
                                            <div id="salaryTable"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Salary Table -->
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

<!-- Handsontable JS -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/handsontable/dist/handsontable.full.min.js"></script>

<script>
    const employeeData = <?= json_encode($employees); ?>;
    let hot;

    let originalData = employeeData;

    var monthInput = document.getElementById('monthSelect');
    monthInput.addEventListener('click', function() {
        this.showPicker && this.showPicker();
    });

    function loadPayrollData(month) {
        if (!month) return;

        // Create a map of employee percentages and default basicSalary from initial data
        const employeePercentMap = {};
        employeeData.forEach(emp => {
            employeePercentMap[emp.id] = {
                employeeGosiPercent: emp.employeeGosiPercent,
                employerGosiPercent: emp.employerGosiPercent,
                defaultBasicSalary: emp.basicSalary
            };
        });

        $.ajax({
            url: '/ajax/payroll/read.php',
            type: 'GET',
            data: { month: month },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'error') {
                    showErrorMessage(response.message);
                    applyEmployeeFilter(); // Apply filter to default data
                    return;
                }
                // Add percentages and default basicSalary to loaded data
                response.forEach(row => {
                    const percents = employeePercentMap[row.id] || { 
                        employeeGosiPercent: 0.10, 
                        employerGosiPercent: 0.12, 
                        defaultBasicSalary: null 
                    };
                    row.employeeGosiPercent = percents.employeeGosiPercent;
                    row.employerGosiPercent = percents.employerGosiPercent;
                    // Use payroll basicSalary if available, otherwise fall back to users table
                    row.basicSalary = row.basicSalary !== null ? row.basicSalary : percents.defaultBasicSalary;
                });
                console.log('Loaded data from read.php:', response); // Debug: Log loaded data
                originalData = response; // Update original data with loaded data
                applyEmployeeFilter(); // Apply filter to loaded data
            },
            error: function (xhr, status, error) {
                showErrorMessage('Failed to load payroll data: ' + error);
                applyEmployeeFilter(); // Apply filter to default data
            }
        });
    }

    function applyEmployeeFilter() {
        const selectedEmployeeIds = $('#filterEmployee').val(); // Get array of selected IDs
        let filteredData = originalData;

        // If no employees are selected or "All Employees" is selected, show all data
        if (selectedEmployeeIds && selectedEmployeeIds.length > 0 && !selectedEmployeeIds.includes("")) {
            filteredData = originalData.filter(employee => selectedEmployeeIds.includes(String(employee.id)));
        }

        console.log('Selected employee IDs:', selectedEmployeeIds); // Debug: Log selected IDs
        console.log('Filtered data:', filteredData); // Debug: Log filtered data
        hot.loadData(filteredData);
        hot.render(); // Ensure table is re-rendered
    }

    function calculateRowValues(row, basicSalary, oa) {
        const rowData = hot.getSourceDataAtRow(row); // Get the entire row data
        if (basicSalary === null || basicSalary === '' || isNaN(parseFloat(basicSalary)) || !isFinite(basicSalary) || parseFloat(basicSalary) < 0) {
            console.log(`Row ${row}: Invalid basicSalary=${basicSalary}`); // Debug: Log invalid basicSalary
            // Reset all fields
            ['ha', 'ta', 'totalBeforeGosi', 'totalSalary', 'deductionEmployee10', 'deductionCompany12', 'totalGosi', 'totalPaid'].forEach(prop => {
                hot.setDataAtRowProp(row, prop, null);
                rowData[prop] = null;
            });
        } else {
            const basic = parseFloat(basicSalary);
            const ha = (basic * 0.25).toFixed(2);
            const ta = (basic * 0.10).toFixed(2);
            const oaValue = parseFloat(oa) || 0;
            const totalBeforeGosi = (basic + parseFloat(ha) + parseFloat(ta) + oaValue).toFixed(2);
            const totalSalary = (basic + parseFloat(ha)).toFixed(2);
            const deductionEmployee10 = (parseFloat(totalSalary) * rowData.employeeGosiPercent).toFixed(2);
            const deductionCompany12 = (parseFloat(totalSalary) * rowData.employerGosiPercent).toFixed(2);
            const totalGosi = (parseFloat(deductionEmployee10) + parseFloat(deductionCompany12)).toFixed(2);
            const totalPaid = (basic + parseFloat(ha) + parseFloat(ta) + oaValue - parseFloat(deductionEmployee10)).toFixed(2);

            console.log(`Row ${row}: Calculated values - basic=${basic}, ha=${ha}, ta=${ta}, oa=${oaValue}, totalBeforeGosi=${totalBeforeGosi}, totalSalary=${totalSalary}, deductionEmployee10=${deductionEmployee10}, deductionCompany12=${deductionCompany12}, totalGosi=${totalGosi}, totalPaid=${totalPaid}`); // Debug: Log calculations

            // Update Handsontable and rowData explicitly
            const updates = {
                ha: ha,
                ta: ta,
                totalBeforeGosi: totalBeforeGosi,
                totalSalary: totalSalary,
                deductionEmployee10: deductionEmployee10,
                deductionCompany12: deductionCompany12,
                totalGosi: totalGosi,
                totalPaid: totalPaid
            };
            Object.keys(updates).forEach(prop => {
                hot.setDataAtRowProp(row, prop, updates[prop]);
                rowData[prop] = updates[prop]; // Update the source data object
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('salaryTable');

        // Custom renderer to highlight invalid cells
        function numericRenderer(instance, td, row, col, prop, value, cellProperties) {
            Handsontable.renderers.NumericRenderer.apply(this, arguments);
            if (cellProperties.invalid) {
                td.className += ' htInvalid';
            }
        }

        hot = new Handsontable(container, {
            data: employeeData,
            colHeaders: ['Employee Name', 'Basic Salary', 'Housing Allowance (HA)', 'Travel Allowance (TA)', 'Other Allowance (OA)', 'Total Before GOSI', 'Total Salary', 'GOSI Deduction Employee', 'GOSI Deduction Company', 'Total GOSI', 'Total Paid to Employee'],
            columns: [
                { data: 'name', readOnly: true, width: 150 },
                { 
                    data: 'basicSalary', 
                    type: 'numeric', 
                    numericFormat: { pattern: '0,0.00' },
                    width: 120,
                    validator: function(value, callback) {
                        if (value === null || value === '' || (!isNaN(parseFloat(value)) && isFinite(value) && parseFloat(value) >= 0)) {
                            callback(true);
                        } else {
                            callback(false);
                        }
                    },
                    renderer: numericRenderer
                },
                { data: 'ha', type: 'numeric', numericFormat: { pattern: '0,0.00' }, readOnly: true },
                { data: 'ta', type: 'numeric', numericFormat: { pattern: '0,0.00' }, readOnly: true },
                { 
                    data: 'oa', 
                    type: 'numeric', 
                    numericFormat: { pattern: '0,0.00' },
                    validator: function(value, callback) {
                        if (value === null || value === '' || (!isNaN(parseFloat(value)) && isFinite(value) && parseFloat(value) >= 0)) {
                            callback(true);
                        } else {
                            callback(false);
                        }
                    },
                    renderer: numericRenderer
                },
                { data: 'totalBeforeGosi', type: 'numeric', numericFormat: { pattern: '0,0.00' }, readOnly: true },
                { data: 'totalSalary', type: 'numeric', numericFormat: { pattern: '0,0.00' }, readOnly: true },
                { data: 'deductionEmployee10', type: 'numeric', numericFormat: { pattern: '0,0.00' }, readOnly: true },
                { data: 'deductionCompany12', type: 'numeric', numericFormat: { pattern: '0,0.00' }, readOnly: true },
                { data: 'totalGosi', type: 'numeric', numericFormat: { pattern: '0,0.00' }, readOnly: true },
                { data: 'totalPaid', type: 'numeric', numericFormat: { pattern: '0,0.00' }, readOnly: true }
            ],
            rowHeaders: true,
            fixedColumnsStart: 2, // Fix Employee Name and Basic Salary columns
            width: '100%', // Ensure table uses full container width
            height: 'auto', // Fixed height to enable vertical scrollbar
            autoWrapRow: false, // Prevent wrapping to next row
            autoWrapCol: false, // Prevent wrapping to next column
            manualColumnResize: true, // Allow manual column resizing
            manualColumnMove: true,
            filters: true,
            viewportColumnRenderingOffset: 50, // Render extra columns for smoother scrolling
            licenseKey: 'non-commercial-and-evaluation',
            afterValidate: function(isValid, value, row, prop) {
                if (prop === 'basicSalary' || prop === 'oa') {
                    hot.getCellMeta(row, hot.propToCol(prop)).invalid = !isValid;
                    if (!isValid) {
                        showErrorMessage(`${prop === 'basicSalary' ? 'Basic Salary' : 'Other Allowance'} must be a non-negative number.`);
                    }
                }
            },
            afterChange: function (changes, source) {
                if (source === 'edit' && changes) {
                    console.log('Handsontable changes:', changes, 'Source:', source); // Debug: Log all changes
                    changes.forEach(([row, prop, oldValue, newValue]) => {
                        if (prop === 'basicSalary' || prop === 'oa') {
                            const basicSalary = hot.getDataAtRowProp(row, 'basicSalary');
                            const oa = hot.getDataAtRowProp(row, 'oa');
                            console.log(`Row ${row}: basicSalary=${basicSalary}, oa=${oa}`); // Debug: Log input values
                            calculateRowValues(row, basicSalary, oa);
                            hot.render(); // Ensure changes are rendered
                        }
                    });
                }
            },
            afterLoadData: function(sourceData, initialLoad, source) {  // Fixed: Correct parameters per Handsontable docs
                if (!initialLoad) {
                    // Recalculate all rows when data is loaded (non-initial)
                    hot.getSourceData().forEach((rowData, index) => {
                        calculateRowValues(index, rowData.basicSalary, rowData.oa);
                    });
                    hot.render();
                }
            }
        });

        // Load initial data for current month
        const monthSelect = document.getElementById('monthSelect');
        const currentMonth = new Date().toISOString().slice(0, 7); // YYYY-MM
        monthSelect.value = currentMonth;
        loadPayrollData(currentMonth);

        // Handle month change
        monthSelect.addEventListener('change', function () {
            loadPayrollData(this.value);
        });

        // Handle employee filter change
        document.getElementById('filterEmployee').addEventListener('change', function () {
            applyEmployeeFilter();
        });

        // Handle save button click
        document.getElementById('saveButton').addEventListener('click', function () {
            const month = monthSelect.value;
            if (!month) {
                showErrorMessage('Please select a month to save payroll data.');
                return;
            }

            // Validate all cells to ensure changes are committed
            hot.validateCells(function (valid) {
                console.log('Validation result:', valid); // Debug: Log validation result
                console.log('Raw Handsontable data:', hot.getSourceData()); // Debug: Log raw data
                const payrollData = hot.getSourceData().filter(row => 
                    row.basicSalary !== null && 
                    row.basicSalary !== '' && 
                    !isNaN(parseFloat(row.basicSalary)) && 
                    isFinite(row.basicSalary) && 
                    parseFloat(row.basicSalary) >= 0
                );
                console.log('Payroll data to save:', JSON.stringify(payrollData, null, 2)); // Debug: Log filtered data with details
                if (payrollData.length === 0) {
                    showErrorMessage('No valid payroll data to save. Please ensure Basic Salary is provided for at least one employee.');
                    return;
                }

                $.ajax({
                    url: '/ajax/payroll/save.php',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ month: month, payrollData: payrollData }),
                    dataType: 'json',
                    success: function (response) {
                        console.log('Save response:', response); // Debug: Log server response
                        if (response.status === 'success') {
                            showSuccessMessage(response.message);
                            loadPayrollData(month); // Refresh table
                        } else {
                            showErrorMessage(response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.log('Save error:', { status, error, response: xhr.responseText }); // Debug: Log AJAX error
                        showErrorMessage('Failed to save payroll data: ' + error);
                    }
                });
            });
        });
    });
</script>
</body>
</html>