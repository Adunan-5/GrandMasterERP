<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";
require_once('../../vendor/autoload.php');

// Silence errors during PDF generation to prevent output
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/pdf_errors.log');

$isDownload = false;
$isDownload = filter_input(INPUT_GET, 'isdownload', FILTER_VALIDATE_INT);

$poID = "";
$poID = filter_input(INPUT_GET, 'poID', FILTER_VALIDATE_INT);
if ($poID === null || $poID === false || filter_var($poID, FILTER_VALIDATE_INT) === false) {
    header("location:/po/list");
    exit();
}

//$fontName = "grandmasterbook";
$fontNameArabic = "dejavusans";
$fontName = "dejavusans";
//$fontName = "expoarabicbook";
//$fontName = "tajawal";
$fontNameCurrency = "dejavusansbook";
class CustomPDF extends TCPDF
{
    // Page Header
    public function Header()
    {
        global $pageMargin;
        global $pageWidth;
        global $poNumber;
        global $poDocument;
        global $fontName;

        $pageWidth = $this->getPageWidth();

//Main Header
        $this->Image(__DIR__ . '/../../assets/img/branding/G-Logo.png', 5, 5, 15);            // Adjust logo size and position
        $this->Image(__DIR__ . '/../../assets/img/branding/Kavero_light.png', 250, 3, 45); // Adjust Corner badge
        // $this->Image(__DIR__ . '/../../assets/img/branding/document_corner.jpg', 255, 0, 45); // Adjust Corner badge


// Title
        $this->SetFont($fontName, 'B', 16);
        $titleText   = 'Purchase Order';
        $titleWidth  = $this->GetStringWidth('Purchase Order Purchase Order'); // Approximate width without HTML
        $titleHeight = $this->getStringHeight($titleWidth, 'Purchase Order Purchase Order');
        $centerX = ($pageWidth - $titleWidth) / 2;
        $this->SetXY($centerX, 10);
        $this->writeHTMLCell($titleWidth, $titleHeight, $centerX, 10, $titleText, 0, 1, false, true, 'C', true);

// Set position for the "Quotation" text
        $centerX = (($pageWidth) - $titleWidth) / 2;
        $this->SetXY($centerX, 10);
        $this->Cell($titleWidth, $titleHeight, $titleText, 0, 1, 'C');  // '1' moves to the next line after this cell


        $lineHeight = 6;

        // Position for the left side (English content)
        $this->SetFont($fontName, '', 12);
        $this->SetXY(5, 30);

// RFQ Number
        // $this->Cell(0, $lineHeight, 'PO #: ' . $poNumber, 0, 1, 'L');

// Date
        $this->SetX(5);
        // $this->Cell(0, $lineHeight, 'Date: ' . formatDate($poDocument->poDateCreated, false), 0, 1, 'L');

// Adjust margins
        $this->SetMargins($pageMargin, $pageMargin + 50, $pageMargin);

//// Position for the left side (English content)
//        $this->SetFont($fontName, '', 12);
////$this->SetXY(5, 30);
//        $this->SetXY(5, 30);
//
//        $this->Cell(0, $lineHeight, 'RFQ #: ' . $rfpNumber, 0, 0, 'L');
//
//        $arabicLabel      = 'عرض اسعار #:';
//        $arabicLabelWidth = $this->GetStringWidth($arabicLabel);
//
//        $this->Cell(0, $lineHeight, $arabicLabel, 0, 0, 'R');
//        $this->setX($pageWidth - $arabicLabelWidth - $pageMargin - 2);
//        $this->Cell(1, $lineHeight, $rfpNumber, 0, 1, 'R');
//
//        $this->SetX(5);
//        $this->Cell(0, $lineHeight, 'Date: ' . formatDate($rfpDocument->rfpDateCreated, false), 0, 0, 'L');
//        $arabicLabel      = 'التاريخ: ';
//        $arabicLabelWidth = $this->GetStringWidth($arabicLabel);
//
//        $this->Cell(0, $lineHeight, $arabicLabel, 0, 0, 'R');
//        $this->setX($pageWidth - $arabicLabelWidth - $pageMargin - 2);
//        $this->Cell(1, $lineHeight, formatDate($rfpDocument->rfpDateCreated, false), 0, 0, 'R');


//        $this->SetMargins($pageMargin, $pageMargin + 50, $pageMargin);

    }

