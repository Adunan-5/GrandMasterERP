<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";
require_once('../../vendor/autoload.php');

// Silence errors during PDF generation to prevent output
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/pdf_errors.log');

$isDownload = false;
$isDownload = filter_input(INPUT_GET, 'isdownload', FILTER_VALIDATE_INT);

$smaID = "";
$smaID = filter_input(INPUT_GET, 'smaID', FILTER_VALIDATE_INT);
if ($smaID === null || $smaID === false || filter_var($smaID, FILTER_VALIDATE_INT) === false) {
    header("location:/consultation/sma/list");
    exit();
}

class SMA_PDF extends TCPDF {
    // Page Header - Empty to avoid interference
    public function Header() {}

    // Page Footer
    public function Footer() {
    }
}

$proposalID = getProposalIDForSMAID($smaID);

$proposalTitle = getProposalTitleFromProposalID($proposalID);

$proposalNumber = getProposalNumberFromProposalID($proposalID);

$consultationSMA = new ConsultationSMADocument();
$consultationSMA->loadById($smaID);

$customerID = $consultationSMA->customerID;

$customer = new Customer();
$customer->loadById($customerID);

$companyInfo = new Company();
$companyInfo->loadById(2);

$htmlContent = $consultationSMA->content;

$sections = preg_split('/(<h1[^>]*>.*?<\/h1>)/s', $htmlContent, -1, PREG_SPLIT_DELIM_CAPTURE);


$fontRegular = 'ttsupermolot';

