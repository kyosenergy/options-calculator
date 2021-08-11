<?php

namespace Kyos\BlackScholes;

class BlackScholes
{
    private float $underlyingPrice;
    private float $strikePrice;
    private float $timeToMaturity;
    private float $riskFreeInterestRate;
    private float $volatility;

    public function __construct(
        float $underlyingPrice,
        float $strikePrice,
        float $timeToMaturity,
        float $riskFreeInterestRate,
        float $volatility
    ) {
        $this->underlyingPrice = $underlyingPrice;
        $this->strikePrice = $strikePrice;
        $this->timeToMaturity = $timeToMaturity;
        $this->riskFreeInterestRate = $riskFreeInterestRate;
        $this->volatility = $volatility;
    }

    /**
     * Cumulative normal distribution
     */
    private function cnd(float $x): float
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

    private function bs(): array
    {
        $d1 = (log($this->underlyingPrice / $this->strikePrice) + ($this->riskFreeInterestRate + pow($this->volatility, 2) / 2) * $this->timeToMaturity) / ($this->volatility * pow($this->timeToMaturity, 0.5));
        $d2 = $d1 - $this->volatility * pow($this->timeToMaturity, 0.5);

        return [$d1, $d2];
    }

    public function valueCall(): float
    {
        [$d1, $d2] = $this->bs();
        $call = $this->underlyingPrice * $this->cnd($d1) - $this->strikePrice * exp(-$this->riskFreeInterestRate * $this->timeToMaturity) * $this->cnd($d2);

        return round($call, 8);
    }

    public function valuePut(): float
    {
        [$d1, $d2] = $this->bs();
        $put = $this->strikePrice * exp(-$this->riskFreeInterestRate * $this->timeToMaturity) * $this->cnd(-$d2) - $this->underlyingPrice * $this->cnd(-$d1);

        return round($put, 8);
    }

    public function volatilityCall(): float
    {
        return 0.1;
    }

    public function volatilityPut(): float
    {
        return 0.1;
    }
}
