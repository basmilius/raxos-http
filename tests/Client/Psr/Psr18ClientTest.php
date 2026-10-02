<?php
declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Exception\{ConnectException, RequestException};
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\{Request, Response};
use Raxos\Http\Client\HttpClient;
use Raxos\Http\Client\Psr\Error\{Psr18NetworkException, Psr18RequestException};
use Raxos\Http\Client\Psr\Psr18Client;

covers(Psr18Client::class);

it('returns unsuccessful HTTP responses without throwing or following redirects', function (int $status): void {
    $response = new Response($status, ['Location' => 'https://other.example'], 'body');
    $client = new Psr18Client(new HttpClient(client: new Client(['handler' => new MockHandler([$response])])));
    $result = $client->sendRequest(new Request('GET', 'https://example.org'));
    expect($result->getStatusCode())->toBe($status)->and((string)$result->getBody())->toBe('body');
})->with([302, 400, 500]);

it('translates transport failures to the correct PSR exception and original request', function (bool $network): void {
    $request = new Request('GET', 'https://example.org');
    $cause = $network ? new ConnectException('offline', $request) : new RequestException('bad request', $request);
    $client = new Psr18Client(new HttpClient(client: new Client(['handler' => new MockHandler([$cause])])));
    try {
        $client->sendRequest($request);
        test()->fail('Transport failures must propagate.');
    } catch (Psr18NetworkException|Psr18RequestException $error) {
        expect($error)->toBeInstanceOf($network ? Psr18NetworkException::class : Psr18RequestException::class)
            ->and($error->getRequest())->toBe($request)->and($error->getPrevious())->toBe($cause);
    }
})->with([true, false]);
