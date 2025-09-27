<?php
declare(strict_types=1);

namespace Raxos\Http\Structure;

use Raxos\Collection\Map;
use function setcookie;

/**
 * Class HttpCookiesMap
 *
 * @extends Map<string, string>
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Structure
 * @since 1.2.0
 */
final class HttpCookiesMap extends Map
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
     * @since 1.2.0
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
     * @since 1.2.0
     */
    public function unset(string $key, string $path = '', string $domain = '', bool $secure = false, bool $httpOnly = false): void
    {
        parent::unset($key);

        setcookie($key, '', -1, $path, $domain, $secure, $httpOnly);
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
        return new self($_COOKIE);
    }

}
