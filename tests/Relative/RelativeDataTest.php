<?php declare(strict_types=1);

namespace Tests\Glider88\RelativeCoordinates\Relative;

use Glider88\RelativeCoordinates\Relative\RelativeDate;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class RelativeDateTest extends TestCase
{
    public static function absoluteProvider(): array
    {
        // A  B  C  D  E  F  G  H  I  J  K  L
        // 1  2  3  4  5  6  7  8  9
        $titles = ['one', 'two', 'three', 'four', 'five'];
        return [
            '' => [],
        ];
    }

    #[DataProvider('absoluteProvider')]
    public function testAbsolute(string $start, array $titles, string $relCoord, string $expected): void
    {
        $coordinateT = RelativeDate::generate($start, $titles);
        $actual = $coordinateT->absolute($relCoord);

        $this->assertSame($expected, $actual);
    }

    #[TestWith([], '')]
    public function testAbsoluteFailed(array $titles, string $relCoord): void
    {
        $coordinateT = RelativeDate::generate('A1', $titles);
        $this->expectException(\InvalidArgumentException::class);
        $coordinateT->absolute($relCoord);
    }
}
