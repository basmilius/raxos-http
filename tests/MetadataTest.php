<?php
declare(strict_types=1);

use Raxos\Http\HttpRequest;
use Raxos\Http\Response\JsonHttpResponse;
use Raxos\Http\Structure\{HttpHeadersMap, HttpQueryMap, HttpServerMap};

it('normalizes constructor headers and preserves multiple values and zero', function (): void {
    $headers = new HttpHeadersMap(['Accept' => 'application/json', 'X-Items' => ['first', 'second'], 'Content-Length' => '0']);
    expect($headers->get('ACCEPT'))->toBe('application/json')
        ->and($headers->getAll('x-items'))->toBe(['first', 'second'])
        ->and($headers->get('content-length'))->toBe('0');
    $headers->add('X-Items', 'third');
    expect($headers->getAll('X-ITEMS'))->toBe(['first', 'second', 'third']);
    $headers->unset('AcCePt');
    expect($headers->has('accept'))->toBeFalse();
});

it('parses the URI query and preserves explicit query overrides', function (): void {
    $request = HttpRequest::create(uri: '/items?filter=a%20b&zero=0');
    expect($request->pathName)->toBe('/items')->and($request->query->toArray())->toBe(['filter' => 'a b', 'zero' => '0']);
    $explicit = HttpRequest::create(uri: '/items?filter=ignored', query: new HttpQueryMap(['filter' => 'explicit']));
    expect($explicit->query->get('filter'))->toBe('explicit');
});

it('handles missing client metadata and extracts the first forwarded IP', function (): void {
    $empty = HttpRequest::create(headers: new HttpHeadersMap(), server: new HttpServerMap());
    expect($empty->ip())->toBeNull()->and($empty->bearerToken())->toBeNull()
        ->and($empty->contentType())->toBeNull()->and($empty->languages())->toBe([])->and($empty->isSecure())->toBeFalse();
    $request = HttpRequest::create(headers: new HttpHeadersMap(['X-Forwarded-For' => '203.0.113.10, 198.51.100.1']), server: new HttpServerMap(['HTTPS' => 'on']));
    expect((string)$request->ip())->toBe('203.0.113.10')->and($request->isSecure())->toBeTrue();
});

it('parses bearer tokens without case sensitivity or empty-token acceptance', function (string $header, ?string $expected): void {
    expect(HttpRequest::create(headers: new HttpHeadersMap(['Authorization' => $header]))->bearerToken())->toBe($expected);
})->with([['Bearer abc.def', 'abc.def'], ['bearer abc.def', 'abc.def'], ['Basic secret', null], ['Bearer ', null], ['Bearer', null]]);

it('orders acceptable languages and trims their surrounding whitespace', function (): void {
    $request = HttpRequest::create(headers: new HttpHeadersMap(['Accept-Language' => 'nl-NL, en;q=0.8, de;q=0,fr;q=0.9', 'Content-Type' => 'application/json; charset=utf-8']));
    expect($request->languages())->toBe(['nl-NL', 'fr', 'en'])->and($request->language())->toBe('nl-NL')->and($request->contentType())->toBe('application/json');
});

it('sanitizes query parameters and applies falsey defaults', function (): void {
    $request = HttpRequest::create(query: new HttpQueryMap(['limit' => '10']));
    $request->addParameterFromQuery('limit', 'limit', static fn(string $value): int => (int)$value);
    $request->addParameterFromQuery('enabled', 'missing', defaultValue: false);
    expect($request->parameters->toArray())->toBe(['limit' => 10, 'enabled' => false]);
});

it('serializes JSON responses with Unicode and unescaped URLs', function (): void {
    $response = new JsonHttpResponse(['name' => 'é😀', 'url' => 'https://example.org']);
    ob_start();
    try {
        new ReflectionMethod($response, 'sendBody')->invoke($response);
        $body = ob_get_contents();
    } finally {
        ob_end_clean();
    }
    expect($body)->toBe('{"name":"é😀","url":"https://example.org"}')
        ->and($response->headers->get('content-type'))->toBe('application/json');
});
