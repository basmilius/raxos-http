<?php
declare(strict_types=1);

use Raxos\Http\Response\JsonHttpResponse;
use function RaxosTests\Http\responseBody;

covers(JsonHttpResponse::class);

it('sends the supplied content without changing its representation', function (): void {
    $response = new JsonHttpResponse(['name' => 'é', 'url' => 'https://example.org']);
    expect(responseBody($response))->toBe('{"name":"é","url":"https://example.org"}');
    expect($response->headers->get('Content-Type'))->toBe('application/json');
});
