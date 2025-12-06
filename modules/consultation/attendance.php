<?php 
$PAGE_ID = "HR_ATTENDANCE";
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable@latest/dist/handsontable.full.min.css" />

    <?php include_once __DIR__ . "/../../includes/dashboard/dashboard_head_section_post_vendor.php"; ?>

    <style>
        #attendanceTable {
            max-width: 100%;
            margin: auto;
        }
        .handsontable {
            font-size: 14px;
        }
        .ht_master .wtHider {
            width: 100% !important;
        }
        .hr-color {
            border-top: 1px solid #e0e0e0;
            margin: 20px 0;
        }
        .htDropdownMenu .wtSubmenu {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #d1d1d1;
            background: #fff;
        }
        .htDropdownMenu .ht_master .wtHolder::-webkit-scrollbar,
        .handsontable .wtHolder::-webkit-scrollbar {
            width: 8px;
        }
        .htDropdownMenu .ht_master .wtHolder::-webkit-scrollbar-thumb,
        .handsontable .wtHolder::-webkit-scrollbar-thumb {
            background-color: #666;
            border-radius: 4px;
        }
        .htDropdownMenu .ht_master .wtHolder::-webkit-scrollbar-track,
        .handsontable .wtHolder::-webkit-scrollbar-track {
            background-color: #eee;
        }
    </style>

    <title>GrandMaster ERP | Employee Attendance</title>
</head>

<?php
$companyId = intval($_SESSION['SES_SELECTED_COMPANY']);
$employees = [];
$query = "SELECT u.userID, u.employeeId, u.firstName, u.lastName, u.hoursPerDay, u.countryId 
          FROM users u
          WHERE u.active = 1 
          AND u.userID != 1
          ORDER BY u.firstName ASC";
