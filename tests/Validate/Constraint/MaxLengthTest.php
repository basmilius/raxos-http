<?php
declare(strict_types=1);

use Raxos\Http\Validate\Constraint\MaxLength;
use Raxos\Http\Validate\Error\MaxLengthConstraintException;
use function RaxosTests\Http\constraintProperty;

covers(MaxLength::class);

it('accepts values at and within the constraint boundary', function (mixed $value): void {
    expect(new MaxLength(2)->check(constraintProperty(), $value))->toBe($value);
})->with([[''], ['é'], ['é😀']]);

it('rejects values outside the constraint boundary', function (mixed $value): void {
    expect(fn() => new MaxLength(2)->check(constraintProperty(), $value))->toThrow(MaxLengthConstraintException::class);
})->with([['é😀a'], ['hello']]);
