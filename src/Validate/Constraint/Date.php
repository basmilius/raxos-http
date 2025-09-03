<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Constraint;

use Attribute;
use Raxos\DateTime\Date as RaxosDate;
use Raxos\Http\Validate\Contract\ConstraintAttributeInterface;
use Raxos\Http\Validate\Error\HttpConstraintException;
use ReflectionProperty;
use Throwable;
use function is_string;
use function preg_match;

/**
 * Class Date
 *
 * @implements ConstraintAttributeInterface<RaxosDate>
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Constraint
 * @since 2.0.0
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class Date implements ConstraintAttributeInterface
{

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function check(ReflectionProperty $property, mixed $value): RaxosDate
    {
        if (!is_string($value) || !preg_match('/\d{4}-\d{2}-\d{2}/', $value)) {
            throw HttpConstraintException::date();
        }

        try {
            return RaxosDate::parse($value);
        } catch (Throwable) {
            throw HttpConstraintException::date();
        }
    }

}
