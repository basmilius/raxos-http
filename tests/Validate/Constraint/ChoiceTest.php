<?php
declare(strict_types=1);

use Raxos\Http\Validate\Constraint\Choice;
use Raxos\Http\Validate\Error\ChoiceConstraintException;
use function RaxosTests\Http\constraintProperty;

covers(Choice::class);

it('accepts values at and within the constraint boundary', function (mixed $value): void {
    expect(new Choice(['0', 1])->check(constraintProperty(), $value))->toBe($value);
})->with([['0'], [1]]);

it('rejects values outside the constraint boundary', function (mixed $value): void {
    expect(fn () => new Choice(['0', 1])->check(constraintProperty(), $value))->toThrow(ChoiceConstraintException::class);
})->with([[0], ['1'], [null], [[]]]);
