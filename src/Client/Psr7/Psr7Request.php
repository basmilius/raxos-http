<?php
declare(strict_types=1);

namespace Raxos\Http\Client\Psr7;

use GuzzleHttp\Psr7\MessageTrait;
use InvalidArgumentException;
use Psr\Http\Message\{RequestInterface, UriInterface};
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

    /** @noinspection PhpGetterAndSetterCanBeReplacedWithPropertyHooksInspection */
    private ?string $method = null;
    private mixed $requestTarget = null;
    /** @noinspection PhpGetterAndSetterCanBeReplacedWithPropertyHooksInspection */
    private ?UriInterface $uri = null;

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * {@inheritdoc}
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
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function withMethod($method): self
    {
        if (!is_string($method) || $method === '') {
            throw new InvalidArgumentException('Method must be a non-empty string.');
        }

        $this->method = strtoupper($method);

        return $this;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function withRequestTarget($requestTarget): self
    {
        if (preg_match('#\s#', $requestTarget)) {
            throw new InvalidArgumentException('Invalid request target provided; cannot contain whitespace.');
        }

        $this->requestTarget = $requestTarget;

        return $this;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function withUri(UriInterface $uri, $preserveHost = false): self
    {
        if ($this->uri === $uri) {
            return $this;
        }

        $this->uri = $uri;

        if (!$preserveHost || !isset($this->headerNames['host'])) {
            $this->updateHostFromUri();
        }

        return $this;
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
