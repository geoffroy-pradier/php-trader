<?php

namespace GeoffroyPradier\PhpTrader;

use InvalidArgumentException;

class Rsi
{
    /**
     * Calculate the Relative Strength Index (RSI) for a given period.
     *
     * @param array $prices Array of closing prices (floats or integers).
     * @param int $period Calculation period (default: 14).
     * @return array Array of RSI values starting after the initial period.
     *
     * @throws InvalidArgumentException If insufficient data or invalid input.
     */
    public static function calculate(array $prices, int $period = 14): array
    {
        if ($period < 1) {
            throw new InvalidArgumentException('Period must be at least 1.');
        }
        if (count($prices) < $period + 1) {
            throw new InvalidArgumentException('Not enough data to calculate RSI.');
        }
        foreach ($prices as $price) {
            if (!is_numeric($price)) {
                throw new InvalidArgumentException('Prices must be numeric.');
            }
        }

        $rsi = [];
        $gains = [];
        $losses = [];

        // Calculate price changes
        for ($i = 1; $i < count($prices); $i++) {
            $change = $prices[$i] - $prices[$i - 1];
            $gains[] = max($change, 0);
            $losses[] = $change < 0 ? abs($change) : 0;
        }

        // Initial averages
        $avgGain = array_sum(array_slice($gains, 0, $period)) / $period;
        $avgLoss = array_sum(array_slice($losses, 0, $period)) / $period;

        // Initial RSI
        $rsi[] = $avgLoss > 0 ? 100 - (100 / (1 + ($avgGain / $avgLoss))) : 100;

        // Subsequent RSI values (Wilder's smoothing)
        for ($i = $period; $i < count($gains); $i++) {
            $avgGain = (($avgGain * ($period - 1)) + $gains[$i]) / $period;
            $avgLoss = (($avgLoss * ($period - 1)) + $losses[$i]) / $period;
            $rsi[] = $avgLoss > 0 ? 100 - (100 / (1 + ($avgGain / $avgLoss))) : 100;
        }

        return $rsi;
    }
}