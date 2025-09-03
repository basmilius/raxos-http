<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Constraint;

use Attribute;
use Raxos\DateTime\Time as RaxosTime;
use Raxos\Http\Validate\Contract\ConstraintAttributeInterface;
use Raxos\Http\Validate\Error\HttpConstraintException;
use ReflectionProperty;
use Throwable;
use function is_string;
use function preg_match;

/**
 * Class Time
 *
 * @implements ConstraintAttributeInterface<RaxosTime>
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Constraint
 * @since 2.0.0
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class Time implements ConstraintAttributeInterface
{

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function check(ReflectionProperty $property, mixed $value): RaxosTime
    {
        if (!is_string($value) || !preg_match('/\d{2}:\d{2}(:\d{2})?/', $value)) {
            throw HttpConstraintException::date();
        }

        try {
            return RaxosTime::parse($value);
        } catch (Throwable) {
            throw HttpConstraintException::date();
        }
    }

}
