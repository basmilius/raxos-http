<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Constraint;

use Attribute;
use Raxos\Http\Validate\Contract\ConstraintAttributeInterface;
use Raxos\Http\Validate\Error\HttpConstraintException;
use ReflectionProperty;
use function assert;
use function filter_var;
use function is_string;
use const FILTER_VALIDATE_EMAIL;

/**
 * Class Email
 *
 * @implements ConstraintAttributeInterface<string>
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Constraint
 * @since 1.7.0
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class Email implements ConstraintAttributeInterface
{

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function check(ReflectionProperty $property, mixed $value): mixed
    {
        assert(is_string($value));

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw HttpConstraintException::email();
        }

        return $value;
    }

}
