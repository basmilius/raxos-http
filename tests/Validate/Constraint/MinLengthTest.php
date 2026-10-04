<?php
declare(strict_types=1);

use Raxos\Http\Validate\Constraint\MinLength;
use Raxos\Http\Validate\Error\MinLengthConstraintException;
use function RaxosTests\Http\constraintProperty;

covers(MinLength::class);

it('accepts values at and within the constraint boundary', function (mixed $value): void {
    expect(new MinLength(2)->check(constraintProperty(), $value))->toBe($value);
})->with([['é😀'], ['hello']]);

it('rejects values outside the constraint boundary', function (mixed $value): void {
    expect(fn() => new MinLength(2)->check(constraintProperty(), $value))->toThrow(MinLengthConstraintException::class);
})->with([[''], ['é']]);
