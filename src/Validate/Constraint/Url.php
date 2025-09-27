<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Constraint;

use Attribute;
use Raxos\Contract\Http\Validate\ConstraintAttributeInterface;
use Raxos\Http\Validate\Error\UrlConstraintException;
use ReflectionProperty;
use function assert;
use function filter_var;
use function is_string;
use const FILTER_VALIDATE_URL;

/**
 * Class Url
 *
 * @implements ConstraintAttributeInterface<string>
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Constraint
 * @since 1.7.0
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class Url implements ConstraintAttributeInterface
{

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function check(ReflectionProperty $property, mixed $value): mixed
    {
        assert(is_string($value));

        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            throw new UrlConstraintException();
        }

        return $value;
    }

}
