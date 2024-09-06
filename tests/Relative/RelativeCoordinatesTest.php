<?php declare(strict_types=1);

namespace Tests\Glider88\RelativeCoordinates\Relative;

use Glider88\RelativeCoordinates\Relative\RelativeCoordinates;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class RelativeCoordinatesTest extends TestCase
{
    public static function absoluteProvider(): array
    {
        // A  B  C  D  E  F  G  H  I  J  K  L
        // 1  2  3  4  5  6  7  8  9
        $titles = ['one', 'two', 'three', 'four', 'five'];
        return [
            'only column'     => ['B3', $titles,        'two', 'C'],
            'only row'        => ['A2', $titles,          '3', '4'],
            'cell'            => ['C4', $titles,      'four4', 'F7'],
            'range'           => ['A1', $titles, 'one1:five4', 'A1:E4'],
            'default columns' => ['D5',      [],         'C2', 'F6'],
            'subset columns'  =>
                ['B1', ['a', 'aa', 'aaa'], 'aa2', 'C2'],
        ];
    }

    #[DataProvider('absoluteProvider')]
    public function testAbsolute(string $start, array $titles, string $relCoord, string $expected): void
    {
        $coordinateT = RelativeCoordinates::generate($start, $titles);
        $actual = $coordinateT->absolute($relCoord);

        $this->assertSame($expected, $actual);
    }

    #[TestWith([['one'], 'one0'], 'zero row')]
    public function testAbsoluteFailed(array $titles, string $relCoord): void
    {
        $coordinateT = RelativeCoordinates::generate('A1', $titles);
        $this->expectException(\InvalidArgumentException::class);
        $coordinateT->absolute($relCoord);
    }
}
