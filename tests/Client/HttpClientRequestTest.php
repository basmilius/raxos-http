<?php
declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Psr7\{Request, Response};
use Psr\Http\Message\RequestInterface;
use Raxos\Http\Client\Error\RequestFailedException;
use Raxos\Http\Client\{HttpClient, HttpClientRequest};

covers(HttpClientRequest::class);

it('builds the requested verb, headers and payload before dispatch', function (string $verb): void {
    $sent = null;
    $received = null;
    $handler = static function (RequestInterface $request, array $options) use (&$sent, &$received) {
        $sent = $request;
        $received = $options;
        return Create::promiseFor(new Response(200, [], 'ok'));
    };
    $builder = new HttpClientRequest(new HttpClient(client: new Client(['handler' => $handler])));
    expect($builder->bearerToken('unit-token'))->toBe($builder)->and($builder->header('X-Unit', 'first'))->toBe($builder)
        ->and($builder->header('X-Unit', 'second', false))->toBe($builder)->and($builder->timeout(2.5))->toBe($builder);
    $response = match ($verb) {
        'GET' => $builder->get('https://example.org/items', ['zero' => 0]),
        'POST' => $builder->post('https://example.org/items', ['name' => 'é']),
        'DELETE' => $builder->delete('https://example.org/items')
    };
    expect($sent->getMethod())->toBe($verb)->and($sent->getHeaderLine('Authorization'))->toBe('Bearer unit-token')
        ->and($sent->getHeader('X-Unit'))->toBe(['first', 'second'])->and($received['timeout'])->toBe(2.5)->and($response->body())->toBe('ok');
    if ($verb === 'GET') {
        expect($sent->getUri()->getQuery())->toBe('zero=0');
    }
    if ($verb === 'POST') {
        expect(json_decode((string)$sent->getBody(), true))->toBe(['name' => 'é']);
    }
})->with(['GET', 'POST', 'DELETE']);

it('forwards digest authentication and multipart options while replacing nested settings', function (): void {
    $received = null;
    $handler = static function (RequestInterface $request, array $options) use (&$received) {
        $received = $options;
        return Create::promiseFor(new Response(200));
    };
    $builder = new HttpClientRequest(new HttpClient(client: new Client(['handler' => $handler])));
    $builder->digestAuth('user', 'password')->multipart([['name' => 'field', 'contents' => 'value']])
        ->options(['timeout' => 1, 'custom' => ['a' => 1, 'b' => 2]])->options(['timeout' => 2, 'custom' => ['a' => 3]])->post('https://example.org');
    expect($received['auth'])->toBe(['user', 'password', 'digest'])->and($received['custom'])->toBe(['a' => 3, 'b' => 2])->and($received['timeout'])->toBe(2);
});

it('adds basic authorization using the supplied credentials', function (): void {
    $sent = null;
    $handler = static function (RequestInterface $request, array $options) use (&$sent) {
        $sent = $request;
        return Create::promiseFor(new Response(200));
    };
    new HttpClientRequest(new HttpClient(client: new Client(['handler' => $handler])))->basicAuth('user', 'password')->get('https://example.org');
    expect($sent->getHeaderLine('Authorization'))->toBe('Basic ' . base64_encode('user:password'));
});

it('wraps transport exceptions while preserving the original failure', function (): void {
    $cause = new ConnectException('offline', new Request('GET', 'https://example.org'));
    $handler = static fn () => Create::rejectionFor($cause);
    try {
        new HttpClientRequest(new HttpClient(client: new Client(['handler' => $handler])))->get('https://example.org');
        test()->fail('Transport failures must propagate.');
    } catch (RequestFailedException $error) {
        expect($error->getPrevious())->toBe($cause);
    }
});
