<?php
declare(strict_types=1);

namespace Raxos\Http\Store;

use Raxos\Foundation\Collection\ReadonlyMap;
use function parse_str;

/**
 * Class HttpQueryStore
 *
 * @extends ReadonlyMap<string|string[]>
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Store
 * @since 1.1.0
 */
final readonly class HttpQueryStore extends ReadonlyMap
{

    /**
     * {@inheritdoc}
     *
     * @param callable(mixed):mixed $converter
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.1.0
     */
    public function get(string $key, ?callable $converter = null): mixed
    {
        if ($converter !== null) {
            return $converter(parent::get($key));
        }

        return parent::get($key);
    }

    /**
     * Returns the query string store from globals.
     *
     * @return self
     * @author Bas Milius <bas@mili.us>
     * @since 1.1.0
     */
    public static function fromGlobals(): self
    {
        parse_str($_SERVER['QUERY_STRING'] ?? '', $queryString);

        return new self($queryString);
    }

    /**
     * Returns the query string store from the given string.
     *
     * @param string $raw
     *
     * @return self
     * @author Bas Milius <bas@mili.us>
     * @since 1.1.0
     */
    public static function fromString(string $raw): self
    {
        parse_str($raw, $queryString);

        return new self($queryString);
    }

}
