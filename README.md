# Relative Coordinates
Plugin for PhpSpreadsheet, add relative coordinate transformer.

Installation:
```shell
composer require glider88/relative-coordinates
```

The core idea is to define area with relative sub coordinates.
This allows you to define a table template and use it in many places in an Excel document. 
Also, you can easily add, remove, swap columns without affecting the rest of the sheet.  

```php
$coordT = RelativeCoordinates::new('B3', ['one', 'two', 'three']);
```
If you need formulas in Excel:
```php
$dataT = new RelativeData($coordT);
```
Might be useful, class for easy selection of columns and rows: 
```php
$positional = new PositionalCoordinates($columns, $height);

$firstColumn = $positional->relativeColumn(1);
$lastRow = $positional->relativeRow(-1);
```

Table template:
```php
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Glider88\RelativeCoordinates\Relative\RelativeCoordinates;
use Glider88\RelativeCoordinates\Relative\RelativeData;
use PhpOffice\PhpSpreadsheet\Style\Fill;

$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

$start = 'B3';
$avg = static fn(string $color) => "=AVERAGE({{{$color}2:{$color}4}})";
$data = [
    ['Color', 'Red', 'Green', 'Blue'],
    ['yellow',  255, 255,   0],
    ['cyan',      0, 255, 255],
    ['magenta', 255,   0, 255],
    ['Average color', $avg('red'), $avg('green'), $avg('blue')]
];

$coordT = RelativeCoordinates::new($start, ['color', 'red', 'green', 'blue']);
$dataT = new RelativeData($coordT);

// table styles
$worksheet
    ->getStyle($coordT->absolute('color1:blue1'))
    ->getFont()
    ->setBold(true);

$worksheet
    ->getStyle($coordT->absolute('color1:blue1'))
    ->getFill()
    ->setFillType(Fill::FILL_SOLID)
    ->getStartColor()
    ->setARGB('509965');

$worksheet
    ->getStyle($coordT->absolute('color5:blue5'))
    ->getFill()
    ->setFillType(Fill::FILL_SOLID)
    ->getStartColor()
    ->setARGB('81a8f0');
// end styles

$worksheet->fromArray($dataT->absolute($data), null, $start);

$writer = IOFactory::createWriter($spreadsheet, 'Xls');
$writer->save('table.xls');
```

More complex example: `example/Table.php`

Start docker container with: `bin/up`, test with: `bin/unit`, run command in container: `bin/sh`