// Create new PDF document
$pdf = new SMA_PDF('P', 'mm', 'A4', true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('GrandMaster');
$pdf->SetTitle('Service Maintenance Agreement');
$pdf->SetSubject('SMA for ' . $proposalTitle);

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
$subTitleFontSize = 23;

$mainTitle = 'Service Maintenance Agreement (SMA)';

// Set font for main title
$pdf->SetFont($fontRegular, 'B', $mainTitleFontSize);
$pdf->SetXY(15, 80); // X=15 for margin, Y=60 from top
$pdf->MultiCell(180, 15, $mainTitle, 0, 'C'); // Width 180 mm, center aligned

$pdf->Ln(10);

// Set font for subtitle
$pdf->SetFont($fontRegular, '', $subTitleFontSize);
$pdf->SetX(15); // Same left margin
$pdf->MultiCell(180, 10, $proposalTitle, 0, 'C');

// Add space before tables
$pdf->Ln(30);

// Set font for tables
$pdf->SetFont($fontRegular, '', 11);

// First table - Customer and Company details
$col1Width = 80;
$col2Width = 30;
$col3Width = 80;

// SMA for row
$pdf->Cell($col1Width, 7, 'SMA for:', 0, 0, 'L');
$pdf->Cell($col2Width, 7, '', 0, 0);
$pdf->Cell($col3Width, 7, '', 0, 1);

// Company names row
$pdf->SetFont($fontRegular, 'B', 11);
$yStart = $pdf->GetY();

$pdf->MultiCell($col1Width, 7, $customer->companyName, 0, 'L', false, 0);
$pdf->MultiCell($col2Width, 7, '', 0, 'L', false, 0);
$pdf->MultiCell($col3Width, 7, $companyInfo->companyName, 0, 'L', false, 1);

// Arabic names row
$pdf->SetFont('dejavusansbook', '', 11);

// Reset Y to same start if line height increased by any MultiCell
$pdf->SetY($pdf->GetY());

$pdf->MultiCell($col1Width, 7, $customer->companyNameAr, 0, 'L', false, 0);
$pdf->MultiCell($col2Width, 7, '', 0, 'L', false, 0);
$pdf->MultiCell($col3Width, 7, $companyInfo->companyNameAr, 0, 'L', false, 1);

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
$pdf->Cell($col3Width, 7, 'For Proposal:', 0, 1, 'L');

// Names row
// $pdf->SetFont($fontRegular, '', 11);
// $pdf->Cell($col1Width, 7, getDisplayNameFromUserID($consultationSMA->createdBy) . ',', 0, 0, 'L');
// $pdf->Cell($col2Width, 7, '', 0, 0);
// $pdf->SetFont($fontRegular, 'B', 11);
// $pdf->Cell($col3Width, 7, $proposalNumber, 0, 1, 'L');

// Company row
// $pdf->SetFont($fontRegular, '', 11);
// $pdf->Cell($col1Width, 7, $companyInfo->companyName, 0, 1, 'L');

// Names row
$pdf->SetFont($fontRegular, '', 11);
$pdf->Cell($col1Width, 7, $companyInfo->companyName . ',', 0, 0, 'L');
$pdf->Cell($col2Width, 7, '', 0, 0);
$pdf->SetFont($fontRegular, 'B', 11);
$pdf->Cell($col3Width, 7, $proposalNumber, 0, 1, 'L');

// Company row
// $pdf->SetFont($fontRegular, '', 11);
// $pdf->Cell($col1Width, 7, $companyInfo->companyName, 0, 1, 'L');

// Add second page
$pdf->AddPage('P', 'A4');

// Set margins for second page
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(true, 25);

// Add heading for second page
$pdf->SetFont($fontRegular, '', 18);
$pdf->SetTextColor(240, 190, 17);
$pdf->SetXY(15, 25); // Left margin 15mm, top 15mm
$pdf->Cell(180, 10, 'Detailed Line Items', 0, 1, 'L');

// Add space before table
$pdf->Ln(10);

// Set table column widths
$colItemWidth = 15; // Item# column
$colDescWidth = 125; // Description column
$colSubTotalWidth = 40; // Sub Total column

$pdf->setLineWidth(0.01); // Set line width for table borders

// Set font for table
$pdf->SetFont($fontRegular, '', 11);
// Set text color for table header
$pdf->SetTextColor(0, 0, 0);

// Table header
$pdf->SetFont($fontRegular, 'B', 11);
$pdf->Cell($colItemWidth, 8, 'Item#', 1, 0, 'L', 0);
$pdf->Cell($colDescWidth, 8, 'Description', 1, 0, 'L', 0);
$pdf->Cell($colSubTotalWidth, 8, 'Sub Total', 1, 1, 'L', 0);

// Fetch line items
$res = $db->query("SELECT * FROM consultation_proposal_line_items WHERE documentId = ?s", $proposalID);
// $totalBeforeVAT = 0;
// $vatTotal = 0;
// $itemSerialNumber = 0;

// while ($row = mysqli_fetch_assoc($res)) {
//     $itemId = $row['itemId'];
//     $quantity = floatval($row['quantity']);
//     $unitPrice = floatval($row['unitPrice']);
//     $description = getItemDescriptionForConsultationServiceItemID($itemId);
//     $subtotal = $quantity * $unitPrice;
//     $vat = $subtotal * 0.15; // 15% VAT
//     $totalBeforeVAT += $subtotal;
//     $vatTotal += $vat;

//     $pdf->SetFont($fontRegular, '', 11);

//     // Starting positions
//     $startX = $pdf->GetX();
//     $startY = $pdf->GetY();

//     $lineHeight = 8;

//     // Calculate height based on number of lines in description
//     $nbLines = $pdf->getNumLines($description, $colDescWidth);
//     $rowHeight = $lineHeight * $nbLines;

//     // 1. Item#
//     $pdf->MultiCell($colItemWidth, $rowHeight, ++$itemSerialNumber, 1, 'C', false, 0, '', '', true, 0, false, true, $rowHeight, 'M');

//     // 2. Description
//     $pdf->MultiCell($colDescWidth, $rowHeight, $description, 1, 'L', false, 0, '', '', true, 0, false, true, $rowHeight, 'M');

//     // 3. Subtotal
//     $pdf->MultiCell($colSubTotalWidth, $rowHeight, number_format($subtotal, 2), 1, 'R', false, 1, '', '', true, 0, false, true, $rowHeight, 'M');
// }

// Get amounts from SMA
$totalBeforeVAT = floatval($consultationSMA->totalAmount);
$discountType = $consultationSMA->discountType;
$discountAmount = 0.00;
if($discountType === 'percentage') {
    $discountAmount = floatval($consultationSMA->smaDiscount) / 100 * $totalBeforeVAT;
} else {
    $discountAmount = floatval($consultationSMA->smaDiscount);
}
$vatTotal = ($totalBeforeVAT - $discountAmount) * 0.15; // 15% VAT on amount after discount
$grandTotal = $totalBeforeVAT - $discountAmount + $vatTotal;

$totalAfterDiscount = $totalBeforeVAT - $discountAmount;


$pdf->SetFont($fontRegular, '', 11);

// Only one line item: "Proposal"
$pdf->MultiCell($colItemWidth, 8, 1, 1, 'C', false, 0);
$pdf->MultiCell($colDescWidth, 8, ' For Proposal: ' . $proposalNumber, 1, 'L', false, 0);
$pdf->MultiCell($colSubTotalWidth, 8, number_format($totalBeforeVAT, 2), 1, 'R', false, 1);

// Table row  (empty)
$pdf->Cell($colItemWidth, 8, '', 1, 0, 'C');
$pdf->Cell($colDescWidth, 8, '', 1, 0, 'L');
$pdf->Cell($colSubTotalWidth, 8, '', 1, 1, 'R');

// Table row - Total before VAT
$pdf->Cell($colItemWidth, 8, '', 1, 0, 'C');
$pdf->Cell($colDescWidth, 8, 'Total Amount (Before VAT & Discount)', 1, 0, 'R');
$pdf->Cell($colSubTotalWidth, 8, number_format($totalBeforeVAT, 2), 1, 1, 'R');

// Table row - VAT 15%
$pdf->Cell($colItemWidth, 8, '', 1, 0, 'C');
$pdf->Cell($colDescWidth, 8, 'VAT 15%', 1, 0, 'R');
$pdf->Cell($colSubTotalWidth, 8, number_format($vatTotal, 2), 1, 1, 'R');

// Table row - Discount
$label = '';
if ($discountType === 'percentage') {
    $discountFormatted = rtrim(rtrim(number_format(floatval($consultationSMA->smaDiscount), 2), '0'), '.');
    $label = 'Discount ' . $discountFormatted . '%';
} else {
    $label = 'Discount';
}
$pdf->Cell($colItemWidth, 8, '', 1, 0, 'C');
$pdf->Cell($colDescWidth, 8, $label, 1, 0, 'R');
$pdf->Cell($colSubTotalWidth, 8, number_format($discountAmount, 2), 1, 1, 'R');

// Table row - Discount %
// $pdf->Cell($colItemWidth, 8, '', 1, 0, 'C');
// $pdf->Cell($colDescWidth, 8, 'Discount %', 1, 0, 'R');
// $pdf->Cell($colSubTotalWidth, 8, number_format($discountPercentage, 2) . '%', 1, 1, 'R');

// Table row - Total after discount
$pdf->Cell($colItemWidth, 8, '', 1, 0, 'C');
$pdf->Cell($colDescWidth, 8, 'Total after discount', 1, 0, 'R');
$pdf->Cell($colSubTotalWidth, 8, number_format($totalAfterDiscount, 2), 1, 1, 'R');

// Table row - Total VAT after discount
$pdf->Cell($colItemWidth, 8, '', 1, 0, 'C');
$pdf->Cell($colDescWidth, 8, 'Total VAT after discount', 1, 0, 'R');
$pdf->Cell($colSubTotalWidth, 8, number_format($vatTotal, 2), 1, 1, 'R');

// Table row - Grand Total
$pdf->SetFont($fontRegular, 'B', 11);
$grandTotal = $totalBeforeVAT + $vatTotal;
$pdf->Cell($colItemWidth, 8, '', 1, 0, 'C');
$pdf->Cell($colDescWidth, 8, 'Grand Total', 1, 0, 'R');
$pdf->Cell($colSubTotalWidth, 8, number_format($grandTotal, 2), 1, 1, 'R');

// Add space after table
$pdf->Ln(25);

//Content from the database
foreach ($sections as $index => $section) {
    if (preg_match('/<h1[^>]*>(.*?)<\/h1>/s', $section, $matches)) {
        $heading = trim($matches[1]);
        // Remove non-breaking spaces from the heading
        $heading = str_replace('&nbsp;', ' ', $heading);
        $pdf->SetFont($fontRegular, 'B', 25);
        $pdf->SetTextColor(240, 190, 17);
        $pdf->SetX(15); // Align with left margin
        $pdf->SetMargins(15, 15, 15);
        $pdf->Cell(180, 8, $heading, 0, 1, 'L');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(10); // 10mm space after heading

        // Get the content following the heading
        $contentStart = strpos($htmlContent, $section) + strlen($section);
        $nextHeadingPos = strpos($htmlContent, '<h1', $contentStart);
        $content = ($nextHeadingPos === false) ? substr($htmlContent, $contentStart) : substr($htmlContent, $contentStart, $nextHeadingPos - $contentStart);
        $content = trim($content);

        if (!empty($content)) {
            $pdf->SetFont($fontRegular, '', 15);
            $pdf->SetX(15);
            // Replace <br> with new lines and adjust <ul> styling
            $content = str_replace('&nbsp;', '', $content);
            $content = str_replace('<br>', '', $content);
            $content = preg_replace('/<ul>/', '<ul style="margin-left: 2mm; list-style-type: disc; list-style-position: outside; line-height: 1.5;">', $content);
            $content = preg_replace('/<li>/', '<li>', $content); // Ensure <li> is not altered
            $pdf->writeHTML($content, true, false, true, false, 'J');
            $pdf->Ln(10); // 15mm space after content before next heading
        }
    }
}

// Add new page for Acceptance section
$pdf->AddPage('P', 'A4');

// Set margins for new page
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(true, 25);

// Add space before section
$pdf->Ln(15);

// Add "Acceptance" heading
$pdf->SetFont($fontRegular, 'B', 25);
$pdf->SetTextColor(240, 190, 17);
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
$pdf->SetX(25);
$pdf->Cell($col1Width, 7, $companyInfo->companyName, 0, 0, 'L');
$pdf->Cell($col2Width, 7, $customer->companyName, 0, 1, 'L');

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
    $pdf->Output('Service_Maintenance_Agreement_' . $proposalNumber . '.pdf', 'D');
} else {
    $pdf->Output('Service_Maintenance_Agreement_' . $proposalNumber . '.pdf', 'I');
}