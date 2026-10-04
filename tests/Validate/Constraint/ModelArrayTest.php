<?php
declare(strict_types=1);

use Raxos\Http\Validate\Constraint\ModelArray;
use Raxos\Http\Validate\Error\{InvalidValueTransformerException, ModelArrayConstraintException};
use RaxosTests\Http\ConstraintModel;
use function RaxosTests\Http\constraintProperty;

covers(ModelArray::class);

it('returns every resolved model and rejects incomplete selections', function (): void {
    $constraint = new ModelArray(ConstraintModel::class);
    expect(array_column($constraint->check(constraintProperty(), [1, 2]), 'id'))->toBe([1, 2])
        ->and($constraint->check(constraintProperty(), []))->toBe([]);
    expect(fn() => $constraint->check(constraintProperty(), [1, 0]))->toThrow(ModelArrayConstraintException::class);
    try {
        $constraint->check(constraintProperty(), ['error']);
        test()->fail('Database failures must be wrapped.');
    } catch (ModelArrayConstraintException $error) {
        expect($error->getPrevious())->toBeInstanceOf(Raxos\Database\Orm\Error\NotFoundException::class);
    }
});

it('validates every primary key without changing order or type', function (): void {
    $constraint = new ModelArray(ConstraintModel::class);
    expect($constraint->transform([0, 'id', 2]))->toBe([0, 'id', 2]);
    foreach ([null, 'id', [false], [1.5], [[]]] as $value) {
        expect(fn() => $constraint->transform($value))->toThrow(InvalidValueTransformerException::class);
    }
});
