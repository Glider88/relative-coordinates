<?php declare(strict_types=1);

namespace Glider88\RelativeCoordinates\Relative;

class PositionalCoordinates
{
    private int $height;

    /** @var string[] */
    private array $columnNames;

    /**
     * @param string[] $columnNames
     * @param int $height
     */
    public function __construct(array $columnNames, int $height)
    {
        $this->height = $height;
        $this->columnNames = array_values($columnNames);
    }

    /**
     * @param int $offset 1 -> first row, -1 -> last row
     * @return int
     */
    public function relativeRow(int $offset): int
    {
        if ($offset === 0) {
            throw new \InvalidArgumentException('Incorrect row offset: cannot be 0');
        }

        $abs = abs($offset);
        if ($abs > $this->height) {
            throw new \InvalidArgumentException("Incorrect row offset: must be in [-$abs, $abs]");
        }

        if ($offset > 0) {
            $result = $offset;
        } else {
            $result = $this->height - $abs + 1;
        }

        return $result;
    }

    /**
     * @param int $offset 1 -> first column, -1 -> last column
     * @return string
     */
    public function relativeColumn(int $offset): string
    {
        if ($offset === 0) {
            throw new \InvalidArgumentException('Incorrect column offset: cannot be 0');
        }

        $height = count($this->columnNames);
        $abs = abs($offset);
        if ($abs > $height) {
            throw new \InvalidArgumentException("Incorrect column offset: must be in [-$abs, $abs]");
        }

        if ($offset > 0) {
            $index = $offset - 1;
        } else {
            $index = $height - $abs;
        }

        return $this->columnNames[$index];
    }
}
