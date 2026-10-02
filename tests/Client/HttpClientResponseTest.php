<?php
declare(strict_types=1);

use GuzzleHttp\Psr7\Response;
use Raxos\Http\Client\{HttpClient, HttpClientRequest, HttpClientResponse};
use Raxos\Http\HttpResponseCode;

covers(HttpClientResponse::class);

it('classifies status boundaries and exposes the native response metadata', function (int $status, bool $success, bool $clientError, bool $serverError): void {
    $client = new HttpClient();
    $native = new Response($status, ['X-Values' => ['first', 'second']], 'body', '1.1');
    $response = new HttpClientResponse($client, new HttpClientRequest($client), $native);
    expect($response->success())->toBe($success)->and($response->failed())->toBe(!$success)
        ->and($response->clientError())->toBe($clientError)->and($response->serverError())->toBe($serverError)
        ->and($response->body())->toBe('body')->and($response->stream())->toBe($native->getBody())
        ->and($response->responseCode)->toBe(HttpResponseCode::from($status))->and($response->protocolVersion)->toBe('1.1')
        ->and($response->header('X-Values'))->toBe('first, second')->and($response->header('X-Values', false))->toBe(['first', 'second'])
        ->and($response->headers())->toBe(['X-Values' => 'first'])->and($response->headers(false))->toBe(['X-Values' => ['first', 'second']])
        ->and($response->hasHeader('x-values'))->toBeTrue()->and($response->hasHeader('missing'))->toBeFalse()
        ->and($response->__debugInfo()['headers'])->toBe($native->getHeaders());
})->with([[100, false, false, false], [200, true, false, false], [226, true, false, false], [300, false, false, false], [400, false, true, false], [451, false, true, false], [500, false, false, true], [511, false, false, true]]);

it('decodes valid JSON values without changing their type', function (string $json, mixed $expected): void {
    $client = new HttpClient();
    $response = new HttpClientResponse($client, new HttpClientRequest($client), new Response(200, [], $json));
    expect($response->json())->toBe($expected);
})->with([['{"value":1}', ['value' => 1]], ['[1,2]', [1, 2]], ['null', null], ['false', false], ['0', 0], ['"text"', 'text']]);

it('supports object decoding and rejects malformed JSON', function (): void {
    $client = new HttpClient();
    $response = new HttpClientResponse($client, new HttpClientRequest($client), new Response(200, [], '{"value":1}'));
    expect($response->json(false))->toBeInstanceOf(stdClass::class)->and($response->json(false)->value)->toBe(1);
    $response = new HttpClientResponse($client, new HttpClientRequest($client), new Response(200, [], 'invalid'));
    expect(fn () => $response->json())->toThrow(JsonException::class);
});
