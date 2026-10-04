<?php
declare(strict_types=1);

namespace Raxos\Http\Validate;

use Raxos\Contract\Http\Validate\ConstraintAttributeInterface;
use Raxos\Foundation\Util\ReflectionUtil;
use Raxos\Http\Validate\Attribute\Property;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionProperty;

/**
 * Class RequestPropertyMetadata
 *
 * Shared input metadata; conditional optional rules are evaluated only at request validation time.
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate
 * @since 3.3.0
 */
final readonly class RequestPropertyMetadata
{
    /**
     * Retains reflection attributes without instantiating constraints until validation or schema generation needs them.
     *
     * @param ReflectionProperty $property
     * @param Property $attribute
     * @param list<string> $types
     * @param bool $hasDefault
     * @param mixed $default
     * @param list<ReflectionAttribute<ConstraintAttributeInterface>> $constraints
     *
     * @author Bas Milius <bas@mili.us>
     * @since 3.3.0
     */
    private function __construct(
        public ReflectionProperty $property,
        public Property $attribute,
        public array $types,
        public bool $hasDefault,
        public mixed $default,
        public array $constraints
    ) {}

    /**
     * Reads input aliases, constraints and defaults, including promoted constructor defaults.
     *
     * @param ReflectionProperty $property
     * @param ReflectionClass|null $context
     *
     * @return self|null
     * @author Bas Milius <bas@mili.us>
     * @since 3.3.0
     */
    public static function from(
        ReflectionProperty $property,
        ?ReflectionClass $context = null
    ): ?self
    {
        $attribute = $property->getAttributes(Property::class)[0] ?? null;

        if ($attribute === null) {
            return null;
        }

        $hasDefault = $property->hasDefaultValue();
        $default = $hasDefault ? $property->getDefaultValue() : null;

        if ($property->isPromoted()) {
            foreach (($context ?? $property->getDeclaringClass())->getConstructor()?->getParameters() ?? [] as $parameter) {
                if ($parameter->name === $property->name && $parameter->isDefaultValueAvailable()) {
                    $hasDefault = true;
                    $default = $parameter->getDefaultValue();
                }
            }
        }

        $constraints = [];

        foreach ($property->getAttributes(ConstraintAttributeInterface::class, ReflectionAttribute::IS_INSTANCEOF) as $constraint) {
            $constraints[] = $constraint;
        }

        return new self($property, $attribute->newInstance(), ReflectionUtil::getTypes($property->getType()), $hasDefault, $default, $constraints);
    }

    /**
     * Uses the input alias when present, independently from response serialization names.
     *
     * @return string
     * @author Bas Milius <bas@mili.us>
     * @since 3.3.0
     */
    public function name(): string
    {
        return $this->attribute->alias ?? $this->property->name;
    }

    /**
     * Null identifies a conditional rule that static schema generation cannot evaluate.
     *
     * @return bool|null
     * @author Bas Milius <bas@mili.us>
     * @since 3.3.0
     */
    public function optional(): ?bool
    {
        return is_bool($this->attribute->optional) ? $this->attribute->optional : null;
    }

    /**
     * Matches the validator fallback for an omitted or blank input value.
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 3.3.0
     */
    public function canDefault(): bool
    {
        return $this->default !== null || ($this->property->getType()?->allowsNull() ?? false);
    }
}
