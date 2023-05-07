<?php
/**
 * Returns currency exchange rates in relation to BTC using the blockchain.info API
 * 
 * PHP 5 >= 5.2
 * 
 * Using examples:
 * 
 * $rate = ExchangeRatesBTC::getRate('USD','buy');
 * $rate = ExchangeRatesBTC::getRate('USD');
 * $rate = ExchangeRatesBTC::getRate();
 */
class ExchangeRatesBTC
{
    const ERROR_LOG = true;

    const UPDATE_PERIOD = 900;

    protected static $jsonFileUrl = 'https://blockchain.info/ticker';

    protected static $jsonFileName = __DIR__ . '/' . __CLASS__ . '.json';

    protected static $logFileName = __DIR__ . '/' . __CLASS__ . '.log';

    protected static $rates = [
        'updated' => 0,
        'data' => [],
    ];

    /**
     * Returns currency exchange rate in relation to BTC
     * 
     * @param string $code 'USD','RUB', ...
     * @param string $type 'buy','sell', '15m', ...
     * @return float|null
     * @see self::$jsonFileName
     */
    public static function getRate($code = 'USD', $type = '15m')
    {
        self::updateRates();
        $code = strtoupper($code);
        $type = strtolower($type);
        return isset(self::$rates['data'][$code][$type]) ? self::$rates['data'][$code][$type] : null;
    }

    /**
     * Updates work array of currency rates
     *
     * @return void
     */
    public static function updateRates()
    {
        if (!self::isNeedUpdateRates()) return;
        if (session_id() && $_SESSION[__CLASS__]['updated'] && $_SESSION[__CLASS__]['updated'] > self::$rates['updated']) {
            self::$rates = $_SESSION[__CLASS__];
        }
        if (!self::isNeedUpdateRates()) return;
        if (file_exists(self::$jsonFileName) && filemtime(self::$jsonFileName) > self::$rates['updated']) {
            $json = file_get_contents(self::$jsonFileName);
            if (!$ratesData = json_decode($json, true)) {
                self::log('Can not decode json data from '.basename(self::$jsonFileName));
            } else {
                self::$rates = [
                    'updated' => filemtime(self::$jsonFileName),
                    'data' => $ratesData,
                ];
                if (session_id() && $_SESSION[__CLASS__]['updated'] && self::$rates['updated'] > $_SESSION[__CLASS__]['updated']) {
                    $_SESSION[__CLASS__] = self::$rates;
                }
            }
        }
        if (!self::isNeedUpdateRates()) return;
        if (!$json = file_get_contents(self::$jsonFileUrl)) {
            self::log('Can not open '.self::$jsonFileUrl);
            return;
        }
        if (!$ratesData = json_decode($json, true)) {
            self::log('Can not decode json data from '.self::$jsonFileUrl);
            return;
        }
        if (file_put_contents(self::$jsonFileName, $json, LOCK_EX)) {
            clearstatcache();
        }
        self::$rates = [
            'updated' => time(),
            'data' => $ratesData,
        ];
        if (session_id()) {
            $_SESSION[__CLASS__] = self::$rates;
        } else {
            self::log('Session unavailable');
        }
    }

    /**
     * Is it need to update rates?
     *
     * @return bool
     */
    public static function isNeedUpdateRates()
    {
        if (self::$rates['updated'] + self::UPDATE_PERIOD < time()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Writes message to log
     * 
     * @param string $message
     * @return bool
     */
    public static function log($message = '')
    {
        if (!self::ERROR_LOG) return true;
        if (!$message) return true;

        $message = date('Y-m-d H:i:s') . ' (' . debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0]['line'] . ') ' . trim($message) . "\n";

        return file_put_contents(self::$logFileName, $message, FILE_APPEND|LOCK_EX);
    }
}