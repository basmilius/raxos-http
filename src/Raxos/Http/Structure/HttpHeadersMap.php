<?php
declare(strict_types=1);

namespace Raxos\Http\Structure;

use Raxos\Foundation\Collection\Map;
use Raxos\Http\{HttpHeader, HttpUtil};
use function is_array;
use function strtolower;

/**
 * Class HttpHeadersMap
 *
 * @extends Map<HttpHeader|string, string[]>
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Structure
 * @since 1.2.0
 */
final class HttpHeadersMap extends Map
{

    /**
     * Adds a header.
     *
     * @param HttpHeader|string $key
     * @param mixed $value
     *
     * @return void
     * @author Bas Milius <bas@mili.us>
     * @since 1.2.0
     */
    public function add(HttpHeader|string $key, mixed $value): void
    {
        $key = $this->normalize($key);

        if (isset($this->data[$key]) && !is_array($this->data[$key])) {
            $this->data[$key] = [$this->data[$key]];
        }

        $this->data[$key][] = $value;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.2.0
     */
    public function get(HttpHeader|string $key): mixed
    {
        return parent::get($this->normalize($key));
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.2.0
     */
    public function has(HttpHeader|string $key): bool
    {
        return parent::has($this->normalize($key));
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.2.0
     */
    public function set(HttpHeader|string $key, mixed $value): void
    {
        parent::set($this->normalize($key), [$value]);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.2.0
     */
    public function unset(HttpHeader|string $key): void
    {
        parent::unset($this->normalize($key));
    }

    /**
     * Normalizes the header to a string.
     *
     * @param HttpHeader|string $key
     *
     * @return string
     * @author Bas Milius <bas@mili.us>
     * @since 1.2.0
     */
    private function normalize(HttpHeader|string $key): string
    {
        if ($key instanceof HttpHeader) {
            return $key->value;
        }

        return strtolower($key);
    }

    /**
     * Creates from the global request.
     *
     * @return self
     * @author Bas Milius <bas@mili.us>
     * @since 1.2.0
     */
    public static function createFromGlobals(): self
    {
        $headers = [];

        foreach (HttpUtil::getAllHeaders() as $name => $value) {
            $headers[$name] = is_array($value) ? $value : [$value];
        }

        return new self($headers);
    }

}
