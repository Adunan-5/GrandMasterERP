<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";
require_once('../../vendor/autoload.php');

// Silence errors during PDF generation to prevent output
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/pdf_errors.log');

$isDownload = false;
$isDownload = filter_input(INPUT_GET, 'isdownload', FILTER_VALIDATE_INT);

$documentID = "";
$documentID = filter_input(INPUT_GET, 'documentID', FILTER_VALIDATE_INT);
if ($documentID === null || $documentID === false || filter_var($documentID, FILTER_VALIDATE_INT) === false) {
    header("location:/consultation/quotation/list");
    exit();
}

class Quotation_PDF extends TCPDF {
    // Page Header - Empty to avoid interference
    public function Header() {}

    // Page Footer
    public function Footer() {
    }
}

// $proposalTitle = getProposalTitleFromProposalID($documentID);
$quotationTitle = getConsultationQuotationTitleFromDocumentID($documentID);
$keyDocument = new ConsultationKeyDocument();
$keyDocument->loadById($documentID);

$customerID = $keyDocument->customerId;

$customer = new Customer();
$customer->loadById($customerID);

$companyInfo = new Company();
$companyInfo->loadById(2);

$quotationData = $db->getRow("SELECT * FROM consultation_key_documents WHERE documentId = ?i", $documentID);
$quotationDateIssued = date('d - m - Y', strtotime($quotationData['quotationDateIssued']));
$quotationDateExpiry = date('d - m - Y', strtotime($quotationData['quotationDateExpiry']));

$fontRegular = 'ttsupermolot';
$fontCurrency = 'dejavusansbook';

