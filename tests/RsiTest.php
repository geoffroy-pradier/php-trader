<?php

namespace GeoffroyPradier\PhpTrader\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use GeoffroyPradier\PhpTrader\Rsi;

class RsiTest extends TestCase
{
    public function testRsiCalculation(): void
    {
        $prices = [100, 110, 90, 110, 100, 90, 110, 105, 110, 120, 130, 100, 120, 100, 110];
        $expectedRsi = 52.5; // Based on manual calculation

        $rsi = Rsi::calculate($prices);

        $this->assertNotEmpty($rsi, 'RSI array should not be empty.');
        $this->assertCount(1, $rsi, 'Expected one RSI value for 15 prices with period 14.');
        $this->assertEqualsWithDelta($expectedRsi, $rsi[0], 0.01, 'First RSI value is incorrect.');
    }

    public function testRsiInvalidData(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Not enough data to calculate RSI.');
        Rsi::calculate([100, 102]);
    }

    public function testRsiWithZeroLoss(): void
    {
        $prices = [100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114];
        $rsi = Rsi::calculate($prices);
        $this->assertEquals(100, $rsi[0], 'RSI should be 100 when there are no losses.');
    }

    public function testRsiWithZeroGain(): void
    {
        $prices = [114, 113, 112, 111, 110, 109, 108, 107, 106, 105, 104, 103, 102, 101, 100];
        $rsi = Rsi::calculate($prices);
        $this->assertEquals(0, $rsi[0], 'RSI should be 0 when there are no gains.');
    }

    public function testRsiNonNumericPrices(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Prices must be numeric.');
        Rsi::calculate([100, 110, 'invalid', 110, 100, 90, 110, 105, 110, 120, 130, 100, 120, 100, 110]);
    }
}