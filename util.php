<?php

namespace FreedomClub;
class Indicators {
    public function __construct(){}
    public function sma($array, $periodLength)
    { // Simple Moving Average
        $isArray = false;
        $keys = array_keys($array);
        $length = sizeof($array);

        if($periodLength != $length) {
            $result = array();
            $isArray = true;
        }
        
        if ($periodLength > $length) 
            return NULL; // Purpose: Error. It should be checked.

        if ($periodLength < 1)
            return NULL; // Purpose: Error. It should be checked.
        
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

    public function ema($array, $periodLength)
    { // Exponential Moving Average
        $isArray = false;
        $keys = array_keys($array);
        $multiplier = 2 / ($periodLength + 1);
        $length = sizeof($array);


        if ($periodLength != $length) {
            $result = array();
            $isArray = true;
        }
        
        if ($periodLength > $length)
            return NULL; // Purpose: Error. It should be checked.

        if ($periodLength < 1)
            return NULL; // Purpose: Error. It should be checked.

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

    public function macd($array)
    { // Moving Average Convergance Divergance
        if (!($macdLine = $this->macdLine($array))) return NULL;
        if (!($signalLine = $this->signalLine($macdLine))) return NULL;;

        $lengthSignalLine = sizeof($signalLine);
        $macdLine = array_slice($macdLine, -1*$lengthSignalLine); // it takes last $lengthSignalLine of $macdLine array

        $macdHistogram = $this->macdHistogram($macdLine, $signalLine);
        
        return array($macdLine, $signalLine, $macdHistogram);
    }

    public function boll($array)
    { // Bollinger Bands
        $periodLength = 20;
        if(!($smaW20Period = $this->sma($array, $periodLength))) return NULL;

        $upperBand = array();
        $lowerBand = array();

        $lengthArray = sizeof($array);
        $lengthSmaW20Period = sizeof($smaW20Period);
        
        for ($i = 0; $i < $lengthArray - $periodLength + 1; $i++) {
            $sum = 0;
            for ($j = 0; $j < $periodLength; $j++) {
                $diff = ($array[$i+$j] - $smaW20Period[$i]);
                $sum += $diff*$diff;
            }
            $sum /= $periodLength;
            $sum = sqrt($sum);
            array_push($upperBand, $smaW20Period[$i] + $sum*2);
            array_push($lowerBand, $smaW20Period[$i] - $sum*2);
            
        }
        return array($smaW20Period, $upperBand, $lowerBand);
    }
    
    private function macdLine($array)
    {
        if (!($emaW12Period = $this->ema($array, 12))) return NULL;
        if (!($emaW26Period = $this->ema($array, 26))) return NULL;
                
        $lengthEmaW26Period = sizeof($emaW26Period);
        $emaW12Period = array_slice($emaW12Period, -1*$lengthEmaW26Period); // it takes last $lengthEmaW26Periof of $emaW12Period
        
        if (!($result = $this->arraySubstract($emaW12Period, $emaW26Period))) return NULL;

        return $result;
    }
    
    private function signalLine($macdLine)
    {
        if (!($emaW9Period = $this->ema($macdLine, 9))) return NULL;
        
        return $emaW9Period;
    }

    private function arraySubstract($array1, $array2)
    {
        $length1 = sizeof($array1);
        $length2 = sizeof($array2);
        if ($length1 != $length2) return NULL;

        $result = array();

        for ($i = 0; $i < $length1; ++$i) {
            array_push($result, $array1[$i] - $array2[$i]);
        }
        
        return $result;
    }

    private function macdHistogram($macdLine, $signalLine)
    {
        if (!($macdHistogram = $this->arraySubstract($macdLine, $signalLine))) return NULL;

        return $macdHistogram;
    }
}


?>