// Create new PDF document
$pdf = new Quotation_PDF('P', 'mm', 'A4', true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('GrandMaster');
$pdf->SetTitle('Quotation');
$pdf->SetSubject('Quotation for ' . $quotationTitle);

// Set margins (left, top, right)
$pdf->SetMargins(0, 0, 0);
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(0);
$pdf->SetAutoPageBreak(false, 0);

// Add a page
$pdf->AddPage('P', 'A4');

// Add background image with proper scaling to fill height
$bgImagePath = __DIR__ . '/../../assets/img/branding/backgroundImage-for-template.png';
if (file_exists($bgImagePath)) {
    $pdf->Image(
        $bgImagePath,
        0,     // X position
        0,     // Y position
        210,   // Width in mm
        297,   // Height in mm
        'PNG', '', '', false, 300, '', false, false, 0
    );
}

$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(true, 25);

// $logoPath = __DIR__ . '/../../assets/img/branding/G-Logo-consultation.png';
$logoPath = __DIR__ . '/../../assets/img/branding/Logo_IT.png';
if (file_exists($logoPath)) {
    $pdf->Image($logoPath, 15, 15, 30);
}

// Add the second logo (G-Logo.png) on the right side
$secondLogoPath = __DIR__ . '/../../assets/img/branding/G-Logo.png';
if (file_exists($secondLogoPath)) {
    $xPosition = 210 - 15 - 30;
    $pdf->Image($secondLogoPath, $xPosition, 15, 12); // x=165mm from left, y=15mm from top, width=15mm
}

// Set fixed font sizes
$mainTitleFontSize = 35;

$mainTitle = 'Fuel Level & Consumption Monitoring System';

// Set font for main title
$pdf->SetFont($fontRegular, 'B', $mainTitleFontSize);
$pdf->SetXY(15, 80); // X=15 for margin, Y=60 from top
$pdf->MultiCell(180, 15, $quotationTitle, 0, 'C'); // Width 180 mm, center aligned

// Add space before tables
$pdf->Ln(40);

// Set font for tables
$pdf->SetFont($fontRegular, '', 11);

// First table - Customer and Company details
$col1Width = 80;
$col2Width = 30;
$col3Width = 80;

// Proposal for row
$pdf->Cell($col1Width, 7, 'Quotation for:', 0, 0, 'L');
$pdf->Cell($col2Width, 7, '', 0, 0);
$pdf->Cell($col3Width, 7, '', 0, 1);

// Company names row
$pdf->SetFont($fontRegular, 'B', 11);
$xStart = $pdf->GetX();
$yStart = $pdf->GetY();

// Calculate heights using GetStringHeight
$customerEngHeight = $pdf->getStringHeight($col1Width, $customer->companyName);
$companyEngHeight  = $pdf->getStringHeight($col3Width, $companyInfo->companyName);
$rowHeightEng = max($customerEngHeight, $companyEngHeight);


// Print English names row
$pdf->MultiCell($col1Width, $rowHeightEng, $customer->companyName, 0, 'L', false, 0);
$pdf->MultiCell($col2Width, $rowHeightEng, '', 0, 'L', false, 0);
$pdf->MultiCell($col3Width, $rowHeightEng, $companyInfo->companyName, 0, 'L', false, 1);

// Arabic names row
$pdf->SetFont('dejavusansbook', '', 11);

// Reset Y to same start if line height increased by any MultiCell
$pdf->SetY($pdf->GetY());

$customerArHeight = $pdf->getStringHeight($col1Width, $customer->companyNameAr);
$companyArHeight  = $pdf->getStringHeight($col3Width, $companyInfo->companyNameAr);
$rowHeightAr = max($customerArHeight, $companyArHeight);

$pdf->Ln(0.5);

$pdf->MultiCell($col1Width, $rowHeightAr, $customer->companyNameAr, 0, 'L', false, 0);
$pdf->MultiCell($col2Width, $rowHeightAr, '', 0, 'L', false, 0);
$pdf->MultiCell($col3Width, $rowHeightAr, $companyInfo->companyNameAr, 0, 'L', false, 1);

$pdf->Ln(0.5);

// CR numbers row - bold label, regular value
$pdf->SetFont($fontRegular, 'B', 11);
$pdf->Cell(20, 7, 'CR: ' . $customer->companyCRNumber, 0, 0, 'L');
$pdf->SetFont($fontRegular, '', 11);
$pdf->Cell(90, 7, '', 0, 0, 'L');
$pdf->SetFont($fontRegular, 'B', 11);
$pdf->Cell(20, 7, 'CR: ' . $companyInfo->companyCRNumber, 0, 0, 'L');
$pdf->SetFont($fontRegular, '', 11);
$pdf->Cell(0, 7, '', 0, 1, 'L');

// VAT numbers row - optimized spacing
$pdf->SetFont($fontRegular, 'B', 11);
$pdf->Cell(20, 7, 'VAT: ' . $customer->vatNumber, 0, 0, 'L');
$pdf->SetFont($fontRegular, '', 11);
$pdf->Cell(90, 7, '', 0, 0, 'L');
$pdf->SetFont($fontRegular, 'B', 11);
$pdf->Cell(20, 7, 'VAT: ' . $companyInfo->vatNumber, 0, 0, 'L');
$pdf->SetFont($fontRegular, '', 11);
$pdf->Cell(0, 7, '', 0, 1, 'L');

// Add space between tables
$pdf->Ln(40);

// Second table - Prepared by and Proposal info
$pdf->SetFont($fontRegular, 'B', 11);
$pdf->Cell($col1Width, 7, 'Prepared by:', 0, 0, 'L');
$pdf->Cell($col2Width, 7, '', 0, 0);
$pdf->SetFont($fontRegular, '', 11);
$html = 'Quotation Number: <b>' . getQuotationNumberFromConsultationDocumentID($documentID) . '</b>';
$pdf->writeHTMLCell($col3Width, 7, '', '', $html, 0, 1, false, true, 'L', true);

// Names row
$pdf->SetFont($fontRegular, '', 11);
$pdf->Cell($col1Width, 7, $companyInfo->companyName, 0, 0, 'L');
$pdf->Cell($col2Width, 7, '', 0, 0);
$pdf->SetFont($fontRegular, '', 11);
$pdf->Cell($col3Width, 7, 'Quotation Date: ' . $quotationDateIssued, 0, 1, 'L');

// Company row
$pdf->SetFont($fontRegular, '', 11);
$pdf->Cell($col1Width, 7, '', 0, 0, 'L');
$pdf->Cell($col2Width, 7, '', 0, 0);
$pdf->SetFont($fontRegular, '', 11);
$pdf->Cell($col3Width, 7, 'Valid until: ' . $quotationDateExpiry, 0, 1, 'L');

// Add second page
$pdf->AddPage('P', 'A4');

// Set margins for second page
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(true, 25);

$pdf->setCellHeightRatio(1.3); // improve spacing

// Add "Quotation Summary" heading
$pdf->SetFont($fontRegular, 'B', 25);
$pdf->SetTextColor(130, 22, 92);
$pdf->Ln(15);
$pdf->SetX(15);
$pdf->Cell(180, 8, 'Quotation Summary', 0, 1, 'L');
$pdf->SetTextColor(0, 0, 0);

// Add quotation summary paragraph
$pdf->Ln(10);
$pdf->SetFont($fontRegular, '', 15);
$pdf->SetX(15);
$pdf->SetMargins(15, 15, 15);
$summaryText = "We are pleased to submit our quotation for the <b>\"{$quotationTitle}\"</b>.";
$associatedProposalID = $keyDocument->associatedProposalID;
if (!empty($associatedProposalID) && $associatedProposalID !== null) {
    $proposalNumber = getProposalNumberFromProposalID($associatedProposalID); // Assuming this function exists
    if (!empty($proposalNumber)) {
        $summaryText .= " as proposed in the proposal number <b>\"{$proposalNumber}\"</b>.";
    }
}
$paragraph = '<p style="line-height: 1.5;">' . $summaryText . '<br><br>';
// Calculate total project cost
$totalBeforeVAT = 0;
$res = $db->query("SELECT * FROM consultation_line_items WHERE documentId = ?s", $documentID);
while ($row = mysqli_fetch_assoc($res)) {
    $quantity = floatval($row['quantity']);
    $unitPrice = floatval($row['unitPrice']);
    $subtotal = $quantity * $unitPrice;
    $totalBeforeVAT += $subtotal;
}
$totalInWords = numberToEnglishWords($totalBeforeVAT);
$paragraph .= 'Total Project Cost: <font face="dejavusansbook">₥</font> <b>' 
    . number_format($totalBeforeVAT, 2) 
    . '</b><br>(' . $totalInWords . ' Exclusive of VAT)</p>';
$pdf->writeHTML($paragraph, true, false, true, false, 'J');

// $pdf->Ln(15);

// Add "Proposal Summary" heading
// $pdf->SetFont($fontRegular, 'B', 25);
// $pdf->SetTextColor(240, 190, 17);
// $pdf->SetX(15);
// $pdf->Cell(180, 8, 'Proposal Summary', 0, 1, 'L');
// $pdf->SetTextColor(0, 0, 0);

// $pdf->Ln(10);

// // Add paragraph with justified text
// $pdf->SetFont($fontRegular, '', 15);
// $pdf->SetX(15);
// $pdf->SetMargins(15, 15, 15);
// $paragraph = '<p style="line-height: 1.5;">We are pleased to submit our Proposal for the <b>design, development, and deployment of an Automatic Fuel Level & Consumption Monitoring System</b> that integrates with the Fleet Management System via API, with optional support for anti-tampering system. This system will provide <b>real-time tank level readings and fuel dispensing data</b>, transmitted securely to a centralized server with an accessible dashboard for monitoring and reporting.<br><br>Total Project Cost: $ <b></b><br>(Exclusive of VAT)</p>';
// $pdf->writeHTML($paragraph, true, false, true, false, 'J');

// Add heading for second page
$pdf->SetFont($fontRegular, 'B', 25);
$pdf->SetTextColor(130, 22, 92);
$pdf->Ln(10);
$pdf->Cell(180, 10, 'Detailed Line Items', 0, 1, 'L');

// Add space before table
$pdf->Ln(10);

// Set table column widths
$colItemWidth = 15; // Item# column
$colDescWidth = 85; // Description column
$colQtyWidth = 20;  // Quantity column
$colUnitPriceWidth = 30; // Unit Price column
$colSubTotalWidth = 30; // Sub Total column

$pdf->setLineWidth(0.1); // Set line width for table borders

// Set font for table
// $pdf->SetFont($fontRegular, '', 11);
// // Set text color for table header
// $pdf->SetTextColor(0, 0, 0);

// // Table header (5 columns)
// $pdf->SetFont($fontRegular, 'B', 11);
// $pdf->Cell($colItemWidth, 8, 'Item#', 1, 0, 'L', 0);
// $pdf->Cell($colDescWidth, 8, 'Description', 1, 0, 'L', 0);
// $pdf->Cell($colQtyWidth, 8, 'Quantity', 1, 0, 'C', 0);
// $pdf->Cell($colUnitPriceWidth, 8, 'Unit Price', 1, 0, 'C', 0);
// $pdf->Cell($colSubTotalWidth, 8, 'Sub Total', 1, 1, 'C', 0);

// Function to draw table header
function drawTableHeader($pdf, $colItemWidth, $colDescWidth, $colQtyWidth, $colUnitPriceWidth, $colSubTotalWidth, $fontRegular) {
    $pdf->SetFont($fontRegular, 'B', 11);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($colItemWidth, 8, 'Item#', 1, 0, 'L', 0);
    $pdf->Cell($colDescWidth, 8, 'Description', 1, 0, 'L', 0);
    $pdf->Cell($colQtyWidth, 8, 'Quantity', 1, 0, 'C', 0);
    $pdf->Cell($colUnitPriceWidth, 8, 'Unit Price', 1, 0, 'C', 0);
    $pdf->Cell($colSubTotalWidth, 8, 'Sub Total', 1, 1, 'C', 0);

    $pdf->SetFont($fontRegular, '', 11);
    // $pdf->SetTextColor(0, 0, 0);
    
}

// Draw initial table header
drawTableHeader($pdf, $colItemWidth, $colDescWidth, $colQtyWidth, $colUnitPriceWidth, $colSubTotalWidth, $fontRegular);

// Fetch line items
$res = $db->query("SELECT * FROM consultation_line_items WHERE documentId = ?s", $documentID);
$totalBeforeVAT = 0;
$vatTotal = 0;
$itemSerialNumber = 0;

while ($row = mysqli_fetch_assoc($res)) {
    $itemId = $row['itemId'];
    $quantity = floatval($row['quantity']);
    $unitPrice = floatval($row['unitPrice']);
    $itemName = getItemNameForItemID($itemId); // Fetch item name
    $description = getItemDescriptionForConsultationServiceItemID($itemId);
    $subtotal = $quantity * $unitPrice;
    $vat = $subtotal * 0.15; // 15% VAT
    $totalBeforeVAT += $subtotal;
    $vatTotal += $vat;

    $pdf->SetFont($fontRegular, '', 11);

    // Starting positions
    $startX = $pdf->GetX();
    $startY = $pdf->GetY();

    $lineHeight = 8;

    // Combine item name (bold) and description
    $fullDescription = $itemName . "\n" . $description;

    // Calculate height based on number of lines in description
    $nbLines = $pdf->getNumLines($fullDescription, $colDescWidth);
    $rowHeight = $lineHeight * $nbLines;

    // Check if there is enough space for the row
    $currentY = $pdf->GetY();
    $pageBottom = $pdf->getPageHeight() - 25; // Account for bottom margin
    if ($currentY + $rowHeight > $pageBottom) {
        // Add new page
        $pdf->AddPage('P', 'A4');
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 25);

        $pdf->SetX(15);
        // Redraw table header
        drawTableHeader($pdf, $colItemWidth, $colDescWidth, $colQtyWidth, $colUnitPriceWidth, $colSubTotalWidth, $fontRegular);
    }

    // 1. Item#
    $pdf->MultiCell($colItemWidth, $rowHeight, ++$itemSerialNumber, 1, 'C', false, 0, '', '', true, 0, false, true, $rowHeight, 'M');

    // 2. Description
    $pdf->MultiCell($colDescWidth, $rowHeight, $fullDescription, 1, 'L', false, 0, '', '', true, 0, false, true, $rowHeight, 'M');

    // 3. Quantity
    $pdf->MultiCell($colQtyWidth, $rowHeight, number_format($quantity, 2), 1, 'C', false, 0, '', '', true, 0, false, true, $rowHeight, 'M');

    $pdf->SetFont($fontCurrency, '', 11);

    // 4. Unit Price (Using MultiCell with manual font switching)
    $pdf->SetX($pdf->GetX()); // Ensure starting X position
    $startX = $pdf->GetX();
    $startY = $pdf->GetY();

    $pdf->SetFont($fontCurrency, '', 11);
    $currencySymbol = '₥ ';
    $symbolWidth = $pdf->GetStringWidth($currencySymbol);
    $pdf->MultiCell($colUnitPriceWidth, $rowHeight, $currencySymbol, 0, 'L', false, 0, $startX, $startY, true, 0, false, true, $rowHeight, 'M');

    $pdf->SetFont($fontRegular, '', 11);
    $numberText = number_format($unitPrice, 2);
    $pdf->MultiCell($colUnitPriceWidth - $symbolWidth, $rowHeight, $numberText, 0, 'L', false, 0, $startX + $symbolWidth, $startY, true, 0, false, true, $rowHeight, 'M');

    // Draw border around the entire cell
    $pdf->Rect($startX, $startY, $colUnitPriceWidth, $rowHeight, 'D');

    // Move to next column
    $pdf->SetXY($startX + $colUnitPriceWidth, $startY);

    // 5. Sub Total (Using MultiCell with manual font switching)
    $startX = $pdf->GetX();
    $startY = $pdf->GetY();

    $pdf->SetFont($fontCurrency, '', 11);
    $symbolWidth = $pdf->GetStringWidth($currencySymbol);
    $pdf->MultiCell($colSubTotalWidth, $rowHeight, $currencySymbol, 0, 'L', false, 0, $startX, $startY, true, 0, false, true, $rowHeight, 'M');

    $pdf->SetFont($fontRegular, '', 11);
    $numberText = number_format($subtotal, 2);
    $pdf->MultiCell($colSubTotalWidth - $symbolWidth, $rowHeight, $numberText, 0, 'L', false, 0, $startX + $symbolWidth, $startY, true, 0, false, true, $rowHeight, 'M');

    // Draw border around the entire cell
    $pdf->Rect($startX, $startY, $colSubTotalWidth, $rowHeight, 'D');

    // Move to next line
    $pdf->Ln($rowHeight);
}

