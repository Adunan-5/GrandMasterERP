<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";
require_once('../../vendor/autoload.php');


$isDownload = false;
$isDownload = filter_input(INPUT_GET, 'isdownload', FILTER_VALIDATE_INT);

$documentID = "";
$documentID = filter_input(INPUT_GET, 'documentID', FILTER_VALIDATE_INT);
if ($documentID === null || $documentID === false || filter_var($documentID, FILTER_VALIDATE_INT) === false) {
    header("location:/quotation/list");
    exit();
}


class CustomPDF extends TCPDF
{
    // Page Header
    public function Header()
    {
        global $pageMargin;
        global $pageWidth;
        global $quotationNumber;
        global $keyDocument;

        $pageWidth = $this->getPageWidth();

//Main Header
        $this->Image(__DIR__ . '/../../assets/img/branding/G-Logo.png', 5, 5, 15);            // Adjust logo size and position
        $this->Image(__DIR__ . '/../../assets/img/branding/document_corner.jpg', 255, 0, 45); // Adjust Corner badge


// Title
        $this->SetFont('dejavusans', 'B', 16);
        $titleText   = 'Quotation عرض أسعار';
        $titleWidth  = $this->GetStringWidth($titleText);
        $titleHeight = $this->getStringHeight($titleWidth, $titleText);

// Set position for the "Quotation" text
        $centerX = (($pageWidth) - $titleWidth) / 2;
        $this->SetXY($centerX, 10);
        $this->Cell($titleWidth, $titleHeight, $titleText, 0, 1, 'C');  // '1' moves to the next line after this cell


        $lineHeight = 6;
// Position for the left side (English content)
        $this->SetFont('dejavusans', '', 12);
//$this->SetXY(5, 30);
        $this->SetXY(5, 30);

        $this->Cell(0, $lineHeight, 'Quotation #: ' . $quotationNumber, 0, 0, 'L');

        $arabicLabel      = 'عرض اسعار #: ';
        $arabicLabelWidth = $this->GetStringWidth($arabicLabel);

        $this->Cell(0, $lineHeight, $arabicLabel, 0, 0, 'R');
        $this->setX($pageWidth - $arabicLabelWidth - $pageMargin - 2);
        $this->Cell(1, $lineHeight, $quotationNumber, 0, 1, 'R');

        $this->SetX(5);
        $this->Cell(0, $lineHeight, 'Date: ' . formatDate($keyDocument->quotationDateIssued, false), 0, 0, 'L');
        $arabicLabel      = 'التاريخ: ';
        $arabicLabelWidth = $this->GetStringWidth($arabicLabel);

        $this->Cell(0, $lineHeight, $arabicLabel, 0, 0, 'R');
        $this->setX($pageWidth - $arabicLabelWidth - $pageMargin - 2);
        $this->Cell(1, $lineHeight, formatDate($keyDocument->quotationDateIssued, false), 0, 0, 'R');


        $this->SetMargins($pageMargin, $pageMargin + 50, $pageMargin);

    }

    // Page Footer
    public function Footer()
    {
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
        $this->SetFont('dejavusans', '', 10);
        $this->Cell(0, 6, 'Thanks for choosing GrandMaster – The sole supplier in middle east for EU & US brands', 0, 1, 'C');
        $this->Cell(0, 6, 'شكرًا لاختيارك جراند ماستر - المورد الوحيد في الشرق الأوسط للعلامات التجارية الأوروبية والأمريكية', 0, 1, 'C');
    }
}


//////////////////////////////////

$keyDocument = new KeyDocument();
$keyDocument->loadById($documentID);
$quotationNumber = getQuotationNumberFromDocumentID($documentID);

$companyInfo = new Company();
$companyInfo->loadById(1);

$customerInfo = new Customer();
$customerInfo->loadById($keyDocument->customerId);

//////////////////////////////////