    // Page Footer
    public function Footer()
    {
        global $fontName;
        // Position at 1.5 cm from bottom
        $this->SetY(-15);

        $this->SetDrawColor(255, 204, 0);
        $this->SetLineWidth(0.5);
        $leftMargin  = 5;
        $rightMargin = 5;   // Space from the right edge (in mm)
        $lineStartX  = $leftMargin;
        $lineEndX    = 297 - $rightMargin;
        $lineY       = $this->GetY() - 5; // Y position for the line, 2 mm above the current Y position

        // Draw the line with the specified margins
        $this->Line($lineStartX, $lineY, $lineEndX, $lineY);

        // Thank you note
       $this->SetFont($fontName, '', 10);
       $this->Cell(0, 6, 'Thanks for choosing GrandMaster – The sole supplier in middle east for EU & US brands', 0, 1, 'C');
       $this->Cell(0, 6, 'شكرًا لاختيارك جراند ماستر - المورد الوحيد في الشرق الأوسط للعلامات التجارية الأوروبية والأمريكية', 0, 1, 'C');
    }
}

//////////////////////////////////

$poDocument = new PurchaseOrder();
$poDocument->loadById($poID);
$poNumber = getPONumberFromDocumentID($poID);

$companyInfo = new Company();
$companyInfo->loadById(1);

$supplierInfo = new Supplier();
$supplierInfo->loadById($poDocument->supplierId);

$warehouseInfo = new Warehouse();
$warehouseInfo->loadById($poDocument->warehouseId);

//////////////////////////////////