// Summary rows (3 columns)
$colItemWidth = 15; // Item# column
$colDescWidth = 135; // Merged Description column
$colSubTotalWidth = 30; // Sub Total column

// Check space for summary rows
$currentY = $pdf->GetY();
$summaryRowsHeight = 8 * 7; // 7 rows of 8mm each
if ($currentY + $summaryRowsHeight > $pageBottom) {
    $pdf->AddPage('P', 'A4');
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetAutoPageBreak(true, 25);
    // drawTableHeader($pdf, $colItemWidth, $colDescWidth, $colQtyWidth, $colUnitPriceWidth, $colSubTotalWidth, $fontRegular);
    $pdf->SetX(15);
}
$pdf->SetFont($fontRegular, '', 11);
// Empty row
$pdf->Cell($colItemWidth, 8, '', 1, 0, 'C');
$pdf->Cell($colDescWidth, 8, '', 1, 0, 'L');
$pdf->Cell($colSubTotalWidth, 8, '', 1, 1, 'R');

// Total Amount (Before VAT & Discount)
$pdf->Cell($colItemWidth, 8, '', 1, 0, 'C');
$pdf->Cell($colDescWidth, 8, 'Total Amount (Before VAT & Discount)', 1, 0, 'R');
$pdf->SetFont($fontCurrency, '', 11);
$pdf->MultiCell($colSubTotalWidth, 8, '₥ ' . number_format($totalBeforeVAT, 2), 1, 'R', false, 1, '', '', true, 0, false, true, 8, 'M');
$pdf->SetFont($fontRegular, '', 11);
// VAT 15%
$pdf->Cell($colItemWidth, 8, '', 1, 0, 'C');
$pdf->Cell($colDescWidth, 8, 'VAT 15%', 1, 0, 'R');
$pdf->SetFont($fontCurrency, '', 11);
$pdf->MultiCell($colSubTotalWidth, 8, '₥ ' . number_format($vatTotal, 2), 1, 'R', false, 1, '', '', true, 0, false, true, 8, 'M');

