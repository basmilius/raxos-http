<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Constraint;

use Attribute;
use Raxos\Contract\Http\Validate\ConstraintAttributeInterface;
use Raxos\DateTime\DateTime as RaxosDateTime;
use Raxos\Http\Validate\Error\DateTimeConstraintException;
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
            throw new DateTimeConstraintException();
        }

        try {
            return RaxosDateTime::parse($value);
        } catch (Throwable) {
            throw new DateTimeConstraintException();
        }
    }

}
