<?php
require_once('../../vendor/autoload.php');
// require_once('quotation_form.php');

// Extend the TCPDF class to customize the header and footer
class CustomPDF extends TCPDF {
    // Page Header
    public function Header() {
        // Logo
        // Add logo
        if ($this->getY() < 40) {
            $this->Image('gmmsa_favicon.png', 3, 5, 15); // Adjust logo size and position

            // Add QR Code
            $this->Image('qr.png', 7, 22, 10);

            // Adjust QR Code size and position
            $this->Image('Presentation1-1_corner.jpg', 255, 0, 45); // Adjust QR Code size and position

            // Title
            // Set font for the main title
            $this->SetFont('dejavusans', 'B', 14);

            // Set position for the "Quotation" text
            $this->SetXY(120, 15);
            $this->Cell(50, 10, 'Quotation عرض أسعار', 0, 1, 'C'); // '1' moves to the next line after this cell

            // Set font for the "Sales Order" text
            $this->SetFont('dejavusans', '', 8);

            // Set position for the "Sales Order" text (adjust Y-coordinate to be below the "Quotation" text)
            $this->SetXY(100, 22); // Y-coordinate moved down to position it below the "Quotation" text
            $this->Cell(50, 10, 'Sales Order', 0, 0, 'C');

            // Set font for the diagonal text
            $this->SetFont('dejavusans', '', 8);

            // Add Invoice details (English)
            $this->SetFont('dejavusans', '', 10); // Set bold font with size 14

            // Position for the left side (English content)
            $this->SetXY(20, 20); // Position to the right of the QR code
            $this->Cell(95, 10, 'Invoice #: 1234', 0, 0, 'L'); // Display "Invoice #"

            // Set the font for Arabic content
            $this->SetFont('dejavusans', '', 10); // Set bold font with size 14 for Arabic

            // Position for the right side (Arabic content)
            $this->SetXY(180, 20); // Adjust X coordinate for right alignment
            $this->Cell(95, 10, 'فاتورة #: 1234', 0, 0, 'R'); // Display Arabic "Invoice #"

            // Set the font for Date (English)
            $this->SetFont('dejavusans', '', 10); // Set normal font with size 10

            // Position for the left side (English date)
            $this->SetXY(20, 30); // Adjust Y coordinate for the next line
            $this->Cell(95, 10, 'Date: ' . date('d-m-Y'), 0, 0, 'L'); // Display "Date"

            // Set the font for Date (Arabic)
            $this->SetFont('dejavusans', '', 10); // Set normal font with size 10 for Arabic

            // Position for the right side (Arabic date)
            $this->SetXY(180, 30); // Adjust X coordinate for right alignment
            $this->Cell(95, 10, 'التاريخ: ' . date('d-m-Y'), 0, 0, 'R'); // Display Arabic "Date"

            // Set font for English content
            $this->SetFont('dejavusans', '', 8); // Set normal font with size 10

            // Define a starting position for the table
            $startX = 10; // Starting X position for the English section
            $startY = 40; // Starting Y position
            $rowHeight = 5; // Row height
            $labelWidth = 30; // Width for labels
            $valueWidth = 65; // Width for values

            // Details to display
            $details = [
                ['Messrs: ', 'Adunan', 'السادة: ', 'Adunan'],
                ['Cust. VAT: ', '12345678976567257', 'الرقم الضريبي للعميل: ', '12345678976567257'],
                ['Cust. CR: ', '789012', 'رقم السجل التجاري للعميل: ', '789012'],
                ['Grandmaster VAT: ', '654321', 'الرقم الضريبي لجراند ماستر: ', '654321'],
                ['Grandmaster CR: ', '987654', 'رقم السجل التجاري لجراند ماستر: ', '987654'],
                ['Payment Terms: ', 'Net 30', 'شروط الدفع: ', 'صافي 30']
            ];

            // Loop through the details to create rows
            foreach ($details as $row) {
                // English Content (Left Section)
                $this->SetFont('dejavusans', '', 8); // Normal font for English

                // Label (English - Right Aligned within the cell)
                $this->SetXY($startX, $startY); // Position for the English label
                $this->Cell($labelWidth, $rowHeight, $row[0], 0, 0, 'R'); // Right-aligned label

                // Value (English - Left Aligned)
                $this->SetXY($startX + $labelWidth, $startY); // Position for the English value
                $this->Cell($valueWidth, $rowHeight, $row[1], 0, 0, 'L'); // Left-aligned value

                // Arabic Content (Right Section)
                $this->SetFont('dejavusans', '', 8); // Normal font for Arabic

                // Value (Arabic - Left Aligned within the right column)
                $this->SetXY($this->getPageWidth() - $startX - $valueWidth, $startY); // Position for the Arabic value
                $this->Cell($valueWidth, $rowHeight, $row[2], 0, 0, 'L'); // Left-aligned value

                // Label (Arabic - Right Aligned within the right column)
                $this->SetXY($this->getPageWidth() - $startX - $valueWidth - $labelWidth, $startY); // Position for the Arabic label
                $this->Cell($labelWidth, $rowHeight, $row[3], 0, 0, 'R'); // Right-aligned label

                // Move to the next row
                $startY += $rowHeight;
            }

        }
    }

