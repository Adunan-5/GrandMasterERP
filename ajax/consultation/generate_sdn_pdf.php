<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";
require_once('../../vendor/autoload.php');

// Silence errors during PDF generation to prevent output
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/pdf_errors.log');

// Import the necessary classes in the global scope
use Salla\ZATCA\GenerateQrCode;
use Salla\ZATCA\Tags\Seller;
use Salla\ZATCA\Tags\TaxNumber;
use Salla\ZATCA\Tags\InvoiceDate;
use Salla\ZATCA\Tags\InvoiceTotalAmount;
use Salla\ZATCA\Tags\InvoiceTaxAmount;

$isDownload = false;
$isDownload = filter_input(INPUT_GET, 'isdownload', FILTER_VALIDATE_INT);

$invoiceID = "";
$invoiceID = filter_input(INPUT_GET, 'invoiceID', FILTER_VALIDATE_INT);
if ($invoiceID === null || $invoiceID === false || filter_var($invoiceID, FILTER_VALIDATE_INT) === false) {
    header("location:/consultation/salesorder/list");
    exit();
}

$fontNameArabic = "dejavusans";
$fontName = "dejavusans";
$fontNameCurrency = "dejavusansbook";

class CustomPDF extends TCPDF
{
    public function Header()
    {
        global $pageMargin;
        global $pageWidth;
        global $fontName;

        $pageWidth = $this->getPageWidth();
        $this->SetMargins($pageMargin, 60, $pageMargin);

        // Main Header
        $this->Image(__DIR__ . '/../../assets/img/branding/G-Logo.png', 5, 5, 15);
        $rightLogoX = $pageWidth - 45 - 5;
        $this->Image(__DIR__ . '/../../assets/img/branding/Logo_IT.png', $rightLogoX, 5, 45);

        // Title
        $this->SetFont($fontName, 'B', 16);
        $titleText = 'Service Delivery Note <span style="unicode-bidi: embed; direction: rtl;">مذكرة تسليم البضائع</span>';
        $titleWidth = $this->GetStringWidth('Service Delivery Note مذكرة تسليم البضائع');
        $titleHeight = $this->getStringHeight($titleWidth, 'Service Delivery Note مذكرة تسليم البضائع');
        $centerX = ($pageWidth - $titleWidth) / 2;
        $this->SetXY($centerX, 10);
        $this->writeHTMLCell($titleWidth, $titleHeight, $centerX, 10, $titleText, 0, 1, false, true, 'C', true);
    }

    public function Footer()
    {
        global $fontName;
        $this->SetY(-15);
        $this->SetDrawColor(255, 204, 0);
        $this->SetLineWidth(0.5);
        $leftMargin = 5;
        $rightMargin = 5;
        $lineStartX = $leftMargin;
        $lineEndX = 297 - $rightMargin;
        $lineY = $this->GetY() - 5;
        $this->Line($lineStartX, $lineY, $lineEndX, $lineY);
        $this->SetFont($fontName, '', 10);
        $this->Cell(0, 6, 'Thanks for choosing GrandMaster – The sole supplier in middle east for EU & US brands', 0, 1, 'C');
        $this->Cell(0, 6, 'شكرًا لاختيارك جراند ماستر - المورد الوحيد في الشرق الأوسط للعلامات التجارية الأوروبية والأمريكية', 0, 1, 'C');
    }
}

ob_start();

$invoiceDocument = new InvoiceDocument();
$invoiceDocument->loadById($invoiceID);
$sdnNumber = getSDNNumberFromInvoiceID($invoiceID);
$gdnNumber = '';

$companyID = $invoiceDocument->companyId;

$companyInfo = new Company();
$companyInfo->loadById($companyID);

$customerInfo = new Customer();
$customerInfo->loadById($invoiceDocument->customerId);

