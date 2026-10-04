<?php
declare(strict_types=1);

use GuzzleHttp\Psr7\Uri;
use Raxos\Http\Client\Psr7\Psr7Request;

covers(Psr7Request::class);

it('provides valid defaults for a new request', function (): void {
    $request = new Psr7Request();
    expect($request->getMethod())->toBe('GET')->and((string)$request->getUri())->toBe('')->and($request->getRequestTarget())->toBe('/');
});

it('keeps previous requests immutable when method, URI and target change', function (): void {
    $original = new Psr7Request()->withMethod('get')->withUri(new Uri('https://example.org/a?x=1'));
    $method = $original->withMethod('post');
    $uri = $original->withUri(new Uri('https://other.example:8443/b'));
    $target = $original->withRequestTarget('*');
    expect($original->getMethod())->toBe('GET')->and($method->getMethod())->toBe('POST')
        ->and($original->getRequestTarget())->toBe('/a?x=1')->and($uri->getRequestTarget())->toBe('/b')
        ->and($uri->getHeaderLine('Host'))->toBe('other.example:8443')->and($original->getHeaderLine('Host'))->toBe('example.org')
        ->and($target->getRequestTarget())->toBe('*');
});

it('preserves an explicit nonempty host but updates an empty host', function (): void {
    $request = new Psr7Request()->withHeader('Host', 'explicit.example');
    expect($request->withUri(new Uri('https://other.example'), true)->getHeaderLine('Host'))->toBe('explicit.example')
        ->and($request->withHeader('Host', '')->withUri(new Uri('https://other.example'), true)->getHeaderLine('Host'))->toBe('other.example');
});

it('rejects empty or non-string methods and whitespace in request targets', function (): void {
    expect(fn() => new Psr7Request()->withMethod(''))->toThrow(InvalidArgumentException::class);
    expect(fn() => new Psr7Request()->withMethod(12))->toThrow(InvalidArgumentException::class);
    expect(fn() => new Psr7Request()->withRequestTarget('/bad target'))->toThrow(InvalidArgumentException::class);
});
