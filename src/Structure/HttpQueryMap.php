<?php
declare(strict_types=1);

namespace Raxos\Http\Structure;

use Raxos\Collection\Map;
use function parse_str;

/**
 * Class HttpQueryMap
 *
 * @extends Map<string, mixed>
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Structure
 * @since 1.2.0
 */
final class HttpQueryMap extends HttpParametersMap
{

    /**
     * Creates from the global request.
     *
     * @return self
     * @author Bas Milius <bas@mili.us>
     * @since 1.2.0
     */
    public static function createFromGlobals(): self
    {
        return self::createFromString($_SERVER['QUERY_STRING'] ?? '');
    }

    /**
     * Creates from a string.
     *
     * @param string $raw
     *
     * @return self
     * @author Bas Milius <bas@mili.us>
     * @since 1.2.0
     */
    public static function createFromString(string $raw): self
    {
        if (empty($raw)) {
            return new self();
        }

        parse_str($raw, $query);

        return new self($query);
    }

}
