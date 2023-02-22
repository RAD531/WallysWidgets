<?php

namespace Tests\Unit;

use App\Actions\CalculatePacks;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    public const DEFAULT_SIZES = [250, 500, 1000, 2000, 5000];

    /** @test */
    public function testExampleValuesReturned()
    {
        $calculator = app(CalculatePacks::class);

        $this->assertEquals([250 => 1, 500 => 0, 1000 => 0, 2000 => 0, 5000 => 0], $calculator->getPacks(1, self::DEFAULT_SIZES));
        $this->assertEquals([250 => 1, 500 => 0, 1000 => 0, 2000 => 0, 5000 => 0], $calculator->getPacks(250, self::DEFAULT_SIZES));
        $this->assertEquals([250 => 0, 500 => 1, 1000 => 0, 2000 => 0, 5000 => 0], $calculator->getPacks(251, self::DEFAULT_SIZES));
        $this->assertEquals([250 => 1, 500 => 1, 1000 => 0, 2000 => 0, 5000 => 0], $calculator->getPacks(501, self::DEFAULT_SIZES));
        $this->assertEquals([250 => 1, 500 => 0, 1000 => 0, 2000 => 1, 5000 => 2], $calculator->getPacks(12001, self::DEFAULT_SIZES));
    }

    /** @test */
    public function testPrimitiveValuesReturned()
    {
        $calculator = app(CalculatePacks::class);

        $this->assertEquals([250 => 1, 500 => 0, 1000 => 0, 2000 => 0, 5000 => 0], $calculator->getPacks(250, self::DEFAULT_SIZES));
        $this->assertEquals([250 => 0, 500 => 1, 1000 => 0, 2000 => 0, 5000 => 0], $calculator->getPacks(500, self::DEFAULT_SIZES));
        $this->assertEquals([250 => 0, 500 => 0, 1000 => 1, 2000 => 0, 5000 => 0], $calculator->getPacks(1000, self::DEFAULT_SIZES));
        $this->assertEquals([250 => 0, 500 => 0, 1000 => 0, 2000 => 1, 5000 => 0], $calculator->getPacks(2000, self::DEFAULT_SIZES));
        $this->assertEquals([250 => 0, 500 => 0, 1000 => 0, 2000 => 0, 5000 => 1], $calculator->getPacks(5000, self::DEFAULT_SIZES));
    }

    /** @test */
    public function testEdgeCaseValuesReturned()
    {
        $calculator = app(CalculatePacks::class);

        $this->assertEquals([250 => 0, 500 => 0, 1000 => 0, 2000 => 2, 5000 => 0], $calculator->getPacks(3751, self::DEFAULT_SIZES));
        $this->assertEquals([250 => 1, 500 => 0, 1000 => 0, 2000 => 2, 5000 => 2], $calculator->getPacks(14001, self::DEFAULT_SIZES));
        $this->assertEquals([250 => 0, 500 => 0, 1000 => 0, 2000 => 1, 5000 => 0], $calculator->getPacks(1999, self::DEFAULT_SIZES));
        $this->assertEquals([250 => 1, 500 => 1, 1000 => 1, 2000 => 0, 5000 => 0], $calculator->getPacks(1650, self::DEFAULT_SIZES));
        $this->assertEquals([250 => 0, 500 => 0, 1000 => 0, 2000 => 0, 5000 => 9], $calculator->getPacks(44844, self::DEFAULT_SIZES));
    }
}
