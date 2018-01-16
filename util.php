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
    
    public function rsi() 
    { // A function to make Rsi functions as if they are overloaded.
        switch (func_num_args()) {
        case 1:
            return $this->rsiConsecutive(func_get_arg(0));
        case 2:
            return $this->rsiOneAtEach(func_get_arg(0), func_get_arg(1));
        case 4:
            return $this->rsiSingle(func_get_arg(0), func_get_arg(1), func_get_arg(2), func_get_arg(3));
        default:
            return NULL;
        }
    }
    
    private function rsiConsecutive($array) 
    { // Relative Strength Index
        $periodLength = 14;
        $keys = array_keys($array);
        $length = sizeof($array);

        $averageGain = array();
        $averageLoss = array();
        $rsi = array();

        for ($i = 0; $i < $length - $periodLength; $i++) {
            $positiveSum = 0;
            $negativeSum = 0;
            for ($j = 0; $j < $periodLength; $j++) {
                
                $diff = $array[$keys[$i+$j+1]] - $array[$keys[$i+$j]];
                if($diff > 0)
                    $positiveSum += $diff;
                else
                    $negativeSum += $diff;
            }
            array_push($averageGain, $positiveSum/$periodLength);
            array_push($averageLoss, abs($negativeSum)/$periodLength);
            if ($negativeSum != 0)
                array_push($rsi, 100 - (100/(1+$positiveSum/abs($negativeSum))));
            else 
                array_push($rsi, 100);
        }
        return array($rsi, $averageGain, $averageLoss);
    }

    private function rsiOneAtEach($openArray, $closeArray)
    {
        $periodLength = 14;
        $keysOpen = array_keys($openArray);
        $keysClose = array_keys($closeArray);
        $lengthOpenArray = sizeof($openArray);
        $lengthCloseArray = sizeof($closeArray);
        
        if ($lengthOpenArray != $lengthCloseArray)
            return NULL;
        $length = $lengthOpenArray;

        $averageGain = array();
        $averageLoss = array();
        $rsi = array();

        for ($i = 0; $i < $length - $periodLength + 1; $i++) {
            $positiveSum = 0;
            $negativeSum = 0;
            for ($j = 0; $j < $periodLength; $j++) {
                
                $diff = $closeArray[$keysClose[$i+$j]] - $openArray[$keysOpen[$i+$j]];
                if($diff > 0)
                    $positiveSum += $diff;
                else
                    $negativeSum += $diff;
            }
            array_push($averageGain, $positiveSum/$periodLength);
            array_push($averageLoss, abs($negativeSum)/$periodLength);
            if ($negativeSum != 0)
                array_push($rsi, 100 - (100/(1+$positiveSum/abs($negativeSum))));
            else 
                array_push($rsi, 100);
        }
        return array($rsi, $averageGain, $averageLoss);
    } 

    private function rsiSingle($openVal, $closeVal, $lastAverageGain, $lastAverageLoss) 
    {
        $periodLength = 14;
        $diff = $closeVal - $openVal;
        if ($diff > 0) {
            $gain = $diff;
            $loss = 0;
        } else {
            $gain = 0;
            $loss = abs($diff);
        }

        $averageGain = ($lastAverageGain*($periodLength-1) + $gain) / $periodLength;
        $averageLoss = ($lastAverageLoss*($periodLength-1) + $loss) / $periodLength;
        if ($averageLoss != 0) {
            return array(100 - 100/(1+$averageGain/$averageLoss), $averageGain, $averageLoss);
        } else {
            return array(100, $averageGain, $averageLoss);
        }
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
