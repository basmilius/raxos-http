<?php
declare(strict_types=1);

namespace Raxos\Http\Store;

use Raxos\Foundation\Collection\Map;
use function setcookie;

/**
 * Class HttpCookieStore
 *
 * @extends Map<string>
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Store
 * @since 1.1.0
 */
final class HttpCookieStore extends Map
{

    /**
     * {@inheritdoc}
     *
     * @param int $expires
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httpOnly
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.1.0
     */
    public function set(string $key, mixed $value, int $expires = 0, string $path = '', string $domain = '', bool $secure = false, bool $httpOnly = false): void
    {
        parent::set($key, $value);

        setcookie($key, $value, $expires, $path, $domain, $secure, $httpOnly);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httpOnly
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.1.0
     */
    public function unset(string $key, string $path = '', string $domain = '', bool $secure = false, bool $httpOnly = false): void
    {
        parent::unset($key);

        setcookie($key, '', -1, $path, $domain, $secure, $httpOnly);
    }

    /**
     * Returns the cookie store from globals.
     *
     * @return self
     * @author Bas Milius <bas@mili.us>
     * @since 1.1.0
     */
    public static function fromGlobals(): self
    {
        return new self($_COOKIE);
    }

}
