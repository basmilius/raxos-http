<?php
declare(strict_types=1);

namespace Raxos\Http\Structure;

use Raxos\Collection\Map;

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
     * @author Bas Milius <bas@mili.us>
     * @since 1.2.0
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return parent::get($key, $default);
    }

}
