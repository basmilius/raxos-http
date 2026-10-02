<?php
declare(strict_types=1);

use Raxos\Http\HttpResponseCode;
use Raxos\Http\Response\ResultHttpResponse;
use Raxos\Http\Structure\HttpHeadersMap;
use function RaxosTests\Http\responseBody;

covers(ResultHttpResponse::class);

it('requires an explicit representation while preserving headers and status', function (): void {
    $headers = new HttpHeadersMap(['X-Unit' => 'value']);
    $result = new ResultHttpResponse('value', $headers, HttpResponseCode::CREATED);
    expect(fn () => $result->send())->toThrow(RuntimeException::class);
    $html = $result->asHtml();
    expect(responseBody($html))->toBe('value')->and($html->responseCode)->toBe(HttpResponseCode::CREATED)->and($html->headers)->toBe($headers);
    $json = $result->asJson();
    expect(responseBody($json))->toBe('"value"')->and($json->responseCode)->toBe(HttpResponseCode::CREATED)->and($json->headers)->toBe($headers);
});
