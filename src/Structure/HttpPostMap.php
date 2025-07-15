<?php
declare(strict_types=1);

namespace Raxos\Http\Structure;

use Raxos\Foundation\Collection\Map;

/**
 * Class HttpPostMap
 *
 * @extends Map<string, mixed>
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Structure
 * @since 1.2.0
 */
final class HttpPostMap extends HttpParametersMap
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
        return new self($_POST);
    }

}
