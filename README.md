# ExchangeRatesBTC
Returns currency exchange rates in relation to BTC using the blockchain.info API

 PHP 5 >= 5.2
  
 Using examples:
 
 ```php 
 $rate = ExchangeRatesBTC::getRate('USD','buy');
 $rate = ExchangeRatesBTC::getRate('USD');
 $rate = ExchangeRatesBTC::getRate();
 ```