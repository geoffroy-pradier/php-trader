<?php

namespace GeoffroyPradier\PhpTrader\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use GeoffroyPradier\PhpTrader\Stochastic;

class StochasticTest extends TestCase
{
    public function testStochasticCalculation(): void
    {
        $highs = [101, 103, 102, 106, 104, 108, 111, 109, 113, 116, 114, 119, 121, 123, 120, 126, 124];
        $lows = [99, 101, 100, 104, 102, 106, 109, 107, 111, 114, 112, 117, 119, 121, 118, 124, 122];
        $closes = [100, 102, 101, 105, 103, 107, 110, 108, 112, 115, 113, 118, 120, 122, 119, 125, 123];
        $expectedK = 82.61; // Based on manual calculation
        $expectedD = 88.75; // Based on manual calculation

        $stochastic = Stochastic::calculate($highs, $lows, $closes);

        $this->assertNotEmpty($stochastic['k'], '%K array should not be empty.');
        $this->assertNotEmpty($stochastic['d'], '%D array should not be empty.');
        $this->assertCount(4, $stochastic['k'], 'Expected 4 %K values for 17 prices with kLength=14, kSmoothing=1.');
        $this->assertCount(2, $stochastic['d'], 'Expected 2 %D values with dSmoothing=3.');
        $this->assertEqualsWithDelta($expectedK, $stochastic['k'][1], 0.01, 'Second %K value is incorrect.');
        $this->assertEqualsWithDelta($expectedD, $stochastic['d'][1], 0.01, 'Second %D value is incorrect.');
    }

    public function testStochasticWithKSmoothing3(): void
    {
        $highs = [101, 103, 102, 106, 104, 108, 111, 109, 113, 116, 114, 119, 121, 123, 120, 126, 124, 125];
        $lows = [99, 101, 100, 104, 102, 106, 109, 107, 111, 114, 112, 117, 119, 121, 118, 124, 122, 123];
        $closes = [100, 102, 101, 105, 103, 107, 110, 108, 112, 115, 113, 118, 120, 122, 119, 125, 123, 124];

        $stochastic = Stochastic::calculate($highs, $lows, $closes, 14, 3);
        $this->assertCount(3, $stochastic['k'], 'Expected 3 %K values with kSmoothing=3 on 18 prices.');
        $this->assertCount(1, $stochastic['d'], 'Expected 1 %D value with dSmoothing=3.');
    }

    public function testStochasticInvalidData(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Insufficient or inconsistent data/');
        Stochastic::calculate([100], [99], [100]);
    }

    public function testStochasticInconsistentArrays(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Insufficient or inconsistent data/');
        Stochastic::calculate([100, 101], [99], [100, 101]);
    }

    public function testStochasticNonNumericPrices(): void
    {
        $highs = array_fill(0, 16, 100);
        $lows = array_fill(0, 16, 99);
        $closes = array_fill(0, 15, 100);
        $closes[] = 'invalid'; // 16th element non-numeric
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Prices must be numeric.');
        Stochastic::calculate($highs, $lows, $closes);
    }

    public function testStochasticInvalidPeriod(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('All periods (kLength, kSmoothing, dSmoothing) must be at least 1.');
        Stochastic::calculate([100, 101], [99, 100], [100, 101], 14, 0);
    }

    public function testStochasticInsufficientDataForSlow(): void
    {
        $highs = [101, 103, 102, 106, 104, 108, 111, 109, 113, 116, 114, 119, 121, 123, 120, 126, 124];
        $lows = [99, 101, 100, 104, 102, 106, 109, 107, 111, 114, 112, 117, 119, 121, 118, 124, 122];
        $closes = [100, 102, 101, 105, 103, 107, 110, 108, 112, 115, 113, 118, 120, 122, 119, 125, 123];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Insufficient or inconsistent data/');
        Stochastic::calculate($highs, $lows, $closes, 14, 3);
    }
}