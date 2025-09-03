<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Constraint;

use Attribute;
use Raxos\DateTime\DateTime as RaxosDateTime;
use Raxos\Http\Validate\Contract\ConstraintAttributeInterface;
use Raxos\Http\Validate\Error\HttpConstraintException;
use ReflectionProperty;
use Throwable;
use function is_string;

/**
 * Class DateTime
 *
 * @implements ConstraintAttributeInterface<RaxosDateTime>
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Constraint
 * @since 2.0.0
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class DateTime implements ConstraintAttributeInterface
{

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function check(ReflectionProperty $property, mixed $value): RaxosDateTime
    {
        if (!is_string($value)) {
            throw HttpConstraintException::datetime();
        }

        try {
            return RaxosDateTime::parse($value);
        } catch (Throwable) {
            throw HttpConstraintException::datetime();
        }
    }

}
