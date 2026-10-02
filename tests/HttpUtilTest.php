<?php
declare(strict_types=1);

use Raxos\Http\HttpUtil;

covers(HttpUtil::class);

it('does not treat CLI environment variables as request headers', function (): void {
    $previous = $_SERVER;
    try {
        $_SERVER['HTTP_AUTHORIZATION'] = 'unit-value';
        expect(HttpUtil::getAllHeaders())->toBe([]);
    } finally {
        $_SERVER = $previous;
    }
});
