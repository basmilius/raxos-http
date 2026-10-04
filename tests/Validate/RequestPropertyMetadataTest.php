<?php
declare(strict_types=1);

use Raxos\Http\Validate\RequestPropertyMetadata;
use RaxosTests\Http\MetadataInput;
use RaxosTests\Http\MetadataProbeConstraint;

covers(RequestPropertyMetadata::class);

it('ignores public properties outside the request contract', function (): void {
    expect(RequestPropertyMetadata::from(new ReflectionProperty(MetadataInput::class, 'ignored')))->toBeNull();
});

it('retains the input alias and defers constructing custom constraints', function (): void {
    MetadataProbeConstraint::$instances = 0;
    $metadata = RequestPropertyMetadata::from(new ReflectionProperty(MetadataInput::class, 'name'));

    expect($metadata->name())->toBe('display_name')
        ->and($metadata->optional())->toBeTrue()
        ->and($metadata->types)->toBe(['string'])
        ->and($metadata->default)->toBe('fallback')
        ->and($metadata->constraints)->toHaveCount(1)
        ->and(MetadataProbeConstraint::$instances)->toBe(0);

    expect($metadata->constraints[0]->newInstance())->toBeInstanceOf(MetadataProbeConstraint::class)
        ->and(MetadataProbeConstraint::$instances)->toBe(1);
});

it('distinguishes null and zero promoted defaults from a missing non-nullable default', function (string $name, bool $hasDefault, mixed $default, bool $canDefault): void {
    $metadata = RequestPropertyMetadata::from(new ReflectionProperty(MetadataInput::class, $name), new ReflectionClass(MetadataInput::class));

    expect($metadata->name())->toBe($name)
        ->and($metadata->hasDefault)->toBe($hasDefault)
        ->and($metadata->default)->toBe($default)
        ->and($metadata->canDefault())->toBe($canDefault);
})->with([
    'zero remains a usable promoted default' => ['count', true, 0, true],
    'null is a usable nullable promoted default' => ['note', true, null, true],
    'missing non-nullable input cannot default' => ['missing', false, null, false]
]);
