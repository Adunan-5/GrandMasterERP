<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";
require_once('../../vendor/autoload.php');

$isDownload = false;
$isDownload = filter_input(INPUT_GET, 'isdownload', FILTER_VALIDATE_INT);

$documentID = "";
$documentID = filter_input(INPUT_GET, 'documentID', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

if ($documentID === null || $documentID === false || empty($documentID)) {
    echo "Invalid";
    exit();
}

$documentID = decryptString($documentID);

class CustomPDF extends TCPDF
{
    // Page Header
    public function Header()
    {
        global $pageMargin;
        global $pageWidth;
        global $quotationNumber;
        global $orderNumber;
        global $keyDocument;

        $pageWidth = $this->getPageWidth();

//Main Header
        $this->Image(__DIR__ . '/../../assets/img/branding/G-Logo.png', 5, 5, 15);            // Adjust logo size and position
        $this->Image(__DIR__ . '/../../assets/img/branding/document_corner.jpg', 255, 0, 45); // Adjust Corner badge


// Title
        $this->SetFont('dejavusans', 'B', 16);
        $titleText = 'E-commerce Order' . ' طلب إلكتروني ';
        $titleWidth  = $this->GetStringWidth($titleText);
        $titleHeight = $this->getStringHeight($titleWidth, $titleText);

// Set position for the "E-commerce" text
        $centerX = (($pageWidth) - $titleWidth) / 2;
        $this->SetXY($centerX, 10);
        $this->Cell($titleWidth, $titleHeight, $titleText, 0, 1, 'C');  // '1' moves to the next line after this cell


        $lineHeight = 6;
// Position for the left side (English content)
        $this->SetFont('dejavusans', '', 12);
//$this->SetXY(5, 30);
        $this->SetXY(5, 30);

        $this->Cell(0, $lineHeight, 'E-commerce Order #: ' . $orderNumber, 0, 0, 'L');

        $arabicLabel      = 'طلب إلكتروني #:';
        $arabicLabelWidth = $this->GetStringWidth($arabicLabel);

        $this->Cell(0, $lineHeight, $arabicLabel, 0, 0, 'R');
        $this->setX($pageWidth - $arabicLabelWidth - $pageMargin - 2);
        $this->Cell(1, $lineHeight, $orderNumber, 0, 1, 'R');

        $this->SetX(5);
        $this->Cell(0, $lineHeight, 'Date: ' . formatDate($keyDocument->orderDateIssued, false), 0, 0, 'L');
        $arabicLabel      = 'التاريخ: ';
        $arabicLabelWidth = $this->GetStringWidth($arabicLabel);

        $this->Cell(0, $lineHeight, $arabicLabel, 0, 0, 'R');
        $this->setX($pageWidth - $arabicLabelWidth - $pageMargin - 2);
        $this->Cell(1, $lineHeight, formatDate($keyDocument->orderDateIssued, false), 0, 0, 'R');


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
$orderNumber = $keyDocument->orderId;

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
$pdf->SetTitle('E-commerce Order ' . $orderNumber);
$pdf->SetMargins($pageMargin, $pageMargin, $pageMargin);
$pdf->AddPage();


// Page width (in mm) for A4 landscape is 297 mm, with some margins taken into account
$pageWidth = 297 - 10;                                // 5 mm left and right margins
$pageWidth = $pdf->getPageWidth();


//Customer Information
$pdf->SetFont('dejavusans', '', 10);

// Define a starting position for the table
$startX     = 5;
$startY     = 45;
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
    ['Payment Terms ', $keyDocument->getPaymentTermName(), 'شروط الدفع ', $keyDocument->getPaymentTermName()],
    ['Carrier ', $keyDocument->carrier, 'الناقل ', $keyDocument->carrier]
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
    [' ', ' '],
    ['Item', 'رقم الصنف والوصف'],
    ['Qty', 'الكمية'],
    ['Unit Price', 'سعر الوحدة'],
    ['Discount %', 'نسبة الخصم'],
    ['Price After Discount', 'السعر بعد الخصم'],
    ['VAT', 'الضريبة'],
    ['Shipping', 'الشحن'],
    ['Total Value', 'القيمة الإجمالية'],
    ['ETA', 'تاريخ الوصول']
];
//$headerWidths = [8, 25, 45, 12, 18, 20, 25, 15, 25, 30]; // Initial widths
//$headerWidths = [10, 45, 12, 18, 20, 25, 15, 25, 30]; // Initial widths
$headerWidths = [10, 30, 40, 12, 18, 20, 35, 15, 20, 30, 20]; // Added width for the image column


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
$resLineItems     = $db->query("SELECT * FROM line_items WHERE `documentId` = ?s", $keyDocument->documentId);
$itemSerialNumber = 0;
while ($rowLineItem = mysqli_fetch_assoc($resLineItems)) {

    $imageUrl = getImageForSparepartID($rowLineItem['itemId']);
    $imageFullPath = $imageBasePath . $imageUrl;
    $partNumber = getPartNumberForSparepartID($rowLineItem['itemId']);
    $description = getItemDescriptionForSparepartID($rowLineItem['itemId']);
    $brandName = getItemBrandNameForSparepartID($rowLineItem['itemId']);
    $discountPercentage = $rowLineItem['discountPercentage'];
    $unitPriceAfterDiscount =number_format($rowLineItem['unitPrice'] - ($rowLineItem['unitPrice'] * $discountPercentage / 100),2, '.', '');
    $shippingAmount = isset($rowLineItem['shippingSubTotal']) ? $rowLineItem['shippingSubTotal'] : '0.00';

    $item    = [
        (string)++$itemSerialNumber, // Serial Number
        file_exists($imageFullPath) ? $imageFullPath : '', // Full image path if it exists
        ['partNumber' => $partNumber, 'description' => $description,  'brand' => $brandName], // Part Number & Description as an array
        $rowLineItem['quantity'], // Quantity
        $rowLineItem['unitPrice'], // Unit Price
        $discountPercentage, // Discount Percentage
        $unitPriceAfterDiscount, // Unit Price After Discount
        $rowLineItem['vatAmount'], // VAT Amount
        $shippingAmount, //Shipping Amount
        $rowLineItem['subTotal'], // Subtotal
        formatDateShort($rowLineItem['eta'], false) ?? "-"
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

            $pdf->SetFont('dejavusans', 'B', 10);
            $partHeight = $pdf->getStringHeight($cellWidth, $partNumber);

            $pdf->SetFont('dejavusans', '', 8);
            $descHeight = $pdf->getStringHeight($cellWidth, $description);

            $manufacturerLine = 'Manufacturer: ' . $brandName;
            $combinedLineHeight = $pdf->getStringHeight($cellWidth, $manufacturerLine);

            $rowHeights[] = $partHeight + $descHeight + $combinedLineHeight +10;
        } else {
            $pdf->SetFont('dejavusans', '', 10);
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
            $pdf->Image($value, $xStart + 5, $yStart + 1, 28, 28, '', '', '', false, 300, '', false, false, 1, false, false, false);
            $pdf->SetXY($xStart + $cellWidth, $yStart);
        } elseif ($key == 2 && is_array($value)) { // Item column
            $partNumber = $value['partNumber'];
            $description = $value['description'];
            $brandName = $value['brand'];

            // Calculate the height for each text element
            $pdf->SetFont('dejavusans', 'B', 10);
            $partHeight = $pdf->getStringHeight($cellWidth, $partNumber);

            $pdf->SetFont('dejavusans', '', 8);
            $descHeight = $pdf->getStringHeight($cellWidth, $description);

            $pdf->SetFont('dejavusans', 'B', 8);
            $manufacturerLabel = 'Manufacturer: ';
            $labelWidth = $pdf->GetStringWidth($manufacturerLabel);
            $manufacturerHeight = $pdf->getStringHeight($cellWidth - $labelWidth, $brandName);

            // Calculate total content height and vertical offset
            $contentHeight = $partHeight + $descHeight + max($manufacturerHeight, 5);
            $verticalOffset = ($maxHeight - $contentHeight) / 2;

            // Print part number in bold
            $pdf->SetFont('dejavusans', 'B', 10);
            $pdf->SetXY($xStart, $yStart + $verticalOffset);
            $pdf->MultiCell($cellWidth, 5, $partNumber, 0, 'L', false);

            // Print description with smaller font
            $pdf->SetFont('dejavusans', '', 8);
            $pdf->SetX($xStart);
            $pdf->MultiCell($cellWidth, 5, $description, 0, 'L', false);

            // Print manufacturer label and brand name on the same line
            $pdf->SetX($xStart);
            $pdf->SetFont('dejavusans', 'B', 8);
            $pdf->Cell($labelWidth, 5, $manufacturerLabel, 0, 0, 'L');

            $pdf->SetFont('dejavusans', '', 8);
            $pdf->Cell(0, 5, $brandName, 0, 1, 'L');

            $pdf->SetXY($xStart + $cellWidth, $yStart);
        } else {
            // For other columns, calculate vertical alignment and print value
            $contentHeight = $pdf->getStringHeight($cellWidth, $value);
            $verticalOffset = ($maxHeight - $contentHeight) / 2;

            $alignment = ($key == 2) ? 'L' : 'C';

            $pdf->SetXY($xStart, $yStart + $verticalOffset);
            $pdf->SetFont('dejavusans', '', 10);
            $pdf->MultiCell($cellWidth, $contentHeight, $value, 0, $alignment, false);

            $pdf->SetXY($xStart + $cellWidth, $yStart);
        }
    }

    $pdf->Ln($maxHeight);
}

$pdf->SetXY(5, $pdf->GetY() -8); // Adjust Y-coordinate to place below the table

$pdf->AddPage();


// Add total amount details (English)
$pdf->SetFont('dejavusans', '', 10);

// Add total amount details (English and Arabic on the same line)
$pdf->SetXY(10, $pdf->GetY() + 10); // Adjust Y-coordinate to place below the table
$pdf->Cell(95, 10, 'Total Amount (Before VAT, Discount & Shipping): SAR ' . getTotalAmountByDocumentID($documentID)['totalAmountBeforeVAT'], 0, 0, 'L');
$pdf->SetXY(190, $pdf->GetY());    // Adjust X-coordinate for right alignment and keep the Y-coordinate the same
$pdf->Cell(95, 10, 'المبلغ الإجمالي (قبل الضريبة والخصم والشحن): ' . getTotalAmountByDocumentID($documentID)['totalAmountBeforeVAT'], 0, 0, 'R');

// Add VAT amount details (English and Arabic on the same line)
$pdf->SetXY(10, $pdf->GetY() + 5); // Move down to the next line
$pdf->Cell(95, 10, 'VAT Amount: SAR ' . getTotalAmountByDocumentID($documentID)['totalVATAmount'], 0, 0, 'L');
$pdf->SetXY(190, $pdf->GetY());    // Adjust X-coordinate for right alignment and keep the Y-coordinate the same
$pdf->Cell(95, 10, 'قيمة الضريبة: ' . getTotalAmountByDocumentID($documentID)['totalVATAmount'], 0, 0, 'R');


// Add Total discount (English and Arabic on the same line)
$pdf->SetXY(10, $pdf->GetY() + 5); // Move down to the next line
$pdf->Cell(95, 10, 'Total Discount: SAR ' . getTotalAmountByDocumentID($documentID)['totalDiscountAmount'], 0, 0, 'L');
$pdf->SetXY(190, $pdf->GetY());    // Adjust X-coordinate for right alignment and keep the Y-coordinate the same
$pdf->Cell(95, 10, ' اجمالي مبلغ الخصم: ' . getTotalAmountByDocumentID($documentID)['totalDiscountAmount'], 0, 0, 'R');

// Add shipping amount (English and Arabic on the same line)
$pdf->SetXY(10, $pdf->GetY() + 5); // Move down to the next line
$pdf->Cell(95, 10, 'Shipping Amount: SAR ' . getTotalAmountByDocumentID($documentID)['totalShippingAmount'], 0, 0, 'L');
$pdf->SetXY(190, $pdf->GetY());    // Adjust X-coordinate for right alignment and keep the Y-coordinate the same
$pdf->Cell(95, 10, 'مبلغ الشحن: ' . getTotalAmountByDocumentID($documentID)['totalShippingAmount'], 0, 0, 'R');

// Add total amount after VAT details (English and Arabic on the same line)
$pdf->SetXY(10, $pdf->GetY() + 5); // Move down to the next line
$pdf->Cell(95, 10, 'Total Amount (After VAT, Discount & Shipping): SAR ' . getTotalAmountByDocumentID($documentID)['totalAmountAfterVATAndShipping'], 0, 0, 'L');
$pdf->SetXY(190, $pdf->GetY());     // Adjust X-coordinate for right alignment and keep the Y-coordinate the same
$pdf->Cell(95, 10, 'المبلغ الإجمالي (بعد الضريبة والخصم والشحن): ' . getTotalAmountByDocumentID($documentID)['totalAmountAfterVATAndShipping'], 0, 0, 'R');

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
    $pdf->Output('E-commerce Order_' . $orderNumber . '.pdf', 'D');
else
    $pdf->Output('E-commerce Order_' . $orderNumber . '.pdf', 'I');












function printTableHeaders($pdf, $headers, $scaledHeaderWidths) {
    $pdf->SetFont('dejavusans', 'B', 10);
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