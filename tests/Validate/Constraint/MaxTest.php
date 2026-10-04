<?php
declare(strict_types=1);

use Raxos\Http\Validate\Constraint\Max;
use Raxos\Http\Validate\Error\MaxConstraintException;
use function RaxosTests\Http\constraintProperty;

covers(Max::class);

it('accepts values at and within the constraint boundary', function (mixed $value): void {
    expect(new Max(3)->check(constraintProperty(), $value))->toBe($value);
})->with([[3], [2], [2.5]]);

it('rejects values outside the constraint boundary', function (mixed $value): void {
    expect(fn() => new Max(3)->check(constraintProperty(), $value))->toThrow(MaxConstraintException::class);
})->with([[4], [3.5]]);