$pdf->SetFont($fontRegular, '', 11);
// Discount
$pdf->Cell($colItemWidth, 8, '', 1, 0, 'C');
$pdf->Cell($colDescWidth, 8, 'Discount', 1, 0, 'R');
$pdf->SetFont($fontCurrency, '', 11);
$pdf->Cell($colSubTotalWidth, 8, '₥ 0.00', 1, 1, 'R');
$pdf->SetFont($fontRegular, '', 11);
// Discount %
$pdf->Cell($colItemWidth, 8, '', 1, 0, 'C');
$pdf->Cell($colDescWidth, 8, 'Discount %', 1, 0, 'R');
$pdf->SetFont($fontCurrency, '', 11);
$pdf->Cell($colSubTotalWidth, 8, '0%', 1, 1, 'R');
$pdf->SetFont($fontRegular, '', 11);
// Total after discount
$pdf->Cell($colItemWidth, 8, '', 1, 0, 'C');
$pdf->Cell($colDescWidth, 8, 'Total after discount', 1, 0, 'R');
$pdf->SetFont($fontCurrency, '', 11);
$pdf->MultiCell($colSubTotalWidth, 8, '₥ ' . number_format($totalBeforeVAT, 2), 1, 'R', false, 1, '', '', true, 0, false, true, 8, 'M');
$pdf->SetFont($fontRegular, '', 11);
// Total VAT after discount
$pdf->Cell($colItemWidth, 8, '', 1, 0, 'C');
$pdf->Cell($colDescWidth, 8, 'Total VAT after discount', 1, 0, 'R');
$pdf->SetFont($fontCurrency, '', 11);
$pdf->MultiCell($colSubTotalWidth, 8, '₥ ' . number_format($vatTotal, 2), 1, 'R', false, 1, '', '', true, 0, false, true, 8, 'M');