// Create PDF instance in landscape mode
$pageMargin = 5;
$pdf        = new CustomPDF('L', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('GrandMaster');
$pdf->SetTitle('Quotation ' . $quotationNumber);
$pdf->SetMargins($pageMargin, $pageMargin, $pageMargin);
$pdf->AddPage();


// Page width (in mm) for A4 landscape is 297 mm, with some margins taken into account
$pageWidth = 297 - 10;                                // 5 mm left and right margins
$pageWidth = $pdf->getPageWidth();


//Customer Information
$pdf->SetFont('dejavusans', '', 10);

// Define a starting position for the table
$startX     = 5;
$startY     = 50;
$rowHeight  = 5;
$labelWidth = 30;
$valueWidth = 65;
$space      = 2;

// Details to display
$details = [
    ['Messrs ', $customerInfo->companyName, 'السادة ', $customerInfo->companyNameAr],
    ['Cust. VAT ', $customerInfo->vatNumber, 'الرقم الضريبي للعميل ', $customerInfo->vatNumber],
    ['Cust. CR ', $customerInfo->companyCRNumber, 'رقم السجل التجاري للعميل ', $customerInfo->companyCRNumber],
    ['Grandmaster VAT ', $companyInfo->vatNumber, 'الرقم الضريبي لجراند ماستر ', $companyInfo->vatNumber],
    ['Grandmaster CR ', $companyInfo->companyCRNumber, 'رقم السجل التجاري لجراند ماستر ', $companyInfo->companyCRNumber],
    ['Payment Terms ', $keyDocument->getPaymentTermName(), 'شروط الدفع ', $keyDocument->getPaymentTermName()]
];

// Loop through the details to create rows
foreach ($details as $row) {

    $pdf->SetXY($startX, $startY);
    $pdf->SetFont('dejavusans', 'B', 10);
    $pdf->Cell($pdf->GetStringWidth($row[0]), $rowHeight, $row[0], 0, 0, 'L');


    $pdf->SetX($startX + $pdf->GetStringWidth($row[0]) + $space);
    $pdf->SetFont('dejavusans', '', 10);
    $pdf->Cell(0, $rowHeight, $row[1], 0, 0, 'L');


    $pdf->SetFont('dejavusans', 'B', 10);
    $pdf->SetX($pdf->getPageWidth() - $pdf->GetStringWidth($row[2]) - $pageMargin);
    $pdf->Cell(0, $rowHeight, $row[2], 0, 0, 'L');


    $pdf->SetX($pdf->getPageWidth() - $pdf->GetStringWidth($row[2]) - $pdf->GetStringWidth($row[3]) - $space);
    $pdf->SetFont('dejavusans', '', 10);
    $pdf->Cell(0, $rowHeight, $row[3], 0, 1, 'L');

    // Move to the next row
    $startY += $rowHeight;
}


// Add some space before the table
//$pdf->Ln(20);                                         // Adds 40 mm of space after the payment terms

//// Add table header and rows
//$pdf->SetFont('dejavusans', 'B', 5);
//$pdf->SetFillColor(230, 230, 230);
//


//Line Items table header
// Define the initial header widths and scale them
$headers      = [
    ['NO.', 'رقم'],
    ['Item No.', 'رقم الصنف'],
    ['Description', 'الوصف'],
    ['Unit', 'الوحدة'],
    ['Qty', 'الكمية'],
    ['Unit Price', 'سعر الوحدة'],
    ['VAT', 'الضريبة'],
    ['Total Value', 'القيمة الإجمالية'],
    ['ETA', 'تاريخ الوصول']
];
$headerWidths = [20, 30, 50, 15, 15, 20, 15, 25, 30]; // Initial widths

// Calculate the total width of the table
$totalWidth = array_sum($headerWidths) + 7;

// Calculate scale factor to fit the table within the page width
$scaleFactor = $pageWidth / $totalWidth;

// Adjust header widths proportionally
$scaledHeaderWidths = array_map(function ($width) use ($scaleFactor) {
    return $width * $scaleFactor;
}, $headerWidths);


//Build the line Items
$items            = array();
$resLineItems     = $db->query("SELECT * FROM line_items WHERE `documentId` = ?s", $keyDocument->documentId);
$itemSerialNumber = 0;
while ($rowLineItem = mysqli_fetch_assoc($resLineItems)) {

    $item    = [
        (string)++$itemSerialNumber, // Serial Number
        getPartNumberForSparepartID($rowLineItem['itemId']), // Part Number
        getItemDescriptionForSparepartID($rowLineItem['itemId']), // Description
        getUOMNameFromID($rowLineItem['UOM']), // UOM (Unit of Measure)
        $rowLineItem['quantity'], // Quantity
        $rowLineItem['unitPrice'], // Unit Price
        $rowLineItem['vatAmount'], // VAT Amount
        $rowLineItem['subTotal'], // Subtotal
        formatDate($rowLineItem['eta'], false) ?? "-"
    ];
    $items[] = $item;
}

//$pdf->SetXY(5, 95);
// Set up the PDF document
//$pdf->SetMargins(10, 10, 10);              // Left, Top, Right margins
$pdf->SetAutoPageBreak(TRUE, 25);

// Print headers with scaled widths
$pdf->SetFont('dejavusans', 'B', 10);
$pdf->SetFillColor(230, 230, 230);

$headerHeight = 10;                 // Uniform height for header cells

$pdf->SetXY(5, 85);
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
        $headerHeight, // Divide height between two lines
        $headerText,
        1,             // Border
        'C',           // Center alignment
        true           // Fill background
    );

    // Reset cursor position for the next cell
    $pdf->SetXY($xStart + $cellWidth, $yStart);
}

// Move to the next line after the header
$pdf->Ln();

// Set font for body content
$pdf->SetFont('dejavusans', '', 10);

