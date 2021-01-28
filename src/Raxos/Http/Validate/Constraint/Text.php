<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Constraint;

use Attribute;
use Raxos\Http\Validate\RequestField;

/**
 * Class Text
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Constraint
 * @since 1.0.0
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Text extends Constraint
{

    /**
     * Text constructor.
     *
     * @param int|null $maxLength
     * @param int|null $minLength
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function __construct(
        protected ?int $maxLength = null,
        protected ?int $minLength = null
    )
    {
    }

    /**
     * Gets the max length.
     *
     * @return int|null
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function getMaxLength(): ?int
    {
        return $this->maxLength;
    }

    /**
     * Gets the min length.
     *
     * @return int|null
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function getMinLength(): ?int
    {
        return $this->minLength;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function transform(mixed $data): string
    {
        return (string)$data;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function validate(RequestField $field, mixed $data): void
    {
    }

}
