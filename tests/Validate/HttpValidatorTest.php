<?php
declare(strict_types=1);

use Raxos\Http\Validate\Error\ValidationNotOkException;
use Raxos\Http\Validate\HttpValidator;
use RaxosTests\Http\ScalarInput;

covers(HttpValidator::class);

it('returns the validated class and propagates detailed failures', function (): void {
    expect(HttpValidator::validate(ScalarInput::class, ['value' => '12'])->value)->toBe(12);
    expect(fn() => HttpValidator::validate(ScalarInput::class, []))->toThrow(ValidationNotOkException::class);
});
