<?php

// Including API. Please respect folder location!
require '../php-binance-api/vendor/autoload.php';

// Loading API Key & Secret
$keyfile = fopen("keysecret/key", "r") or die("Unable to open file!");
$secretfile = fopen("keysecret/secret", "r") or die("Unable to open file!");
$key = fread($keyfile, filesize("keysecret/key"));
$secret = fread($secretfile, filesize("keysecret/secret"));
fclose($keyfile);
fclose($secretfile);

// Creating an API object
$api = new Binance\API($key, $secret);

require("util.php");

$indicator = new FreedomClub\Indicators();

$symbol = "ETHBTC";
$timeResolution = "1m";

$ticks = $api->candlesticks($symbol, $timeResolution);

$closeArray = array();
foreach ($ticks as $tick) {
    array_push($closeArray, $tick["close"]);
}

list($macdLine, $signalLine, $macdHistogram) = $indicator->macd($closeArray);

$len = sizeof($macdLine);

$file = fopen("macd_data.txt", "w");

for ($i = 0; $i < $len; $i++) {
    fwrite($file, $macdLine[$i]."\t".$signalLine[$i]."\t".$macdHistogram[$i].PHP_EOL);
}
                               
fclose($file);

?>