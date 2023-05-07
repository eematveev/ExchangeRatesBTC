# ExchangeRatesBTC

Returns currency exchange rates in relation to BTC using the blockchain.info API

## PHP requirements

 PHP 5 >= 5.2
  
## Installation using Composer

```bash
composer require eematveev/exchange-rates-btc
```
 ## Usage examples

 ```php 
 $rate = ExchangeRatesBTC::getRate('USD','buy');
 $rate = ExchangeRatesBTC::getRate('USD');
 $rate = ExchangeRatesBTC::getRate();
 ```