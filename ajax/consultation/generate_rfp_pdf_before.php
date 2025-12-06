<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";
require_once('../../vendor/autoload.php');

// Silence errors during PDF generation to prevent output
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/pdf_errors.log');

$isDownload = false;
$isDownload = filter_input(INPUT_GET, 'isdownload', FILTER_VALIDATE_INT);

$rfpID = "";
$rfpID = filter_input(INPUT_GET, 'rfpID', FILTER_VALIDATE_INT);
if ($rfpID === null || $rfpID === false || filter_var($rfpID, FILTER_VALIDATE_INT) === false) {
    header("location:/consultation/rfp/list");
    exit();
}

//$fontName = "grandmasterbook";
$fontNameArabic = "dejavusans";
$fontName = "dejavusans";
//$fontName = "expoarabicbook";
//$fontName = "tajawal";
class CustomPDF extends TCPDF
{
    // Page Header
    public function Header()
    {
        global $pageMargin;
        global $pageWidth;
        global $rfpNumber;
        global $rfpDocument;
        global $fontName;

        $pageWidth = $this->getPageWidth();
        $this->SetMargins($pageMargin, 60, $pageMargin);

//Main Header
        $this->Image(__DIR__ . '/../../assets/img/branding/G-Logo.png', 5, 5, 15);            // Adjust logo size and position
        $rightLogoX = $pageWidth - 45 - 5;
        $this->Image(__DIR__ . '/../../assets/img/branding/Logo_IT.png', $rightLogoX, 5, 45);


// Title
        $this->SetFont($fontName, 'B', 16);
        $titleText   = 'Request for Proposal';
        $titleWidth  = $this->GetStringWidth('Request for Proposal Request for Proposal'); // Approximate width without HTML
        $titleHeight = $this->getStringHeight($titleWidth, 'Request for Proposal Request for Proposal');
        $centerX = ($pageWidth - $titleWidth) / 2;
        $this->SetXY($centerX, 10);
        $this->writeHTMLCell($titleWidth, $titleHeight, $centerX, 10, $titleText, 0, 1, false, true, 'C', true);

        $lineHeight = 6;

        // Position for the left side (English content)
        $this->SetFont($fontName, '', 12);
        $this->SetXY(5, 30);

// Adjust margins
        $this->SetMargins($pageMargin, $pageMargin + 50, $pageMargin);

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

$rfpDocument = new ConsultationRFPDocument();
$rfpDocument->loadById($rfpID);
$rfpNumber = getRFPNumberFromConsultationDocumentID($rfpID);

$companyInfo = new Company();
$companyInfo->loadById(2);

$supplierInfo = new Supplier();
$supplierInfo->loadById($rfpDocument->supplierId);

//////////////////////////////////


// Create PDF instance in landscape mode
$pageMargin = 5;
$pdf        = new CustomPDF('L', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('GrandMaster');
$pdf->SetTitle('RFP ' . $rfpNumber);
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

// Fifth Row: RFP Headers (6 columns with Arabic+English in bold)
$headerStartY = $afterCompanyY + $headerHeight + $supplierHeight;
$headerWidth = ($pageWidth - 2 * $pageMargin) / 6;
$rfpHeaderHeight = 14;
$lineHeight = 4.5;
$verticalPadding = ($rfpHeaderHeight - ($lineHeight * 2)) / 2;

$rfpHeaders = [
    ['رقم الفاتورة', 'RFP No.'],
    ['تاريخ الفاتورة', 'RFP Date'],
    ['شروط الدفع', 'Payment Terms'],
    ['تاريخ التوريد', 'Estimated Arrival Date'],
    ['رقم أمر شراء العميل', 'Customer PO No.'],
    ['مرجعنا', 'Our Reference']
];


$pdf->SetFont($fontName, 'B', 9); // Bold for both Arabic & English
foreach ($rfpHeaders as $index => $headerPair) {
    $xPos = $startX + $index * $headerWidth;
    $yPos = $headerStartY;

    $pdf->SetFillColor(230, 230, 230);
    $pdf->Rect($xPos, $yPos, $headerWidth, $rfpHeaderHeight, 'F');
    $pdf->Rect($xPos, $yPos, $headerWidth, $rfpHeaderHeight);

    // Arabic Line (Bold)
    $pdf->SetXY($xPos, $yPos + $verticalPadding);
    $pdf->Cell($headerWidth, $lineHeight, $headerPair[0], 0, 2, 'C', false);

    // English Line (Bold)
    $pdf->SetXY($xPos, $yPos + $verticalPadding + $lineHeight);
    $pdf->Cell($headerWidth, $lineHeight, $headerPair[1], 0, 0, 'C', false);
}


// Sixth Row: RFP Values
$pdf->SetFont($fontName, '', 10);
$valueHeight = 8;
$values = [
    $rfpNumber,
    formatDate($rfpDocument->rfpDateCreated, false),
    $rfpDocument->getPaymentTermName(),
    formatDate($rfpDocument->etaDate, false) ?? "-",
    $rfpDocument->PONumber ?? "-",
    $rfpDocument->reference ?? "-"
];

$pdf->SetXY($startX, $headerStartY + $rfpHeaderHeight);
foreach ($values as $index => $value) {
    $xPos = $startX + $index * $headerWidth;
    $pdf->SetXY($xPos, $headerStartY + $rfpHeaderHeight);
    $pdf->Cell($headerWidth, $valueHeight, $value, 1, 0, 'C');
}

// Space before product table
$productTableY = $headerStartY + $rfpHeaderHeight + $valueHeight + 10;

//Customer Information
$pdf->SetFont($fontName, '', 10);

// Define a starting position for the table
$startX     = 5;
$startY     = 50;
$rowHeight  = 5;
$labelWidth = 30;
$valueWidth = 65;
$space      = 2;


$currentIndex = 2; // Start after the first two required fields


//Line Items table header
// Define the initial header widths and scale them
$headers = [
    ['NO.', 'رقم'],
    ['Service/Product', 'الخدمة/المنتج'],
    ['Description', 'الوصف'],
    ['Qty', 'الكمية']
];

$headerWidths = [15, 60, 100, 25]; // Adjusted widths after removing image column

// Calculate the total width of the table
$totalWidth = array_sum($headerWidths) + 7;

// Calculate scale factor to fit the table within the page width
$scaleFactor = $pageWidth / $totalWidth;

// Adjust header widths proportionally
$scaledHeaderWidths = array_map(function ($width) use ($scaleFactor) {
    return $width * $scaleFactor;
}, $headerWidths);

//Build the line Items
$items = array();
$resLineItems = $db->query("SELECT * FROM consultation_rfp_line_items WHERE `rfpId` = ?s", $rfpDocument->rfpId);
$itemSerialNumber = 0;
while ($rowLineItem = mysqli_fetch_assoc($resLineItems)) {
    $description = getItemDescriptionForItemID($rowLineItem['itemId']);
    $serviceProduct = getItemNameForItemID($rowLineItem['itemId']); // You'll need to implement this function
    
    $item = [
        (string)++$itemSerialNumber, // Serial Number
        $serviceProduct, // Service/Product name
        $description, // Description
        $rowLineItem['quantity'], // Quantity
    ];
    $items[] = $item;
}

// Print headers with scaled widths
$pdf->SetFont($fontName, 'B', 10);
$pdf->SetFillColor(230, 230, 230);
$headerHeight = 10; // Uniform height for header cells

$afterSupplierY = $afterCompanyY + $headerHeight + $supplierHeight + 10;

$pdf->SetXY(5, 140);
// Line Item Table Header
foreach ($headers as $key => $headerPair) {
    $cellWidth = $scaledHeaderWidths[$key];

    // Prepare the content for the header cell with a line break
    $headerText = $headerPair[1] . "\n" . $headerPair[0];

    // Save current X and Y positions
    $xStart = $pdf->GetX();
    $yStart = $pdf->GetY();
    $pdf->MultiCell(
        $cellWidth,
        $headerHeight,
        $headerText,
        1,     // Border
        'C',   // Center alignment
        true   // Fill background
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
        $rowHeights[] = $pdf->getStringHeight($cellWidth, $value) + $defaultHeight;
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

        // Calculate vertical alignment
        $contentHeight = $pdf->getStringHeight($cellWidth, $value);
        $verticalOffset = ($maxHeight - $contentHeight) / 2;

        // Set alignment (center for NO. and Qty, left for others)
        $alignment = ($key == 0 || $key == 3) ? 'C' : 'L';

        $pdf->SetXY($xStart, $yStart + $verticalOffset);
        $pdf->MultiCell($cellWidth, $contentHeight, $value, 0, $alignment, false);

        $pdf->SetXY($xStart + $cellWidth, $yStart);
    }

    $pdf->Ln($maxHeight);
}

$pdf->SetXY(5, $pdf->GetY() -8); // Adjust Y-coordinate to place below the table
$pdf->AddPage();

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
    $pdf->Output('RFP_' . $rfpNumber . '.pdf', 'D');
else
    $pdf->Output('RFP_' . $rfpNumber . '.pdf', 'I');


function printTableHeaders($pdf, $headers, $scaledHeaderWidths) {
    global $fontName;
    $pdf->SetFont($fontName, 'B', 10);
    $pdf->SetFillColor(230, 230, 230);
    $headerHeight = 10;

    $pdf->SetXY(5, $pdf->GetY() + 2); // Adjust the Y-coordinate to add a small margin from the top
    foreach ($headers as $key => $headerPair) {
        $cellWidth = $scaledHeaderWidths[$key];

        // Prepare the content for the header cell with a line break
        $headerText = $headerPair[1] . "\n" . $headerPair[0];

        // Save current X and Y positions
        $xStart = $pdf->GetX();
        $yStart = $pdf->GetY();
        $pdf->MultiCell(
            $cellWidth,
            $headerHeight,
            $headerText,
            1,
            'C',
            true
        );

        // Reset cursor position for the next cell
        $pdf->SetXY($xStart + $cellWidth, $yStart);
    }

    // Move to the next line after the header
    $pdf->Ln();
}