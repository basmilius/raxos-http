<?php
declare(strict_types=1);

use Raxos\Http\Response\BinaryHttpResponse;
use function RaxosTests\Http\responseBody;

covers(BinaryHttpResponse::class);

it('sends the supplied content without changing its representation', function (): void {
    $response = new BinaryHttpResponse('abc' . chr(0) . 'def');
    expect(responseBody($response))->toBe('abc' . chr(0) . 'def');
});