// Grand Total
$pdf->SetFont($fontRegular, 'B', 11);
$grandTotal = $totalBeforeVAT + $vatTotal;
$pdf->Cell($colItemWidth, 8, '', 1, 0, 'C');
$pdf->Cell($colDescWidth, 8, 'Grand Total', 1, 0, 'R');
$pdf->SetFont($fontCurrency, '', 11);
$pdf->MultiCell($colSubTotalWidth, 8, '₥ ' . number_format($grandTotal, 2), 1, 'R', false, 1, '', '', true, 0, false, true, 8, 'M');


// Add space after table
$pdf->Ln(25);

// Add third page
$pdf->AddPage('P', 'A4');

// Set margins for third page
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(true, 25);

$pdf->Ln(15);

if (!empty($quotationData['contentScopeOfWork'])) {
    // Clean content: remove empty <p> or <br> at start/end
    $content = trim($quotationData['contentScopeOfWork']);

    // Remove <p><br></p> or <p>&nbsp;</p> at the start
    $content = preg_replace('/^(<p>(<br>|&nbsp;)?<\/p>)+/i', '', $content);

    // Remove <p><br></p> or <p>&nbsp;</p> at the end
    $content = preg_replace('/(<p>(<br>|&nbsp;)?<\/p>)+$/i', '', $content);

    if (!empty(strip_tags($content))) { // Ensure content is not empty after stripping
        $pdf->SetFont($fontRegular, 'B', 25);
        $pdf->SetTextColor(130, 22, 92);
        $pdf->SetX(15);
        $pdf->SetMargins(15, 15, 15);
        $pdf->Cell(180, 8, 'Scope of Work', 0, 1, 'L');

        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(10);
        $pdf->SetFont($fontRegular, '', 15);
        $pdf->writeHTML($content, true, false, true, false, 'J');
        $pdf->Ln(10);
    }
}

