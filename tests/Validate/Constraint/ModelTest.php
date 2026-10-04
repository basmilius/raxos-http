<?php
declare(strict_types=1);

use Raxos\Http\Validate\Constraint\Model;
use Raxos\Http\Validate\Error\{InvalidValueTransformerException, ModelConstraintException};
use RaxosTests\Http\ModelConstraintFields;

covers(Model::class);

it('finds the model by primary key and reports missing models', function (): void {
    $property = new ReflectionProperty(ModelConstraintFields::class, 'model');
    expect(new Model()->check($property, 1)->id)->toBe(1);
    expect(fn() => new Model()->check($property, 2))->toThrow(ModelConstraintException::class);
    expect(fn() => new Model()->check(new ReflectionProperty(ModelConstraintFields::class, 'invalid'), 1))->toThrow(ModelConstraintException::class);
    try {
        new Model()->check($property, 'error');
        test()->fail('Database failures must be wrapped.');
    } catch (ModelConstraintException $error) {
        expect($error->getPrevious())->toBeInstanceOf(Raxos\Database\Orm\Error\NotFoundException::class);
    }
});

it('preserves integer or string keys and rejects other input types', function (): void {
    expect(new Model()->transform(0))->toBe(0)->and(new Model()->transform('0'))->toBe('0');
    foreach ([null, false, 1.5, [], new stdClass()] as $value) {
        expect(fn() => new Model()->transform($value))->toThrow(InvalidValueTransformerException::class);
    }
});
