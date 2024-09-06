<?php declare(strict_types=1);

namespace Glider88\RelativeCoordinates\Relative;

use PhpOffice\PhpSpreadsheet\Cell\CellAddress;

class RelativeCoordinates
{
    private int $rowOffset;

    /** @var string[] */
    private array $relativeColumns;

    /** @var string[] */
    private array $absoluteColumns;

    public static function generate(
        string $absTopLeftCoordinate,
        array $columnNames = [],
    ): self {
        $absCell = CellAddress::fromCellAddress($absTopLeftCoordinate);
        $rowOffset = $absCell->rowId();

        if (empty($columnNames)) {
            $columnNames = range('A', 'Z');
        }

        $relToAbs = [];
        foreach ($columnNames as $column) {
            $relToAbs[$column] = $absCell->columnName();
            $absCell = $absCell->nextColumn();
        }

        uksort($relToAbs, static fn(string $a, $b) => strcmp($a, $b) * -1);

        $relatives = array_keys($relToAbs);
        $absolutes = array_values($relToAbs);

        return new RelativeCoordinates($rowOffset, $relatives, $absolutes);
    }

    private function __construct(int $rowOffset, array $relativeColumns, array $absoluteColumns)
    {
        $this->rowOffset = $rowOffset;
        $this->relativeColumns = $relativeColumns;
        $this->absoluteColumns = $absoluteColumns;
    }

    public function absolute(string $relCoord): string
    {
        $fixedColumn = str_replace($this->relativeColumns, $this->absoluteColumns, $relCoord);

        $fn = function ($args) use ($relCoord) {
            $number = (int) $args[0];
            if ($number === 0) {
                throw new \InvalidArgumentException("Relative row starts with 1, not '$relCoord'");
            }

            return $number + $this->rowOffset - 1;
        };

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $fixedRowAndColumn = preg_replace_callback('/\d+/', $fn, $fixedColumn);

        return $fixedRowAndColumn;
    }
}
