<?php

use Kyos\OptionsCalculator\Black76;

// Prices & Greeks
it('calculates greeks for CALL options', function () {
    $this->expect((new Black76())->getValues(Black76::CALL, 10.5, 12, 0.082, 0.60))->toBe([
        'value' => 0.2405826183655344,
        'delta' => 0.24449431791580983,
        'gamma' => 0.17399585222314845,
        'vega' => 0.009438057012140243,
        'theta' => -3.450541861184725,
        'rho' => -0.00019727774705973822,
    ]);
});

it('calculates greeks for PUT options', function () {
    $this->expect((new Black76())->getValues(Black76::PUT, 10.5, 12, 0.082, 0.60))->toBe([
        'value' => 1.7393531225277215,
        'delta' => -0.7546860181923143,
        'gamma' => 0.17399585222314845,
        'vega' => 0.009438057012140243,
        'theta' => -3.435554156143103,
        'rho' => -0.0014262695604727318,
    ]);
});

// Implied volatilities
it('calculates implied volatility for CALL options when we know the value', function () {
    $this->expect((new Black76())->getImpliedVolatility(Black76::CALL, 10.5, 12, 0.082, 0.2405826183655344))->toBe(0.60);
});

it('calculates implied volatility for PUT options when we know the value', function () {
    $this->expect((new Black76())->getImpliedVolatility(Black76::PUT, 10.5, 12, 0.082, 1.7393531225277215))->toBe(0.60);
});

// Expections
it('throws expection if we try to derive implied volatility using newton rapshon', function () {
    (new Black76())->getImpliedVolatility(Black76::PUT, 10.5, 12, 0.082, 0.240, Black76::METHOD_NEWTON_RAPHSON);
})->throws('Not implemented');

it('throws expection if we try to derive implied volatility using wrong method', function () {
    (new Black76())->getImpliedVolatility(Black76::PUT, 10.5, 12, 0.082, 0.240, 99);
})->throws('Wrong method 99 or not supported');
