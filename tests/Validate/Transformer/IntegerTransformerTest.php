<?php
declare(strict_types=1);

use Raxos\Http\Validate\Error\InvalidValueTransformerException;
use Raxos\Http\Validate\Transformer\IntegerTransformer;

covers(IntegerTransformer::class);

it('transforms each accepted value into its native type', function (mixed $input, mixed $expected): void {
    expect(new IntegerTransformer()->transform($input))->toBe($expected);
})->with([[0, 0], ['12', 12], ['-3', -3], [4.0, 4]]);

it('rejects malformed input through the validation exception contract', function (mixed $input): void {
    expect(fn() => new IntegerTransformer()->transform($input))->toThrow(InvalidValueTransformerException::class);
})->with([[null], [true], ['bad'], [[]]]);
