<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Constraint;

use Attribute;
use Raxos\Http\Validate\Contract\ConstraintAttributeInterface;
use Raxos\Http\Validate\Error\HttpConstraintException;
use ReflectionProperty;
use function assert;
use function is_numeric;

/**
 * Class Min
 *
 * @implements ConstraintAttributeInterface<float|int>
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Constraint
 * @since 1.7.0
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class Min implements ConstraintAttributeInterface
{

    /**
     * Min constructor.
     *
     * @param int $min
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function __construct(
        public int $min
    ) {}

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function check(ReflectionProperty $property, mixed $value): mixed
    {
        assert(is_numeric($value));

        if ($this->min !== null && $value < $this->min) {
            throw HttpConstraintException::min($this->min);
        }

        return $value;
    }

}
