<?php
declare(strict_types=1);

namespace Raxos\Http\Structure;

use Raxos\Collection\Map;

/**
 * Class HttpServerMap
 *
 * @extends Map<string, mixed>
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Structure
 * @since 1.2.0
 */
final class HttpServerMap extends HttpParametersMap
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
        return new self($_SERVER);
    }

}
