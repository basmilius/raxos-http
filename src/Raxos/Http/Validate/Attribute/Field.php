<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Attribute;

use Attribute;

/**
 * Class Field
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Attribute
 * @since 1.0.0
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Field
{

    /**
     * Field constructor.
     *
     * @param string $name
     * @param string|null $alias
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function __construct(
        public readonly string $name,
        public readonly ?string $alias = null
    )
    {
    }

}
