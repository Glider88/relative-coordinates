<?php declare(strict_types=1);

namespace Glider88\RelativeCoordinates\Relative;

class RelativeData
{
    private RelativeCoordinates $transformer;
    private string $startEscape;
    private string $endEscape;

    public function __construct(RelativeCoordinates $transformer, array $escapes = ['{{', '}}'])
    {
        if (count($escapes) !== 2) {
            throw new \InvalidArgumentException("Invalid escapes: " . implode(', ', $escapes));
        }

        if ($escapes[0] === $escapes[1]) {
            throw new \InvalidArgumentException("Invalid escapes: " . implode(', ', $escapes));
        }

        [$start, $end] = $escapes;

        $this->transformer = $transformer;
        $this->startEscape = $start;
        $this->endEscape = $end;
    }

    /**
     * @param string[] | string[][] $relData
     * @return string[] | string[][]
     */
    public function absolute(array $relData): array
    {
        $applyFn = function ($args) {
            if (is_array($args)) {
                return array_map(fn($arg) => $this->absoluteData($arg), $args);
            }

            return $this->absoluteData($args);
        };

        return array_map($applyFn, $relData);
    }

    /**
     * @param string[]|string|mixed $cellData
     * @return string[]|string|mixed
     */
    private function absoluteData(mixed $cellData): mixed
    {
        if (!is_string($cellData)) {
            return $cellData;
        }

        $parts = explode($this->startEscape, $cellData);
        $prefix = array_shift($parts);

        $fn = function ($arg) use ($cellData) {
            $relAndStr = explode($this->endEscape, $arg);
            if (count($relAndStr) === 1) {
                array_unshift($relAndStr, '');
            }
            [$rel, $str] = $relAndStr;

            return $this->transformer->absolute($rel) . $str;
        };

        $absParts = array_map($fn, $parts);

        return $prefix . implode('', $absParts);
    }

    public function height(array $data): int
    {
        $firstRow = current($data);
        if (!is_array($firstRow)) {
            return 1;
        }

        return count($data);
    }
}
