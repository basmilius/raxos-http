<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Constraint;

use Attribute;
use Raxos\Http\Validate\Error\FieldException;
use Raxos\Http\Validate\RequestField;

/**
 * Class Integer
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Constraint
 * @since 1.0.0
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Integer extends Constraint
{

    /**
     * Integer constructor.
     *
     * @param int|null $maxValue
     * @param int|null $minValue
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function __construct(
        protected ?int $maxValue = null,
        protected ?int $minValue = null
    )
    {
    }

    /**
     * Gets the max value.
     *
     * @return int|null
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function getMaxValue(): ?int
    {
        return $this->maxValue;
    }

    /**
     * Gets the min value.
     *
     * @return int|null
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function getMinValue(): ?int
    {
        return $this->minValue;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function transform(mixed $data): int
    {
        return (int)$data;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function validate(RequestField $field, mixed $data): void
    {
        if (!is_numeric($data)) {
            throw new FieldException($field, '{{name}} is not a number.');
        }

        if ((float)$data != (int)$data) {
            throw new FieldException($field, '{{name}} is not a round number.');
        }

        $data = (int)$data;

        if ($this->maxValue !== null && $data > $this->maxValue) {
            throw new FieldException($field, '{{name}} must not be higher than {{maxValue}}.', [
                'maxValue' => $this->maxValue
            ]);
        }

        if ($this->minValue !== null && $data < $this->minValue) {
            throw new FieldException($field, '{{name}} must not be lower than {{minValue}}.', [
                'minValue' => $this->minValue
            ]);
        }
    }

}
