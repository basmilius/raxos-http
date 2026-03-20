<?php
declare(strict_types=1);

namespace Raxos\Http\Structure;

use Raxos\Collection\Map;
use Raxos\Http\HttpUtil;
use function array_map;
use function is_array;
use function strtolower;

/**
 * Class HttpHeadersMap
 *
 * @extends Map<string, string[]>
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
     * @param string $key
     * @param mixed $value
     *
     * @return void
     * @author Bas Milius <bas@mili.us>
     * @since 1.2.0
     */
    public function add(string $key, mixed $value): void
    {
        $key = strtolower($key);

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
    public function get(string $key, mixed $default = null): mixed
    {
        $result = parent::get(strtolower($key));

        if (empty($result)) {
            return $default;
        }

        return $result[0] ?? $default;
    }

    /**
     * Returns all values for the given header.
     *
     * @param string $key
     *
     * @return string[]
     * @author Bas Milius <bas@mili.us>
     * @since 1.2.0
     */
    public function getAll(string $key): array
    {
        $result = parent::get(strtolower($key));

        if (empty($result)) {
            return [];
        }

        return is_array($result) ? $result : [$result];
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.2.0
     */
    public function has(string $key): bool
    {
        return parent::has(strtolower($key));
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.2.0
     */
    public function set(string $key, mixed $value): void
    {
        parent::set(strtolower($key), [$value]);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.2.0
     */
    public function unset(string $key): void
    {
        parent::unset(strtolower($key));
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
        return new self(array_map(static fn($value) => is_array($value) ? $value : [$value], HttpUtil::getAllHeaders()));
    }

}