// Fetch project timeline data
$total_days = 0;
$timelines = [];
$res = $db->query("SELECT * FROM consultation_project_timeline WHERE documentId = ?s ORDER BY timelineId", $documentID);
while ($row = mysqli_fetch_assoc($res)) {
    $timelines[] = $row;
    $total_days += intval($row['durationDays']);
}

if (!empty($timelines)) {
    // Add "Project Timeline" heading (similar style to Scope of Work)
    $pdf->SetFont($fontRegular, 'B', 25);
    $pdf->SetTextColor(130, 22, 92);
    $pdf->SetX(15);
    $pdf->Cell(180, 8, 'Project Timeline', 0, 1, 'L');
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Ln(10);

    // Set table column widths for timeline
    // --- Timeline Table Setup ---
    $colPhaseWidth = 140; // Phase column
    $colDurationWidth = 40; // Duration column
    $lineHeight = 8;

    // Function to draw timeline table header with bottom border only
    function drawTimelineHeader($pdf, $colPhaseWidth, $colDurationWidth, $fontRegular) {
        $pdf->SetFont($fontRegular, 'B', 11);
        $pdf->SetTextColor(0, 0, 0);

        // Header row: bottom border only
        $pdf->Cell($colPhaseWidth, 8, 'Phase', 'BI', 0, 'L', 0);
        $pdf->Cell($colDurationWidth, 8, 'Duration (Days)', 'B', 1, 'C', 0);

        // Reset font for body
        $pdf->SetFont($fontRegular, '', 11);
    }

    // Draw initial header
    drawTimelineHeader($pdf, $colPhaseWidth, $colDurationWidth, $fontRegular);

    // Add timeline rows
    foreach ($timelines as $timeline) {
        $nbLines = $pdf->getNumLines($timeline['phase'], $colPhaseWidth);
        $rowHeight = $lineHeight * $nbLines;

        // Check page break
        $currentY = $pdf->GetY();
        $pageBottom = $pdf->getPageHeight() - $pdf->getBreakMargin();
        if ($currentY + $rowHeight > $pageBottom) {
            $pdf->AddPage('P', 'A4');
            $pdf->SetMargins(15, 15, 15);
            $pdf->SetAutoPageBreak(true, 25);
            $pdf->SetX(15);
            drawTimelineHeader($pdf, $colPhaseWidth, $colDurationWidth, $fontRegular);
        }

        $xStart = $pdf->GetX();
        $yStart = $pdf->GetY();

        // Phase cell (no border)
        $pdf->MultiCell($colPhaseWidth, $rowHeight, $timeline['phase'], 0, 'L', false, 0, '', '', true, 0, false, true, $rowHeight, 'M');

        // Vertical line separator
        $pdf->Line($xStart + $colPhaseWidth, $yStart, $xStart + $colPhaseWidth, $yStart + $rowHeight);

        // Duration cell (no border)
        $pdf->MultiCell($colDurationWidth, $rowHeight, $timeline['durationDays'], 0, 'C', false, 1, '', '', true, 0, false, true, $rowHeight, 'M');
    }

    // Total row
    $total_weeks = ceil($total_days / 7);
    $total_str = $total_weeks . ' weeks';

    // Check page break for total row
    $currentY = $pdf->GetY();
    if ($currentY + 8 > $pageBottom) {
        $pdf->AddPage('P', 'A4');
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 25);
        $pdf->SetX(15);
        drawTimelineHeader($pdf, $colPhaseWidth, $colDurationWidth, $fontRegular);
    }

    // Total row: bottom border
    $pdf->SetFont($fontRegular, 'B', 11);
    $xStart = $pdf->GetX();
    $yStart = $pdf->GetY();

    // Phase cell with "Total"
    $pdf->MultiCell($colPhaseWidth, 8, 'Total Duration', 0, 'L', false, 0, '', '', true, 0, false, true, 8, 'M');

    // Vertical line separator
    $pdf->Line($xStart + $colPhaseWidth, $yStart, $xStart + $colPhaseWidth, $yStart + 8);

    // Duration cell with total weeks
    $pdf->MultiCell($colDurationWidth, 8, $total_str, 0, 'C', false, 1, '', '', true, 0, false, true, 8, 'M');

    $pdf->Ln(15); // Space after timeline
}