$pageMargin = 5;
$pdf = new CustomPDF('L', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('GrandMaster');
$pdf->SetTitle('Service Delivery Note ' . $sdnNumber);
$pdf->SetMargins($pageMargin, $pageMargin, $pageMargin);
$pdf->AddPage();

$pageWidth = 297 - 10;
$pageWidth = $pdf->getPageWidth();

$pdf->SetFont($fontName, '', 10);
$startX = $pageMargin;
$startY = 35;
$columnWidth = ($pageWidth - 2 * $pageMargin) / 2;
$rowHeight = 5;
$headerHeight = 7;

$pdf->SetXY($startX, $startY);
$pdf->SetFont($fontName, 'B', 10);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell($columnWidth, $headerHeight, 'Our Details', 1, 0, 'C', true);
$pdf->Cell($columnWidth, $headerHeight, 'تفاصيلنا', 1, 1, 'C', true);

$pdf->SetFont($fontName, '', 10);
$companyAddressEnParts = [];
if (!empty($companyInfo->addressLine1)) $companyAddressEnParts[] = $companyInfo->addressLine1;
if (!empty($companyInfo->addressLine2)) $companyAddressEnParts[] = $companyInfo->addressLine2;
if (!empty($companyInfo->cityId)) $companyAddressEnParts[] = getCityFromID($companyInfo->cityId);
if (!empty($companyInfo->stateId)) $companyAddressEnParts[] = getStateFromID($companyInfo->stateId);
if (!empty($companyInfo->countryId)) $companyAddressEnParts[] = getCountryFromID($companyInfo->countryId);
if (!empty($companyInfo->postalCode)) $companyAddressEnParts[] = $companyInfo->postalCode;
$companyAddressEn = implode(", ", $companyAddressEnParts);

$companyAddressArParts = [];
if (!empty($companyInfo->addressLine1)) $companyAddressArParts[] = $companyInfo->addressLine1;
if (!empty($companyInfo->addressLine2)) $companyAddressArParts[] = $companyInfo->addressLine2;
if (!empty($companyInfo->cityId)) $companyAddressArParts[] = getCityFromID($companyInfo->cityId);
if (!empty($companyInfo->stateId)) $companyAddressArParts[] = getStateFromID($companyInfo->stateId);
if (!empty($companyInfo->countryId)) $companyAddressArParts[] = getCountryFromID($companyInfo->countryId);
if (!empty($companyInfo->postalCode)) $companyAddressArParts[] = $companyInfo->postalCode;
$companyAddressAr = implode("، ", $companyAddressArParts);

$companyDetailsEn = "\nCompany Almord Al-Lamhedod Commercial Limited Liability/Holding\n{$companyInfo->companyName}\n{$companyAddressEn}\nCR Number: {$companyInfo->companyCRNumber}\nVAT Number: {$companyInfo->vatNumber}\n\n";
$companyDetailsAr = "\nشركة المورد اللامحدود التجارية قابضة ذات مسئولية محدودة\n{$companyInfo->companyNameAr}\n{$companyAddressAr}\nرقم السجل التجاري: " . convertToArabicNumbers($companyInfo->companyCRNumber) . "\nرقم الضريبة: " . convertToArabicNumbers($companyInfo->vatNumber) . "\n\n";
$companyHeight = max(
    $pdf->getStringHeight($columnWidth, $companyDetailsEn),
    $pdf->getStringHeight($columnWidth, $companyDetailsAr)
);
$pdf->SetXY($startX, $startY + $headerHeight);
$pdf->MultiCell($columnWidth, $companyHeight, $companyDetailsEn, 1, 'L');
$pdf->SetXY($startX + $columnWidth, $startY + $headerHeight);
$pdf->MultiCell($columnWidth, $companyHeight, $companyDetailsAr, 1, 'R');

$afterCompanyY = $startY + $headerHeight + $companyHeight;
$pdf->SetXY($startX, $afterCompanyY);
$pdf->SetFont($fontName, 'B', 10);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell($columnWidth, $headerHeight, 'Client Details', 1, 0, 'C', true);
$pdf->Cell($columnWidth, $headerHeight, 'تفاصيل العميل', 1, 1, 'C', true);

$pdf->SetFont($fontName, '', 10);
$clientAddressEnParts = [];
if (!empty($customerInfo->addressLine1)) $clientAddressEnParts[] = $customerInfo->addressLine1;
if (!empty($customerInfo->addressLine2)) $clientAddressEnParts[] = $customerInfo->addressLine2;
if (!empty($customerInfo->cityId)) $clientAddressEnParts[] = getCityFromID($customerInfo->cityId);
if (!empty($customerInfo->stateId)) $clientAddressEnParts[] = getStateFromID($customerInfo->stateId);
if (!empty($customerInfo->countryId)) $clientAddressEnParts[] = getCountryFromID($customerInfo->countryId);
if (!empty($customerInfo->postalCode)) $clientAddressEnParts[] = $customerInfo->postalCode;
$clientAddressEn = implode(", ", $clientAddressEnParts);

$clientAddressArParts = [];
if (!empty($customerInfo->addressLine1)) $clientAddressArParts[] = $customerInfo->addressLine1;
if (!empty($customerInfo->addressLine2)) $clientAddressArParts[] = $customerInfo->addressLine2;
if (!empty($customerInfo->cityId)) $clientAddressArParts[] = getCityFromID($customerInfo->cityId);
if (!empty($customerInfo->stateId)) $clientAddressArParts[] = getStateFromID($customerInfo->stateId);
if (!empty($customerInfo->countryId)) $clientAddressArParts[] = getCountryFromID($customerInfo->countryId);
if (!empty($customerInfo->postalCode)) $clientAddressArParts[] = $customerInfo->postalCode;
$clientAddressAr = implode("، ", $clientAddressArParts);

$clientDetailsEn = "{$customerInfo->companyName}\n{$clientAddressEn}\nCR Number: {$customerInfo->companyCRNumber}\nVAT Number: {$customerInfo->vatNumber}\n";
$clientDetailsAr = "{$customerInfo->companyNameAr}\n{$clientAddressAr}\nرقم السجل التجاري: " . convertToArabicNumbers($customerInfo->companyCRNumber) . "\nرقم الضريبة: " . convertToArabicNumbers($customerInfo->vatNumber) . "\n";
$clientHeight = max(
    $pdf->getStringHeight($columnWidth, $clientDetailsEn),
    $pdf->getStringHeight($columnWidth, $clientDetailsAr)
);
$pdf->SetXY($startX, $afterCompanyY + $headerHeight);
$pdf->MultiCell($columnWidth, $clientHeight, $clientDetailsEn, 1, 'L');
$pdf->SetXY($startX + $columnWidth, $afterCompanyY + $headerHeight);
$pdf->MultiCell($columnWidth, $clientHeight, $clientDetailsAr, 1, 'R');

$headerStartY = $afterCompanyY + $headerHeight + $clientHeight;
$headerWidth = ($pageWidth - 2 * $pageMargin) / 6;
$invoiceHeaderHeight = 14;
$lineHeight = 4.5;
$verticalPadding = ($invoiceHeaderHeight - ($lineHeight * 2)) / 2;

$invoiceHeaders = [
    ['رقم إذن التسليم', 'SDN No.'],
    ['تاريخ', 'Date'],
    ['شروط الدفع', 'Payment Terms'],
    ['تاريخ التوريد', 'Date of Supply'],
    ['رقم أمر شراء العميل', 'Customer PO No.'],
    ['مرجعنا', 'Our Reference']
];

$pdf->SetFont($fontName, 'B', 9);
foreach ($invoiceHeaders as $index => $headerPair) {
    $xPos = $startX + $index * $headerWidth;
    $yPos = $headerStartY;
    $pdf->SetFillColor(230, 230, 230);
    $pdf->Rect($xPos, $yPos, $headerWidth, $invoiceHeaderHeight, 'F');
    $pdf->Rect($xPos, $yPos, $headerWidth, $invoiceHeaderHeight);
    $pdf->SetXY($xPos, $yPos + $verticalPadding);
    $pdf->Cell($headerWidth, $lineHeight, $headerPair[0], 0, 2, 'C', false);
    $pdf->SetXY($xPos, $yPos + $verticalPadding + $lineHeight);
    $pdf->Cell($headerWidth, $lineHeight, $headerPair[1], 0, 0, 'C', false);
}

$pdf->SetFont($fontName, '', 10);
$heights = [];
$values = [
    $sdnNumber,
    formatDate($invoiceDocument->invoiceDateIssued, false),
    $invoiceDocument->getPaymentTermName(),
    formatDate($invoiceDocument->invoiceDateIssued, false) ?? "-",
    $invoiceDocument->PONumber ?? "-",
    $invoiceDocument->reference ?? "-"
];

foreach ($values as $index => $value) {
    $heights[] = $pdf->getStringHeight($headerWidth, $value);
}
$valueHeight = max($heights) + 2;

$yPos = $headerStartY + $invoiceHeaderHeight;
if ($yPos + $valueHeight > $pdf->getPageHeight() - $pdf->getMargins()['bottom']) {
    $pdf->AddPage();
    $yPos = $pdf->GetY();
}

$pdf->SetXY($startX, $yPos);
foreach ($values as $index => $value) {
    $xPos = $startX + $index * $headerWidth;
    $pdf->MultiCell($headerWidth, $valueHeight, $value, 1, 'C', false, 0, $xPos, $yPos);
}

$productTableY = $headerStartY + $invoiceHeaderHeight + $valueHeight + 10;

$headers = [
    ['NO.', 'رقم'],
    ['Service/Product', 'الخدمة/المنتج'],
    ['Description', 'الوصف'],
    ['Qty', 'الكمية'],
    ['Requested Qty', 'الكمية المطلوبة']
];

$headerWidths = [10, 30, 45, 12, 12];
$totalWidth = array_sum($headerWidths) + 7;
$scaleFactor = $pageWidth / $totalWidth;
$scaledHeaderWidths = array_map(function ($width) use ($scaleFactor) {
    return $width * $scaleFactor;
}, $headerWidths);

$items = array();
$resLineItems = $db->query("SELECT * FROM consultation_invoice_line_items WHERE `invoiceId` = ?s", $invoiceID);
$itemSerialNumber = 0;
while ($rowLineItem = mysqli_fetch_assoc($resLineItems)) {
    $partNumber = getItemNameForItemID($rowLineItem['itemId']);
    $description = html_entity_decode(getItemDescriptionForItemID($rowLineItem['itemId']));
    $item = [
        (string)++$itemSerialNumber,
        $partNumber,
        $description,
        $rowLineItem['quantity'],
        $rowLineItem['quantity'] // Assuming Requested Qty is same as Qty for simplicity
    ];
    $items[] = $item;
}

$pdf->SetY($productTableY);

$headerHeight = 10;
if (!empty($items)) {
    $firstItem = $items[0];
    $rowHeights = [];
    $defaultHeight = 10;
    foreach ($firstItem as $key => $value) {
        $cellWidth = $scaledHeaderWidths[$key];
        $pdf->SetFont($fontName, '', 10);
        $rowHeights[] = $pdf->getStringHeight($cellWidth, $value) + $defaultHeight;
    }
    $firstMaxHeight = max($rowHeights);
    if ($pdf->GetY() + $headerHeight + $firstMaxHeight > $pdf->getPageHeight() - $pdf->getMargins()['bottom']) {
        $pdf->AddPage();
    }
}

$pdf->SetFont($fontName, 'B', 10);
$pdf->SetFillColor(230, 230, 230);
$xStart = 5;
$yStart = $pdf->GetY();
foreach ($headers as $key => $headerPair) {
    $cellWidth = $scaledHeaderWidths[$key];
    $headerText = $headerPair[1] . "\n" . $headerPair[0];
    $xStart = $pdf->GetX();
    $yStart = $pdf->GetY();
    $pdf->MultiCell($cellWidth, $headerHeight, $headerText, 1, 'C', true);
    $pdf->SetXY($xStart + $cellWidth, $yStart);
}
$pdf->Ln();

$pdf->SetFont($fontName, '', 10);
foreach ($items as $item) {
    $rowHeights = [];
    $defaultHeight = 10;
    foreach ($item as $key => $value) {
        $cellWidth = $scaledHeaderWidths[$key];
        $pdf->SetFont($fontName, '', 10);
        $rowHeights[] = $pdf->getStringHeight($cellWidth, $value) + $defaultHeight;
    }
    $maxHeight = max($rowHeights);
    if ($pdf->GetY() + $maxHeight > $pdf->getPageHeight() - $pdf->getMargins()['bottom']) {
        $pdf->AddPage();
        printTableHeaders($pdf, $headers, $scaledHeaderWidths);
    }
    foreach ($item as $key => $value) {
        $cellWidth = $scaledHeaderWidths[$key];
        $xStart = $pdf->GetX();
        $yStart = $pdf->GetY();
        $pdf->Rect($xStart, $yStart, $cellWidth, $maxHeight);
        $contentHeight = $pdf->getStringHeight($cellWidth, $value);
        $verticalOffset = ($maxHeight - $contentHeight) / 2;
        $alignment = ($key == 2) ? 'L' : 'C';
        $pdf->SetXY($xStart, $yStart + $verticalOffset);
        $pdf->SetFont($fontName, '', 10);
        $pdf->MultiCell($cellWidth, $contentHeight, $value, 0, $alignment, false);
        $pdf->SetXY($xStart + $cellWidth, $yStart);
    }
    $pdf->Ln($maxHeight);
}
$pdf->AddPage();
$noteStartY = $pdf->GetY() + 10;
// English & Arabic text
$leftText  = "I have received the delivered IT solution in good working condition and as per the agreed specifications";
$rightText = "لقد استلمت الحلول التقنية المسلمة بحالة جيدة ووفقًا للمواصفات المتفق عليها";

// column width (half of available width)
$colWidth = ($pageWidth - 2 * $pageMargin) / 2;
$y = $noteStartY;

// Write English text (left column)
$pdf->MultiCell(
    $colWidth, 
    0, 
    $leftText, 
    0, 
    'L', 
    0, 
    0,   // don't move to next line
    $pageMargin, 
    $y, 
    true, 
    0, 
    false, 
    true, 
    0, 
    'T', 
    false
);

// Write Arabic text (right column)
$pdf->MultiCell(
    $colWidth, 
    0, 
    $rightText, 
    0, 
    'R', 
    0, 
    1,   // move to next line after this cell
    $pageMargin + $colWidth, 
    $y, 
    true, 
    0, 
    false, 
    true, 
    0, 
    'T', 
    false
);

// get current Y after multicells
$noteEndY = $pdf->GetY() + 10;

// Table start position after note
$tableStartY = $noteEndY + 5;
$colWidths = [77, 70, 70, 70];
$headerHeight = 10;

if ($tableStartY + 30 > $pdf->getPageHeight() - $pdf->getMargins()['bottom']) {
    $pdf->AddPage();
    $tableStartY = 10;
}

$pdf->SetFont($fontName, 'B', 10);
$pdf->SetFillColor(230, 230, 230);
$currentX = $pageMargin;
$headers = ['Name', 'Date', 'Signature', 'Contact Number'];
foreach ($headers as $i => $header) {
    $pdf->SetXY($currentX, $tableStartY);
    $pdf->Cell($colWidths[$i], $headerHeight, $header, 1, 0, 'C', true);
    $currentX += $colWidths[$i];
}

$rowHeight = 20;
$rowY = $tableStartY + $headerHeight;
$currentX = $pageMargin;
for ($i = 0; $i < 4; $i++) {
    $pdf->SetXY($currentX, $rowY);
    $pdf->Cell($colWidths[$i], $rowHeight, '', 1, 0, 'C');
    $currentX += $colWidths[$i];
}

if ($isDownload == 1)
    $pdf->Output('SDN_' . $sdnNumber . '.pdf', 'D');
else
    $pdf->Output('SDN_' . $sdnNumber . '.pdf', 'I');

ob_end_flush();

function printTableHeaders($pdf, $headers, $scaledHeaderWidths)
{
    global $fontName;
    $pdf->SetFont($fontName, 'B', 10);
    $pdf->SetFillColor(230, 230, 230);
    $headerHeight = 10;
    $pdf->SetXY(5, $pdf->GetY() + 2);
    foreach ($headers as $key => $headerPair) {
        $cellWidth = $scaledHeaderWidths[$key];
        $headerText = $headerPair[1] . "\n" . $headerPair[0];
        $xStart = $pdf->GetX();
        $yStart = $pdf->GetY();
        $pdf->MultiCell($cellWidth, $headerHeight, $headerText, 1, 'C', true);
        $pdf->SetXY($xStart + $cellWidth, $yStart);
    }
    $pdf->Ln();
}

function generateZATCAQRCode($sellerName, $vatNumber, $invoiceDate, $invoiceTotal, $invoiceTax)
{
    try {
        $qrCodeBase64 = GenerateQrCode::fromArray([
            new Seller($sellerName),
            new TaxNumber($vatNumber),
            new InvoiceDate($invoiceDate),
            new InvoiceTotalAmount($invoiceTotal),
            new InvoiceTaxAmount($invoiceTax)
        ])->render();
        if (empty($qrCodeBase64) || strpos($qrCodeBase64, 'data:image/png;base64,') !== 0) {
            error_log("Invalid QR code generated: " . (empty($qrCodeBase64) ? 'Empty' : $qrCodeBase64));
            return false;
        }
        return $qrCodeBase64;
    } catch (Exception $e) {
        error_log("Error generating ZATCA QR code: " . $e->getMessage());
        return false;
    }
}
?>