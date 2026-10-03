<?php
declare(strict_types=1);

use Raxos\Collection\Map;
use Raxos\Http\Client\{HttpClient, HttpClientRequest};
use Raxos\Http\{HttpMethod, HttpRequest};
use RaxosTests\Http\JsonRequest;

covers(HttpRequest::class);

it('decodes JSON collections and null exactly once', function (?string $body, ?array $expected): void {
    $base = HttpRequest::create(method: HttpMethod::POST);
    $request = new JsonRequest($base->cookies, $base->files, $base->headers, $base->post, $base->query, $base->server, $base->method, '/', '/', new Map(['fixture_body' => $body]));
    expect($request->json())->toBe($expected);
    $request->parameters->set('fixture_body', '{"changed":true}');
    expect($request->json())->toBe($expected);
})->with([['{"name":"Raxos"}', ['name' => 'Raxos']], ['[1,2]', [1, 2]], ['null', null], [null, null]]);

it('rejects malformed and scalar request JSON with a client error', function (string $body): void {
    $base = HttpRequest::create(method: HttpMethod::POST);
    $request = new JsonRequest($base->cookies, $base->files, $base->headers, $base->post, $base->query, $base->server, $base->method, '/', '/', new Map(['fixture_body' => $body]));
    try {
        $request->json();
        test()->fail('Expected invalid request body.');
    } catch (RuntimeException $error) {
        expect($error->getCode())->toBe(400);
    }
})->with(['42', 'true', '"hello"', '{invalid']);

it('overwrites scalar HTTP options while retaining independent options', function (): void {
    $options = null;
    $handler = static function (Psr\Http\Message\RequestInterface $request, array $received) use (&$options) {
        $options = $received;
        return GuzzleHttp\Promise\Create::promiseFor(new GuzzleHttp\Psr7\Response(200, [], 'ok'));
    };
    $request = new HttpClientRequest(new HttpClient(client: new GuzzleHttp\Client(['handler' => $handler])));
    $response = $request->options(['timeout' => 1.0, 'verify' => true])->options(['timeout' => 2.0])->get('https://example.org');
    expect($options['timeout'])->toBe(2.0);
    expect($options['verify'])->toBeTrue();
});