// Add heading for Terms and Conditions
$pdf->SetFont($fontRegular, 'B', 25);
$pdf->SetTextColor(130, 22, 92);
$pdf->SetX(15);
$pdf->Cell(180, 8, 'Terms and Conditions', 0, 1, 'L');
$pdf->SetTextColor(0, 0, 0);
// $pdf->Ln(10);

// Terms content array
$terms = [
    'Payment Terms' => [
        '50% advance upon acceptance.',
        '50% upon system deployment.'
    ],
    'Scope of Work' => [
        'The project scope includes only the features and modules outlined in this quotation.',
        'Any additional features or changes requested after the project has started may require a change-order and additional costs.'
    ],
    'Project Timeline and Delays' => [
        'The project timeline outlined in this quotation is an estimate and may be subject to adjustments based on client feedback, unforeseen technical challenges, or scope changes.',
        'Any delays caused by the client (e.g., late approvals, incomplete information) may impact the delivery schedule.'
    ],
    'Client Responsibilities' => [
        'The client shall provide all necessary access, data, and documentation required to complete the project.',
        'The client shall designate a point of contact for communication and approvals throughout the project.'
    ],
    'Warranty and Support' => [
        'We provide a warranty period of 60 days post-deployment to address any bugs or issues identified in the system.',
        'Support beyond the warranty period will be available under a separate maintenance agreement.'
    ],
    'Ownership and Intellectual Property' => [
        'Upon full payment, the client will own the source code and documentation of the custom ERP system.',
        'GrandMaster Consultation retains the right to reuse non-client specific components or libraries developed during the project.'
    ],
    'Confidentiality' => [
        'Both parties agree to maintain the confidentiality of all proprietary information shared during the project.',
        'Neither party shall disclose or use the other party’s confidential information for any purpose outside the scope of this agreement.'
    ],
    'Limitation of Liability' => [
        'GrandMaster Consultation shall not be liable for any indirect, incidental, or consequential damage arising from the use or performance of the system.',
        'Our liability is limited to the amount paid by the client for the project.'
    ],
    'Termination' => [
        'Either party may terminate the project by providing 30 days’ written notice.',
        'In the event of termination, the client shall pay for all completed work and reimbursable expenses up to the termination date.'
    ]
];

// Set fonts for subheadings and bullets
$subFont = [$fontRegular, 'B', 14];
$bulletFont = [$fontRegular, 'B', 12]; // Bold bullet
$contentFont = [$fontRegular, '', 12]; // Normal content
$lineHeight = 6; 
$bulletSpacing = 4; // space between bullet and text

// Build HTML content for terms
$termsHtml = '<div style="font-size:14px; margin-left:0;">'; // Ensure HTML respects PDF margin

foreach ($terms as $heading => $points) {
    // Subheading aligned with main heading
    $termsHtml .= '<p style="font-weight:bold; margin:0 0 4px 0; padding:0;">' . htmlspecialchars($heading) . '</p>';
    
    // Bullet points with no extra left margin
    $termsHtml .= '<ul style="margin:0 0 10px 15px; padding:0; list-style-type: disc;">';
    foreach ($points as $point) {
        $termsHtml .= '<li style="font-weight:normal; margin-bottom:4px;">' . htmlspecialchars($point) . '</li>';
    }
    $termsHtml .= '</ul>';
}

