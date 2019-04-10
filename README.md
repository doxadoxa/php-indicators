# PHP Indicators

Trading indicators library. It's need for 
simplify working with `trader` extension.

## Requires
* `php 7.3+`
* `ext-trader`

## Install
```bash
composer install doxadoxa/phpindicators
```

## Tests
Test not exists yet.

## Example
```php

$klines = ExternalApi::getKlines('btcusdt', '1m');

$ohlvc = new OHLVC( $klines );

$sma14 = $ohlvc->close()->sma(14);
$sma3 = $ohlvc->close()->sma(3);
$atr = $ohlvc->atr(3);

if ( $sma3->crossBelow( $sma14 ) ) {
    Trader::setStopLossOn( $sma3[0] - $atr[0] * 10 );
}

```