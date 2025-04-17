<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Attribute;

use Attribute;
use Raxos\Http\Validate\Contract\AttributeInterface;

/**
 * Class Property
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Attribute
 * @since 1.7.0
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class Property implements AttributeInterface
{

    /**
     * Property constructor.
     *
     * @param string|null $alias
     * @param bool $optional
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function __construct(
        public ?string $alias = null,
        public bool $optional = false
    ) {}

}
