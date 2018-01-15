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
foreach ($ticks as $tick) {
    array_push($closeArray, $tick["close"]);
}


list($middleBand, $upperBand, $lowerBand) = $indicator->boll($closeArray);

$len = sizeof($middleBand);

$file = fopen("boll_data.txt", "w");

for ($i = 0; $i < $len; $i++) {
    fwrite($file, $middleBand[$i]."\t".$upperBand[$i]."\t".$lowerBand[$i].PHP_EOL);
}
                               
fclose($file);

?>