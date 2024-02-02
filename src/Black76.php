<?php

namespace Kyos\OptionsCalculator;

use Exception;
use RuntimeException;

class Black76
{
    public const CALL = 'C';
    public const PUT = 'P';

    public const METHOD_BISECTION = 1;
    public const METHOD_NEWTON_RAPHSON = 2;

    private float $interestRate;

    /**
     * @param float $interestRate Annual risk-free interest rate, defaults to 0.01 (1%)
     */
    public function __construct(float $interestRate = 0.01)
    {
        $this->interestRate = $interestRate;
    }

    /**
     * These are internal values for the Black'76 formula
     *
     * @param float  $underlyingPrice Futures price
     * @param float  $strikePrice     Strike price
     * @param float  $timeToMaturity  Time to expiry
     * @param float  $volatility      Volatility
     *
     * @return float[]
     */
    private function d1d2(float $underlyingPrice, float $strikePrice, float $timeToMaturity, float $volatility): array
    {
        $d1 = (log($underlyingPrice / $strikePrice) + (pow($volatility, 2) / 2) * $timeToMaturity) / ($volatility * sqrt($timeToMaturity));
        $d2 = $d1 - $volatility * sqrt($timeToMaturity);

        return [$d1, $d2];
    }

    /**
     * Calculates the option price and the greeks:
     *   delta (Δ), gamma (Γ), vega (v), theta (Θ), rho (ρ)
     * using the Black'76 model.
     *
     * @param string $type            C (CALL) or P (PUT)
     * @param float  $underlyingPrice Futures price
     * @param float  $strikePrice     Strike price
     * @param float  $timeToMaturity  Time to expiry
     * @param float  $volatility      Volatility
     *
     * @return float[]
     */
    public function getValues(
        string $type,
        float $underlyingPrice,
        float $strikePrice,
        float $timeToMaturity,
        float $volatility
    ): array {
        $discountFactor = exp(-$this->interestRate * $timeToMaturity);

        [$d1, $d2] = $this->d1d2($underlyingPrice, $strikePrice, $timeToMaturity, $volatility);
        $sign = [self::CALL => 1, self::PUT => -1][$type];

        $nd1 = NormalDistribution::cdf($d1 * $sign);
        $nd2 = NormalDistribution::cdf($d2 * $sign);
        $normpdf = NormalDistribution::pdf($d1);
        $sqrtTimeToMaturity = sqrt($timeToMaturity);

        $value = $sign * $discountFactor * ($underlyingPrice * $nd1 - $strikePrice * $nd2);

        $delta = $sign * $discountFactor * $nd1;
        $gamma = $discountFactor * $normpdf / ($volatility * $underlyingPrice * $sqrtTimeToMaturity);
        $vega = 0.01 * $underlyingPrice * $discountFactor * $normpdf * $sqrtTimeToMaturity;
        $theta = (-$underlyingPrice * $discountFactor * $normpdf * $volatility / (2 * $sqrtTimeToMaturity)) +
            $sign * $this->interestRate * $discountFactor * ($underlyingPrice * $nd1 - $strikePrice * $nd2);
        $rho = -0.01 * $timeToMaturity * $value;

        return [
            'value' => $value,
            'delta' => $delta,
            'gamma' => $gamma,
            'vega' => $vega,
            'theta' => $theta,
            'rho' => $rho,
        ];
    }

    /**
     * Extract implied volatility (σ) using the Bisection method
     *
     * @param string $type            C (CALL) or P (PUT)
     * @param float  $underlyingPrice Futures price
     * @param float  $strikePrice     Strike price
     * @param float  $timeToMaturity  Time to expiry
     * @param float  $marketPrice     Option price
     *
     * @return float
     *
     * @throws RuntimeException
     */
    private function impliedVolaUsingBisection(
        string $type,
        float $underlyingPrice,
        float $strikePrice,
        float $timeToMaturity,
        float $marketPrice
    ): float {
        $epsilon = 0.0001;
        $i = 0;
        $maxIterations = 100;

        $volMin = 0.00001;
        $volMax = 5;
        $volGuess = $volMin;

        $valueMin = $this->getValues($type, $underlyingPrice, $strikePrice, $timeToMaturity, $volMin)['value'];
        $valueMax = $this->getValues($type, $underlyingPrice, $strikePrice, $timeToMaturity, $volMax)['value'];

        if ($marketPrice < $valueMin || $marketPrice > $valueMax) {
            throw new RuntimeException("Implied volatility could not be found in the range {$volMin} - {$volMax}.");
        }

        while (++$i < $maxIterations && ($volMax - $volMin > $epsilon || $valueMin != $valueMax)) {
            $valueMin = $this->getValues($type, $underlyingPrice, $strikePrice, $timeToMaturity, $volMin)['value'];
            $valueMax = $this->getValues($type, $underlyingPrice, $strikePrice, $timeToMaturity, $volMax)['value'];

            $volGuess = $volMin + ($marketPrice - $valueMin) * ($volMax - $volMin) / ($valueMax - $valueMin);
            $valueGuess = $this->getValues($type, $underlyingPrice, $strikePrice, $timeToMaturity, $volGuess)['value'];

            if ($valueGuess < $marketPrice) {
                $volMin = $volGuess;
            } else {
                $volMax = $volGuess;
            }
        }

        return $volGuess;
    }

    /**
     * Extract implied volatility
     *
     * @param string $type             C (CALL) or P (PUT)
     * @param float  $underlyingPrice  Futures price (F)
     * @param float  $strikePrice      Strike price (X)
     * @param float  $timeToMaturity   Time to expiry (T)
     * @param float  $marketPrice      Option price
     * @param int    $method           Method to extract implied volatility (defaults to Bisection method)
     *
     * @return float
     * @throws Exception
     */
    public function getImpliedVolatility(
        string $type,
        float $underlyingPrice,
        float $strikePrice,
        float $timeToMaturity,
        float $marketPrice,
        int $method = self::METHOD_BISECTION
    ): float {
        switch ($method) {
            case self::METHOD_BISECTION:
                return $this->impliedVolaUsingBisection($type, $underlyingPrice, $strikePrice, $timeToMaturity, $marketPrice);
            case self::METHOD_NEWTON_RAPHSON:
                throw new Exception('Not implemented');
            default:
                throw new Exception("Wrong method {$method} or not supported");
        }
    }
}
