<?php
declare(strict_types=1);

use Raxos\Http\Validate\Error\{UnvalidatableException, ValidationNotOkException};
use Raxos\Http\Validate\HttpClassValidator;
use RaxosTests\Http\{NullableInput, OptionalWithoutDefaultInput, ScalarInput, UnitInput, UnitState};

covers(HttpClassValidator::class);

it('validates aliases, typed fields, enums, nested inputs and promoted defaults', function (): void {
    $validator = new HttpClassValidator(UnitInput::class);
    $validator->validate(['email_address' => 'unit@example.org', 'count' => '3', 'price' => '1.25', 'enabled' => 'yes', 'state' => 'active', 'address' => ['city' => 'Amsterdam'], 'ignored' => 'untrusted']);
    $input = $validator->get();
    expect($input->email)->toBe('unit@example.org')->and($input->count)->toBe(3)->and($input->price)->toBe(1.25)
        ->and($input->enabled)->toBeTrue()->and($input->state)->toBe(UnitState::Active)->and($input->address->city)->toBe('Amsterdam')
        ->and($input->note)->toBeNull()->and($input->default)->toBe('trusted-default')->and($input->ignored)->toBe('ignored');
});

it('collects all validation errors under their input aliases', function (): void {
    $validator = new HttpClassValidator(UnitInput::class);
    $validator->validate(['email_address' => 'invalid', 'count' => '-1', 'price' => 'invalid', 'enabled' => [], 'state' => 'missing', 'address' => ['city' => 'x']]);
    try {
        $validator->get();
        test()->fail('Invalid fields must fail together.');
    } catch (ValidationNotOkException $error) {
        expect(array_keys($error->errors))->toBe(['email_address', 'count', 'price', 'enabled', 'state', 'address']);
    }
});

it('resets previous errors and values before validating the next input', function (): void {
    $validator = new HttpClassValidator(ScalarInput::class);
    $validator->validate(['value' => 'bad']);
    expect(fn () => $validator->get())->toThrow(ValidationNotOkException::class);
    $validator->validate(['value' => '0']);
    expect($validator->get()->value)->toBe(0);
    $validator->validate(['value' => '9']);
    expect($validator->get()->value)->toBe(9);
});

it('treats null and whitespace as missing and preserves optional nullable values', function (): void {
    $validator = new HttpClassValidator(NullableInput::class);
    $validator->validate(['value' => '  ', 'state' => null]);
    expect($validator->get()->value)->toBeNull()->and($validator->get()->state)->toBeNull();
    $validator = new HttpClassValidator(OptionalWithoutDefaultInput::class);
    $validator->validate([]);
    expect(fn () => $validator->get())->toThrow(ValidationNotOkException::class);
});

it('rejects classes outside the request input contract', function (): void {
    expect(fn () => new HttpClassValidator(stdClass::class))->toThrow(UnvalidatableException::class);
});

it('returns validation errors for malformed nested and enum input types', function (string $field, mixed $value): void {
    $data = ['email_address' => 'unit@example.org', 'count' => 3, 'price' => 1.25, 'enabled' => true, 'state' => 'active', 'address' => ['city' => 'Amsterdam']];
    $data[$field] = $value;
    $validator = new HttpClassValidator(UnitInput::class);
    $validator->validate($data);
    expect(fn () => $validator->get())->toThrow(ValidationNotOkException::class);
})->with([['address', 'not an object'], ['address', 42], ['state', []], ['state', true]]);