// Print each row with scaled column widths
foreach ($items as $item) {
    $rowHeights    = [];
    $defaultHeight = 10; // Set default row height

    // First pass: Calculate the maximum height for each cell in the row
    foreach ($item as $key => $value) {
        $cellWidth        = $scaledHeaderWidths[$key];
        $calculatedHeight = $pdf->getStringHeight($cellWidth, $value) + $defaultHeight;
        $rowHeights[]     = max($defaultHeight, $calculatedHeight); // Use the greater of the default or calculated height
    }

    $maxHeight = max($rowHeights); // Maximum height for the row

    // Check if the row will overflow the page
    if ($pdf->GetY() + $maxHeight > $pdf->getPageHeight() - $pdf->getMargins()['bottom']) {
        $pdf->AddPage(); // Add a new page before printing the row
    }

    // Print the row with all cells aligned to the maximum height
    foreach ($item as $key => $value) {
        $cellWidth = $scaledHeaderWidths[$key];

        // Calculate the vertical offset for center alignment
        $contentHeight  = $pdf->getStringHeight($cellWidth, $value);
        $verticalOffset = ($maxHeight - $contentHeight) / 2;

        $xStart = $pdf->GetX();
        $yStart = $pdf->GetY();

        // Draw the cell border
        $pdf->Rect($xStart, $yStart, $cellWidth, $maxHeight);

        // Adjust Y position for vertical centering
        $pdf->SetXY($xStart, $yStart + $verticalOffset);

        // Print the text within the cell
        $pdf->MultiCell($cellWidth, $contentHeight, $value, 0, 'C', false);

        // Reset X and Y for next cell
        $pdf->SetXY($xStart + $cellWidth, $yStart);
    }

    // Move to the next line
    $pdf->Ln($maxHeight);
}



$pdf->SetXY(5, $pdf->GetY() -8); // Adjust Y-coordinate to place below the table


// Add total amount details (English)
$pdf->SetFont('dejavusans', '', 10);

// Add total amount details (English and Arabic on the same line)
$pdf->SetXY(10, $pdf->GetY() + 10); // Adjust Y-coordinate to place below the table
$pdf->Cell(95, 10, 'Total Amount (Before VAT): SAR ' . getTotalAmountByDocumentID($documentID)['totalAmountBeforeVAT'], 0, 0, 'L');
$pdf->SetXY(190, $pdf->GetY());    // Adjust X-coordinate for right alignment and keep the Y-coordinate the same
$pdf->Cell(95, 10, 'المبلغ الإجمالي (قبل الضريبة): ' . getTotalAmountByDocumentID($documentID)['totalAmountBeforeVAT'], 0, 0, 'R');

// Add VAT amount details (English and Arabic on the same line)
$pdf->SetXY(10, $pdf->GetY() + 5); // Move down to the next line
$pdf->Cell(95, 10, 'VAT Amount: SAR ' . getTotalAmountByDocumentID($documentID)['totalVATAmount'], 0, 0, 'L');
$pdf->SetXY(190, $pdf->GetY());    // Adjust X-coordinate for right alignment and keep the Y-coordinate the same
$pdf->Cell(95, 10, 'قيمة الضريبة: ' . getTotalAmountByDocumentID($documentID)['totalVATAmount'], 0, 0, 'R');

// Add total amount after VAT details (English and Arabic on the same line)
$pdf->SetXY(10, $pdf->GetY() + 5); // Move down to the next line
$pdf->Cell(95, 10, 'Total Amount (After VAT): SAR' . getTotalAmountByDocumentID($documentID)['totalAmountAfterVAT'], 0, 0, 'L');
$pdf->SetXY(190, $pdf->GetY());     // Adjust X-coordinate for right alignment and keep the Y-coordinate the same
$pdf->Cell(95, 10, 'المبلغ الإجمالي (بعد الضريبة): ' . getTotalAmountByDocumentID($documentID)['totalAmountAfterVAT'], 0, 0, 'R');

// Add the "Approved By" section (English and Arabic)
$pdf->SetXY(60, $pdf->GetY() + 10); // Adjust Y-coordinate to place below the total amount after VAT
$pdf->SetFont('dejavusans', '', 10);
$pdf->Cell(0, 10, 'Approved By:', 0, 0, 'L');
$pdf->SetXY(130, $pdf->GetY()); // Adjust X-coordinate for right alignment and keep the Y-coordinate the same
$pdf->Cell(95, 10, 'موافق من قبل:', 0, 0, 'R');

// Add space for the signature line
$pdf->SetXY(60, $pdf->GetY() + 5);
$pdf->Cell(95, 10, 'Name:', 0, 0, 'L');
$pdf->SetXY(130, $pdf->GetY());
$pdf->Cell(95, 10, 'الاسم:', 0, 0, 'R');

// Add signature line (with English and Arabic labels)
$pdf->SetXY(60, $pdf->GetY() + 5);
$pdf->Cell(95, 10, 'Signature:', 0, 0, 'L');
$pdf->SetXY(130, $pdf->GetY());
$pdf->Cell(95, 10, 'التوقيع:', 0, 0, 'R');


if ($isDownload == 1)
// Output the PDF
    $pdf->Output('Quotation_' . $quotationNumber . '.pdf', 'D');
else
    $pdf->Output('Quotation_' . $quotationNumber . '.pdf', 'I');