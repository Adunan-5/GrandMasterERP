<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";
require_once('../../vendor/autoload.php');

$isDownload = false;
$isDownload = filter_input(INPUT_GET, 'isdownload', FILTER_VALIDATE_INT);

$gdnID = "";
$gdnID = filter_input(INPUT_GET, 'gdnID', FILTER_VALIDATE_INT);
if ($gdnID === null || $gdnID === false || filter_var($gdnID, FILTER_VALIDATE_INT) === false) {
    header("location:/order-management/list");
    exit();
}

//$fontName = "grandmasterbook";
$fontNameArabic = "dejavusans";
$fontName       = "dejavusans";
//$fontNameCurrency = "dejavusansbook";
//$fontName = "expoarabicbook";
//$fontName = "tajawal";
$fontName = "dejavusansbook";

class CustomPDF extends TCPDF
{
    // Page Header
    public function Header()
    {
        global $pageMargin;
        global $pageWidth;
        global $gdnID;
        global $gdnDocument;
        global $fontName;

        $pageWidth = $this->getPageWidth();

//Main Header
        $this->Image(__DIR__ . '/../../assets/img/branding/G-Logo.png', 5, 5, 15);            // Adjust logo size and position
        $this->Image(__DIR__ . '/../../assets/img/branding/Kavero_light.png', 250, 3, 45); // Adjust Corner badge
        // $this->Image(__DIR__ . '/../../assets/img/branding/document_corner.jpg', 255, 0, 45); // Adjust Corner badge


// Title
        $this->SetFont($fontName, 'B', 16);
        $titleText = 'Pick and Pack ' . 'الجمع والتعبئة';
        $titleWidth  = $this->GetStringWidth($titleText);
        $titleHeight = $this->getStringHeight($titleWidth, $titleText);

// Set position for the "Goods Delivery Note" text
        $centerX = (($pageWidth) - $titleWidth) / 2;
        $this->SetXY($centerX, 10);
        $this->Cell($titleWidth, $titleHeight, $titleText, 0, 1, 'C');  // '1' moves to the next line after this cell


        $lineHeight = 6;

        // Position for the left side (English content)
        $this->SetFont($fontName, '', 12);
        $this->SetXY(5, 30);

// GDN Number
//        $this->Cell(0, $lineHeight, 'GDN #: ' . getGDNNumberFromDocumentID($gdnID), 0, 0, 'L');
//        $arabicLabel      = 'رقم السند #:';
//        $arabicLabelWidth = $this->GetStringWidth($arabicLabel);
//
//        $this->Cell(0, $lineHeight, $arabicLabel, 0, 0, 'R');
//        $this->setX($pageWidth - $arabicLabelWidth - $pageMargin - 2);
//        $this->Cell(1, $lineHeight, getGDNNumberFromDocumentID($gdnID), 0, 1, 'R');
//
//// Date
//        $this->SetX(5);
//        $this->Cell(0, $lineHeight, 'Date: ' . formatDate($gdnDocument->createdAt, false), 0, 0, 'L');
//        $arabicLabel      = 'التاريخ: ';
//        $arabicLabelWidth = $this->GetStringWidth($arabicLabel);
//
//        $this->Cell(0, $lineHeight, $arabicLabel, 0, 0, 'R');
//        $this->setX($pageWidth - $arabicLabelWidth - $pageMargin - 2);
//        $this->Cell(1, $lineHeight, formatDate($gdnDocument->createdAt, false), 0, 0, 'R');

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

$gdnDocument = new GDNDocument();
$gdnDocument->loadById($gdnID);

$companyInfo = new Company();
$companyInfo->loadById(1);

$customerInfo = null;
if ($gdnDocument->transactionType !== 'TRANSFER') {
    $customerInfo = new Customer();
    $customerInfo->loadById($gdnDocument->customerId);

    $salesOrderID = $gdnDocument->documentId;

    $keyDocument = new KeyDocument();
    $keyDocument->loadById($salesOrderID);
}


//////////////////////////////////


// Create PDF instance in landscape mode
$pageMargin = 5;
$pdf        = new CustomPDF('L', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('GrandMaster');
$pdf->SetTitle('GDN ' . getGDNNumberFromDocumentID($gdnID));
$pdf->SetMargins($pageMargin, $pageMargin, $pageMargin);
$pdf->AddPage();


// Page width (in mm) for A4 landscape is 297 mm, with some margins taken into account
$pageWidth = 297 - 10;                                // 5 mm left and right margins
$pageWidth = $pdf->getPageWidth();

if ($gdnDocument->transactionType !== 'TRANSFER' && $customerInfo !== null) {
    //Customer Information
    $pdf->SetFont($fontName, '', 10);

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
        ['Cust. PO# ', $keyDocument->PONumber, 'امر شراء العميل ', $keyDocument->PONumber],
        ['Payment Terms ', $keyDocument->getPaymentTermName(), 'شروط الدفع ', $keyDocument->getPaymentTermName()],
        ['Shipment# ', getGDNNumberFromDocumentID($gdnID), 'رقم الشحن  ', getGDNNumberFromDocumentID($gdnID)],
        ['Carrier ', getCarrierFromGDNID($gdnID), 'الناقل ', getCarrierFromGDNID($gdnID)],
        ['Tracking# ', getCarrierWayBillFromGDNID($gdnID), 'رقم التتبع للناقل ', getCarrierWayBillFromGDNID($gdnID)]
    ];

    // Loop through the details to create rows
    //foreach ($details as $row) {
    //    $pdf->SetXY($startX, $startY);
    //    $pdf->SetFont($fontName, 'B', 10);
    //    $pdf->Cell($pdf->GetStringWidth($row[0]), $rowHeight, $row[0], 0, 0, 'L');
    //
    //    $pdf->SetX($startX + $pdf->GetStringWidth($row[0]) + $space);
    //    $pdf->SetFont($fontName, '', 10);
    //    $pdf->Cell(0, $rowHeight, $row[1], 0, 0, 'L');
    //
    //    // Arabic label (e.g., السادة), right-aligned
    //    $pdf->SetFont($fontName, 'B', 10);
    //    $arabicLabelWidth = $pdf->GetStringWidth($row[2]);
    //    $pdf->SetX($pdf->getPageWidth() - $arabicLabelWidth - $pageMargin);
    //    $pdf->Cell($arabicLabelWidth, $rowHeight, $row[2], 0, 0, 'R');
    //
    //    // Arabic value (e.g., company name), right-aligned with space
    //    $pdf->SetFont($fontName, '', 10);
    //    $arabicValueWidth = $pdf->GetStringWidth($row[3]);
    //    $pdf->SetX($pdf->getPageWidth() - $arabicLabelWidth - $arabicValueWidth - $space - $pageMargin);
    //    $pdf->Cell($arabicValueWidth, $rowHeight, $row[3], 0, 0, 'R');
    //
    //    // Move to the next row
    //    $startY += $rowHeight;
    //}
}


//Line Items table header
// Define the initial header widths and scale them
$headers      = [
    ['NO.', 'رقم'],
    [' ', ' '],
    ['Item', 'رقم الصنف والوصف'],
    ['Shipment#', 'رقم الشحنة'],
    ['Qty', 'الكمية']
];
$headerWidths = [20, 40, 80, 20, 20];

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
$resLineItems     = $db->query("SELECT * FROM pick_and_pack_line_items WHERE `gdnId` = ?s AND `orderWHStatus` = ?s", $gdnDocument->gdnId, ORDER_WH_STATUS_READY);
$itemSerialNumber = 0;
while ($rowLineItem = mysqli_fetch_assoc($resLineItems)) {

    $imageUrl = getImageForSparepartID($rowLineItem['itemId']);
    $imageFullPath = $imageBasePath . $imageUrl;
    $partNumber = getInternalReferenceNumberForSparepartID($rowLineItem['itemId']);
    $description = getItemDescriptionForSparepartID($rowLineItem['itemId']);
    $brandName = getItemBrandNameForSparepartID($rowLineItem['itemId']);

    $item    = [
        (string)++$itemSerialNumber, // Serial Number
        file_exists($imageFullPath) ? $imageFullPath : '', // Full image path if it exists
        ['partNumber' => $partNumber, 'description' => $description,  'brand' => $brandName], // Part Number & Description as an array
        $rowLineItem['shipmentNumberPrefix'] . $rowLineItem['shipmentNumber'], // Shipment Number
        $rowLineItem['quantity'], // Quantity
    ];
    $items[] = $item;
}

$pdf->SetAutoPageBreak(TRUE, 25);

// Print headers with scaled widths
$pdf->SetFont($fontName, 'B', 10);
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

//$pdf->SetXY(5, $pdf->GetY() -8); // Adjust Y-coordinate to place below the table
//$pdf->AddPage();


$pdf->SetFont($fontName, 'B', 10);

// Add total amount details (English and Arabic on the same line)
$startY = $pdf->GetY() + 10;

// Add the English text, wrapped into two lines
//$pdf->SetXY(10, $startY); // Set the starting position
//$pdf->MultiCell(95, 5, 'I received the goods in good condition and according to the required specifications', 0, 'L', false);

// Add the Arabic text, wrapped into two lines, starting at the same Y position
//$pdf->SetXY(190, $startY); // Use the same starting Y position for alignment
//$pdf->MultiCell(95, 5, 'استلمت البضاعة بحالة جيدة وحسب المواصفات المطلوبة', 0, 'R', false);

// Add the "Name" section (English and Arabic)
//$pdf->SetXY(60, $pdf->GetY() + 10);
//$pdf->SetFont($fontName, '', 10);
//$pdf->Cell(0, 10, 'Name:', 0, 0, 'L');
//$pdf->SetXY(130, $pdf->GetY());
//$pdf->Cell(95, 10, 'الاسم:', 0, 0, 'R');

// Add Date line (with English and Arabic labels)
//$pdf->SetXY(60, $pdf->GetY() + 5);
//$pdf->Cell(95, 10, 'Date:', 0, 0, 'L');
//$pdf->SetXY(130, $pdf->GetY());
//$pdf->Cell(95, 10, 'التاريخ:', 0, 0, 'R');

// Add signature line (with English and Arabic labels)
//$pdf->SetXY(60, $pdf->GetY() + 5);
//$pdf->Cell(95, 10, 'Signature:', 0, 0, 'L');
//$pdf->SetXY(130, $pdf->GetY());
//$pdf->Cell(95, 10, 'التوقيع:', 0, 0, 'R');

// Add Contact line (with English and Arabic labels)
//$pdf->SetXY(60, $pdf->GetY() + 5);
//$pdf->Cell(95, 10, 'Contact Number:', 0, 0, 'L');
//$pdf->SetXY(130, $pdf->GetY());
//$pdf->Cell(95, 10, 'رقم: التواصل:', 0, 0, 'R');


if ($isDownload == 1)
// Output the PDF
    $pdf->Output(getGDNNumberFromDocumentID($gdnID)  . '.pdf', 'D');
else
    $pdf->Output(getGDNNumberFromDocumentID($gdnID)  . '.pdf', 'I');


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