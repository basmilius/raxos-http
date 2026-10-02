<?php
declare(strict_types=1);

use Raxos\Http\Client\Psr\HttpFactory;

covers(HttpFactory::class);

it('creates requests, responses, server requests and URIs with supplied metadata', function (): void {
    $factory = new HttpFactory();
    expect($factory->createRequest('POST', 'https://example.org')->getMethod())->toBe('POST')
        ->and($factory->createResponse(201, 'Unit')->getReasonPhrase())->toBe('Unit')
        ->and($factory->createServerRequest('GET', '/unit', ['REMOTE_ADDR' => '127.0.0.1'])->getServerParams())->toBe(['REMOTE_ADDR' => '127.0.0.1'])
        ->and($factory->createUri('https://example.org:8443/path?x=1')->getPort())->toBe(8443);
});

it('creates streams from content, files and existing resources and uploaded file metadata', function (): void {
    $factory = new HttpFactory();
    $file = tempnam(sys_get_temp_dir(), 'raxos-psr-');
    file_put_contents($file, 'content');
    $resource = fopen($file, 'r');
    try {
        expect((string)$factory->createStream('text'))->toBe('text')->and((string)$factory->createStreamFromFile($file))->toBe('content')
            ->and((string)$factory->createStreamFromResource($resource))->toBe('content');
        $uploaded = $factory->createUploadedFile($factory->createStream('content'), 7, UPLOAD_ERR_OK, 'unit.txt', 'text/plain');
        expect($uploaded->getSize())->toBe(7)->and($uploaded->getClientFilename())->toBe('unit.txt')->and($uploaded->getClientMediaType())->toBe('text/plain');
    } finally {
        if (is_resource($resource)) {
            fclose($resource);
        }
        unlink($file);
    }
});
