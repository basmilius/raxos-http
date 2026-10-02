<?php
declare(strict_types=1);

use Raxos\Http\HttpResponseCode;
use Raxos\Http\Response\RedirectHttpResponse;
use Raxos\Http\Structure\HttpHeadersMap;

covers(RedirectHttpResponse::class);

it('sets the destination and supports explicit status and headers', function (): void {
    $headers = new HttpHeadersMap(['Location' => 'old', 'X-Unit' => 'value']);
    $response = new RedirectHttpResponse('/new?x=1', $headers, HttpResponseCode::TEMPORARY_REDIRECT);
    expect($response->destination)->toBe('/new?x=1')->and($response->responseCode)->toBe(HttpResponseCode::TEMPORARY_REDIRECT)
        ->and($response->headers)->toBe($headers)->and($headers->get('Location'))->toBe('/new?x=1')->and($headers->get('X-Unit'))->toBe('value');
    expect(new RedirectHttpResponse('/')->responseCode)->toBe(HttpResponseCode::FOUND);
});
