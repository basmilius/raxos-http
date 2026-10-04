<?php
declare(strict_types=1);

use Raxos\Http\Validate\Constraint\Email;
use Raxos\Http\Validate\Error\EmailConstraintException;
use function RaxosTests\Http\constraintProperty;

covers(Email::class);

it('accepts values at and within the constraint boundary', function (mixed $value): void {
    expect(new Email()->check(constraintProperty(), $value))->toBe($value);
})->with([['unit@example.org'], ['user+tag@example.org']]);

it('rejects values outside the constraint boundary', function (mixed $value): void {
    expect(fn() => new Email()->check(constraintProperty(), $value))->toThrow(EmailConstraintException::class);
})->with([['invalid'], ['unit@'], ['a b@example.org']]);
