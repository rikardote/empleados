<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

// Create a CSV with Windows-1252 encoding containing Ñ
$text = "ID,Nombre\n1,NUÑEZ";
$win1252 = mb_convert_encoding($text, 'Windows-1252', 'UTF-8');
file_put_contents('test_win1252.csv', $win1252);

echo "--- Reading Windows-1252 CSV as UTF-8 (default) ---\n";
try {
    $reader = IOFactory::createReader('Csv');
    // By default it might try to detect or use UTF-8
    $spreadsheet = $reader->load('test_win1252.csv');
    $val = $spreadsheet->getActiveSheet()->getCell('B2')->getValue();
    echo "Value: " . $val . "\n";
    echo "Hex: " . bin2hex($val) . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n--- Reading Windows-1252 CSV with explicit encoding ---\n";
try {
    $reader = IOFactory::createReader('Csv');
    $reader->setInputEncoding('Windows-1252');
    $spreadsheet = $reader->load('test_win1252.csv');
    $val = $spreadsheet->getActiveSheet()->getCell('B2')->getValue();
    echo "Value: " . $val . "\n";
    echo "Hex: " . bin2hex($val) . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

unlink('test_win1252.csv');
