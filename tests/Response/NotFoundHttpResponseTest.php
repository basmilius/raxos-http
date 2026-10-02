<?php
declare(strict_types=1);

use Raxos\Http\HttpResponseCode;
use Raxos\Http\Response\NotFoundHttpResponse;
use function RaxosTests\Http\responseBody;

covers(NotFoundHttpResponse::class);

it('produces the correct status and an empty body', function (): void {
    $response = new NotFoundHttpResponse();
    expect($response->responseCode)->toBe(HttpResponseCode::NOT_FOUND)->and(responseBody($response))->toBe('');
});
