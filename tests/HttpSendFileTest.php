<?php
declare(strict_types=1);

use Raxos\Error\InvalidArgumentException;
use Raxos\Http\HttpSendFile;
use Symfony\Component\Process\Process;

covers(HttpSendFile::class);

it('sends exact full, closed, open and suffix ranges', function (string $range, string $expected, int $status): void {
    $file = tempnam(sys_get_temp_dir(), 'raxos-file-');
    file_put_contents($file, '0123456789');
    try {
        $process = new Process([PHP_BINARY, __DIR__ . '/Support/send-file.php', $file, $range]);
        $process->mustRun();
        expect($process->getOutput())->toBe($expected);
        expect((int)$process->getErrorOutput())->toBe($status);
    } finally {
        unlink($file);
    }
})->with([
    ['none', '0123456789', 200], ['bytes=0-0', '0', 206], ['bytes=2-4', '234', 206],
    ['bytes=7-', '789', 206], ['bytes=-3', '789', 206], ['bytes=7-999', '789', 206],
    ['bytes=-999', '0123456789', 206], ['bytes=10-', '', 416], ['bytes=-0', '', 416],
    ['bytes=4-2', '', 416], ['bytes=abc-def', '', 416], ['bytes=0-1,3-4', '', 416],
]);

it('uses unthrottled downloads by default and validates tuning', function (): void {
    expect(new HttpSendFile('/unused')->throttle)->toBe(0.0);
    expect(fn() => new HttpSendFile('/unused', bytes: 0))->toThrow(InvalidArgumentException::class);
    expect(fn() => new HttpSendFile('/unused', throttle: -0.1))->toThrow(InvalidArgumentException::class);
});
