<?php

namespace FreedomClub;
class Indicators {
    public function __construct(){}
    public function sma($array, $periodLength) { // Simple Moving Average
        $result = array();
        $keys = array_keys($array);
        $length = sizeof($array);

        if ($periodLength > $length) 
            return -1; // Purpose: Error. It should be checked.

        if($periodLength < 1)
            return -1; // Purpose: Error. It should be checked.

        for ($i = 0; $i < $length - $periodLength + 1; ++$i) {
            $sum = 0;
            for ($j = 0; $j < $periodLength; ++$j) {
                $sum += $array[$keys[$i+$j]];
            }
            $sum /= $periodLength;
            array_push($result, $sum);
        }

        return $result;
    }
}


?>
