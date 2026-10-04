<?php
declare(strict_types=1);

namespace Raxos\Http\Client\Psr7;

use GuzzleHttp\Psr7\MessageTrait;
use GuzzleHttp\Psr7\Uri;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use function is_string;
use function preg_match;
use function strtoupper;

/**
 * Class Psr7Request
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Client\Psr7
 * @since 1.0.0
 */
final class Psr7Request implements RequestInterface
{

    use MessageTrait;

    /**
     * Retains the concrete HTTP method used by this PSR request.
     *
     * @var string
     * @author Bas Milius <bas@mili.us>
     * @since 3.2.0
     */
    private string $method = 'GET';

    /**
     * Caches the request target derived from the current URI.
     *
     * @var mixed
     * @author Bas Milius <bas@mili.us>
     * @since 3.2.0
     */
    private mixed $requestTarget = null;

    /**
     * Retains the URI used to derive request targeting and host information.
     *
     * @var UriInterface
     * @author Bas Milius <bas@mili.us>
     * @since 3.2.0
     */
    private UriInterface $uri;

    /**
     * Initializes a request with an empty URI.
     *
     * @author Bas Milius <bas@mili.us>
     * @since 3.2.0
     */
    public function __construct()
    {
        $this->uri = new Uri();
    }

    /**
     * {@inheritdoc}
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * {@inheritdoc}
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function getRequestTarget(): string
    {
        if ($this->requestTarget !== null) {
            return $this->requestTarget;
        }

        $target = $this->uri->getPath();

        if ($target === '') {
            $target = '/';
        }

        if ($this->uri->getQuery() !== '') {
            $target .= '?' . $this->uri->getQuery();
        }

        return $target;
    }

    /**
     * {@inheritdoc}
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    /**
     * {@inheritdoc}
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function withMethod($method): self
    {
        if (!is_string($method) || $method === '') {
            throw new InvalidArgumentException('Method must be a non-empty string.');
        }

        $request = clone $this;
        $request->method = strtoupper($method);

        return $request;
    }

    /**
     * {@inheritdoc}
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function withRequestTarget($requestTarget): self
    {
        if (preg_match('#\s#', $requestTarget)) {
            throw new InvalidArgumentException('Invalid request target provided; cannot contain whitespace.');
        }

        $request = clone $this;
        $request->requestTarget = $requestTarget;

        return $request;
    }

    /**
     * {@inheritdoc}
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function withUri(
        UriInterface $uri,
        $preserveHost = false
    ): self
    {
        if ($this->uri === $uri) {
            return $this;
        }

        $request = clone $this;
        $request->uri = $uri;

        if (!$preserveHost || $this->getHeaderLine('Host') === '') {
            $request->updateHostFromUri();
        }

        return $request;
    }

    /**
     * Updates the host header.
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    private function updateHostFromUri(): void
    {
        $host = $this->uri->getHost();

        if ($host === '') {
            return;
        }

        if (($port = $this->uri->getPort()) !== null) {
            $host .= ':' . $port;
        }

        if (isset($this->headerNames['host'])) {
            $header = $this->headerNames['host'];
        } else {
            $header = 'Host';
            $this->headerNames['host'] = 'Host';
        }

        $this->headers = [$header => [$host]] + $this->headers;
    }

}
