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

    public string $property;

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
        $this->property = $this->field->alias ?? $this->name;
    }

}
