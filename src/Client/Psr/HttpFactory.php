<?php
declare(strict_types=1);

namespace Raxos\Http\Client\Psr;

use GuzzleHttp\Psr7\HttpFactory as GuzzleFactory;
use Psr\Http\Message\{RequestFactoryInterface, RequestInterface, ResponseFactoryInterface, ResponseInterface, ServerRequestFactoryInterface, ServerRequestInterface, StreamFactoryInterface, StreamInterface, UploadedFileFactoryInterface, UploadedFileInterface, UriFactoryInterface, UriInterface};
use const UPLOAD_ERR_OK;

/**
 * Class HttpFactory
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Client\Psr
 * @since 2.2.0
 */
final readonly class HttpFactory implements RequestFactoryInterface, ResponseFactoryInterface, ServerRequestFactoryInterface, StreamFactoryInterface, UploadedFileFactoryInterface, UriFactoryInterface
{

    private GuzzleFactory $delegate;

    /**
     * HttpFactory constructor.
     *
     * @author Bas Milius <bas@mili.us>
     * @since 2.2.0
     */
    public function __construct()
    {
        $this->delegate = new GuzzleFactory();
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.2.0
     */
    public function createRequest(string $method, $uri): RequestInterface
    {
        return $this->delegate->createRequest($method, $uri);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.2.0
     */
    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        return $this->delegate->createResponse($code, $reasonPhrase);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.2.0
     */
    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        return $this->delegate->createServerRequest($method, $uri, $serverParams);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.2.0
     */
    public function createStream(string $content = ''): StreamInterface
    {
        return $this->delegate->createStream($content);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.2.0
     */
    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        return $this->delegate->createStreamFromFile($filename, $mode);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.2.0
     */
    public function createStreamFromResource($resource): StreamInterface
    {
        return $this->delegate->createStreamFromResource($resource);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.2.0
     */
    public function createUploadedFile(StreamInterface $stream, ?int $size = null, int $error = UPLOAD_ERR_OK, ?string $clientFilename = null, ?string $clientMediaType = null): UploadedFileInterface
    {
        return $this->delegate->createUploadedFile($stream, $size, $error, $clientFilename, $clientMediaType);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.2.0
     */
    public function createUri(string $uri = ''): UriInterface
    {
        return $this->delegate->createUri($uri);
    }

}
