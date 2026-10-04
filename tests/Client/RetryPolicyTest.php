<?php
declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Raxos\Error\InvalidArgumentException;
use Raxos\Http\Client\HttpClient;
use Raxos\Http\Client\RetryPolicy;

covers(RetryPolicy::class);

it('backs off retryable statuses and bounds per-attempt transport timeouts', function (): void {
    $now = 0.0;
    $delays = [];
    $timeouts = [];
    $attempts = 0;
    $policy = new RetryPolicy(maxElapsed: 5, baseDelay: 0.1, clock: static function () use (&$now): float {
        return $now;
    }, sleep: static function (float $delay) use (&$now, &$delays): void {
        $delays[] = $delay;
        $now += $delay;
    });
    $handler = static function (RequestInterface $request, array $options) use (&$attempts, &$timeouts, &$now) {
        $timeouts[] = $options['timeout'];
        ++$attempts;
        $now += 0.5;

        return Create::promiseFor(new Response($attempts < 3 ? 503 : 200));
    };
    $client = new HttpClient(client: new Client(['handler' => $handler, 'timeout' => 10]));
    expect($client->request()->retry($policy)->get('https://example.org')->responseCode->value)->toBe(200)
        ->and($attempts)->toBe(3)->and($delays)->toBe([0.1, 0.2])->and($timeouts)->toBe([5.0, 4.4, 3.7]);
});

it('does not retry mutation methods unless explicitly enabled', function (bool $unsafe, int $expected): void {
    $calls = 0;
    $policy = new RetryPolicy(baseDelay: 0, retryUnsafe: $unsafe, sleep: static function (): void {
    });
    $response = $policy->execute(static function () use (&$calls): Response {
        ++$calls;

        return new Response(503);
    }, 'POST');
    expect($calls)->toBe($expected)->and($response->getStatusCode())->toBe(503);
})->with([[false, 1], [true, 3]]);

it('honors Retry-After seconds and HTTP dates without exceeding delay or elapsed budgets', function (string $header, int $attempts, array $expectedDelays): void {
    $calls = 0;
    $delays = [];
    $now = 0.0;
    $policy = new RetryPolicy(maxAttempts: 2, clock: static function () use (&$now): float {
        return $now;
    }, wallClock: static fn(): int => 1000, sleep: static function (float $delay) use (&$now, &$delays): void {
        $now += $delay;
        $delays[] = $delay;
    });
    $policy->execute(static function () use (&$calls, $header): Response {
        ++$calls;

        return new Response(429, ['Retry-After' => $header]);
    }, 'GET');
    expect($calls)->toBe($attempts)->and($delays)->toBe($expectedDelays);
})->with([['1', 2, [1.0]], ['Thu, 01 Jan 1970 00:16:41 GMT', 2, [1.0]], ['10', 1, []]]);

it('does not retry a non-replayable body or a non-retryable exception response', function (): void {
    $calls = 0;
    $policy = new RetryPolicy(baseDelay: 0, sleep: static function (): void {
    });
    $policy->execute(static function () use (&$calls): Response {
        ++$calls;

        return new Response(503);
    }, 'PUT', replayable: false);
    expect($calls)->toBe(1);
    $error = new RequestException('bad request', new Request('GET', 'https://example.org'), new Response(400));

    try {
        $policy->execute(static function () use (&$calls, $error): Response {
            ++$calls;

            throw $error;
        }, 'GET');
        test()->fail('The original transport exception should propagate.');
    } catch (RequestException $caught) {
        expect($caught)->toBe($error)->and($calls)->toBe(2);
    }
});

it('does not begin another attempt when the injected sleeper exhausts the total budget', function (): void {
    $calls = 0;
    $now = 0.0;
    $policy = new RetryPolicy(clock: static function () use (&$now): float {
        return $now;
    }, sleep: static function () use (&$now): void {
        $now += 10;
    });
    $policy->execute(static function () use (&$calls): Response {
        ++$calls;

        return new Response(503);
    }, 'GET');
    expect($calls)->toBe(1);
});

it('retries connection failures and preserves the final original transport cause', function (): void {
    $failure = new ConnectException('offline', new Request('GET', 'https://example.org'));
    $calls = 0;
    $policy = new RetryPolicy(baseDelay: 0, sleep: static function (): void {
    });

    try {
        $policy->execute(static function () use (&$calls, $failure): never {
            ++$calls;

            throw $failure;
        }, 'GET');
        test()->fail('The transport failure should propagate.');
    } catch (ConnectException $error) {
        expect($error)->toBe($failure)->and($calls)->toBe(3);
    }
});

it('rejects invalid budgets and status lists without numeric coercion', function (array $options): void {
    expect(fn() => new RetryPolicy(...$options))->toThrow(InvalidArgumentException::class);
})->with([
    [['maxAttempts' => 0]], [['maxElapsed' => INF]], [['maxElapsed' => 0]],
    [['baseDelay' => -1]], [['maxDelay' => 0.01]], [['statuses' => ['503']]],
    [['statuses' => [true]]], [['statuses' => [600]]], [['statuses' => ['named' => 503]]]
]);
