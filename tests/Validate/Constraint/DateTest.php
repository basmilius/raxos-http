<?php
declare(strict_types=1);

use Raxos\Http\Validate\Constraint\Date;
use Raxos\Http\Validate\Error\DateConstraintException;
use function RaxosTests\Http\constraintProperty;

covers(Date::class);

it('converts accepted strings into the corresponding date type', function (string $value, string $expected): void {
    expect(new Date()->check(constraintProperty(), $value)->format('Y-m-d'))->toBe($expected);
})->with([['2024-02-29', '2024-02-29']]);

it('wraps malformed or non-string date values', function (mixed $value): void {
    expect(fn () => new Date()->check(constraintProperty(), $value))->toThrow(DateConstraintException::class);
})->with([['bad'], ['2024-99-99'], [12], [[]]]);

it('rejects dates that would roll over into another calendar day', function (string $value): void {
    expect(fn () => new Date()->check(constraintProperty(), $value))->toThrow(DateConstraintException::class);
})->with(['2023-02-29', '2024-02-30', '2024-04-31', '2024-00-01']);
