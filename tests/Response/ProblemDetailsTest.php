<?php
declare(strict_types=1);

use Raxos\Error\InvalidArgumentException;
use Raxos\Http\Response\ProblemDetails;
use function RaxosTests\Http\responseBody;

covers(ProblemDetails::class);

it('keeps problem status, content type and extensions consistent', function (): void {
    $problem = new ProblemDetails(422, 'Invalid input', 'The name is required.', 'https://example.org/errors/input', '/requests/42', ['fields' => ['name']]);
    $response = $problem->response();
    expect($response->responseCode->value)->toBe(422)->and($response->headers->get('Content-Type'))->toBe('application/problem+json')
        ->and(json_decode(responseBody($response), true))->toBe($problem->jsonSerialize());
});

it('requires public details explicitly and never serializes exception causes or messages', function (): void {
    $error = new LogicException('secret database password', previous: new RuntimeException('private cause'));
    $body = responseBody(ProblemDetails::fromException($error)->response());
    expect($body)->not->toContain('secret', 'password', 'private', 'cause');
    expect(ProblemDetails::fromException($error, 404, detail: 'No item exists.')->jsonSerialize()['detail'])->toBe('No item exists.');
});

it('rejects unsupported statuses and overrides of reserved members', function (): void {
    expect(fn() => new ProblemDetails(200, 'OK'))->toThrow(InvalidArgumentException::class);
    expect(fn() => new ProblemDetails(422, 'Error', extensions: ['status' => 200]))->toThrow(InvalidArgumentException::class);
});
