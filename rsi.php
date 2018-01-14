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
$onePeriodDiff = 0;

// Get Last 500 point
	$ticks = $api->candlesticks("ETHBTC", "1m");
	$ptick = current($ticks);
	$ctick = next($ticks);
	$currentTimeInstant = $ctick["openTime"];
	$periodNum = 1;
	$averageGain = 0;
	$averageLoss = 0;
	$RS = 0;
	$RSI = 0;
	$arrayDiff = [];
	$rsiPeriod = 14;
	$RSIArray = [];
for($x=0; $x<=498; $x++){
			$value = $ctick["high"] - $ptick["high"];
			//echo $key."\t".$x."\t".$ptick["high"]."\t".$ctick["high"]."\t".$value."\t".$ctick["volume"]."\t".PHP_EOL;
			$ptick = current($ticks);
			$ctick = next($ticks);
			array_push($arrayDiff,$value);
		}
		
		
		// Moving Calculation
		for($r=0; $r<=count($arrayDiff) - $rsiPeriod; $r++){
		$onePeriodDiff = array_slice($arrayDiff,$r,$rsiPeriod,true);
		$cumSumPositiveValues = 0;
		$cumSumNegativeValues = 0;
			foreach($onePeriodDiff as $dValue){
			if($dValue>=0)
				$cumSumPositiveValues += $dValue;
			else
				$cumSumNegativeValues += $dValue;
			}
			$averageGain = $cumSumPositiveValues /$rsiPeriod;
			$averageLoss = abs($cumSumNegativeValues)/$rsiPeriod;
			$RS = $averageGain / $averageLoss;
			$RSI = 100 - (100/(1+$RS));
			array_push($RSIArray,$RSI);
			print_r("\t".$RSI.PHP_EOL);
		}
		
		$prevTimeInstant = $currentTimeInstant;
		
		
		// Continue to calculate RSI from newer values
 while(true) {
	$ticks = $api->candlesticks("ETHBTC", "1m");
	// Get last candlesticks
	end($ticks);
	$ctick = prev($ticks);
	$currentTimeInstant = $ctick["openTime"];
	
	if ($currentTimeInstant > $prevTimeInstant) {
		array_push($onePeriodDiff,$ctick["high"]);
		$onePeriodDiff = array_slice($onePeriodDiff,1,$rsiPeriod,true);
		foreach($onePeriodDiff as $dValue){
			if($dValue>=0)
				$cumSumPositiveValues += $dValue;
			else
				$cumSumNegativeValues += $dValue;
			}
			$averageGain = $cumSumPositiveValues /$rsiPeriod;
			$averageLoss = abs($cumSumNegativeValues)/$rsiPeriod;
			$RS = $averageGain / $averageLoss;
			$RSI = 100 - (100/(1+$RS));
			array_push($RSIArray,$RSI);
			print_r("\t".$RSI.PHP_EOL);
			$prevTimeInstant = $currentTimeInstant;
	}
} 

?>