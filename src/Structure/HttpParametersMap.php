<?php
declare(strict_types=1);

namespace Raxos\Http\Structure;

use Raxos\Foundation\Collection\Map;

/**
 * Class HttpParametersMap
 *
 * @extends Map<string, mixed>
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Structure
 * @since 1.2.0
 */
abstract class HttpParametersMap extends Map
{

    /**
     * {@inheritdoc}
     *
     * @param callable(mixed):mixed $map
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.2.0
     */
    public function get(string $key, ?callable $map = null): mixed
    {
        if ($map !== null) {
            return $map(parent::get($key));
        }

        return parent::get($key);
    }

}