$termsHtml .= '</div>';

// Write HTML using TCPDF
$pdf->SetFont($fontRegular, '', 15); // Normal font for content
$pdf->writeHTMLCell(0, 0, 15, '', $termsHtml, 0, 1, 0, true, 'J', true);
$pdf->Ln(10);

// Add new page for Acceptance section
$pdf->AddPage('P', 'A4');

// Set margins for new page
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(true, 25);

// Add space before section
$pdf->Ln(15);

// Add "Acceptance" heading
$pdf->SetFont($fontRegular, 'B', 25);
$pdf->SetTextColor(130, 22, 92);
$pdf->SetX(15);
$pdf->Cell(180, 8, 'Acceptance', 0, 1, 'L');
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(10);

// Add paragraph with justified text
$pdf->SetFont($fontRegular, '', 15);
$pdf->SetX(15);
$pdf->SetMargins(15, 15, 15);
$paragraph = 'If you find the above terms acceptable, please sign below and return a copy to us to proceed with the project.';
$pdf->writeHTML($paragraph, true, false, true, false, 'J');

// Add space before table
$pdf->Ln(15);

// Set font for table
$pdf->SetFont($fontRegular, 'B', 11);

// Table - Company names row
$col1Width = 90;
$col2Width = 90;
$pdf->SetFont($fontRegular, 'B', 11);

$yStart = $pdf->GetY();
$xStart = 25; // starting X position

// Calculate required heights for both columns
$companyHeight  = $pdf->getStringHeight($col1Width, $companyInfo->companyName);
$customerHeight = $pdf->getStringHeight($col2Width, $customer->companyName);
$rowHeight = max($companyHeight, $customerHeight);

// First column - company name
$pdf->SetXY($xStart, $yStart);
$pdf->MultiCell($col1Width, $rowHeight, $companyInfo->companyName, 0, 'L', false, 0);

// Second column - customer name
$pdf->SetXY($xStart + $col1Width, $yStart);
$pdf->MultiCell($col2Width, $rowHeight, $customer->companyName, 0, 'L', false, 0);

// Move cursor to next line
$pdf->Ln($rowHeight);

// Add 3 rows of whitespace (7mm each, total 21mm)
$pdf->Ln(7);
$pdf->SetX(25);
$pdf->Cell($col1Width, 7, '', 0, 0, 'L');
$pdf->Cell($col2Width, 7, '', 0, 1, 'L');
$pdf->Ln(7);
$pdf->SetX(25);
$pdf->Cell($col1Width, 7, '', 0, 0, 'L');
$pdf->Cell($col2Width, 7, '', 0, 1, 'L');

// Name row
$pdf->SetFont($fontRegular, '', 11);
$pdf->SetX(25);
$pdf->Cell($col1Width, 7, 'Name:', 0, 0, 'L');
$pdf->Cell($col2Width, 7, 'Name:', 0, 1, 'L');

// Title row
$pdf->SetX(25);
$pdf->Cell($col1Width, 7, 'Title:', 0, 0, 'L');
$pdf->Cell($col2Width, 7, 'Title:', 0, 1, 'L');

// Add 3 rows of whitespace (7mm each, total 21mm)
$pdf->Ln(7);
$pdf->SetX(25);
$pdf->Cell($col1Width, 7, '', 0, 0, 'L');
$pdf->Cell($col2Width, 7, '', 0, 1, 'L');
$pdf->Ln(7);
$pdf->SetX(25);
$pdf->Cell($col1Width, 7, '', 0, 0, 'L');
$pdf->Cell($col2Width, 7, '', 0, 1, 'L');

// Signature row
$pdf->SetX(25);
$pdf->Cell($col1Width, 7, 'Signature:', 0, 0, 'L');
$pdf->Cell($col2Width, 7, 'Signature:', 0, 1, 'L');

// Date row
$pdf->SetX(25);
$pdf->Cell($col1Width, 7, 'Date:', 0, 0, 'L');
$pdf->Cell($col2Width, 7, 'Date:', 0, 1, 'L');

// Output the PDF
if($isDownload == 1) {
    $pdf->Output('Quotation_' . getQuotationNumberFromConsultationDocumentID($documentID) . '.pdf', 'D');
} else {
    $pdf->Output('Quotation_' . getQuotationNumberFromConsultationDocumentID($documentID) . '.pdf', 'I');
}