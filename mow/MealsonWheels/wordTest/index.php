<?php
require_once '../PhpWord/vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Converter;

$phpWord = new PhpWord();

// Define page size and orientation (Letter size, portrait orientation)
$sectionStyle = [
    'orientation' => 'portrait',
    'pageSizeW' => Converter::inchToTwip(8.5),
    'pageSizeH' => Converter::inchToTwip(11),
    'marginLeft' => Converter::inchToTwip(0.156),
    'marginRight' => Converter::inchToTwip(0.156),
    'marginTop' => Converter::inchToTwip(0.5),
    'marginBottom' => Converter::inchToTwip(0.5),
];

// Create a new section in the document
$section = $phpWord->addSection($sectionStyle);

// Define the table style
$tableStyle = [
    'borderSize' => 0,
    'cellMargin' => 0,
    'alignment'  => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER,
];

// Avery 5161 dimensions
$labelWidth = Converter::inchToTwip(4);    // Slightly adjusted to ensure it fits within the margins
$labelHeight = Converter::inchToTwip(1);      // Label height
$gapWidth = Converter::inchToTwip(0.188);   // Gap width between columns

// Define the number of rows (Avery 5161 is 2 columns x 10 rows)
$rows = 10;

// Loop through the rows and columns to create cells
for ($row = 0; $row < $rows; $row++) {
    $table = $section->addTable($tableStyle);
    $table->addRow($labelHeight); // Set row height

    // First content column
    $table->addCell($labelWidth, [
        'valign' => 'top',
        'align'  => 'left',
        'marginTop' => 0,
        'marginLeft' => 0,
        'marginRight' => 0,
        'marginBottom' => 0,
    ])->addText("Text");

    // Gap column
    $table->addCell($gapWidth, [
        'valign' => 'top',
        'align'  => 'left',
        'borderSize' => 0, // No border for the gap
    ]);

    // Second content column
    $table->addCell($labelWidth, [
        'valign' => 'top',
        'align'  => 'left',
        'marginTop' => 0,
        'marginLeft' => 0,
        'marginRight' => 0,
        'marginBottom' => 0,
    ])->addText("Text");
}

// Set headers to force download or preview in browser
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="AveryTest_'.time().'"');
header('Cache-Control: max-age=0');

// Save the document to output stream
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
$objWriter->save('php://output');
