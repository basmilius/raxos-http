<?php
declare(strict_types=1);

namespace Raxos\Http\Store;

use Raxos\Foundation\Collection\ReadonlyMap;

/**
 * Class HttpPostStore
 *
 * @extends ReadonlyMap<mixed>
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Store
 * @since 1.1.0
 */
final readonly class HttpPostStore extends ReadonlyMap
{

    /**
     * Returns the post store from globals.
     *
     * @return self
     * @author Bas Milius <bas@mili.us>
     * @since 1.1.0
     */
    public static function fromGlobals(): self
    {
        return new self($_POST);
    }

}
