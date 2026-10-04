<?php
declare(strict_types=1);

use Raxos\Http\Validate\Error\InvalidValueTransformerException;
use Raxos\Http\Validate\Transformer\BooleanTransformer;

covers(BooleanTransformer::class);

it('transforms each accepted value into its native type', function (mixed $input, mixed $expected): void {
    expect(new BooleanTransformer()->transform($input))->toBe($expected);
})->with([[true, true], [1, true], ['1', true], ['yes', true], ['on', true], ['true', true], [false, false], [0, false], ['0', false], ['false', false], ['no', false], ['off', false]]);

it('rejects malformed input through the validation exception contract', function (mixed $input): void {
    expect(fn() => new BooleanTransformer()->transform($input))->toThrow(InvalidValueTransformerException::class);
})->with([[null], [''], ['TRUE'], [2], [[]], [new stdClass()]]);
