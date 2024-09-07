<?php
declare(strict_types=1);

namespace Raxos\Http;

use ArrayIterator;
use IteratorAggregate;
use Raxos\Http\Store\HttpHeaderStore;
use Traversable;
use function array_key_exists;

/**
 * Class HttpHeaders
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http
 * @since 1.1.0
 */
final readonly class HttpHeaders implements IteratorAggregate
{

    /**
     * HttpHeaders constructor.
     *
     * @param array<HttpHeader|string, string[]> $headers
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.1.0
     */
    public function __construct(
        public array $headers = []
    ) {}

    /**
     * Adds the given header.
     *
     * @param HttpHeader|string $name
     * @param string $value
     *
     * @return self
     * @author Bas Milius <bas@mili.us>
     * @since 1.1.0
     */
    public function add(HttpHeader|string $name, string $value): self
    {
        $headers = $this->headers;
        $key = $this->key($name);

        $headers[$key] ??= [];
        $headers[$key][] = $value;

        return new self($headers);
    }

    /**
     * Gets the value for the header.
     *
     * @param HttpHeader|string $name
     *
     * @return string[]|null
     * @author Bas Milius <bas@mili.us>
     * @since 1.1.0
     */
    public function get(HttpHeader|string $name): ?array
    {
        $key = $this->key($name);

        return $this->headers[$key] ?? null;
    }

    /**
     * Returns TRUE if a header with the given name exists.
     *
     * @param HttpHeader|string $name
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.1.0
     */
    public function has(HttpHeader|string $name): bool
    {
        $key = $this->key($name);

        return array_key_exists($key, $this->headers);
    }

    /**
     * Removes the given header.
     *
     * @param HttpHeader|string $name
     *
     * @return self
     * @author Bas Milius <bas@mili.us>
     * @since 1.1.0
     */
    public function remove(HttpHeader|string $name): self
    {
        $headers = $this->headers;
        $key = $this->key($name);

        unset($headers[$key]);

        return new self($headers);
    }

    /**
     * Sets the given header.
     *
     * @param HttpHeader|string $name
     * @param string $value
     *
     * @return self
     * @author Bas Milius <bas@mili.us>
     * @since 1.1.0
     */
    public function set(HttpHeader|string $name, string $value): self
    {
        $headers = $this->headers;
        $key = $this->key($name);

        $headers[$key] = [$value];

        return new self($headers);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.1.0
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->headers);
    }

    /**
     * Transform into a header store for usage in requests.
     *
     * @return HttpHeaderStore
     * @author Bas Milius <bas@mili.us>
     * @since 1.1.0
     */
    public function toStore(): HttpHeaderStore
    {
        return new HttpHeaderStore($this->headers);
    }

    /**
     * Returns the value that should be used as the key.
     *
     * @param HttpHeader|string $name
     *
     * @return string
     * @author Bas Milius <bas@mili.us>
     * @since 1.1.0
     */
    private function key(HttpHeader|string $name): string
    {
        if ($name instanceof HttpHeader) {
            return $name->value;
        }

        return HttpHeader::tryFrom($name)?->value ?? $name;
    }

    /**
     * Returns a new instance.
     *
     * @return self
     * @author Bas Milius <bas@mili.us>
     * @since 1.1.0
     */
    public static function new(): self
    {
        return new self();
    }

}
