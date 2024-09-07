<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Glider88\RelativeCoordinates\Relative\RelativeCoordinates;
use Glider88\RelativeCoordinates\Relative\RelativeData;
use Glider88\RelativeCoordinates\Relative\PositionalCoordinates;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

$columns = ['date', 'region', 'rep', 'item', 'units', 'cost'];
$titles = ['OrderDate', 'Region', 'Rep', 'Item', 'Units', 'UnitCost'];
$data = [
    ['2024-01-06', 'East',    'Jones',   'Pencil',   95,   1.99],
    ['2024-01-23', 'Central', 'Kivell',  'Binder',   50,  19.99],
    ['2024-02-09', 'Central', 'Jardine', 'Pencil',   36,   4.99],
    ['2024-02-26', 'Central', 'Gill',    'Pen',      27,  19.99],
    ['2024-03-15', 'West',    'Sorvino',  'Pencil',  56,   2.99],
    ['2024-04-01', 'East',    'Jones',    'Binder',  60,   4.99],
    ['2024-04-18', 'Central', 'Andrews',  'Pencil',  75,   1.99],
    ['2024-05-05', 'Central', 'Jardine',  'Pencil',  90,   4.99],
    ['2024-05-22', 'West',    'Thompson', 'Pencil',  32,   1.99],
    ['2024-06-08', 'East',    'Jones',    'Binder',  60,   8.99],
    ['2024-06-25', 'Central', 'Morgan',   'Pencil',  90,   4.99],
    ['2024-07-12', 'East',    'Howard',   'Binder',  29,   1.99],
    ['2024-07-29', 'East',    'Parent',   'Binder',  81,  19.99],
    ['2024-08-15', 'East',    'Jones',    'Pencil',  35,   4.99],
    ['2024-09-01', 'Central', 'Smith',    'Desk',     2, 125],
    ['2024-09-18', 'East',    'Jones',    'Pen Set', 16,  15.99],
    ['2024-10-05', 'Central', 'Morgan',   'Binder',  28,   8.99],
    ['2024-10-22', 'East',    'Jones',    'Pen',     64,   8.99],
    ['2024-11-08', 'East',    'Parent',   'Pen',     15,  19.99],
    ['2024-11-25', 'Central', 'Kivell',   'Pen Set', 96,   4.99],
    ['2024-12-12', 'Central', 'Smith',    'Pencil',  67,   1.29],
    ['2024-12-29', 'East',    'Parent',   'Pen Set', 74,  15.99],
];

const GREY = 'c8cfca';
const GREEN = '509965';
const BLUE = '81a8f0';

$data = array_map(
    static fn(array $row) => array_merge([Date::PHPToExcel(strtotime($row[0]))], array_slice($row, 1)),
    $data
);
array_unshift($data, $titles);

$height = count($data) + 1; // + 1 for totals

$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();
$start = 'B3';

$t = RelativeCoordinates::new($start, $columns);
$dt = new RelativeData($t, ['[', ']']);
$positional = new PositionalCoordinates($columns, $height);

$firstColumn = $positional->relativeColumn(1);
$lastColumn = $positional->relativeColumn(-1);
$titleRow1 = $positional->relativeRow(1);
$titleRow2 = $positional->relativeRow(2);
$firstTableRow = $positional->relativeRow(3);
$lastTableRow = $positional->relativeRow(-2);
$totalRow = $positional->relativeRow(-1);


foreach ($columns as $column) {
    $worksheet->mergeCells($t->absolute("$column$titleRow1:$column$titleRow2"));
}

$worksheet
    ->getStyle($t->absolute("$firstColumn$titleRow1:$lastColumn$titleRow2"))
    ->getFont()
    ->setBold(true);

$worksheet
    ->getStyle($t->absolute("$firstColumn$titleRow1:$lastColumn$titleRow2"))
    ->getAlignment()
    ->setVertical(Alignment::VERTICAL_CENTER)
    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

foreach ($columns as $column) {
    $worksheet
        ->getColumnDimension($t->absolute($column))
        ->setAutoSize(true);
}

foreach (range($firstTableRow, $totalRow) as $row) {
    $worksheet
        ->getRowDimension((int) $t->absolute((string) $row))
        ->setRowHeight(16.0);
}

$worksheet
    ->getStyle($t->absolute("date$firstTableRow:date$totalRow"))
    ->getNumberFormat()
    ->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD);

$worksheet
    ->getStyle($t->absolute("$firstColumn$titleRow1:$lastColumn$titleRow2"))
    ->getFill()
    ->setFillType(Fill::FILL_SOLID)
    ->getStartColor()
    ->setARGB(GREEN);

$worksheet
    ->getStyle($t->absolute("$firstColumn$totalRow:$lastColumn$totalRow"))
    ->getFill()
    ->setFillType(Fill::FILL_SOLID)
    ->getStartColor()
    ->setARGB(BLUE);

$oddRows = range($firstTableRow + 1, $lastTableRow,2);
foreach ($oddRows as $oddRow) {
    $worksheet
        ->getStyle($t->absolute("$firstColumn$oddRow:$lastColumn$oddRow"))
        ->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()
        ->setARGB(GREY);
}

$totals = [
    "=MAX([date$firstTableRow:date$lastTableRow])",
    "",
    "",
    "",
    "=MIN([units$firstTableRow:units$lastTableRow])",
    "=SUM([cost$firstTableRow:cost$lastTableRow])",
];

$data[] = $totals;
$absData = $dt->absolute($data);
$worksheet->fromArray($absData, null, $start);

$writer = IOFactory::createWriter($spreadsheet, 'Xls');
$writer->save('table.xls');
