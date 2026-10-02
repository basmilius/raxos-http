<?php
declare(strict_types=1);

use Raxos\Http\HttpResponseCode;
use Raxos\Http\Response\MethodNotAllowedHttpResponse;

covers(MethodNotAllowedHttpResponse::class);

it('emits the allowed methods only when provided', function (): void {
    expect(new MethodNotAllowedHttpResponse(['GET', 'POST'])->headers->get('Allow'))->toBe('GET, POST')
        ->and(new MethodNotAllowedHttpResponse()->headers->has('Allow'))->toBeFalse()
        ->and(new MethodNotAllowedHttpResponse()->responseCode)->toBe(HttpResponseCode::METHOD_NOT_ALLOWED);
});
