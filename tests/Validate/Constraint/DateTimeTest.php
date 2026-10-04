<?php
declare(strict_types=1);

use Raxos\Http\Validate\Constraint\DateTime;
use Raxos\Http\Validate\Error\DateTimeConstraintException;
use function RaxosTests\Http\constraintProperty;

covers(DateTime::class);

it('converts accepted strings into the corresponding date type', function (string $value, string $expected): void {
    expect(new DateTime()->check(constraintProperty(), $value)->format('c'))->toBe($expected);
})->with([['2024-02-29T13:25:00+00:00', '2024-02-29T13:25:00+00:00']]);

it('wraps malformed or non-string date values', function (mixed $value): void {
    expect(fn() => new DateTime()->check(constraintProperty(), $value))->toThrow(DateTimeConstraintException::class);
})->with([['not a date'], [12], [[]]]);
