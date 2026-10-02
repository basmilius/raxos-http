<?php
declare(strict_types=1);

use Raxos\Http\Response\HtmlHttpResponse;
use function RaxosTests\Http\responseBody;

covers(HtmlHttpResponse::class);

it('sends the supplied content without changing its representation', function (): void {
    $response = new HtmlHttpResponse('<p>é</p>');
    expect(responseBody($response))->toBe('<p>é</p>');
    expect($response->headers->get('Content-Type'))->toBe('text/html');
});
