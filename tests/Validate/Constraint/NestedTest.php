<?php
declare(strict_types=1);

use Raxos\Http\Validate\Constraint\Nested;
use Raxos\Http\Validate\Error\{NestedConstraintException, ValidationNotOkException};
use function RaxosTests\Http\constraintProperty;

covers(Nested::class);

it('validates a nested request class and preserves its detailed errors', function (): void {
    expect(new Nested()->check(constraintProperty('address'), ['city' => 'Utrecht'])->city)->toBe('Utrecht');
    expect(fn() => new Nested()->check(constraintProperty('address'), ['city' => 'x']))->toThrow(ValidationNotOkException::class);
});

it('rejects scalar input and properties without a request class', function (): void {
    expect(fn() => new Nested()->check(constraintProperty('address'), 'invalid'))->toThrow(NestedConstraintException::class);
    expect(fn() => new Nested()->check(constraintProperty('invalidNested'), []))->toThrow(NestedConstraintException::class);
});
