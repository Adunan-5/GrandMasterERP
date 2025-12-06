<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";
require_once('../../vendor/autoload.php');

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
$fontName = "dejavusansbook";
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
        $this->Image(__DIR__ . '/../../assets/img/branding/document_corner.jpg', 255, 0, 45); // Adjust Corner badge


// Title
        $this->SetFont($fontName, 'B', 16);
        $titleText   = 'Purchase Order';
        $titleWidth  = $this->GetStringWidth($titleText);
        $titleHeight = $this->getStringHeight($titleWidth, $titleText);

// Set position for the "Quotation" text
        $centerX = (($pageWidth) - $titleWidth) / 2;
        $this->SetXY($centerX, 10);
        $this->Cell($titleWidth, $titleHeight, $titleText, 0, 1, 'C');  // '1' moves to the next line after this cell


        $lineHeight = 6;

        // Position for the left side (English content)
        $this->SetFont($fontName, '', 12);
        $this->SetXY(5, 30);

// RFQ Number
        $this->Cell(0, $lineHeight, 'PO #: ' . $poNumber, 0, 1, 'L');

// Date
        $this->SetX(5);
        $this->Cell(0, $lineHeight, 'Date: ' . formatDate($poDocument->poDateCreated, false), 0, 1, 'L');

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
//        $this->SetFont($fontName, '', 10);
//        $this->Cell(0, 6, 'Thanks for choosing GrandMaster – The sole supplier in middle east for EU & US brands', 0, 1, 'C');
//        $this->Cell(0, 6, 'شكرًا لاختيارك جراند ماستر - المورد الوحيد في الشرق الأوسط للعلامات التجارية الأوروبية والأمريكية', 0, 1, 'C');
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
    ['Messrs. /Spett.le ', $supplierInfo->companyName],
    ['Vendor Shipping Amount/Trasporto ', '₥ ' . $poDocument->shippingCost],
    ['Vendor Ref/Documento: ', $poDocument->supplierQuotationNumber],
    ['ETA/ Datacons: ', formatDateShort($poDocument->etaDate, false)],
    ['GrandMaster paid ref: ', $poDocument->paymentRefNo],
    ['GrandMaster paid Date: ', formatDateShort($poDocument->paymentDate, false)]
];

// Loop through the details to create rows
foreach ($details as $row) {
    $pdf->SetXY($startX, $startY);
    $pdf->SetFont($fontName, 'B', 10);
    $pdf->Cell($pdf->GetStringWidth($row[0]), $rowHeight, $row[0], 0, 0, 'L');

    $pdf->SetX($startX + $pdf->GetStringWidth($row[0]) + $space);
    $pdf->SetFont($fontName, '', 10);
    $pdf->Cell(0, $rowHeight, $row[1], 0, 1, 'L');

    // Move to the next row
    $startY += $rowHeight;
}



// Add some space before the table
//$pdf->Ln(20);                                         // Adds 40 mm of space after the payment terms

//// Add table header and rows
//$pdf->SetFont($fontName, 'B', 5);
//$pdf->SetFillColor(230, 230, 230);
//


//Line Items table header
// Define the initial header widths and scale them
$headers = [
    ['NO.', 'رقم', 'serie'], // English, Arabic, Italian
    [' ', ' ', ' '], // Image column (no text, so all empty)
    ['Item', 'رقم الصنف والوصف', 'Articolo'], // English, Arabic, Italian
    ['Qty', 'الكمية', 'quantità'], // English, Arabic, Italian
    ['Accepted', 'مقبول', 'Accettato'], // English, Arabic, Italian
    ['Rejected', 'مرفوض', 'Rifiutato'], // English, Arabic, Italian
    ['Pending', 'معلق', 'In attesa'], // English, Arabic, Italian
    ['Reject Reason', 'سبب الرفض', 'Motivo del rifiuto'] // English, Arabic, Italian
];

$headerWidths = [20, 40, 90, 30, 30, 30, 30, 50]; // Adjusted widths


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
        $rowLineItem['quantity'], // Quantity
        $rowLineItem['acceptedQty'],
        $rowLineItem['rejectedQty'],
        $rowLineItem['pendingQty'],
        empty($rowLineItem['rejectReason']) ? '-' : $rowLineItem['rejectReason'],
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

$pdf->SetXY(5, 85);
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


//// Add total amount details (English)
$pdf->SetFont($fontName, '', 10);
//
//// Add total amount details (English and Arabic on the same line)
$pdf->SetXY(10, $pdf->GetY() + 10); // Adjust Y-coordinate to place below the table
$pdf->Cell(95, 10, 'Total Amount: ₥ ' . getTotalAmountByPOID($poID), 0, 0, 'L');
//$pdf->SetXY(190, $pdf->GetY());    // Adjust X-coordinate for right alignment and keep the Y-coordinate the same
//$pdf->Cell(95, 10, 'المبلغ الاجمالي (قبل الضريبة و الخصم): ' . getTotalAmountByDocumentID($documentID)['totalAmountBeforeVAT'], 0, 0, 'R');
//
//// Add VAT amount details (English and Arabic on the same line)
$pdf->SetXY(10, $pdf->GetY() + 5); // Move down to the next line
$pdf->Cell(95, 10, 'Total Amount with Shipping: ₥ ' . getTotalAmountWithShippingByPOID($poID), 0, 0, 'L');
//$pdf->SetXY(190, $pdf->GetY());    // Adjust X-coordinate for right alignment and keep the Y-coordinate the same
//$pdf->Cell(95, 10, 'قيمة الضريبة: ' . getTotalAmountByDocumentID($documentID)['totalVATAmount'], 0, 0, 'R');
//
//
//// Add Total discount (English and Arabic on the same line)
$pdf->SetXY(10, $pdf->GetY() + 5); // Move down to the next line
$pdf->Cell(95, 10, 'Currency: ' . getCurrencyNameFromID($companyInfo->currencyId), 0, 0, 'L');
//$pdf->SetXY(190, $pdf->GetY());    // Adjust X-coordinate for right alignment and keep the Y-coordinate the same
//$pdf->Cell(95, 10, ' اجمالي مبلغ الخصم: ' . getTotalAmountByDocumentID($documentID)['totalDiscountAmount'], 0, 0, 'R');
//
//// Add total amount after VAT details (English and Arabic on the same line)
//$pdf->SetXY(10, $pdf->GetY() + 5); // Move down to the next line
//$pdf->Cell(95, 10, 'Total Amount (After VAT & Discount): SAR ' . getTotalAmountByDocumentID($documentID)['totalAmountAfterVAT'], 0, 0, 'L');
//$pdf->SetXY(190, $pdf->GetY());     // Adjust X-coordinate for right alignment and keep the Y-coordinate the same
//$pdf->Cell(95, 10, '  المبلغ الاجمالي (بعد الضريبة و الخصم): ' . getTotalAmountByDocumentID($documentID)['totalAmountAfterVAT'], 0, 0, 'R');
//
// Add the "Approved By" section (English and Arabic)
$pdf->SetXY(60, $pdf->GetY() + 10); // Adjust Y-coordinate to place below the total amount after VAT
$pdf->SetFont($fontName, '', 10);
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