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
final readonly class RequestField
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
        public string $class,
        public string $name,
        public Field $field,
        public Constraint $constraint,
        public bool $isOptional,
        public array $types,
        public mixed $defaultValue
    )
    {
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
        return $this->field->alias ?? $this->name;
    }

}
