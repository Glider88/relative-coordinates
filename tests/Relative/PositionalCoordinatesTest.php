<?php declare(strict_types=1);

namespace Tests\Glider88\RelativeCoordinates\Relative;

use Glider88\RelativeCoordinates\Relative\PositionalCoordinates;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class PositionalCoordinatesTest extends TestCase
{
    public static function relativeRowProvider(): array
    {
        return [
            'first row forward'     => [1,  1],
            'first row backward'    => [-7, 1],
            'second row forward'    => [2,  2],
            'second row backward'   => [-6, 2],
            'last row forward'      => [7,  7],
            'last row backward'     => [-1, 7],
            'previous row forward'  => [6,  6],
            'previous row backward' => [-2, 6],
        ];
    }

    #[DataProvider('relativeRowProvider')]
    public function testRelativeRow(int $positionalRow, int $expected): void
    {
        $positional = new PositionalCoordinates(['one', 'two', 'three'], 7);
        $actual = $positional->relativeRow($positionalRow);

        $this->assertSame($expected, $actual);
    }

    public static function relativeColumnProvider(): array
    {
        return [
            'first column forward'     => [1,  'one'],
            'first column backward'    => [-5, 'one'],
            'second column forward'    => [2,  'two'],
            'second column backward'   => [-4, 'two'],
            'last column forward'      => [5,  'five'],
            'last column backward'     => [-1, 'five'],
            'previous column forward'  => [4,  'four'],
            'previous column backward' => [-2, 'four'],
        ];
    }

    #[DataProvider('relativeColumnProvider')]
    public function testRelativeColumn(int $positionalColumn, string $expected): void
    {
        $positional = new PositionalCoordinates(['one', 'two', 'three', 'four', 'five'], 7);
        $actual = $positional->relativeColumn($positionalColumn);

        $this->assertSame($expected, $actual);
    }

    #[TestWith([0], 'zero row')]
    #[TestWith([8], 'out of bound row')]
    #[TestWith([-8], 'out of bound for negative row')]
    public function testRelativeRowFailed(int $positionalRow): void
    {
        $positional = new PositionalCoordinates(['one', 'two', 'three'], 7);

        $this->expectException(\InvalidArgumentException::class);
        $positional->relativeRow($positionalRow);
    }

    #[TestWith([0], 'zero column')]
    #[TestWith([6], 'out of bound column')]
    #[TestWith([-6], 'out of bound for negative column')]
    public function testRelativeColumnFailed(int $positionalColumn): void
    {
        $positional = new PositionalCoordinates(['one', 'two', 'three', 'four', 'five'], 7);

        $this->expectException(\InvalidArgumentException::class);
        $positional->relativeColumn($positionalColumn);
    }
}
