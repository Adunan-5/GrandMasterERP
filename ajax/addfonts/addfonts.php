<?php
include_once __DIR__ . "/../../includes/baseIncludes.php";
require_once('../../vendor/autoload.php');


$fontPath = __DIR__ . '/../../fonts\GrandMaster-Book.ttf';
$fontPath = __DIR__ . '/../../fonts\ExpoArabic-Book.ttf';
$fontPath = __DIR__ . '/../../fonts\Tajawal-Regular.ttf';
$fontPath = __DIR__ . '/../../fonts\DejaVuSans-Book.ttf';


$fontPath = __DIR__ . '/../../fonts\TTSupermolot-Bold.ttf';
$fontPath = __DIR__ . '/../../fonts\TTSupermolot-Regular.ttf';

//echo $fontPath;

if (!file_exists($fontPath)) {
    die("Error: Font file not found at $fontPath");
}

// Convert the font to TCPDF format
//$fontname = TCPDF_FONTS::addTTFfont($fontPath, 'TrueTypeUnicode', '', 32);
$fontname = TCPDF_FONTS::addTTFfont($fontPath, 'TrueTypeUnicode', '', 96);

if ($fontname) {
    echo "Font successfully added! Font name: " . $fontname . "\n";
} else {
    echo "Failed to add font.\n";
}