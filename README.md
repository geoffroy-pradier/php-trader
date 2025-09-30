# Trader

A pure PHP implementation of technical indicators for trading, including the Relative Strength Index (RSI) and Stochastic Oscillator. No native extensions (e.g., Trader PECL) are required, making it portable across PHP environments.

## Installation

Install the package via Composer:

```bash
composer require geoffroy-pradier/trader
```

## Requirements

- PHP >= 8.2

## Usage

### Relative Strength Index (RSI)
Calculate the RSI for a series of closing prices:

```php
use GeoffroyPradier\Trader\Rsi;

$prices = [100, 102, 101, 105, 103, 107, 110, 108, 112, 115, 113, 118, 120, 122, 119];
$rsi = Rsi::calculate($prices, 14);
print_r($rsi); // Outputs array of RSI values
```

### Stochastic Oscillator (%K and %D)
Calculate the %K and %D lines for high, low, and closing prices:

```php
use GeoffroyPradier\Trader\Stochastic;

$highs = [101, 103, 102, 106, 104, 108, 111, 109, 113, 116, 114, 119, 121, 123, 120, 126, 124];
$lows = [99, 101, 100, 104, 102, 106, 109, 107, 111, 114, 112, 117, 119, 121, 118, 124, 122];
$closes = [100, 102, 101, 105, 103, 107, 110, 108, 112, 115, 113, 118, 120, 122, 119, 125, 123];
$stoch = Stochastic::calculate($highs, $lows, $closes, 14, 3);
print_r($stoch['k']); // %K values
print_r($stoch['d']); // %D values
```

## Testing

Run the PHPUnit tests to verify functionality:

```bash
composer test
```

## Development

To contribute:
1. Fork the repository on GitHub.
2. Add your changes and include corresponding PHPUnit tests.
3. Submit a pull request.

## License

MIT