// Create PDF instance in landscape mode
$pageMargin = 5;
$pdf        = new CustomPDF('L', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('GrandMaster');
$pdf->SetTitle('PO ' . $poNumber);
$pdf->SetMargins($pageMargin, $pageMargin, $pageMargin);
$pdf->AddPage();


// Page width (in mm) for A4 landscape is 297 mm, with some margins taken into account
$pageWidth = 297 - 10;                                // 5 mm left and right margins
$pageWidth = $pdf->getPageWidth();

// Table for Our Details and Supplier Details
$pdf->SetFont($fontName, '', 10);
$startX = $pageMargin;
$startY = 35;
$columnWidth = ($pageWidth - 2 * $pageMargin) / 2; // Two equal columns
$rowHeight = 5;
$headerHeight = 7;

// First Row: Our Details Heading
$pdf->SetXY($startX, $startY);
$pdf->SetFont($fontName, 'B', 10);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell($columnWidth, $headerHeight, 'Our Details', 1, 0, 'C', true);
$pdf->Cell($columnWidth, $headerHeight, 'تفاصيلنا', 1, 1, 'C', true);

// Second Row: Our Company Details
$pdf->SetFont($fontName, '', 10);
// Build English address dynamically
$companyAddressEnParts = [];
if (!empty($companyInfo->addressLine1)) $companyAddressEnParts[] = $companyInfo->addressLine1;
if (!empty($companyInfo->addressLine2)) $companyAddressEnParts[] = $companyInfo->addressLine2;
if (!empty($companyInfo->cityId)) $companyAddressEnParts[] = getCityFromID($companyInfo->cityId);
if (!empty($companyInfo->stateId)) $companyAddressEnParts[] = getStateFromID($companyInfo->stateId);
if (!empty($companyInfo->countryId)) $companyAddressEnParts[] = getCountryFromID($companyInfo->countryId);
if (!empty($companyInfo->postalCode)) $companyAddressEnParts[] = $companyInfo->postalCode;
$companyAddressEn = implode(", ", $companyAddressEnParts);

// Build Arabic address dynamically
$companyAddressArParts = [];
if (!empty($companyInfo->addressLine1)) $companyAddressArParts[] = $companyInfo->addressLine1;
if (!empty($companyInfo->addressLine2)) $companyAddressArParts[] = $companyInfo->addressLine2;
if (!empty($companyInfo->cityId)) $companyAddressArParts[] = getCityFromID($companyInfo->cityId);
if (!empty($companyInfo->stateId)) $companyAddressArParts[] = getStateFromID($companyInfo->stateId);
if (!empty($companyInfo->countryId)) $companyAddressArParts[] = getCountryFromID($companyInfo->countryId);
if (!empty($companyInfo->postalCode)) $companyAddressArParts[] = $companyInfo->postalCode;
$companyAddressAr = implode("، ", $companyAddressArParts);

$companyDetailsEn = "\n{$companyInfo->companyName}\n{$companyAddressEn}\nCR Number: {$companyInfo->companyCRNumber}\nVAT Number: {$companyInfo->vatNumber}\n\n";
$companyDetailsAr = "\n{$companyInfo->companyNameAr}\n{$companyAddressAr}\nرقم السجل التجاري: " . convertToArabicNumbers($companyInfo->companyCRNumber) . "\nرقم الضريبة: " . convertToArabicNumbers($companyInfo->vatNumber) . "\n\n";
$companyHeight = max(
    $pdf->getStringHeight($columnWidth, $companyDetailsEn),
    $pdf->getStringHeight($columnWidth, $companyDetailsAr)
);
$pdf->SetXY($startX, $startY + $headerHeight);
$pdf->MultiCell($columnWidth, $companyHeight, $companyDetailsEn, 1, 'L');
$pdf->SetXY($startX + $columnWidth, $startY + $headerHeight);
$pdf->MultiCell($columnWidth, $companyHeight, $companyDetailsAr, 1, 'R');

// Third Row: Supplier Details Heading
$afterCompanyY = $startY + $headerHeight + $companyHeight;
$pdf->SetXY($startX, $afterCompanyY);
$pdf->SetFont($fontName, 'B', 10);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell($columnWidth, $headerHeight, 'Supplier Details', 1, 0, 'C', true);
$pdf->Cell($columnWidth, $headerHeight, 'تفاصيل العميل', 1, 1, 'C', true);

// Fourth Row: Supplier Details
$pdf->SetFont($fontName, '', 10);
// Build English supplier address dynamically
$supplierAddressEnParts = [];
if (!empty($supplierInfo->addressLine1)) $supplierAddressEnParts[] = $supplierInfo->addressLine1;
if (!empty($supplierInfo->addressLine2)) $supplierAddressEnParts[] = $supplierInfo->addressLine2;
if (!empty($supplierInfo->cityId)) $supplierAddressEnParts[] = getCityFromID($supplierInfo->cityId);
if (!empty($supplierInfo->stateId)) $supplierAddressEnParts[] = getStateFromID($supplierInfo->stateId);
if (!empty($supplierInfo->countryId)) $supplierAddressEnParts[] = getCountryFromID($supplierInfo->countryId);
if (!empty($supplierInfo->postalCode)) $supplierAddressEnParts[] = $supplierInfo->postalCode;
$supplierAddressEn = implode(", ", $supplierAddressEnParts);

// Build Arabic supplier address dynamically
$supplierAddressArParts = [];
if (!empty($supplierInfo->addressLine1)) $supplierAddressArParts[] = $supplierInfo->addressLine1;
if (!empty($supplierInfo->addressLine2)) $supplierAddressArParts[] = $supplierInfo->addressLine2;
if (!empty($supplierInfo->cityId)) $supplierAddressArParts[] = getCityFromID($supplierInfo->cityId);
if (!empty($supplierInfo->stateId)) $supplierAddressArParts[] = getStateFromID($supplierInfo->stateId);
if (!empty($supplierInfo->countryId)) $supplierAddressArParts[] = getCountryFromID($supplierInfo->countryId);
if (!empty($supplierInfo->postalCode)) $supplierAddressArParts[] = $supplierInfo->postalCode;
$supplierAddressAr = implode("، ", $supplierAddressArParts);

$supplierDetailsEn = "\n{$supplierInfo->companyName}\n{$supplierAddressEn}\nCR Number: {$supplierInfo->companyCRNumber}\nVAT Number: {$supplierInfo->vatNumber}\n\n";
$supplierDetailsAr = "\n{$supplierInfo->companyNameAr}\n{$supplierAddressAr}\nرقم السجل التجاري: " . convertToArabicNumbers($supplierInfo->companyCRNumber) . "\nرقم الضريبة: " . convertToArabicNumbers($supplierInfo->vatNumber) . "\n\n";
$supplierHeight = max(
    $pdf->getStringHeight($columnWidth, $supplierDetailsEn),
    $pdf->getStringHeight($columnWidth, $supplierDetailsAr)
);
$pdf->SetXY($startX, $afterCompanyY + $headerHeight);
$pdf->MultiCell($columnWidth, $supplierHeight, $supplierDetailsEn, 1, 'L');
$pdf->SetXY($startX + $columnWidth, $afterCompanyY + $headerHeight);
$pdf->MultiCell($columnWidth, $supplierHeight, $supplierDetailsAr, 1, 'R');

// Fifth Row: PO Headers (6 columns with Arabic+English in bold)
$headerStartY = $afterCompanyY + $headerHeight + $supplierHeight;
$headerWidth = ($pageWidth - 2 * $pageMargin) / 6;
$poHeaderHeight = 14;
$lineHeight = 4.5;
$verticalPadding = ($poHeaderHeight - ($lineHeight * 2)) / 2;

$poHeaders = [
    ['رقم الفاتورة', 'PO No.'],
    ['تاريخ الفاتورة', 'PO Date'],
    ['شروط الدفع', 'Payment Terms'],
    ['تاريخ التوريد', 'Estimated Arrival Date'],
    ['رقم أمر شراء العميل', 'Supplier Quotation No.'],
    ['مرجعنا', 'Our Reference']
];


$pdf->SetFont($fontName, 'B', 9); // Bold for both Arabic & English
foreach ($poHeaders as $index => $headerPair) {
    $xPos = $startX + $index * $headerWidth;
    $yPos = $headerStartY;

    $pdf->SetFillColor(230, 230, 230);
    $pdf->Rect($xPos, $yPos, $headerWidth, $poHeaderHeight, 'F');
    $pdf->Rect($xPos, $yPos, $headerWidth, $poHeaderHeight);

    // Arabic Line (Bold)
    $pdf->SetXY($xPos, $yPos + $verticalPadding);
    $pdf->Cell($headerWidth, $lineHeight, $headerPair[0], 0, 2, 'C', false);

    // English Line (Bold)
    $pdf->SetXY($xPos, $yPos + $verticalPadding + $lineHeight);
    $pdf->Cell($headerWidth, $lineHeight, $headerPair[1], 0, 0, 'C', false);
}


// Sixth Row: PO Values
$pdf->SetFont($fontName, '', 10);
$valueHeight = 8;
$values = [
    $poNumber,
    formatDate($poDocument->poDateCreated, false),
    $poDocument->getPaymentTermName(),
    formatDate($poDocument->etaDate, false) ?? "-",
    $poDocument->supplierQuotationNumber ?? "-",
    $poDocument->reference ?? "-"
];

$pdf->SetXY($startX, $headerStartY + $poHeaderHeight);
foreach ($values as $index => $value) {
    $xPos = $startX + $index * $headerWidth;
    $pdf->SetXY($xPos, $headerStartY + $poHeaderHeight);
    $pdf->Cell($headerWidth, $valueHeight, $value, 1, 0, 'C');
}

// Space before product table
$productTableY = $headerStartY + $poHeaderHeight + $valueHeight + 10;

//Customer Information
$pdf->SetFont($fontName, '', 10);

// Define a starting position for the table
$startX     = 5;
$startY     = 50;
$rowHeight  = 5;
$labelWidth = 30;
$valueWidth = 65;
$space      = 2;

//Line Items table header
// Define the initial header widths and scale them
$headers = [
    ['NO.', 'رقم', 'serie'], // English, Arabic, Italian
    [' ', ' ', ' '], // Image column (no text, so all empty)
    ['Item', 'رقم الصنف والوصف', 'Articolo'], // English, Arabic, Italian
    ['HS Code', 'رمز المنتج', 'Codice HS'], // English, Arabic, Italian (empty for Italian as requested)
    ['Qty', 'الكمية', 'quantità'], // English, Arabic, Italian
    ['Price', 'السعر', 'Prezzo'], // English, Arabic, Italian
    ['Total Value', 'القيمة الإجمالية', 'Totale'] // English, Arabic, Italian
];
//$headerWidths = [8, 25, 45, 12, 18, 20, 25, 15, 25, 30]; // Initial widths
//$headerWidths = [10, 45, 12, 18, 20, 25, 15, 25, 30]; // Initial widths
$headerWidths = [20, 40, 90, 30, 30, 20, 30];


// Calculate the total width of the table
$totalWidth = array_sum($headerWidths) + 7;

// Calculate scale factor to fit the table within the page width
$scaleFactor = $pageWidth / $totalWidth;

// Adjust header widths proportionally
$scaledHeaderWidths = array_map(function ($width) use ($scaleFactor) {
    return $width * $scaleFactor;
}, $headerWidths);


// Define the base path for images
$imageBasePath = $_SERVER['DOCUMENT_ROOT'] . '/uploads/sparepart-images/';

//Build the line Items
$items            = array();
$resLineItems     = $db->query("SELECT * FROM po_line_items WHERE `poId` = ?s", $poDocument->poId);
$itemSerialNumber = 0;
while ($rowLineItem = mysqli_fetch_assoc($resLineItems)) {

    $imageUrl = getImageForSparepartID($rowLineItem['itemId']);
    $imageFullPath = $imageBasePath . $imageUrl;
    $partNumber = getPartNumberForSparepartID($rowLineItem['itemId']);
    $description = getItemDescriptionForSparepartID($rowLineItem['itemId']);
    $brandName = getItemBrandNameForSparepartID($rowLineItem['itemId']);

    $item    = [
        (string)++$itemSerialNumber, // Serial Number
        file_exists($imageFullPath) ? $imageFullPath : '', // Full image path if it exists
        ['partNumber' => $partNumber, 'description' => $description,  'brand' => $brandName], // Part Number & Description as an array
        !empty($rowLineItem['hsCode']) ? $rowLineItem['hsCode'] : getHSCodeForSparepartID($rowLineItem['itemId']), // HS Code
        $rowLineItem['quantity'], // Quantity
        $rowLineItem['costPrice'],
        $rowLineItem['subTotal'],
    ];
    $items[] = $item;
}

//$pdf->SetXY(5, 95);
// Set up the PDF document
//$pdf->SetMargins(10, 10, 10);              // Left, Top, Right margins
$pdf->SetAutoPageBreak(TRUE, 25);

// Print headers with scaled widths
$pdf->SetFont($fontName, 'B', 10);
$pdf->SetFillColor(230, 230, 230);

$headerHeight = 10;                 // Uniform height for header cells

$pdf->SetXY(5, 140);
// Line Item Table Header
foreach ($headers as $key => $headerTriplet) {
    $cellWidth = $scaledHeaderWidths[$key];

    // Prepare the content for the header cell with three lines (English, Arabic, Italian)
    $headerText = $headerTriplet[0] . "\n" . $headerTriplet[1] . "\n" . $headerTriplet[2];

    // Save current X and Y positions
    $xStart = $pdf->GetX();
    $yStart = $pdf->GetY();
    $pdf->MultiCell(
        $cellWidth,
        $headerHeight / 3, // Divide height into three equal parts for each language
        $headerText,
        1, // Border
        'C', // Center alignment
        true // Fill background
    );

    // Reset cursor position for the next cell
    $pdf->SetXY($xStart + $cellWidth, $yStart);
}

// Move to the next line after the header
$pdf->Ln();

// Set font for body content
$pdf->SetFont($fontName, '', 10);

// Print each row with scaled column widths
foreach ($items as $item) {
    $rowHeights = [];
    $defaultHeight = 10;

    // Calculate the maximum row height
    foreach ($item as $key => $value) {
        $cellWidth = $scaledHeaderWidths[$key];

        if ($key == 1 && !empty($value)) { // Image column
            $rowHeights[] = 30; // Fixed image height
        } elseif ($key == 2 && is_array($value)) { // Item column
            $partNumber = $value['partNumber'];
            $description = $value['description'];
            $brandName = $value['brand'];

            $pdf->SetFont($fontName, 'B', 10);
            $partHeight = $pdf->getStringHeight($cellWidth, $partNumber);

            $pdf->SetFont($fontName, '', 8);
            $descHeight = $pdf->getStringHeight($cellWidth, $description);

            $manufacturerLine = 'Manufacturer: ' . $brandName;
            $combinedLineHeight = $pdf->getStringHeight($cellWidth, $manufacturerLine);

            $rowHeights[] = $partHeight + $descHeight + $combinedLineHeight +10;
        } else {
            $pdf->SetFont($fontName, '', 10);
            $rowHeights[] = $pdf->getStringHeight($cellWidth, $value) + $defaultHeight;
        }
    }

    $maxHeight = max($rowHeights);


    // Check if the row will overflow the page
    if ($pdf->GetY() + $maxHeight > $pdf->getPageHeight() - $pdf->getMargins()['bottom']) {
        $pdf->AddPage();
        printTableHeaders($pdf, $headers, $scaledHeaderWidths);
    }


    foreach ($item as $key => $value) {
        $cellWidth = $scaledHeaderWidths[$key];
        $xStart = $pdf->GetX();
        $yStart = $pdf->GetY();

        // Draw the cell border
        $pdf->Rect($xStart, $yStart, $cellWidth, $maxHeight);

        if ($key == 1 && !empty($value)) { // Image column
            $imageWidth = 28; // Width of the image in mm
            $imageHeight = 28; // Height of the image in mm
            $verticalOffset = ($maxHeight - $imageHeight) / 2; // Center vertically
            $horizontalOffset = ($cellWidth - $imageWidth) / 2; // Center horizontally
            $pdf->Image($value, $xStart + $horizontalOffset, $yStart + $verticalOffset, $imageWidth, $imageHeight, '', '', '', false, 300, '', false, false, 1, false, false, false);
            $pdf->SetXY($xStart + $cellWidth, $yStart);
        } elseif ($key == 2 && is_array($value)) { // Item column
            $partNumber = $value['partNumber'];
            $description = $value['description'];
            $brandName = $value['brand'];

            // Calculate the height for each text element
            $pdf->SetFont($fontName, 'B', 10);
            $partHeight = $pdf->getStringHeight($cellWidth, $partNumber);

            $pdf->SetFont($fontName, '', 8);
            $descHeight = $pdf->getStringHeight($cellWidth, $description);

            $pdf->SetFont($fontName, 'B', 8);
            $manufacturerLabel = 'Manufacturer: ';
            $labelWidth = $pdf->GetStringWidth($manufacturerLabel);
            $manufacturerHeight = $pdf->getStringHeight($cellWidth - $labelWidth, $brandName);

            // Calculate total content height and vertical offset
            $contentHeight = $partHeight + $descHeight + max($manufacturerHeight, 5);
            $verticalOffset = ($maxHeight - $contentHeight) / 2;

            // Print part number in bold
            $pdf->SetFont($fontName, 'B', 10);
            $pdf->SetXY($xStart, $yStart + $verticalOffset);
            $pdf->MultiCell($cellWidth, 5, $partNumber, 0, 'L', false);

            // Print description with smaller font
            $pdf->SetFont($fontName, '', 8);
            $pdf->SetX($xStart);
            $pdf->MultiCell($cellWidth, 5, $description, 0, 'L', false);

            // Print manufacturer label and brand name on the same line
            $pdf->SetX($xStart);
            $pdf->SetFont($fontName, 'B', 8);
            $pdf->Cell($labelWidth, 5, $manufacturerLabel, 0, 0, 'L');

            $pdf->SetFont($fontName, '', 8);
            $pdf->Cell(0, 5, $brandName, 0, 1, 'L');

            $pdf->SetXY($xStart + $cellWidth, $yStart);
        } else {
            // For other columns, calculate vertical alignment and print value
            $contentHeight = $pdf->getStringHeight($cellWidth, $value);
            $verticalOffset = ($maxHeight - $contentHeight) / 2;

            $alignment = ($key == 2) ? 'L' : 'C';

            $pdf->SetXY($xStart, $yStart + $verticalOffset);
            $pdf->SetFont($fontName, '', 10);
            $pdf->MultiCell($cellWidth, $contentHeight, $value, 0, $alignment, false);

            $pdf->SetXY($xStart + $cellWidth, $yStart);
        }
    }

    $pdf->Ln($maxHeight);
}
$pdf->AddPage();

$pdf->SetXY(5, $pdf->GetY() -8); // Adjust Y-coordinate to place below the table

// Add total amount details (English and Arabic on the same line)
$totalStartY = $pdf->GetY() - 8 + 10; // Adjust Y-coordinate to place below the table
if ($totalStartY + 50 > $pdf->getPageHeight() - $pdf->getMargins()['bottom']) {
    $pdf->AddPage();
    $totalStartY = 60;
}
$colWidths = [110, 120, 50]; // Total 280 mm
$rowHeight = 10;
$totalRows = 3;
$totalTableHeight = $totalRows * $rowHeight;

// Amount in words

$totalAmount = getTotalAmountByPOID($poID);
$totalAmountWithShipping = getTotalAmountWithShippingByPOID($poID);
$shippingAmount = $totalAmountWithShipping - $totalAmount;
$amountInWordsEn = numberToEnglishWords($totalAmountWithShipping);
$amountInWordsAr = numberToArabicWords($totalAmountWithShipping);
$amountInWordsText = "<div style='display: table-cell; vertical-align: middle; height: {$totalTableHeight}mm;'><strong>Amount in Words / كتابة المبلغ </strong><br><br>" . $amountInWordsEn . "<br><br>" . $amountInWordsAr . "</div>";

// Total amount details
$totalDetails = [
    [
        "Total Amount / المبلغ الإجمالي",
        '₥ ' . $totalAmount
    ],
    [
        "Shipping Amount / تكلفة الشحن",
        '₥ ' . $shippingAmount
    ],
    [
        "Total Amount with Shipping / المبلغ الإجمالي مع الشحن",
        '₥ ' . $totalAmountWithShipping
    ]
];

// Enable RTL support for Arabic
// $pdf->setRTL(true);

// Draw Amount in Words cell
$pdf->SetFont($fontName, '', 11);
$pdf->SetXY($pageMargin + 5, $totalStartY);
$amountInWordsHeight = $pdf->getStringHeight($colWidths[0], $amountInWordsText);
$tableHeight = max($amountInWordsHeight, $totalTableHeight);
$pdf->writeHTMLCell($colWidths[0], $tableHeight, $pageMargin + 5, $totalStartY, $amountInWordsText, 1, 0, false, true, 'L', true);

// Draw total amount rows
$currentX = $pageMargin + 5 + $colWidths[0];
$currentY = $totalStartY;
foreach ($totalDetails as $row) {
    // Label column (English and Arabic with / separator)
    $pdf->setRTL(false); // Disable RTL for mixed text
    $pdf->SetFont($fontNameCurrency, '', 9);
    $pdf->SetXY($currentX, $currentY);
    
    // Split the label to handle English and Arabic separately
    $labelParts = explode(' / ', $row[0]);
    $englishLabel = $labelParts[0];
    $arabicLabel = $labelParts[1] ?? '';
    
    // Create HTML to control text direction
    $labelText = $englishLabel . ' / <span style="unicode-bidi: embed; direction: rtl;">' . $arabicLabel . '</span>';
    $pdf->writeHTMLCell($colWidths[1], $rowHeight, $currentX, $currentY, $labelText, 1, 0, false, true, 'L', true);

    // Amount column
    $pdf->SetXY($currentX + $colWidths[1], $currentY);
    $pdf->Cell($colWidths[2], $rowHeight, $row[1], 1, 0, 'C');
    $currentY += $rowHeight;
}

// Reset RTL to default (false) after the table
$pdf->setRTL(false);


// Approved By Table
$tableStartX = 24;
$gapBetweenSections = 33; // Add 15mm of space between totals and approved by
$tableStartY = $pdf->GetY() + $gapBetweenSections;
$colWidths = [100, 75, 75]; // Total 280 mm, leaving some margin
$headerHeight = 10;

// Check if table fits on current page
if ($tableStartY + 50 > $pdf->getPageHeight() - $pdf->getMargins()['bottom']) {
    $pdf->AddPage();
    $tableStartY = 10;
}

// Draw headers
$pdf->SetFont($fontName, 'B', 10);
$pdf->SetFillColor(230, 230, 230);
$currentX = $tableStartX;
$headers = ['Approved By', 'Name', 'Signature'];
foreach ($headers as $i => $header) {
    $pdf->SetXY($currentX, $tableStartY);
    $pdf->Cell($colWidths[$i], $headerHeight, $header, 1, 0, 'C', true);
    $currentX += $colWidths[$i];
}

// Prepare Approved By content
$pdf->SetFont($fontName, '', 9);
$approvedBy = 
    "";
$approvedByHeight = $pdf->getStringHeight($colWidths[0], $approvedBy);
$size = 5;
$rowHeight = max($approvedByHeight + 10, $size + 10); // Dynamic height with padding

// Check if second row fits
$rowY = $tableStartY + $headerHeight;
if ($rowY + $rowHeight > $pdf->getPageHeight() - $pdf->getMargins()['bottom']) {
    $pdf->AddPage();
    $tableStartY = 10;
    $rowY = $tableStartY + $headerHeight;
    // Redraw headers
    $pdf->SetFont($fontName, 'B', 10);
    $pdf->SetFillColor(230, 230, 230);
    $currentX = $tableStartX;
    foreach ($headers as $i => $header) {
        $pdf->SetXY($currentX, $tableStartY);
        $pdf->Cell($colWidths[$i], $headerHeight, $header, 1, 0, 'C', true);
        $currentX += $colWidths[$i];
    }
}

// Draw second row
$currentX = $tableStartX;
//  Approved By column
$pdf->SetXY($currentX, $rowY);
$pdf->writeHTMLCell($colWidths[0], $rowHeight, $currentX, $rowY, $approvedBy, 1, 0, false, true, 'L');
$currentX += $colWidths[0];

// Name and Signature (empty)
for ($i = 1; $i <= 2; $i++) {
    $pdf->SetXY($currentX, $rowY);
    $pdf->Cell($colWidths[$i], $rowHeight, '', 1, 0, 'C');
    $currentX += $colWidths[$i];
}

if ($isDownload == 1)
// Output the PDF
    $pdf->Output('PO_' . $poNumber . '.pdf', 'D');
else
    $pdf->Output('PO_' . $poNumber . '.pdf', 'I');


function printTableHeaders($pdf, $headers, $scaledHeaderWidths) {
    global $fontName;
    $pdf->SetFont($fontName, 'B', 10);
    $pdf->SetFillColor(230, 230, 230);
    $headerHeight = 10;

    $pdf->SetXY(5, $pdf->GetY() + 2); // Adjust the Y-coordinate to add a small margin from the top
    foreach ($headers as $key => $headerTriplet) {
        $cellWidth = $scaledHeaderWidths[$key];

        // Prepare the content for the header cell with three lines (English, Arabic, Italian)
        $headerText = $headerTriplet[0] . "\n" . $headerTriplet[1] . "\n" . $headerTriplet[2];

        // Save current X and Y positions
        $xStart = $pdf->GetX();
        $yStart = $pdf->GetY();
        $pdf->MultiCell(
            $cellWidth,
            $headerHeight / 3, // Divide height into three equal parts for each language
            $headerText,
            1, // Border
            'C', // Center alignment
            true // Fill background
        );

        // Reset cursor position for the next cell
        $pdf->SetXY($xStart + $cellWidth, $yStart);
    }

    // Move to the next line after the header
    $pdf->Ln();
}