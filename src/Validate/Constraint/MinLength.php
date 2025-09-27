<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Constraint;

use Attribute;
use Raxos\Contract\Http\Validate\ConstraintAttributeInterface;
use Raxos\Http\Validate\Error\MinLengthConstraintException;
use ReflectionProperty;
use function assert;
use function is_string;
use function mb_strlen;

/**
 * Class MinLength
 *
 * @implements ConstraintAttributeInterface<string>
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Constraint
 * @since 1.7.0
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class MinLength implements ConstraintAttributeInterface
{

    /**
     * MinLength constructor.
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
        assert(is_string($value));

        if ($this->min !== null && mb_strlen($value) < $this->min) {
            throw new MinLengthConstraintException($this->min);
        }

        return $value;
    }

}
