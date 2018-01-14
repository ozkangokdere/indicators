<?php

namespace FreedomClub;
class Indicators {
    public function __construct(){}
    public function sma($array, $periodLength) { // Simple Moving Average
        $isArray = false;
        $keys = array_keys($array);
        $length = sizeof($array);

        if($periodLength != $length) {
            $result = array();
            $isArray = true;
        }
        
        if ($periodLength > $length) 
            return -1; // Purpose: Error. It should be checked.

        if ($periodLength < 1)
            return -1; // Purpose: Error. It should be checked.
        
        for ($i = 0; $i < $length - $periodLength + 1; ++$i) {
            $sum = 0;
            for ($j = 0; $j < $periodLength; ++$j) {
                $sum += $array[$keys[$i+$j]];
            }
            $sum /= $periodLength;
            if ($isArray)
                array_push($result, $sum);
            else
                $result = $sum;
        }

        return $result;
    }
    public function ema($array, $periodLength) { // Exponential Moving Average
        $isArray = false;
        $keys = array_keys($array);
        $multiplier = 2 / ($periodLength + 1);
        $length = sizeof($array);


        if ($periodLength != $length) {
            $result = array();
            $isArray = true;
        }
        
        if ($periodLength > $length)
            return -1; // Purpose: Error. It should be checked.

        if ($periodLength < 1)
            return -1; // Purpose: Error. It should be checked.

        $prevElement = $this->sma(array_slice($array, $keys[0], $periodLength), $periodLength);
        
        if ($isArray) {
            array_push($result, $prevElement);
            for ($i = $periodLength; $i < $length; ++$i) {
                $close = $array[$keys[$i]];
                $ema = ($close - $prevElement) * $multiplier + $prevElement;
                array_push($result, $ema);
                $prevElement = $ema;
            }
        }
        else
            $result = $prevElement;
        
        return $result;
    }
}


?>
