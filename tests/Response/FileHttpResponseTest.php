<?php
declare(strict_types=1);

use Raxos\Http\{HttpHeader, HttpRequest};
use Raxos\Http\Response\FileHttpResponse;
use Raxos\Http\Structure\HttpHeadersMap;

covers(FileHttpResponse::class);

it('sends file contents and cache validators or a bodyless conditional response', function (string $condition, int $status, string $body): void {
    $file = tempnam(sys_get_temp_dir(), 'raxos-response-');
    file_put_contents($file, 'asset');
    touch($file, 1_700_000_000);
    $headers = match ($condition) {
        'etag' => ['If-None-Match' => [md5_file($file)]],
        'modified' => ['If-Modified-Since' => [gmdate('D, d M Y H:i:s \G\M\T', filemtime($file))]],
        default => []
    };
    $previousCode = http_response_code();
    try {
        $response = new FileHttpResponse($file, HttpRequest::create(headers: new HttpHeadersMap($headers)), new HttpHeadersMap(['Content-Type' => ['text/custom']]));
        ob_start();
        $response->send();
        $output = ob_get_clean();
        expect($output)->toBe($body)->and($response->responseCode->value)->toBe($status)
            ->and($response->headers->get(HttpHeader::CONTENT_TYPE))->toBe('text/custom')
            ->and($response->headers->get(HttpHeader::ETAG))->toBe(md5_file($file))
            ->and($response->headers->get(HttpHeader::LAST_MODIFIED))->toBe('Tue, 14 Nov 2023 22:13:20 GMT');
    } finally {
        unlink($file);
        http_response_code($previousCode ?: 200);
    }
})->with([['none', 200, 'asset'], ['etag', 304, ''], ['modified', 304, '']]);

it('detects file media types and rejects missing paths before sending anything', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'raxos-response-');
    file_put_contents($file, 'plain text');
    $previousCode = http_response_code();
    try {
        $response = new FileHttpResponse($file, HttpRequest::create());
        ob_start();
        $response->send();
        $body = ob_get_clean();
        expect($body)->toBe('plain text')->and($response->headers->get(HttpHeader::CONTENT_TYPE))->toBe('text/plain');
        expect(fn() => new FileHttpResponse($file . '.missing', HttpRequest::create())->send())->toThrow(RuntimeException::class);
    } finally {
        unlink($file);
        http_response_code($previousCode ?: 200);
    }
});
