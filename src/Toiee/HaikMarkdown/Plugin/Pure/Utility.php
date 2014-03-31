<?php
namespace Toiee\HaikMarkdown\Plugin\Pure;

class Utility {

    /**
     * Get Greatest common divisor (GCD) by Euclidean algorithm
     *
     * @param integer $n
     * @param integer $m
     * @return integer GCD of $n and $m
     */
    public static function getGCD($n, $m)
    {
        $n = (int)$n;
        $m = (int)$m;

        if ($n === 0)
        {
            return $m;
        }

        if ($n > $m)
        {
            list($m, $n) = array($n, $m);
        }

        return self::getGCD($m % $n, $n);
    }

}
