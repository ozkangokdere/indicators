<?php
// Including API. Please respect folder location!
require '../php-binance-api/vendor/autoload.php';

// Loading API Key & Secret
$keyfile = fopen("keysecret/key", "r") or die("Unable to open 
file!");
$secretfile = fopen("keysecret/secret", "r") or die("Unable to 
open file!");
$key = fread($keyfile, filesize("keysecret/key"));
$secret = fread($secretfile, filesize("keysecret/secret"));
fclose($keyfile);
fclose($secretfile);

// Creating an API object
$api = new Binance\API($key, $secret);
$ticker = $api->prices();
$keys = array_keys($ticker);
$prevTimeInstant = -1;
$RSI = 0;
$cumSumPositiveValues = 0;
$cumSumNegativeValues = 0;
$prevValue = 0;
$currentValue = 0;
$value = 0;

while(true) {
	$ticks = $api->candlesticks($key, "1m");
	end($ticks);
	$tick =  prev($ticks);
	$currentTimeInstant = intval($tick["openTime"]);
	if ($currentTimeInstant != $prevTimeInstant) {
		foreach($keys as $key) {
			if($key != 'ETHBTC') {
				continue;
			}
			
				$currentValue = $tick["high"];
				$value = $currentValue - $prevValue;
				if($value>=0)
					$cumSumPositiveValues = $cumSumPositiveValues + $value;
				else
					$cumSumNegativeValues = $cumSumNegativeValues + $value;
				
				echo $key."\t".$tick["high"]."\t".$value."\t".$tick["volume"]."\t".$tick["openTime"].PHP_EOL;
				$prevTimeInstant = $currentTimeInstant;
				
		}
	}
    
}


?>