    // Page Footer
    public function Footer() {
        // Position at 1.5 cm from bottom
        $this->SetY(-30);

        $this->SetDrawColor(255, 204, 0); // Set the color to yellow for the line
        $this->SetLineWidth(0.5); // Set the line width (0.5 mm for thin line)
        $leftMargin = 10; // Space from the left edge (in mm)
        $rightMargin = 10; // Space from the right edge (in mm)
        $lineStartX = $leftMargin;
        $lineEndX = 297 - $rightMargin;
        $lineY = $this->GetY() - 2; // Y position for the line, 2 mm above the current Y position

        // Draw the line with the specified margins
        $this->Line($lineStartX, $lineY, $lineEndX, $lineY);
        // Thank you note
        $this->SetFont('dejavusans', '', 10);
        $this->Cell(0, 10, 'Thanks for choosing GrandMaster – The sole supplier in middle east for EU & US brands', 0, 1, 'C');
        $this->SetFont('dejavusans', '', 10);
        $this->Cell(0, 10, 'شكرًا لاختيارك جراند ماستر - المورد الوحيد في الشرق الأوسط للعلامات التجارية الأوروبية والأمريكية', 0, 1, 'C');
    }
}

// Create PDF instance in landscape mode
$pdf = new CustomPDF('L', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('GrandMaster');
$pdf->SetTitle('Quotation');
$pdf->SetMargins(10, 40, 10);
$pdf->SetAutoPageBreak(TRUE, 40);
$pdf->AddPage();

// Add some space before the table
$pdf->Ln(40); // Adds 40 mm of space after the payment terms

// Add table header and rows
$pdf->SetFont('dejavusans', 'B', 5);
$pdf->SetFillColor(230, 230, 230);

// Header
// Define the initial header widths and scale them
$headers = [
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
$totalWidth = array_sum($headerWidths);

// Page width (in mm) for A4 landscape is 297 mm, with some margins taken into account
$pageWidth = 297 - 20; // 10 mm left and right margins

// Calculate scale factor to fit the table within the page width
$scaleFactor = $pageWidth / $totalWidth;

// Adjust header widths proportionally
$scaledHeaderWidths = array_map(function($width) use ($scaleFactor) {
    return $width * $scaleFactor;
}, $headerWidths);

$items = [
    ["1", "GM-1111", "PPP Manufacturer: True", "EACH", 5, 50, 10, 250, "11/7/2025"],
    ["2", "GM-2222", "GGG Manufacturer: La Marzocco GGG Manufacturer: La Marzocco GGG Manufacturer: La Marzocco GGG Manufacturer: La Marzocco GGG Manufacturer: La Marzocco", "EACH", 1, 100, 20, 100, "11/2/2030"],
    ["3", "GM-3333", "TTT Manufacturer: Baratza", "EACH", 10, 90, 50, 900, "20/06/2050"],
    ["4", "GM-1111", "PPP Manufactur dhcjbdkhc lahcgadyuc y aycgad iygyuld vcodygv dyuvgad uvyjd ver: True", "EACH", 5, 50, 10, 250, "11/7/2025"],
    ["5", "GM-2222", "GGG Manufacturer: La Marzocco", "EACH", 1, 100, 20, 100, "11/2/2030"],
    ["6", "GM-3333", "TTT Manufactiaehcad dhvdyu yudc yd isfiud iuhd viu div diuvs yui rgi uryor yoyu idcyd yuydc yuurer: Baratza aefgidfuyd vf", "EACH", 10, 90, 50, 900, "20/06/2050"],
    ["7", "GM-1111", "PPP Manufacturer: True", "EACH", 5, 50, 10, 250, "11/7/2025"],
    ["8", "GM-2222", "GGG Manufacturer: La Marzocco", "EACH", 1, 100, 20, 100, "11/2/2030"],
    ["9", "GM-3333", "TTT Manufacturer: Baratza", "EACH", 10, 90, 50, 900, "20/06/2050"],
    ["10", "GM-1111", "PPP Manufacturer: True", "EACH", 5, 50, 10, 250, "11/7/2025"],
    ["11", "GM-2222", "GGG Manufacturer: La Marzocco", "EACH", 1, 100, 20, 100, "11/2/2030"],
    ["12", "GM-3333", "TTT Manufacturer: Baratza", "EACH", 10, 90, 50, 900, "20/06/2050"],
];

// Set up the PDF document
$pdf->SetMargins(10, 10, 10); // Left, Top, Right margins
$pdf->SetAutoPageBreak(TRUE, 40);

// Print headers with scaled widths
$pdf->SetFont('dejavusans', 'B', 10);
$pdf->SetFillColor(230, 230, 230);

$headerHeight = 20; // Uniform height for header cells

// Header
foreach ($headers as $key => $headerPair) {
    $cellWidth = $scaledHeaderWidths[$key];

    // Prepare the content for the header cell with a line break
    $headerText = "\n" . $headerPair[1] . "\n" . $headerPair[0];

    // Save current X and Y positions
    $xStart = $pdf->GetX();
    $yStart = $pdf->GetY();
    $pdf->MultiCell(
        $cellWidth,
        $headerHeight, // Divide height between two lines
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
$pdf->SetFont('dejavusans', '', 10);

// Print each row with scaled column widths
foreach ($items as $item) {
    $rowHeights = [];
    $defaultHeight = 20; // Set default row height

    // First pass: Calculate the maximum height for each cell in the row
    foreach ($item as $key => $value) {
        $cellWidth = $scaledHeaderWidths[$key];
        $calculatedHeight = $pdf->getStringHeight($cellWidth, $value) + $defaultHeight;
        $rowHeights[] = max($defaultHeight, $calculatedHeight); // Use the greater of the default or calculated height
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
        $contentHeight = $pdf->getStringHeight($cellWidth, $value);
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

// Add total amount details (English)
$pdf->SetFont('dejavusans', '', 8);

// Add total amount details (English and Arabic on the same line)
$pdf->SetXY(10, $pdf->GetY() + 20); // Adjust Y-coordinate to place below the table
$pdf->Cell(95, 10, 'Total Amount (Before VAT): $1,250', 0, 0, 'L');
$pdf->SetXY(190, $pdf->GetY()); // Adjust X-coordinate for right alignment and keep the Y-coordinate the same
$pdf->Cell(95, 10, 'المبلغ الإجمالي (قبل الضريبة): $1,250', 0, 0, 'R');

// Add VAT amount details (English and Arabic on the same line)
$pdf->SetXY(10, $pdf->GetY() + 5); // Move down to the next line
$pdf->Cell(95, 10, 'VAT Amount: $150', 0, 0, 'L');
$pdf->SetXY(190, $pdf->GetY()); // Adjust X-coordinate for right alignment and keep the Y-coordinate the same
$pdf->Cell(95, 10, 'قيمة الضريبة: $150', 0, 0, 'R');

// Add total amount after VAT details (English and Arabic on the same line)
$pdf->SetXY(10, $pdf->GetY() + 5); // Move down to the next line
$pdf->Cell(95, 10, 'Total Amount (After VAT): $1,400', 0, 0, 'L');
$pdf->SetXY(190, $pdf->GetY()); // Adjust X-coordinate for right alignment and keep the Y-coordinate the same
$pdf->Cell(95, 10, 'المبلغ الإجمالي (بعد الضريبة): $1,400', 0, 0, 'R');

// Add the "Approved By" section (English and Arabic)
$pdf->SetXY(60, $pdf->GetY() + 10); // Adjust Y-coordinate to place below the total amount after VAT
$pdf->SetFont('dejavusans', '', 8);
$pdf->Cell(95, 10, 'Approved By:', 0, 0, 'L');
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

// Output the PDF
$pdf->Output('quotation.pdf', 'I');
?>