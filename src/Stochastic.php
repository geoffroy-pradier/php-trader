<?php

namespace GeoffroyPradier\PhpTrader;

use InvalidArgumentException;

class Stochastic
{
    /**
     * Calculate the Stochastic Oscillator (%K and %D).
     *
     * @param array $highs Array of high prices (floats or integers).
     * @param array $lows Array of low prices (floats or integers).
     * @param array $closes Array of closing prices (floats or integers).
     * @param int $kLength Period for raw %K calculation (default: 14).
     * @param int $kSmoothing Smoothing period for %K (SMA, default: 1).
     * @param int $dSmoothing Smoothing period for %D (SMA of %K, default: 3).
     *
     * @return array Associative array with 'k' (smoothed %K values) and 'd' (smoothed %D values).
     *
     * @throws InvalidArgumentException If data is insufficient, inconsistent, or invalid parameters/non-numeric.
     */
    public static function calculate(array $highs, array $lows, array $closes, int $kLength = 14, int $kSmoothing = 1, int $dSmoothing = 3): array
    {
        // Validate periods
        if ($kLength < 1 || $kSmoothing < 1 || $dSmoothing < 1) {
            throw new InvalidArgumentException('All periods (kLength, kSmoothing, dSmoothing) must be at least 1.');
        }

        // Validate array sizes
        $dataCount = count($closes);
        $minDataRequired = $kLength + $kSmoothing + $dSmoothing - 2; // For at least one %D value
        if ($dataCount < $minDataRequired || count($highs) !== $dataCount || count($lows) !== $dataCount) {
            throw new InvalidArgumentException('Insufficient or inconsistent data for Stochastic. Need at least ' . $minDataRequired . ' data points.');
        }

        // Validate numeric values
        foreach ([$highs, $lows, $closes] as $array) {
            foreach ($array as $value) {
                if (!is_numeric($value)) {
                    throw new InvalidArgumentException('Prices must be numeric.');
                }
            }
        }

        // Step 1: Calculate Raw %K
        $rawK = [];
        for ($i = $kLength - 1; $i < $dataCount; $i++) {
            $sliceHighs = array_slice($highs, $i - $kLength + 1, $kLength);
            $sliceLows = array_slice($lows, $i - $kLength + 1, $kLength);
            $highestHigh = max($sliceHighs);
            $lowestLow = min($sliceLows);
            $range = $highestHigh - $lowestLow;

            // Handle division by zero
            $currentRawK = $range > 0 ? (100 * ($closes[$i] - $lowestLow) / $range) : 0;
            $rawK[] = $currentRawK;
        }

        // Step 2: Smooth Raw %K to get %K (SMA with kSmoothing)
        $k = [];
        $rawKCount = count($rawK);
        for ($i = $kSmoothing - 1; $i < $rawKCount; $i++) {
            $sumRawK = array_sum(array_slice($rawK, $i - $kSmoothing + 1, $kSmoothing));
            $k[] = $sumRawK / $kSmoothing;
        }

        // Step 3: Smooth %K to get %D (SMA with dSmoothing)
        $d = [];
        $kCount = count($k);
        for ($i = $dSmoothing - 1; $i < $kCount; $i++) {
            $sumK = array_sum(array_slice($k, $i - $dSmoothing + 1, $dSmoothing));
            $d[] = $sumK / $dSmoothing;
        }

        return ['k' => $k, 'd' => $d];
    }
}