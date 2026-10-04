<?php
declare(strict_types=1);

use Raxos\Http\Validate\Constraint\Time;
use Raxos\Http\Validate\Error\TimeConstraintException;
use function RaxosTests\Http\constraintProperty;

covers(Time::class);

it('converts accepted strings into the corresponding date type', function (string $value, string $expected): void {
    expect(new Time()->check(constraintProperty(), $value)->format('H:i:s'))->toBe($expected);
})->with([['13:25', '13:25:00'], ['23:59:59', '23:59:59']]);

it('wraps malformed or non-string date values', function (mixed $value): void {
    expect(fn() => new Time()->check(constraintProperty(), $value))->toThrow(TimeConstraintException::class);
})->with([['bad'], ['25:99:99'], [12], [[]]]);
