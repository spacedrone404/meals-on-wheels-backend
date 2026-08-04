<?php

// Save PHP w/o BOM encode
// For the script to work, you need to install the dependency via: composer require phpoffice/phpspreadsheet
// To support xlsx, you need to enable the zip extension in PHP, since xlsx is essentially a zip archive

// Increasing memory limit
ini_set('memory_limit', '2048M');

require 'vendor/autoload.php';
require_once __DIR__ . '/../connection.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReader;

$inputFileName = __DIR__ . '/2base.xlsx'; 

try {
    $pdo = getDbConnection();

// Loading xls files through a reader for optimization and more efficient memory usage

$reader = IOFactory::createReaderForFile($inputFileName);
$reader -> setReadDataOnly(true);
$spreadsheet = $reader -> load($inputFileName);

// List of all tabs
$sheetNames = [
    'Cold',
    'Salads',    
    'Soups',
    'Fish',
    'Meat',
    'Dairy',
    'Vegetables',
    'Drinks',
    'Side',
    'Baked',
    'Diet',
    'Misc'
];

// counters
$totalRecords = 0;      // total data lines up to 8888
$imported = 0;          
$notImported = 0;       
$totalRows = 0;        // total rows in all XLS tabs


$stmt = $pdo->prepare("
    INSERT INTO mainbase 
    (code, name, description, weight, type, workshop)
    VALUES 
    (:code, :name, :description, :weight, :type, :workshop)
");

foreach ($sheetNames as $sheetName) {
    $worksheet = $spreadsheet->getSheetByName($sheetName);
    if ($worksheet === null) {
        continue; // Tab not found > skipping
    }

    $highestRow = $worksheet->getHighestRow();
	$totalRows += $highestRow;

    // we start parsing from the second line
    for ($row = 2; $row <= $highestRow; $row++) {
        // Parsing Code (column D)
        $codeValue = $worksheet->getCell('D' . $row)->getValue();

        // If you encounter marker 8888, go straight to the next tab.
        if (is_numeric($codeValue) && (int)$codeValue === 8888) {
            break;
        }

        // valid entry (we count it in the total)
        $totalRecords++;

        // reading columns
        $weightValue     = trim((string)($worksheet->getCell('A' . $row)->getValue() ?? ''));
        $nameValue       = trim((string)($worksheet->getCell('B' . $row)->getValue() ?? ''));
        $ingredientsValue = trim((string)($worksheet->getCell('C' . $row)->getValue() ?? ''));
        $workshopValue   = trim((string)($worksheet->getCell('E' . $row)->getValue() ?? ''));

        // code must be a number otherwise > unimported entry
        if (!is_numeric($codeValue) || $codeValue === null || $codeValue === '') {
            $notImported++;
            continue;
        }

        $codeInt = (int)$codeValue;

        try {
            $stmt->execute([
                ':code'        => $codeInt,
                ':name'        => $nameValue,
                ':description' => $ingredientsValue,  
                ':weight'      => $weightValue,
                ':type'        => $sheetName,   // dish category = tab name in XLS
                ':workshop'    => $workshopValue,
            ]);
            $imported++;
        } catch (Exception $e) {
            $notImported++;
			// debug errors
            echo "Error in {$sheetName} row {$row}: " . $e->getMessage();
        }	
    }
}

echo "The vacuum cleaner worked successfully! Script version: v0.8с+<br />";
echo "<br />";

echo "All valid dishes from the file were imported. 2base.xlsx <br />";
echo "Here are detailed statistics of downloaded dishes:";
echo "<br />";
echo "<br />";

// counter of imported lines from XLS
function countDataRows($sheet) {
	$highestRow = $sheet->getHighestRow();
	for ($row = 1; $row <= $highestRow; $row++) {
		$hasData =  false;
		foreach($sheet->getRowIterator($row, $row) as $currentRow) {
			$cellIterator = $currentRow -> getCellIterator();
			$cellIterator -> setIterateOnlyExistingCells(true);
			foreach($cellIterator as $cell) {
				$value = $cell->getCalculatedValue();
				if($value !== null && $value !== '') {
					$hasData = true;
					break 2;
				}
			}
		}
		if(!$hasData) {
			return $row - 1;
		}
	}
	return $highestRow;
}

foreach ($spreadsheet->getAllSheets() as $sheet) {
	$rowsInSheet = countDataRows($sheet) - 2; // we subtract 2 since each tab has 2 service lines, the table header is the first line and the last line is the end-of-data marker
	$totalRows += $rowsInSheet;
	echo "Category {$sheet->getTitle()} contain {$rowsInSheet} items <br />";
}

echo "<br />";
echo "Number of successfully imported items: {$imported} <br />";
echo "Take a pie from the shelf! <br />";
	} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'Error when selecting from DB'
    ], JSON_UNESCAPED_UNICODE);
}
?>
