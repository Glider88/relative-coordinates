<?php declare(strict_types=1);

namespace Tests\Glider88\RelativeCoordinates\Relative;

use Glider88\RelativeCoordinates\Relative\RelativeCoordinates;
use Glider88\RelativeCoordinates\Relative\RelativeData;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class RelativeDataTest extends TestCase
{
    public static function absoluteProvider(): array
    {
        // A    B    C      D     E     F   G
        //    one  two  three  four  five

        // 1  2  3  4  5  6  7  8  9
        //       1  2  3  4  5  6  7

        $cs = ['one', 'two', 'three', 'four', 'five'];

        return [
            'only column' =>
                [$cs, ['[three]'], ['D']],
            'only row' =>
                [$cs, ['[2]'], ['4']],
            'cell' =>
                [$cs, ['[four4]'], ['E6']],
            'range' =>
                [$cs, ['[one1:five4]'], ['B3:F6']],
            'multiple rows' =>
                [
                    $cs,
                    ['row1' => ['[one1:two2]', '[one1:two2]'], 'row2' => ['[three2:five5]', '[three2:five5]']],
                    ['row1' => ['B3:C4', 'B3:C4'], 'row2' => ['D4:F7', 'D4:F7']],
                ],
            'different escapes position' =>
                [$cs, ['[two][1][:four4]'], ['C3:E6']],
            'with other words' =>
                [$cs, ['one1[three3]4four5'], ['one1D54four5']],
            'default columns' =>
                [[], ['[C7]'], ['D9']],
        ];
    }

    #[DataProvider('absoluteProvider')]
    public function testAbsolute(array $columns, array $relData, array $expected): void
    {
        $coordinateT = RelativeCoordinates::new('B3', $columns);
        $dataT = new RelativeData($coordinateT, ['[', ']']);
        $actual = $dataT->absolute($relData);

        $this->assertSame($expected, $actual);
    }

    #[TestWith([['{{', '}}', '|||']], 'too many escapes')]
    #[TestWith([['|']], 'only one escape')]
    #[TestWith([[]], 'empty escapes')]
    public function testRelativeColumnFailed(array $escapes): void
    {
        $coordinateT = RelativeCoordinates::new('A1');
        $this->expectException(\InvalidArgumentException::class);
        new RelativeData($coordinateT, $escapes);
    }
}
