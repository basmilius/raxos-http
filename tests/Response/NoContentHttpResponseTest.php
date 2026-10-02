<?php
declare(strict_types=1);

use Raxos\Http\HttpResponseCode;
use Raxos\Http\Response\NoContentHttpResponse;
use function RaxosTests\Http\responseBody;

covers(NoContentHttpResponse::class);

it('produces the correct status and an empty body', function (): void {
    $response = new NoContentHttpResponse();
    expect($response->responseCode)->toBe(HttpResponseCode::NO_CONTENT)->and(responseBody($response))->toBe('');
});
