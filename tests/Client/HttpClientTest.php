<?php
declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Raxos\Http\Client\Error\BadCallException;
use Raxos\Http\Client\{HttpClient, HttpClientRequest};

covers(HttpClient::class);

it('configures base URI, timeout and nonthrowing HTTP status handling', function (): void {
    $http = new HttpClient('https://example.org/api/', 2.5);
    expect((string)$http->client->getConfig('base_uri'))->toBe('https://example.org/api/')
        ->and($http->client->getConfig('timeout'))->toBe(2.5)->and($http->client->getConfig('http_errors'))->toBeFalse();
});

it('uses an injected client and returns a fresh builder for each facade call', function (): void {
    $native = new Client(['handler' => new MockHandler([new Response(200, [], 'ok')])]);
    $http = new HttpClient(client: $native);
    expect($http->client)->toBe($native)->and($http->header('X-Unit', 'value'))->toBeInstanceOf(HttpClientRequest::class)
        ->and($http->timeout(1))->not->toBe($http->timeout(1))->and($http->get('https://example.org')->body())->toBe('ok');
    expect(fn () => $http->missingUnitMethod())->toThrow(BadCallException::class);
});
