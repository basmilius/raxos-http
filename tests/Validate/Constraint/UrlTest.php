<?php
declare(strict_types=1);

use Raxos\Http\Validate\Constraint\Url;
use Raxos\Http\Validate\Error\UrlConstraintException;
use function RaxosTests\Http\constraintProperty;

covers(Url::class);

it('accepts values at and within the constraint boundary', function (mixed $value): void {
    expect(new Url()->check(constraintProperty(), $value))->toBe($value);
})->with([['https://example.org/path?x=1'], ['http://localhost:8080']]);

it('rejects values outside the constraint boundary', function (mixed $value): void {
    expect(fn() => new Url()->check(constraintProperty(), $value))->toThrow(UrlConstraintException::class);
})->with([['invalid'], ['https://'], ['example.org']]);
