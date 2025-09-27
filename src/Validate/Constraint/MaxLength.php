<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Constraint;

use Attribute;
use Raxos\Contract\Http\Validate\ConstraintAttributeInterface;
use Raxos\Http\Validate\Error\MaxLengthConstraintException;
use ReflectionProperty;
use function assert;
use function is_string;
use function mb_strlen;

/**
 * Class MaxLength
 *
 * @implements ConstraintAttributeInterface<string>
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Constraint
 * @since 1.7.0
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class MaxLength implements ConstraintAttributeInterface
{

    /**
     * MaxLength constructor.
     *
     * @param int $max
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function __construct(
        public int $max
    ) {}

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function check(ReflectionProperty $property, mixed $value): mixed
    {
        assert(is_string($value));

        if ($this->max !== null && mb_strlen($value) > $this->max) {
            throw new MaxLengthConstraintException($this->max);
        }

        return $value;
    }

}
