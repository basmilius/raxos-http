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
        private string $name,
        private ?string $alias = null
    )
    {
    }

    /**
     * Gets the alias of the field.
     *
     * @return string|null
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function getAlias(): ?string
    {
        return $this->alias;
    }

    /**
     * Gets the name of the field.
     *
     * @return string
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function getName(): string
    {
        return $this->name;
    }

}
