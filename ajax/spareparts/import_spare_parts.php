<?php
header('Content-Type: text/plain');
include_once __DIR__ . "/../../includes/baseIncludes.php";
require_once __DIR__ . "/../../vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\IOFactory;

try {
    // Check if file was uploaded
    if (!isset($_FILES['importFile']) || $_FILES['importFile']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("No file uploaded or upload error.");
    }

    // Validate file extension
    $allowedExtensions = ['xlsx', 'xls'];
    $fileExtension = strtolower(pathinfo($_FILES['importFile']['name'], PATHINFO_EXTENSION));
    if (!in_array($fileExtension, $allowedExtensions)) {
        throw new Exception("Invalid file format. Only .xlsx or .xls files are allowed.");
    }

    // Load the Excel file
    $filePath = $_FILES['importFile']['tmp_name'];
    $spreadsheet = IOFactory::load($filePath);
    $worksheet = $spreadsheet->getActiveSheet();
    $rows = $worksheet->toArray();

    // Validate headers
    $expectedHeaders = [
        'Internal Reference',
        'Name',
        'Description',
        'Brand Name',
        'Lead Time',
        'Origin',
        'Sales Price',
        'UOM',
        'Cost',
        'HS Code',
        'HS Percentage'
    ];
    if (empty($rows) || array_map('trim', $rows[0]) !== $expectedHeaders) {
        throw new Exception("Invalid Excel template. Expected headers: " . implode(", ", $expectedHeaders));
    }

    // Fetch all brand names and IDs for fuzzy matching
    $brands = [];
    $res = $db->query("SELECT brandId, brandName FROM brands WHERE active = 1");
    while ($row = mysqli_fetch_assoc($res)) {
        $brands[strtolower($row['brandName'])] = (int)$row['brandId'];
    }

    // Fetch all UOM names and IDs for fuzzy matching
    $uoms = [];
    $res = $db->query("SELECT uomId, uomName FROM uoms");
    while ($row = mysqli_fetch_assoc($res)) {
        $uoms[strtolower($row['uomName'])] = (int)$row['uomId'];
    }

    // Helper function for fuzzy matching
    function findClosestMatch($input, $options, $maxDistance = 2) {
        $input = strtolower(trim($input));
        if (isset($options[$input])) {
            return $options[$input]; // Exact match
        }
        $closestMatch = null;
        $minDistance = PHP_INT_MAX;
        foreach ($options as $optionName => $id) {
            $distance = levenshtein($input, $optionName);
            if ($distance <= $maxDistance && $distance < $minDistance) {
                $minDistance = $distance;
                $closestMatch = $id;
            }
        }
        return $closestMatch;
    }

    // Process rows (skip header row)
    $importedCount = 0;
    $errors = [];
    for ($i = 1; $i < count($rows); $i++) {
        // Handle null values before trimming
        $row = array_map(function($value) { return is_string($value) ? trim($value) : ($value === null ? '' : (string)$value); }, $rows[$i]);

        // Map and sanitize row data
        $internalReference = filter_var($row[0], FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: null;
        $name = filter_var($row[1], FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';
        $description = filter_var($row[2], FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: null;
        $brandName = filter_var($row[3], FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: null;
        $leadTime = filter_var($row[4], FILTER_VALIDATE_FLOAT) ?: 14; // Default to 14
        $origin = filter_var($row[5], FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: null;
        // $salesPrice = filter_var($row[6], FILTER_VALIDATE_FLOAT) ?: false;
        $salesPrice = filter_var($row[6], FILTER_VALIDATE_FLOAT);
        $uomName = filter_var($row[7], FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: null;
        // $cost = filter_var($row[8], FILTER_VALIDATE_FLOAT) ?: false;
        $cost = filter_var($row[8], FILTER_VALIDATE_FLOAT);
        $hsCode = filter_var($row[9], FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: null;
        $hsPercentage = filter_var($row[10], FILTER_VALIDATE_FLOAT) ?: 0.00; // Default to 0.00

        // Validate required fields
        if (!$internalReference) {
            $errors[] = "Row " . ($i + 1) . ": Internal Reference is required.";
            continue;
        }
        // if (!$name) {
        //     $errors[] = "Row " . ($i + 1) . ": Name is required.";
        //     continue;
        // }
        // if (!$salesPrice || $salesPrice < 0) {
        //     $errors[] = "Row " . ($i + 1) . ": Sales Price is required and must be non-negative.";
        //     continue;
        // }
        // if (!$cost || $cost < 0) {
        //     $errors[] = "Row " . ($i + 1) . ": Cost is required and must be non-negative.";
        //     continue;
        // }
        if ($salesPrice < 0) {
            $errors[] = "Row " . ($i + 1) . ": Sales Price is required and must be non-negative.";
            continue;
        }
        if ($cost < 0) {
            $errors[] = "Row " . ($i + 1) . ": Cost is required and must be non-negative.";
            continue;
        }

        // Get brandId using getBrandIDByName or fuzzy matching
        $brandId = null;
        if ($brandName) {
            $brandId = getBrandIDByName($brandName); // Try exact match
            if (!$brandId) {
                $brandId = findClosestMatch($brandName, $brands, 2); // Try fuzzy match
                if (!$brandId) {
                    $errors[] = "Row " . ($i + 1) . ": Invalid or unknown Brand Name '$brandName'.";
                    continue;
                }
            }
        } else {
            $errors[] = "Row " . ($i + 1) . ": Brand Name is required.";
            continue;
        }

        // Get uomId using getUOMIDByName or fuzzy matching
        $uomId = null;
        if ($uomName) {
            $uomId = getUOMIDByName($uomName); // Try exact match
            if (!$uomId) {
                $uomId = findClosestMatch($uomName, $uoms, 2); // Try fuzzy match
                if (!$uomId) {
                    $errors[] = "Row " . ($i + 1) . ": Invalid or unknown UOM Name '$uomName'.";
                    continue;
                }
            }
        } else {
            $errors[] = "Row " . ($i + 1) . ": UOM is required.";
            continue;
        }


        // Default image
        $imageName = 'no-image-placeholder.png';

        // Insert into database
        $res = $db->query(
            "INSERT INTO `spareparts` (
                `internalReference`,
                `name`,
                `image`,
                `description`,
                `brandId`,
                `leadTime`,
                `origin`,
                `salesPrice`,
                `uomId`,
                `cost`,
                `hsCode`,
                `hsPercentage`,
                `createdBy`
            ) VALUES (
                ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s
            )",
            $internalReference,
            $name,
            $imageName,
            $description,
            $brandId,
            $leadTime,
            $origin,
            $salesPrice,
            $uomId,
            $cost,
            $hsCode,
            $hsPercentage,
            getUserIDOfCurrentUser()
        );

        $sparepartId = $db->insertId();

        recordSparepartHistory($sparepartId, OT_NEW_SPAREPART, logActivity(OT_NEW_SPAREPART), "Sparepart Created through Excel Template - " . getPartNumberForSparepartID($sparepartId) );

        if ($res) {
            $importedCount++;
        } else {
            $errors[] = "Row " . ($i + 1) . ": Failed to insert spare part.";
        }
    }

    // Prepare response
    if ($importedCount > 0 && empty($errors)) {
        echo "SUCCESS|Successfully imported $importedCount spare parts.";
    } elseif ($importedCount > 0) {
        echo "SUCCESS|Imported $importedCount spare parts with " . count($errors) . " errors: " . implode("; ", $errors);
    } else {
        throw new Exception("No spare parts imported. Errors: " . implode("; ", $errors));
    }

} catch (Exception $e) {
    error_log("Error importing spare parts: " . $e->getMessage());
    echo "ERROR|Failed to import spare parts. Error: " . $e->getMessage();
}
?>