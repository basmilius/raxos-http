<?php
declare(strict_types=1);

namespace Raxos\Http\Validate;

use Raxos\Http\Validate\Attribute\Field;
use Raxos\Http\Validate\Constraint\Constraint;

/**
 * Class RequestField
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate
 * @since 1.0.0
 */
final class RequestField
{

    /**
     * RequestField constructor.
     *
     * @param string $class
     * @param string $name
     * @param Field $field
     * @param Constraint $constraint
     * @param bool $isOptional
     * @param array $types
     * @param mixed $defaultValue
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function __construct(
        private string $class,
        private string $name,
        private Field $field,
        private Constraint $constraint,
        private bool $isOptional,
        private array $types,
        private mixed $defaultValue
    )
    {
    }

    /**
     * Gets the constraint.
     *
     * @return Constraint
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function getConstraint(): Constraint
    {
        return $this->constraint;
    }

    /**
     * Gets the default value.
     *
     * @return mixed
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function getDefaultValue(): mixed
    {
        return $this->defaultValue;
    }

    /**
     * Gets the field details.
     *
     * @return Field
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function getField(): Field
    {
        return $this->field;
    }

    /**
     * Gets the field property in request data.
     *
     * @return string
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function getFieldProperty(): string
    {
        return $this->field->getAlias() ?? $this->name;
    }

    /**
     * Gets the name of the class property.
     *
     * @return string
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function getName(): string
    {
        return $this->name;
    }

    /**
     * Gets the valid property types.
     *
     * @return array
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function getTypes(): array
    {
        return $this->types;
    }

    /**
     * Gets if the field is optional.
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function isOptional(): bool
    {
        return $this->isOptional;
    }

}
