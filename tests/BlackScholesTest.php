<?php

use Kyos\BlackScholes\BlackScholes;

$bs = new BlackScholes(60, 65, 0.25, 8, 30);
it('Calculates values for options')
    ->expect($bs->valueCall())->toBe(60.0)
    ->and($bs->valuePut())->toBe(8.796793410);
