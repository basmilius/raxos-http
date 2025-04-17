<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Constraint;

use Attribute;
use Raxos\Http\Validate\Contract\ConstraintAttributeInterface;
use Raxos\Http\Validate\Error\HttpConstraintException;
use ReflectionProperty;
use function is_string;
use function preg_match;

/**
 * Class Matches
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Constraint
 * @since 1.7.0
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class Matches implements ConstraintAttributeInterface
{

    /**
     * Matches constructor.
     *
     * @param string $pattern
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function __construct(
        public string $pattern
    ) {}

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function check(ReflectionProperty $property, mixed $value): string
    {
        if (!is_string($value) || !preg_match($this->pattern, $value)) {
            throw HttpConstraintException::matches($this->pattern);
        }

        return $value;
    }

}
