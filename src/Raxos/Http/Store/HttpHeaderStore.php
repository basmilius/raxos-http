<?php
declare(strict_types=1);

namespace Raxos\Http\Store;

use Raxos\Foundation\Collection\ReadonlyMap;
use Raxos\Http\{HttpHeader, HttpHeaders, HttpUtil};
use function is_array;

/**
 * Class HttpHeaderStore
 *
 * @extends ReadonlyMap<string[]>
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Store
 * @since 1.1.0
 */
final readonly class HttpHeaderStore extends ReadonlyMap
{

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.1.0
     */
    public function get(HttpHeader|string $key, bool $multiple = false): mixed
    {
        $key = $this->key($key);
        $result = parent::get($key);

        if ($multiple && is_array($result)) {
            return $result;
        }

        if ($multiple) {
            return [$result];
        }

        if (is_array($result)) {
            return $result[0];
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.1.0
     */
    public function has(HttpHeader|string $key): bool
    {
        $key = $this->key($key);

        return parent::has($key);
    }

    /**
     * Returns the mutable version of the http headers.
     *
     * @return HttpHeaders
     * @author Bas Milius <bas@mili.us>
     * @since 1.1.0
     */
    public function toMutable(): HttpHeaders
    {
        return new HttpHeaders($this->toArray());
    }

    /**
     * Returns the string key.
     *
     * @param HttpHeader|string $key
     *
     * @return string
     * @author Bas Milius <bas@mili.us>
     * @since 1.1.0
     */
    private function key(HttpHeader|string $key): string
    {
        if ($key instanceof HttpHeader) {
            return $key->value;
        }

        return HttpHeader::tryFrom($key)?->value ?? $key;
    }

    /**
     * Returns the header store from globals.
     *
     * @return self
     * @author Bas Milius <bas@mili.us>
     * @since 1.1.0
     */
    public static function fromGlobals(): self
    {
        return new self(HttpUtil::getAllHeaders());
    }

}
