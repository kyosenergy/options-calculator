<?php

namespace Kyos\OptionsCalculator;

class NormalDistribution
{
    /**
     * Cumulative distribution function (CDF) of the standard normal distribution
     *
     * @param float $x
     *
     * @return float
     */
    public static function cdf(float $x): float
    {
        $a1 = 0.319381530;
        $a2 = -0.356563782;
        $a3 = 1.781477937;
        $a4 = -1.821255978;
        $a5 = 1.330274429;

        $L = abs($x);
        $k = 1 / (1 + 0.2316419 * $L);
        $p = 1 - 1 / pow(2 * M_PI, 0.5) * exp(-pow($L, 2) / 2) * ($a1 * $k + $a2 * pow($k, 2) + $a3 * pow($k, 3) + $a4 * pow($k, 4) + $a5 * pow($k, 5));

        return $x >= 0 ? $p : 1 - $p;
    }

    /**
     * Probability distribution function (PDF) of the standard normal distribution
     *
     * @param float $x
     *
     * @return float
     */
    public static function pdf(float $x): float
    {
        return exp(-pow($x, 2) / 2) / sqrt(2 * M_PI);
    }
}
