<?php

$relativePath = "../";

// Including API. Please respect folder location!
require $relativePath.'../php-binance-api/vendor/autoload.php';

// Loading API Key & Secret
$keyfile = fopen($relativePath."keysecret/key", "r") or die("Unable to open file!");
$secretfile = fopen($relativePath."keysecret/secret", "r") or die("Unable to open file!");
$key = fread($keyfile, filesize($relativePath."keysecret/key"));
$secret = fread($secretfile, filesize($relativePath."keysecret/secret"));
fclose($keyfile);
fclose($secretfile);

// Creating an API object
$api = new Binance\API($key, $secret);

require($relativePath."util.php");

$indicator = new FreedomClub\Indicators();

$symbol = "ETHBTC";
$timeResolution = "15m";

$ticks = $api->candlesticks($symbol, $timeResolution);

$closeArray = array();
$openArray = array();
foreach ($ticks as $tick) {
    array_push($closeArray, $tick["close"]);
    array_push($openArray, $tick["open"]);
}

// Calculation of RSI
list($rsi, $averageGain, $averageLoss) = $indicator->rsi($openArray, $closeArray);
if (!$rsi) die ("Error occured while calculating RSI!".PHP_EOL);

// Calculation of MACD
list($macdLine, $signalLine, $macdHistogram) = $indicator->macd($closeArray);
if (!$macdLine) die ("Error occured while calculating MACD!".PHP_EOL);

// Calculation of BOLL
list($middleBand, $upperBand, $lowerBand) = $indicator->boll($closeArray);
if (!$middleBand) die ("Error occured while calculating BOLL!".PHP_EOL);

?>