$res = $db->query($query);
while ($row = mysqli_fetch_assoc($res)) {
    $employees[] = [
        'id' => (int)$row['userID'],
        'employeeId' => $row['employeeId'] ?? '',
        'name' => $row['firstName'] . ' ' . $row['lastName'],
        'hoursPerDay' => (float)$row['hoursPerDay'] ?? 8.0,
        'countryId' => (int)$row['countryId'] ?? 0
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
                                    <h4 class="my-0">Employee Attendance</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row invoice-add">
                        <!-- Attendance Table -->
                        <div class="col-lg-12 col-12 mb-lg-0 mb-6">
                            <div class="card invoice-preview-card p-sm-6 p-6">
                                <div class="card-body px-0">
                                    <!-- Header controls -->
                                    <div class="header-controls d-flex justify-content-between align-items-center mb-5">
                                        <div class="d-flex align-items-center">
                                            <label for="dateSelect" class="me-2 mb-0">Select Date: </label>
                                            <input name="attendanceDate" id="dateSelect" type="text" class="form-control gmm-date-format" placeholder="DD - MMM - YYYY" value="<?php echo date('Y-m-d'); ?>"/>
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
                                        <small class="alert-outline-info" role="alert">Select or enter times in HH:MM AM/PM format from dropdown. Calculations update automatically.</small>
                                        <div id="attendanceTableWrapper" style="width: 100%; height: 500px; overflow: auto;">
                                            <div id="attendanceTable"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Attendance Table -->
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
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/handsontable@latest/dist/handsontable.full.min.js"></script>

<script>
    const employeeData = <?= json_encode($employees); ?>;
    let hot;
    let originalData = employeeData;
    let currentDate = '';
    let isUpdating = false; // Flag to prevent recursive updates

    // Generate time options (12-hour format with AM/PM, 5-min increments)
    const timeOptions = [];
    for (let h = 0; h < 24; h++) {
        for (let m = 0; m < 60; m += 5) {
            let hour12 = h % 12 || 12; // convert to 12-hour
            let ampm = h < 12 ? "AM" : "PM";
            let minutes = String(m).padStart(2, '0');
            timeOptions.push(`${hour12}:${minutes} ${ampm}`);
        }
    }

    const datePicker = document.querySelector('#dateSelect')._flatpickr;

    function loadAttendanceData(date) {
        if (!date) return;

        $.ajax({
            url: '/ajax/attendance/read.php',
            type: 'GET',
            data: { date: date },
            dataType: 'json',
            success: function (response) {
                console.log('Loaded data for date ' + date + ':', response);
                if (response.status === 'error') {
                    showErrorMessage(response.message);
                    applyEmployeeFilter();
                    return;
                }
                originalData = response.employees;
                currentDate = date;
                applyEmployeeFilter();
            },
            error: function (xhr, status, error) {
                console.log('Error loading data for date ' + date + ':', error);
                showErrorMessage('Failed to load attendance data: ' + error);
                applyEmployeeFilter();
            }
        });
    }

    function applyEmployeeFilter() {
        const selectedEmployeeIds = $('#filterEmployee').val();
        let filteredData = originalData;

        if (selectedEmployeeIds && selectedEmployeeIds.length > 0 && !selectedEmployeeIds.includes("")) {
            filteredData = originalData.filter(employee => selectedEmployeeIds.includes(String(employee.id)));
        }

        const tableData = filteredData.map(emp => ({
            id: emp.id,
            employeeId: emp.employeeId || '',
            name: emp.name,
            clock_in: emp.clock_in || '',
            clock_out: emp.clock_out || '',
            break_start: emp.break_start || '',
            break_end: emp.break_end || '',
            type: emp.type || '',
            total_hours: emp.total_hours || '',
            overtime_hours: emp.overtime_hours || '',
            early_leaving: emp.early_leaving || '',
            countryId: emp.countryId || 0
        }));

        // Define columns
        const columns = [
            { data: 'employeeId', readOnly: true, width: 120 },
            { data: 'name', readOnly: true, width: 200 },
            { data: 'clock_in', type: 'dropdown', source: timeOptions, strict: true, allowInvalid: true, width: 120 },
            { data: 'clock_out', type: 'dropdown', source: timeOptions, strict: true, allowInvalid: true, width: 120 },
            { data: 'total_hours', readOnly: true, width: 120, renderer: calculatedRenderer },
            { data: 'break_start', type: 'dropdown', source: timeOptions, strict: true, allowInvalid: true, width: 120 },
            { data: 'break_end', type: 'dropdown', source: timeOptions, strict: true, allowInvalid: true, width: 120 },
            { data: 'overtime_hours', readOnly: true, width: 120, renderer: calculatedRenderer },
            { data: 'type', type: 'dropdown', source: ['', 'Business Trip', 'Holiday', 'Leave', 'Week Off'], strict: true, allowInvalid: false, width: 150 },
            { data: 'early_leaving', readOnly: true, width: 120, renderer: calculatedRenderer }
        ];

        const colHeaders = ['Employee ID', 'Employee Name', 'Clock In Time', 'Clock Out Time', 'Total Hours', 'Break Clock Out', 'Break Clock In', 'Overtime Hours', 'Type', 'Early Leaving'];

        if (hot) {
            hot.destroy();
        }

        const container = document.getElementById('attendanceTable');
        hot = new Handsontable(container, {
            data: tableData,
            colHeaders: colHeaders,
            columns: columns,
            rowHeaders: true,
            width: '100%',
            height: 'auto',
            autoWrapRow: false,
            autoWrapCol: false,
            manualColumnResize: true,
            licenseKey: 'non-commercial-and-evaluation',
            afterChange: function (changes, source) {
                if (source === 'edit' && changes && !isUpdating) {
                    isUpdating = true;
                    changes.forEach(([row]) => calculateRow(row));
                    isUpdating = false;
                    hot.render();
                }
            }
        });

        // Initial calculations after data load
        setTimeout(() => {
            if (!isUpdating) {
                isUpdating = true;
                for (let row = 0; row < hot.countRows(); row++) {
                    calculateRow(row);
                }
                isUpdating = false;
                hot.render();
            }
        }, 0);
    }

    // Custom renderer for calculated cells
    function calculatedRenderer(instance, td, row, col, prop, value, cellProperties) {
        Handsontable.renderers.TextRenderer.apply(this, arguments);
        td.style.fontWeight = 'bold';
        td.style.backgroundColor = '#f8f9fa';
        if (prop === 'overtime_hours' && value > 0) {
            td.style.color = '#28a745'; // Green for overtime
        } else if (prop === 'early_leaving' && value > 0) {
            td.innerHTML = hoursToHHMM(value); // Display in HH:MM
            td.style.color = '#dc3545'; // Red for early leaving
        }
    }

    // Convert decimal hours to HH:MM format
    function hoursToHHMM(hours) {
        if (!hours) return '0:00';
        const h = Math.floor(hours);
        const m = Math.round((hours - h) * 60);
        return `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}`;
    }

    // Convert 12h (AM/PM) to 24h format without timezone adjustment
    function convertTo24Hour(time12h) {
        if (!time12h) return null;
        const [time, modifier] = time12h.split(' ');
        let [hours, minutes] = time.split(':').map(Number);

        if (modifier === "PM" && hours !== 12) hours += 12;
        if (modifier === "AM" && hours === 12) hours = 0;

        return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}`;
    }

    // Validate 12h format
    function isValidTime(time) {
        return /^(\d{1,2}):([0-5]\d) (AM|PM)$/i.test(time);
    }

    // Convert 12h string → minutes
    function timeToMinutes(time) {
        if (!time || !isValidTime(time)) return 0;
        const [t, modifier] = time.split(' ');
        let [hours, minutes] = t.split(':').map(Number);

        if (modifier === "PM" && hours !== 12) hours += 12;
        if (modifier === "AM" && hours === 12) hours = 0;

        return hours * 60 + minutes;
    }

    // Calculate values for a row
    function calculateRow(row) {
        const clock_in = hot.getDataAtCell(row, 2);
        const clock_out = hot.getDataAtCell(row, 3);
        const break_start = hot.getDataAtCell(row, 5);
        const break_end = hot.getDataAtCell(row, 6);
        const type = hot.getDataAtCell(row, 8);
        const countryId = hot.getSourceDataAtRow(row).countryId || 0;

        let totalHours = '';
        let overtimeHours = '';
        let earlyLeaving = '';

        if (clock_in && clock_out && isValidTime(clock_in) && isValidTime(clock_out)) {
            // Base working time in minutes
            let totalMin = timeToMinutes(clock_out) - timeToMinutes(clock_in);
            let breakDuration = 0; 

            // Use recorded break if both break_start and break_end are given
            if (break_start && break_end && isValidTime(break_start) && isValidTime(break_end)) {
                breakDuration = timeToMinutes(break_end) - timeToMinutes(break_start);
            }

            // Calculate total hours
            if (totalMin > breakDuration) {
                totalMin -= breakDuration;
                totalHours = (totalMin / 60).toFixed(2); // Show in hours with decimals
                const totalHoursNum = parseFloat(totalHours);

                // Skip overtime and early leaving for certain types
                if (['Holiday', 'Leave', 'Week Off'].includes(type)) {
                    overtimeHours = '0.00';
                    earlyLeaving = '0.00';
                } else {
                    // Calculate overtime: total_hours - 8 (standard workday after 1-hour break)
                    overtimeHours = totalHoursNum > 8 ? (totalHoursNum - 8).toFixed(2) : '0.00';

                    // Determine scheduled end time based on countryId
                    const scheduledStart = countryId === 194 ? '7:00 AM' : '10:00 AM';
                    const scheduledEnd = countryId === 194 ? '4:00 PM' : '7:00 PM';
                    let scheduledEndMin = timeToMinutes(scheduledEnd);

                    // Adjust scheduled end time for break duration
                    if (breakDuration !== 60) {
                        // If recorded break differs from 1 hour, adjust end time
                        scheduledEndMin = timeToMinutes(scheduledStart) + (8 * 60) + breakDuration;
                    }

                    // Calculate early leaving
                    const clockOutMin = timeToMinutes(clock_out);
                    if (clockOutMin < scheduledEndMin) {
                        const earlyMin = scheduledEndMin - clockOutMin;
                        earlyLeaving = (earlyMin / 60).toFixed(2); // Convert to hours
                    } else {
                        earlyLeaving = '0.00';
                    }
                }
            }
        }

        // Update Total Hours, Overtime Hours, and Early Leaving columns
        hot.setDataAtCell(row, 4, totalHours); // Total Hours (col index 4)
        hot.setDataAtCell(row, 7, overtimeHours); // Overtime Hours (col index 7)
        hot.setDataAtCell(row, 9, earlyLeaving); // Early Leaving (col index 9)
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Load initial data for current date
        const initialDate = document.getElementById('dateSelect').value;
        loadAttendanceData(initialDate);

        // Handle date change
        datePicker.config.onChange.push(function (selectedDates) {
            if (selectedDates.length > 0) {
                const d = selectedDates[0];
                const date = d.toLocaleDateString('en-CA'); // YYYY-MM-DD in local time
                loadAttendanceData(date);
            } else {
                loadAttendanceData(initialDate);
            }
        });

        // Handle employee filter change
        document.getElementById('filterEmployee').addEventListener('change', function () {
            applyEmployeeFilter();
        });

        // Handle save button click
        document.getElementById('saveButton').addEventListener('click', function () {
            const date = document.getElementById('dateSelect').value;
            if (!date) {
                showErrorMessage('Please select a date to save attendance data.');
                return;
            }

            const tableData = hot.getSourceData();
            const attendanceData = tableData.map(row => ({
                id: row.id,
                clock_in: convertTo24Hour(row.clock_in?.trim()) || null,
                clock_out: convertTo24Hour(row.clock_out?.trim()) || null,
                break_start: convertTo24Hour(row.break_start?.trim()) || null,
                break_end: convertTo24Hour(row.break_end?.trim()) || null,
                type: row.type?.trim() || null,
                total_hours: row.total_hours ? parseFloat(row.total_hours) : null,
                overtime_hours: row.overtime_hours ? parseFloat(row.overtime_hours) : null,
                early_leaving: row.early_leaving ? parseFloat(row.early_leaving) : null
            })).filter(emp =>
                emp.clock_in || emp.clock_out || emp.break_start || emp.break_end || emp.type || emp.total_hours || emp.overtime_hours || emp.early_leaving
            );

            if (attendanceData.length === 0) {
                showErrorMessage('No valid attendance data to save.');
                return;
            }

            $.ajax({
                url: '/ajax/attendance/save.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ date: date, attendanceData: attendanceData }),
                dataType: 'json',
                success: function (response) {
                    console.log('Save response for date ' + date + ':', response);
                    if (response.status === 'success') {
                        showSuccessMessage(response.message);
                        loadAttendanceData(date);
                    } else {
                        showErrorMessage(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    console.log('Error saving data for date ' + date + ':', error);
                    showErrorMessage('Failed to save attendance data: ' + error);
                }
            });
        });
    });
</script>
</body>
</html>