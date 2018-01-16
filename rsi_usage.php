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
$timeResolution = "15m";

$ticks = $api->candlesticks($symbol, $timeResolution);

$closeArray = array();
$openArray = array();
foreach ($ticks as $tick) {
    array_push($closeArray, $tick["close"]);
    array_push($openArray, $tick["open"]);
}


list($rsi, $averageGain, $averageLoss) = $indicator->rsi($openArray, $closeArray);
if (!$rsi) die ("Bye".PHP_EOL);

$len = sizeof($rsi);

$file = fopen("rsi_data_one.txt", "w");

for ($i = 0; $i < $len; $i++) {
    fwrite($file, $rsi[$i]."\t".$averageGain[$i]."\t".$averageLoss[$i].PHP_EOL);
}
                               
fclose($file);

list($rsi, $averageGain, $averageLoss) = $indicator->rsi($closeArray);
if (!$rsi) die ("Bye".PHP_EOL);

$len = sizeof($rsi);

$file = fopen("rsi_data_cons.txt", "w");

for ($i = 0; $i < $len; $i++) {
    fwrite($file, $rsi[$i]."\t".$averageGain[$i]."\t".$averageLoss[$i].PHP_EOL);
}
                               
fclose($file);
echo "OK".PHP_EOL;
?>