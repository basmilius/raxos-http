<?php
declare(strict_types=1);

use Raxos\Http\Validate\Error\InvalidValueTransformerException;
use Raxos\Http\Validate\Transformer\FloatTransformer;

covers(FloatTransformer::class);

it('transforms each accepted value into its native type', function (mixed $input, mixed $expected): void {
    expect(new FloatTransformer()->transform($input))->toBe($expected);
})->with([[0, 0.0], ['1.5', 1.5], ['-2', -2.0], ['1e3', 1000.0]]);

it('rejects malformed input through the validation exception contract', function (mixed $input): void {
    expect(fn() => new FloatTransformer()->transform($input))->toThrow(InvalidValueTransformerException::class);
})->with([[null], [true], ['bad'], [[]]]);
