<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Constraint;

use Raxos\Http\Validate\Error\{FieldException, ValidationException, ValidatorException};
use Raxos\Http\Validate\RequestField;

/**
 * Class Constraint
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Constraint
 * @since 1.0.0
 */
abstract class Constraint
{

    /**
     * Transforms the data before setting the model property.
     *
     * @param mixed $data
     *
     * @return mixed
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public abstract function transform(mixed $data): mixed;

    /**
     * Validates the data.
     *
     * @param RequestField $field
     * @param mixed $data
     *
     * @throws FieldException
     * @throws ValidationException
     * @throws ValidatorException
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public abstract function validate(RequestField $field, mixed $data): void;

}
