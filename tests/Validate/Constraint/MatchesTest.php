<?php
declare(strict_types=1);

use Raxos\Http\Validate\Constraint\Matches;
use Raxos\Http\Validate\Error\MatchesConstraintException;
use function RaxosTests\Http\constraintProperty;

covers(Matches::class);

it('accepts values at and within the constraint boundary', function (mixed $value): void {
    expect(new Matches('/^[a-z]{2}$/D')->check(constraintProperty(), $value))->toBe($value);
})->with([['ab'], ['xy']]);

it('rejects values outside the constraint boundary', function (mixed $value): void {
    expect(fn() => new Matches('/^[a-z]{2}$/D')->check(constraintProperty(), $value))->toThrow(MatchesConstraintException::class);
})->with([['a'], ['ab
'], [2], [[]]]);
