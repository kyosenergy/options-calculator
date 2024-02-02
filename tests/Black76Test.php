<?php

use Kyos\OptionsCalculator\Black76;

// Prices & Greeks
it('calculates greeks for CALL options', function () {
    $this->expect((new Black76())->getValues(Black76::CALL, 10.5, 12, 30 / 365.25, 0.60))->toBe([
        'value' => 0.24105017253515562,
        'delta' => 0.24471766261590738,
        'gamma' => 0.17393760473172346,
        'vega' => 0.009450490803288298,
        'theta' => -3.4493812641756993,
        'rho' => -0.0001979878213841114,
    ]);
});

it('calculates greeks for PUT options', function () {
    $this->expect((new Black76())->getValues(Black76::PUT, 10.5, 12, 30 / 365.25, 0.60))->toBe([
        'value' => 1.7398186455107651,
        'delta' => -0.7544613193678329,
        'gamma' => 0.17393760473172346,
        'vega' => 0.009450490803288298,
        'theta' => -3.4343935794459433,
        'rho' => -0.0014290091544236264,
    ]);
});

// Implied volatilities
it('calculates implied volatility for CALL options when we know the value', function () {
    $this->expect((new Black76())->getImpliedVolatility(Black76::CALL, 10.5, 12, 30 / 365.25, 0.24105017253515562))->toEqualWithDelta(0.60, 0.00000000000001);
});

it('calculates implied volatility for PUT options when we know the value', function () {
    $this->expect((new Black76())->getImpliedVolatility(Black76::PUT, 10.5, 12, 30 / 365.25, 1.7398186455107651))->toEqualWithDelta(0.60, 0.00000000000001);
});

it('returns an implied volatility of 0.00001 when the true value is below this lower bound', function () {
    $this->expect((new Black76())->getImpliedVolatility(Black76::PUT, 10.5, 12, 30 / 365.25, 1))->toEqualWithDelta(0.00001, 0.00000000000001);
});

it('returns an implied volatility of 5 when the true value is above this upper bound', function () {
    $this->expect((new Black76())->getImpliedVolatility(Black76::PUT, 10.5, 12, 30 / 365.25, 10))->toEqualWithDelta(5, 0.00000000000001);
});

// Exceptions
it('throws exception if we try to derive implied volatility using newton rapshon', function () {
    (new Black76())->getImpliedVolatility(Black76::PUT, 10.5, 12, 30 / 365.25, 0.240, Black76::METHOD_NEWTON_RAPHSON);
})->throws('Not implemented');

it('throws exception if we try to derive implied volatility using wrong method', function () {
    (new Black76())->getImpliedVolatility(Black76::PUT, 10.5, 12, 30 / 365.25, 0.240, 99);
})->throws('Wrong method 99 or not supported');
