<?php
declare(strict_types=1);

use Raxos\Http\Structure\HttpParametersMap;

covers(HttpParametersMap::class);

it('preserves present falsey values and uses defaults only for absent parameters', function (): void {
    $parameters = new class(['zero' => '0', 'false' => false, 'null' => null]) extends HttpParametersMap {};
    expect($parameters->get('zero', 1))->toBe('0')->and($parameters->get('false', true))->toBeFalse()
        ->and($parameters->get('null', 'default'))->toBe('default')->and($parameters->get('missing', 'default'))->toBe('default');
});
