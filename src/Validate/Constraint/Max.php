<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Constraint;

use Attribute;
use Raxos\Contract\Http\Validate\ConstraintAttributeInterface;
use Raxos\Http\Validate\Error\MaxConstraintException;
use ReflectionProperty;
use function assert;
use function is_numeric;

/**
 * Class Max
 *
 * @implements ConstraintAttributeInterface<int|float>
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Constraint
 * @since 1.7.0
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class Max implements ConstraintAttributeInterface
{

    /**
     * Max constructor.
     *
     * @param int|float $max
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function __construct(
        public int|float $max
    ) {}

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function check(ReflectionProperty $property, mixed $value): int|float
    {
        assert(is_numeric($value));

        if ($this->max !== null && $value > $this->max) {
            throw new MaxConstraintException($this->max);
        }

        return $value;
    }

}
