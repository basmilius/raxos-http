<?php
declare(strict_types=1);

use Raxos\Http\Validate\Constraint\NestedArray;
use Raxos\Http\Validate\Error\{NestedArrayConstraintException, ValidationNotOkException};
use RaxosTests\Http\AddressInput;
use function RaxosTests\Http\constraintProperty;

covers(NestedArray::class);

it('creates separate validated objects for every nested entry', function (): void {
    $constraint = new NestedArray(AddressInput::class);
    $result = $constraint->check(constraintProperty(), [['city' => 'Amsterdam'], ['city' => 'Utrecht']]);
    expect(array_column($result, 'city'))->toBe(['Amsterdam', 'Utrecht'])->and($result[0])->not->toBe($result[1]);
    expect($constraint->check(constraintProperty(), []))->toBe([]);
    expect(fn() => $constraint->check(constraintProperty(), [['city' => 'x']]))->toThrow(ValidationNotOkException::class);
});

it('rejects scalar collections, invalid item types and classes outside the input contract', function (): void {
    $constraint = new NestedArray(AddressInput::class);
    expect(fn() => $constraint->check(constraintProperty(), 'invalid'))->toThrow(NestedArrayConstraintException::class);
    expect(fn() => $constraint->check(constraintProperty(), ['invalid']))->toThrow(NestedArrayConstraintException::class);
    expect(fn() => new NestedArray(stdClass::class)->check(constraintProperty(), []))->toThrow(NestedArrayConstraintException::class);
});
