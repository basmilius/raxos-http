<?php
declare(strict_types=1);

use Raxos\Http\HttpResponseCode;

covers(HttpResponseCode::class);

it('provides a reason phrase for every declared response status', function (): void {
    foreach (HttpResponseCode::cases() as $status) {
        expect($status->getMessage())->toBeString()->not->toBe('');
        expect(HttpResponseCode::from($status->value))->toBe($status);
    }
    expect(HttpResponseCode::OK->getMessage())->toBe('OK')->and(HttpResponseCode::NOT_FOUND->getMessage())->toBe('Not Found')
        ->and(HttpResponseCode::UNPROCESSABLE_ENTITY->getMessage())->toBe('Unprocessable Entity')
        ->and(HttpResponseCode::TOO_MANY_REQUESTS->getMessage())->toBe('Too Many Requests')
        ->and(HttpResponseCode::tryFrom(999))->toBeNull();
});
