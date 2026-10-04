<?php
declare(strict_types=1);

use Raxos\Http\Validate\Constraint\Min;
use Raxos\Http\Validate\Error\MinConstraintException;
use function RaxosTests\Http\constraintProperty;

covers(Min::class);

it('accepts values at and within the constraint boundary', function (mixed $value): void {
    expect(new Min(3)->check(constraintProperty(), $value))->toBe($value);
})->with([[3], [4], [3.5]]);

it('rejects values outside the constraint boundary', function (mixed $value): void {
    expect(fn() => new Min(3)->check(constraintProperty(), $value))->toThrow(MinConstraintException::class);
})->with([[2], [-1]]);
