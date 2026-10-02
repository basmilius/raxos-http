<?php
declare(strict_types=1);

use Raxos\Http\HttpResponseCode;
use Raxos\Http\Response\ForbiddenHttpResponse;
use function RaxosTests\Http\responseBody;

covers(ForbiddenHttpResponse::class);

it('produces the correct status and an empty body', function (): void {
    $response = new ForbiddenHttpResponse();
    expect($response->responseCode)->toBe(HttpResponseCode::FORBIDDEN)->and(responseBody($response))->toBe('');
});
