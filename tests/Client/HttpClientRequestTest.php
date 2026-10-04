<?php
declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Psr7\NoSeekStream;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\RequestInterface;
use Raxos\Http\Client\Error\RequestFailedException;
use Raxos\Http\Client\HttpClient;
use Raxos\Http\Client\HttpClientRequest;
use Raxos\Http\Client\RetryPolicy;
use Raxos\Http\HttpMethod;

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
        'DELETE' => $builder->delete('https://example.org/items'),
        'PUT' => $builder->put('https://example.org/items', ['name' => 'é']),
        'PATCH' => $builder->patch('https://example.org/items', ['name' => 'é']),
        'HEAD' => $builder->head('https://example.org/items'),
        'OPTIONS' => $builder->optionsRequest('https://example.org/items'),
        'TRACE' => $builder->trace('https://example.org/items'),
        'CONNECT' => $builder->connect('https://example.org/items')
    };
    expect($sent->getMethod())->toBe($verb)->and($sent->getHeaderLine('Authorization'))->toBe('Bearer unit-token')
        ->and($sent->getHeader('X-Unit'))->toBe(['first', 'second'])->and($received['timeout'])->toBe(2.5)->and($response->body())->toBe('ok');

    if ($verb === 'GET') {
        expect($sent->getUri()->getQuery())->toBe('zero=0');
    }

    if (in_array($verb, ['POST', 'PUT', 'PATCH'], true)) {
        expect(json_decode((string)$sent->getBody(), true))->toBe(['name' => 'é']);
    }
})->with(['GET', 'POST', 'DELETE', 'PUT', 'PATCH', 'HEAD', 'OPTIONS', 'TRACE', 'CONNECT']);

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
    $handler = static fn() => Create::rejectionFor($cause);

    try {
        new HttpClientRequest(new HttpClient(client: new Client(['handler' => $handler])))->get('https://example.org');
        test()->fail('Transport failures must propagate.');
    } catch (RequestFailedException $error) {
        expect($error->getPrevious())->toBe($cause);
    }
});

it('creates isolated public builders and retains the options setter', function (): void {
    $requests = [];
    $handler = static function (RequestInterface $request, array $options) use (&$requests) {
        $requests[] = [$request, $options];

        return Create::promiseFor(new Response(200));
    };
    $client = new HttpClient(client: new Client(['handler' => $handler]));
    $client->request()->header('X-Private', 'one')->options(['timeout' => 2])->send(HttpMethod::OPTIONS, 'https://example.org');
    $client->request()->get('https://example.org');
    expect($requests[0][0]->getMethod())->toBe('OPTIONS')->and($requests[0][1]['timeout'])->toBe(2)
        ->and($requests[1][0]->hasHeader('X-Private'))->toBeFalse();
});

it('replays seekable body streams and resources from their initial position', function (string $kind): void {
    $resource = fopen('php://temp', 'w+');
    fwrite($resource, 'prefix-payload');
    fseek($resource, 7);
    $body = $kind === 'resource' ? $resource : Utils::streamFor($resource);
    $received = [];
    $handler = static function (RequestInterface $request) use (&$received) {
        $received[] = $request->getBody()->getContents();

        return Create::promiseFor(new Response(count($received) === 1 ? 503 : 200));
    };
    $client = new HttpClient(client: new Client(['handler' => $handler]));
    $policy = new RetryPolicy(baseDelay: 0, sleep: static function (): void {});

    try {
        $client->request()->options(['body' => $body])->retry($policy)->put('https://example.org');
        expect($received)->toBe(['payload', 'payload']);
    } finally {
        if (is_resource($resource)) {
            fclose($resource);
        }
    }
})->with(['resource', 'stream']);

it('replays every seekable multipart part but never replays a non-seekable body', function (): void {
    $part = Utils::streamFor('multipart payload');
    $received = [];
    $handler = static function (RequestInterface $request) use (&$received) {
        $received[] = $request->getBody()->getContents();

        return Create::promiseFor(new Response(count($received) === 1 ? 503 : 200));
    };
    $client = new HttpClient(client: new Client(['handler' => $handler]));
    $policy = new RetryPolicy(baseDelay: 0, retryUnsafe: true, sleep: static function (): void {});
    $client->request()->multipart([['name' => 'file', 'contents' => $part]])->retry($policy)->post('https://example.org');
    expect($received)->toHaveCount(2);

    foreach ($received as $body) {
        expect($body)->toContain('multipart payload');
    }

    $received = [];
    $nonSeekable = new NoSeekStream(Utils::streamFor('once'));
    $client->request()->options(['body' => $nonSeekable])->retry($policy)->put('https://example.org');
    expect($received)->toBe(['once']);
});
