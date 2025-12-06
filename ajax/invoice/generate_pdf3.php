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
use Salla\ZATCA\Tags\InvoiceHash;
use Sabre\Xml\Service;
use GuzzleHttp\Client;

$isDownload = false;
$isDownload = filter_input(INPUT_GET, 'isdownload', FILTER_VALIDATE_INT);

$invoiceID = "";
$invoiceID = filter_input(INPUT_GET, 'invoiceID', FILTER_VALIDATE_INT);
if ($invoiceID === null || $invoiceID === false || filter_var($invoiceID, FILTER_VALIDATE_INT) === false) {
    header("location:/invoice/list");
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
        global $invoiceNumber;
        global $invoiceDocument;
        global $fontName;
// After loading $invoiceDocument, $companyInfo, and $items
        global $companyInfo, $items;

        $pageWidth = $this->getPageWidth();

//Main Header
        $this->Image(__DIR__ . '/../../assets/img/branding/G-Logo.png', 5, 5, 15);            // Adjust logo size and position
        $this->Image(__DIR__ . '/../../assets/img/branding/document_corner.jpg', 255, 0, 45); // Adjust Corner badge


// Title
        $this->SetFont($fontName, 'B', 16);
        $titleText   = 'Tax Invoice ' . ' ضريبية فاتورة  ';
        $titleWidth  = $this->GetStringWidth($titleText);
        $titleHeight = $this->getStringHeight($titleWidth, $titleText);

// Set position for the "Invoice" text
        $centerX = (($pageWidth) - $titleWidth) / 2;
        $this->SetXY($centerX, 10);
        $this->Cell($titleWidth, $titleHeight, $titleText, 0, 1, 'C');  // '1' moves to the next line after this cell


        $lineHeight = 6;
// Position for the left side (English content)
        $this->SetFont($fontName, '', 12);
//$this->SetXY(5, 30);
        $this->SetXY(5, 30);

        $this->Cell(0, $lineHeight, 'Invoice #: ' . $invoiceNumber, 0, 0, 'L');

        $arabicLabel      = 'رقم الفاتورة #:';
        $arabicLabelWidth = $this->GetStringWidth($arabicLabel);

        $this->Cell(0, $lineHeight, $arabicLabel, 0, 0, 'R');
        $this->setX($pageWidth - $arabicLabelWidth - $pageMargin - 2);
        $this->Cell(1, $lineHeight, convertToArabicNumbers($invoiceNumber), 0, 1, 'R');

        $this->SetX(5);
        $this->Cell(0, $lineHeight, 'Date: ' . formatDate($invoiceDocument->invoiceDateIssued, false), 0, 0, 'L');
        $arabicLabel      = 'التاريخ: ';
        $arabicLabelWidth = $this->GetStringWidth($arabicLabel);

        $this->Cell(0, $lineHeight, $arabicLabel, 0, 0, 'R');
        $this->setX($pageWidth - $arabicLabelWidth - $pageMargin - 2);
        $this->Cell(1, $lineHeight,  convertToArabicNumbers(convertToHijri($invoiceDocument->invoiceDateIssued)), 0, 0, 'R');


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
// Start output buffering to catch any accidental output
ob_start();

$invoiceDocument = new InvoiceDocument();
$invoiceDocument->loadById($invoiceID);
$invoiceNumber = getInvoiceNumberFromInvoiceID($invoiceID);

// $invoiceDocument->invoiceDateIssued = date("Y-m-d H:i:s");
$companyID = $invoiceDocument->companyId;

$companyInfo = new Company();
$companyInfo->loadById($companyID);

$customerInfo = new Customer();
$customerInfo->loadById($invoiceDocument->customerId);

//Build the line Items
$items            = array();
$resLineItems     = $db->query("SELECT * FROM invoice_line_items WHERE `invoiceId` = ?s", $invoiceID);
$itemSerialNumber = 0;
while ($rowLineItem = mysqli_fetch_assoc($resLineItems)) {

//    $imageUrl = getImageForSparepartID($rowLineItem['itemId']);
//    $imageFullPath = $imageBasePath . $imageUrl;
    $partNumber             = getPartNumberForSparepartID($rowLineItem['itemId']);
    $description            = getItemDescriptionForSparepartID($rowLineItem['itemId']);
    $brandName              = getItemBrandNameForSparepartID($rowLineItem['itemId']);
    $discountPercentage     = $rowLineItem['discountPercentage'];
    $unitPriceAfterDiscount = number_format($rowLineItem['unitPrice'] - ($rowLineItem['unitPrice'] * $discountPercentage / 100), 2, '.', '');

    $item    = [
        (string)++$itemSerialNumber, // Serial Number
        //file_exists($imageFullPath) ? $imageFullPath : '', // Full image path if it exists
        ['partNumber' => $partNumber, 'description' => $description, 'brand' => $brandName], // Part Number & Description as an array
        $rowLineItem['quantity'], // Quantity
        $rowLineItem['unitPrice'], // Unit Price
        $discountPercentage, // Discount Percentage
        $unitPriceAfterDiscount, // Unit Price After Discount
        $rowLineItem['vatAmount'], // VAT Amount
        $rowLineItem['subTotal'], // Subtotal
        //        formatDateShort($rowLineItem['eta'], false) ?? "-"
    ];
    $items[] = $item;
}

$createDate = (new DateTime('now', new DateTimeZone('Asia/Riyadh')))
    ->setTimezone(new DateTimeZone('UTC'))
    ->format('Y-m-d\TH:i:s\Z');

//////////////////////////////////


// Create PDF instance in landscape mode
$pageMargin = 5;
$pdf        = new CustomPDF('L', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('GrandMaster');
$pdf->SetTitle('Invoice ' . $invoiceNumber);
$pdf->setPDFVersion('1.7');
$pdf->setFontSubsetting(true);
$xmp = <<<EOD
<?xpacket begin="" id="W5M0MpCehiHzreSzNTczkc9d"?>
<x:xmpmeta xmlns:x="adobe:ns:meta/" x:xmptk="Adobe XMP Core 5.6-c145">
    <rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
        <rdf:Description rdf:about=""
            xmlns:pdf="http://ns.adobe.com/pdf/1.3/"
            xmlns:pdfaid="http://www.aiim.org/pdfa/ns/id/"
            xmlns:dc="http://purl.org/dc/elements/1.1/"
            xmlns:xmp="http://ns.adobe.com/xmp/1.0/">
            <pdf:Producer>TCPDF</pdf:Producer>
            <pdfaid:part>3</pdfaid:part>
            <pdfaid:conformance>B</pdfaid:conformance>
            <dc:title>
                <rdf:Alt>
                    <rdf:li xml:lang="x-default">Invoice $invoiceNumber</rdf:li>
                    <rdf:li xml:lang="ar-SA">فاتورة $invoiceNumber</rdf:li>
                </rdf:Alt>
            </dc:title>
            <dc:creator>
                <rdf:Seq>
                    <rdf:li>GrandMaster</rdf:li>
                </rdf:Seq>
            </dc:creator>
            <xmp:CreateDate>$createDate</xmp:CreateDate>
        </rdf:Description>
    </rdf:RDF>
</x:xmpmeta>
<?xpacket end="r"?>
EOD;
$pdf->setExtraXMP($xmp);
$pdf->SetMargins($pageMargin, $pageMargin, $pageMargin);
$pdf->AddPage();


// Page width (in mm) for A4 landscape is 297 mm, with some margins taken into account
$pageWidth = 297 - 10;                                  // 5 mm left and right margins
$pageWidth = $pdf->getPageWidth();

$uuid = generateUUID();
$xmlString = generateUBLXML($invoiceDocument, $companyInfo, $customerInfo, $items, $uuid);
$xmlFileName = 'invoice.xml';

$xmlData = $xmlString;

$privateKeyPath = __DIR__ . '/../../zatca/private_key.pem';
$certificatePath = __DIR__ . '/../../zatca/certificate.pem';
$signedXML = signXML($xmlString, $privateKeyPath, $certificatePath);
if ($signedXML === false) {
    ob_end_clean();
    die("Failed to sign invoice XML.");
}

// Generate ZATCA QR code
$sellerName = $companyInfo->companyNameAr;                // Grandmaster company name
// $sellerName = "شركة المورد اللامحدود التجارية القابضة"; // Grandmaster company name arabic
$vatNumber  = $companyInfo->vatNumber;                  // Grandmaster VAT number

$invoiceHash = generateInvoiceHash($signedXML);

$invoiceDate  = (new DateTime($invoiceDocument->invoiceDateIssued, new DateTimeZone('Asia/Riyadh')))
    ->setTimezone(new DateTimeZone('UTC'))
    ->format('Y-m-d\TH:i:s\Z');                                                                                         // Convert to Zulu format
$invoiceTotal = number_format(getTotalAmountByInvoiceID($invoiceID)['totalAmountAfterVAT'], 2, '.', ''); // Total after VAT
$invoiceTax   = number_format(getTotalAmountByInvoiceID($invoiceID)['totalVATAmount'], 2, '.', '');      // VAT amount

$qrCodeBase64 = generateZATCAQRCode($sellerName, $vatNumber, $invoiceDate, $invoiceTotal, $invoiceTax, $uuid, $invoiceHash);
if ($qrCodeBase64) {
    // Remove the "data:image/png;base64," prefix to get raw base64 data
    $rawBase64 = preg_replace('/^data:image\/(?:png|jpeg|gif);base64,/', '', $qrCodeBase64);

    // Verify the base64 data (optional debug step)
    if (empty($rawBase64) || !base64_decode($rawBase64, true)) {
        error_log("Invalid base64 data for QR code: " . $qrCodeBase64);
        return; // Skip QR code if invalid
    }

    // Calculate position to center the QR code below the title
    $qrSize = 40;                         // Size of QR code in mm (adjust as needed)
    $qrX    = ($pageWidth - $qrSize) / 2; // Center horizontally
    $qrY    = 25;                         // Position below title (adjust Y as needed to fit above the table at Y=85)

    // Add QR code as an image using raw base64 data
//            $this->Image('@' . $rawBase64, $qrX, $qrY, $qrSize, $qrSize, 'PNG', '', '', false, 300, '', false, false, 0, false, false, false);


    // Decode the Base64 string into actual binary data
    $rawBinaryData = base64_decode($rawBase64);

    $pdf->Image(
        '@' . $rawBinaryData,  // Now we pass the raw binary data
        $qrX,
        $qrY,
        $qrSize,
        $qrSize,
        'PNG',
        '',
        '',
        false,
        300,
        '',
        false,
        false,
        0,
        false,
        false,
        false
    );

}

$pdf->Annotation(10, 10, 0, 0, 'FileAttachment', [
    'Title' => 'UBL XML Invoice',
    'FS' => [
        'F' => $xmlFileName,
        'EF' => ['F' => $xmlData],
    ],
    'SubType' => 'FileAttachment',
    'Name' => 'Paperclip',
]);

//Customer Information
$pdf->SetFont($fontName, '', 10);
// Define a starting position for the table
$startX     = 5;
$startY     = 45;
$rowHeight  = 5;
$labelWidth = 30;
$valueWidth = 65;
$space      = 2;

// Details to display
$details = [
    ['Messrs ', $customerInfo->companyName, 'السادة       ', $customerInfo->companyNameAr],
    ['Cust. PO# ', $invoiceDocument->PONumber, 'امر شراء العميل       ', convertToArabicNumbers($invoiceDocument->PONumber)],
    ['Cust. VAT ', $customerInfo->vatNumber, 'الرقم الضريبي للعميل       ', convertToArabicNumbers($customerInfo->vatNumber)],
    ['Cust. CR ', $customerInfo->companyCRNumber, 'رقم السجل التجاري للعميل       ', convertToArabicNumbers($customerInfo->companyCRNumber)],
    ['Grandmaster VAT ', $companyInfo->vatNumber, 'الرقم الضريبي لجراند ماستر       ', convertToArabicNumbers($companyInfo->vatNumber)],
    ['Grandmaster CR ', $companyInfo->companyCRNumber, 'رقم السجل التجاري لجراند ماستر       ', convertToArabicNumbers($companyInfo->companyCRNumber)],
    ['Payment Terms ', $invoiceDocument->getPaymentTermName(), 'شروط الدفع       ', convertToArabicNumbers($invoiceDocument->getPaymentTermName())]
];

// Loop through the details to create rows
foreach ($details as $row) {

    $pdf->SetXY($startX, $startY);
    $pdf->SetFont($fontName, 'B', 10);
    $pdf->Cell($pdf->GetStringWidth($row[0]), $rowHeight, $row[0], 0, 0, 'L');


    $pdf->SetX($startX + $pdf->GetStringWidth($row[0]) + $space);
    $pdf->SetFont($fontName, '', 10);
    $pdf->Cell(0, $rowHeight, $row[1], 0, 0, 'L');


    $pdf->SetFont($fontName, 'B', 10);
    $pdf->SetX($pdf->getPageWidth() - $pdf->GetStringWidth($row[2]) - $pageMargin);
    $pdf->Cell(0, $rowHeight, $row[2], 0, 0, 'L');


    $pdf->SetX($pdf->getPageWidth() - $pdf->GetStringWidth($row[2]) - $pdf->GetStringWidth($row[3]) - $space);
    $pdf->SetFont($fontName, '', 10);
    $pdf->Cell(0, $rowHeight, $row[3], 0, 1, 'L');

    // Move to the next row
    $startY += $rowHeight;
}


//Line Items table header
// Define the initial header widths and scale them
$headers = [
    ['NO.', 'رقم'],
    //    [' ', ' '],
    ['Item', 'رقم الصنف والوصف'],
    ['Qty', 'الكمية'],
    ['Unit Price', 'سعر الوحدة'],
    ['Discount %', 'نسبة الخصم'],
    ['Price After Discount', 'السعر بعد الخصم'],
    ['VAT', 'الضريبة'],
    ['Total Value', 'القيمة الإجمالية']
    //    ['ETA', 'تاريخ الوصول']
];
//$headerWidths = [8, 25, 45, 12, 18, 20, 25, 15, 25, 30]; // Initial widths
//$headerWidths = [10, 45, 12, 18, 20, 25, 15, 25, 30]; // Initial widths
//$headerWidths = [10, 30, 45, 12, 18, 20, 35, 15, 25, 20]; // Added width for the image column
$headerWidths = [10, 45, 12, 18, 20, 25, 15, 25];


// Calculate the total width of the table
$totalWidth = array_sum($headerWidths) + 7;

// Calculate scale factor to fit the table within the page width
$scaleFactor = $pageWidth / $totalWidth;

// Adjust header widths proportionally
$scaledHeaderWidths = array_map(function ($width) use ($scaleFactor) {
    return $width * $scaleFactor;
}, $headerWidths);


// Define the base path for images
//$imageBasePath = $_SERVER['DOCUMENT_ROOT'] . '/uploads/sparepart-images/';


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
    $rowHeights    = [];
    $defaultHeight = 10;

    // Calculate the maximum row height
    foreach ($item as $key => $value) {
        $cellWidth = $scaledHeaderWidths[$key];

        if ($key == 1 && !empty($value)) { // Image column
            $rowHeights[] = 30;            // Fixed image height
        } elseif ($key == 2 && is_array($value)) { // Item column
            $partNumber  = $value['partNumber'];
            $description = $value['description'];
            $brandName   = $value['brand'];

            $pdf->SetFont($fontName, 'B', 10);
            $partHeight = $pdf->getStringHeight($cellWidth, $partNumber);

            $pdf->SetFont($fontName, '', 8);
            $descHeight = $pdf->getStringHeight($cellWidth, $description);

            $manufacturerLine   = 'Manufacturer: ' . $brandName;
            $combinedLineHeight = $pdf->getStringHeight($cellWidth, $manufacturerLine);

            $rowHeights[] = $partHeight + $descHeight + $combinedLineHeight + 10;
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
        $xStart    = $pdf->GetX();
        $yStart    = $pdf->GetY();

        // Draw the cell border
        $pdf->Rect($xStart, $yStart, $cellWidth, $maxHeight);

        if ($key == 1 && is_array($value)) { // Item column
            $partNumber  = $value['partNumber'];
            $description = $value['description'];
            $brandName   = $value['brand'];

            // Calculate the height for each text element
            $pdf->SetFont($fontName, 'B', 10);
            $partHeight = $pdf->getStringHeight($cellWidth, $partNumber);

            $pdf->SetFont($fontName, '', 8);
            $descHeight = $pdf->getStringHeight($cellWidth, $description);

            $pdf->SetFont($fontName, 'B', 8);
            $manufacturerLabel  = 'Manufacturer: ';
            $labelWidth         = $pdf->GetStringWidth($manufacturerLabel);
            $manufacturerHeight = $pdf->getStringHeight($cellWidth - $labelWidth, $brandName);

            // Calculate total content height and vertical offset
            $contentHeight  = $partHeight + $descHeight + max($manufacturerHeight, 5);
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
            $contentHeight  = $pdf->getStringHeight($cellWidth, $value);
            $verticalOffset = ($maxHeight - $contentHeight) / 2;

//            $alignment = ($key == 2) ? 'L' : 'C';

            $pdf->SetXY($xStart, $yStart + $verticalOffset);
            $pdf->SetFont($fontName, '', 10);
            $pdf->MultiCell($cellWidth, $contentHeight, $value, 0, 'C', false);

            $pdf->SetXY($xStart + $cellWidth, $yStart);
        }
    }

    $pdf->Ln($maxHeight);
}

$pdf->SetXY(5, $pdf->GetY() - 8);   // Adjust Y-coordinate to place below the table


$pdf->AddPage();
// Add total amount details (English)
$pdf->SetFont($fontName, '', 10);

// Add total amount details (English and Arabic on the same line)
$pdf->SetXY(10, $pdf->GetY() + 10); // Adjust Y-coordinate to place below the table
$pdf->Cell(95, 10, 'Total Amount (Before VAT & Discount): ₥ ' . getTotalAmountByInvoiceID($invoiceID)['totalAmountBeforeVAT'], 0, 0, 'L');
$pdf->SetXY(190, $pdf->GetY());    // Adjust X-coordinate for right alignment and keep the Y-coordinate the same
$pdf->Cell(95, 10, 'المبلغ الاجمالي (قبل الضريبة و الخصم):  ' . convertToArabicNumbers(getTotalAmountByInvoiceID($invoiceID)['totalAmountBeforeVAT']) .' ₥ ', 0, 0, 'R');

// Add VAT amount details (English and Arabic on the same line)
$pdf->SetXY(10, $pdf->GetY() + 5); // Move down to the next line
$pdf->Cell(95, 10, 'VAT Amount: ₥ ' . getTotalAmountByInvoiceID($invoiceID)['totalVATAmount'], 0, 0, 'L');
$pdf->SetXY(190, $pdf->GetY());    // Adjust X-coordinate for right alignment and keep the Y-coordinate the same
$pdf->Cell(95, 10, 'قيمة الضريبة:  ' . convertToArabicNumbers(getTotalAmountByInvoiceID($invoiceID)['totalVATAmount']).' ₥ ', 0, 0, 'R');


// Add Total discount (English and Arabic on the same line)
$pdf->SetXY(10, $pdf->GetY() + 5); // Move down to the next line
$pdf->Cell(95, 10, 'Total Discount: ₥ ' . getTotalAmountByInvoiceID($invoiceID)['totalDiscountAmount'], 0, 0, 'L');
$pdf->SetXY(190, $pdf->GetY());    // Adjust X-coordinate for right alignment and keep the Y-coordinate the same
$pdf->Cell(95, 10, ' اجمالي مبلغ الخصم:  ' . convertToArabicNumbers(getTotalAmountByInvoiceID($invoiceID)['totalDiscountAmount']) .' ₥ ', 0, 0, 'R');

// Add total amount after VAT details (English and Arabic on the same line)
$pdf->SetXY(10, $pdf->GetY() + 5); // Move down to the next line
$pdf->Cell(95, 10, 'Total Amount (After VAT & Discount): ₥ ' . getTotalAmountByInvoiceID($invoiceID)['totalAmountAfterVAT'], 0, 0, 'L');
$pdf->SetXY(190, $pdf->GetY());     // Adjust X-coordinate for right alignment and keep the Y-coordinate the same
$pdf->Cell(95, 10, '  المبلغ الاجمالي (بعد الضريبة و الخصم): ' . convertToArabicNumbers(getTotalAmountByInvoiceID($invoiceID)['totalAmountAfterVAT']) .' ₥ ', 0, 0, 'R');

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

// Move down below the signature section
$pdf->SetXY(10, $pdf->GetY() + 15);
$pdf->SetTextColor(150, 150, 150);
// First line - English Heading
$pdf->Cell(95, 10, 'For settlement of this invoice, please use the following:', 0, 0, 'L');
$pdf->SetXY(190, $pdf->GetY());
$pdf->Cell(95, 10, 'لسداد هذه الفاتورة يرجى استخدام البيانات التالية:', 0, 0, 'R');

// Move to next line
$pdf->SetXY(10, $pdf->GetY() + 5);
$pdf->Cell(95, 10, 'Beneficiary: Almord Al - Lamhedod commercial holding company', 0, 0, 'L');
$pdf->SetXY(190, $pdf->GetY());
$pdf->Cell(95, 10, 'شركة المورد اللامحدود التجارية القابضة', 0, 0, 'R');

// Move to next line
$pdf->SetXY(10, $pdf->GetY() + 5);
$pdf->Cell(95, 10, 'Bank Name: Riyad Bank', 0, 0, 'L');
$pdf->SetXY(190, $pdf->GetY());
$pdf->Cell(95, 10, 'اسم البنك: بنك الرياض', 0, 0, 'R');

// Move to next line
$pdf->SetXY(10, $pdf->GetY() + 5);
$pdf->Cell(95, 10, 'IBAN: SA1220000003492649349940', 0, 0, 'L');
$pdf->SetXY(190, $pdf->GetY());
$pdf->Cell(95, 10, convertToArabicNumbers('رقم الأيبان: SA1220000003492649349940'), 0, 0, 'R');

$apiToken = 'your_zatca_api_token';
$invoiceType = $customerInfo->vatNumber ? 'B2B' : 'B2C';
$zatcaResponse = submitInvoiceToZATCA($signedXML, $apiToken, $invoiceType);
if ($zatcaResponse === false) {
    ob_end_clean();
    die("Failed to send invoice to ZATCA.");
}


if ($isDownload == 1)
// Output the PDF
    $pdf->Output('Invoice_' . $invoiceNumber . '.pdf', 'D');
else
    $pdf->Output('Invoice_' . $invoiceNumber . '.pdf', 'I');


ob_end_flush(); // Flush any buffered output


function printTableHeaders($pdf, $headers, $scaledHeaderWidths)
{
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


/**
 * Generate a ZATCA-compliant QR code for Phase 1.
 *
 * @param string $sellerName The seller's name
 * @param string $vatNumber The seller's VAT registration number
 * @param string $invoiceDate The invoice date in Zulu ISO8601 format (e.g., 2025-02-21T20:55:00Z)
 * @param string $invoiceTotal The total invoice amount (including VAT)
 * @param string $invoiceTax The VAT amount
 * @return string|bool Returns the base64-encoded QR code image or false on failure
 */
function generateZATCAQRCode($sellerName, $vatNumber, $invoiceDate, $invoiceTotal, $invoiceTax, $uuid, $invoiceHash)
{
    try {
        // Generate the QR code as base64
        $qrCodeBase64 = GenerateQrCode::fromArray([
            new Seller($sellerName),
            new TaxNumber($vatNumber),
            new InvoiceDate($invoiceDate),
            new InvoiceTotalAmount($invoiceTotal),
            new InvoiceTaxAmount($invoiceTax),
            new InvoiceHash($invoiceHash)
        ])->render();

        // Debug: Log or check the QR code
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

function generateUBLXML($invoiceDocument, $companyInfo, $customerInfo, $items, $uuid) {
    $xmlService = new Service();
    $xmlService->namespaceMap = [
        'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2' => '',
        'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2' => 'cbc',
        'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2' => 'cac',
    ];

    $totalBeforeVAT = getTotalAmountByInvoiceID($invoiceDocument->id)['totalAmountBeforeVAT'];
    $totalVAT = getTotalAmountByInvoiceID($invoiceDocument->id)['totalVATAmount'];
    $totalAfterVAT = getTotalAmountByInvoiceID($invoiceDocument->id)['totalAmountAfterVAT'];

    $xmlData = [
        'name' => '{}Invoice',
        'value' => [
            ['name' => 'cbc:UUID', 'value' => $uuid],
            ['name' => 'cbc:IssueDate', 'value' => date('Y-m-d', strtotime($invoiceDocument->invoiceDateIssued))],
            ['name' => 'cac:AccountingSupplierParty', 'value' => [
                ['name' => 'cac:Party', 'value' => [
                    ['name' => 'cac:PartyName', 'value' => [
                        ['name' => 'cbc:Name', 'value' => $companyInfo->companyNameAr],
                    ]],
                    ['name' => 'cac:PartyTaxScheme', 'value' => [
                        ['name' => 'cbc:CompanyID', 'value' => $companyInfo->vatNumber],
                    ]],
                ]],
            ]],
            ['name' => 'cac:AccountingCustomerParty', 'value' => [
                ['name' => 'cac:Party', 'value' => [
                    ['name' => 'cac:PartyName', 'value' => [
                        ['name' => 'cbc:Name', 'value' => $customerInfo->companyNameAr],
                    ]],
                    ['name' => 'cac:PartyTaxScheme', 'value' => [
                        ['name' => 'cbc:CompanyID', 'value' => $customerInfo->vatNumber],
                    ]],
                ]],
            ]],
            ['name' => 'cac:LegalMonetaryTotal', 'value' => [
                ['name' => 'cbc:LineExtensionAmount', 'value' => number_format($totalBeforeVAT, 2), 'attributes' => ['currencyID' => 'SAR']],
                ['name' => 'cbc:TaxExclusiveAmount', 'value' => number_format($totalBeforeVAT, 2), 'attributes' => ['currencyID' => 'SAR']],
                ['name' => 'cbc:TaxInclusiveAmount', 'value' => number_format($totalAfterVAT, 2), 'attributes' => ['currencyID' => 'SAR']],
                ['name' => 'cbc:PayableAmount', 'value' => number_format($totalAfterVAT, 2), 'attributes' => ['currencyID' => 'SAR']],
            ]],
            ['name' => 'cac:TaxTotal', 'value' => [
                ['name' => 'cbc:TaxAmount', 'value' => number_format($totalVAT, 2), 'attributes' => ['currencyID' => 'SAR']],
            ]],
        ],
    ];

    foreach ($items as $index => $item) {
        $xmlData['value'][] = [
            'name' => 'cac:InvoiceLine',
            'value' => [
                ['name' => 'cbc:ID', 'value' => $index + 1],
                ['name' => 'cbc:InvoicedQuantity', 'value' => $item[2], 'attributes' => ['unitCode' => 'PCE']],
                ['name' => 'cbc:LineExtensionAmount', 'value' => number_format($item[7], 2), 'attributes' => ['currencyID' => 'SAR']],
                ['name' => 'cac:Item', 'value' => [
                    ['name' => 'cbc:Name', 'value' => $item[1]['description']],
                ]],
                ['name' => 'cac:Price', 'value' => [
                    ['name' => 'cbc:PriceAmount', 'value' => number_format($item[5], 2), 'attributes' => ['currencyID' => 'SAR']],
                ]],
            ],
        ];
    }

    return $xmlService->write('Invoice', $xmlData);
}

function signXML($xmlString, $privateKeyPath, $certificatePath) {
    try {
        $doc = new DOMDocument();
        $doc->loadXML($xmlString);
        $canonicalXML = $doc->C14N(true, false);

        $privateKey = openssl_pkey_get_private(file_get_contents($privateKeyPath));
        if ($privateKey === false) {
            error_log("Failed to load private key: " . openssl_error_string());
            return false;
        }

        $signature = '';
        if (!openssl_sign($canonicalXML, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
            error_log("Failed to sign XML: " . openssl_error_string());
            return false;
        }

        $certificate = file_get_contents($certificatePath);
        $certificate = trim(preg_replace('/(-----BEGIN CERTIFICATE-----|-----END CERTIFICATE-----|\s+)/', '', $certificate));

        $signatureNode = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'Signature');
        $signedInfo = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'SignedInfo');
        $canonicalizationMethod = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'CanonicalizationMethod');
        $canonicalizationMethod->setAttribute('Algorithm', 'http://www.w3.org/2001/10/xml-exc-c14n#');
        $signatureMethod = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'SignatureMethod');
        $signatureMethod->setAttribute('Algorithm', 'http://www.w3.org/2001/04/xmldsig-more#ecdsa-sha256');
        $reference = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'Reference');
        $reference->setAttribute('URI', '');
        $transforms = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'Transforms');
        $transform = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'Transform');
        $transform->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#enveloped-signature');
        $transforms->appendChild($transform);
        $digestMethod = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'DigestMethod');
        $digestMethod->setAttribute('Algorithm', 'http://www.w3.org/2001/04/xmlenc#sha256');
        $digestValue = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'DigestValue', base64_encode(hash('sha256', $canonicalXML, true)));
        $reference->appendChild($transforms);
        $reference->appendChild($digestMethod);
        $reference->appendChild($digestValue);
        $signedInfo->appendChild($canonicalizationMethod);
        $signedInfo->appendChild($signatureMethod);
        $signedInfo->appendChild($reference);
        $signatureValue = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'SignatureValue', base64_encode($signature));
        $keyInfo = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'KeyInfo');
        $x509Data = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'X509Data');
        $x509Certificate = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'X509Certificate', $certificate);
        $x509Data->appendChild($x509Certificate);
        $keyInfo->appendChild($x509Data);
        $signatureNode->appendChild($signedInfo);
        $signatureNode->appendChild($signatureValue);
        $signatureNode->appendChild($keyInfo);
        $doc->documentElement->appendChild($signatureNode);

        return $doc->saveXML();
    } catch (Exception $e) {
        error_log("Error signing XML: " . $e->getMessage());
        return false;
    }
}

function generateInvoiceHash($xmlString) {
    return base64_encode(hash('sha256', $xmlString, true));
}

function submitInvoiceToZatca($signedXML, $apiToken, $invoiceType) {
    try {
        $client = new Client();
        $response = $client->post('https://api.sandbox.zatca.gov.sa/invoices/reporting', [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiToken,
                'Content-Type' => 'application/xml',
                'Accept' => 'application/json',
            ],
            'body' => $signedXML,
            'query' => ['type' => $invoiceType],
        ]);
        return json_decode($response->getBody()->getContents(), true);
    } catch (Exception $e) {
        error_log("Error submitting invoice to ZATCA: " . $e->getMessage());
        return false;
    }
}