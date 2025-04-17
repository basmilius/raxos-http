<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Contract;

use Raxos\Http\Validate\Error\HttpConstraintException;
use ReflectionProperty;

/**
 * Interface ConstraintAttributeInterface
 *
 * @template TValue of mixed
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Contract
 * @since 1.7.0
 */
interface ConstraintAttributeInterface extends AttributeInterface
{

    /**
     * Checks the given value.
     *
     * @param ReflectionProperty $property
     * @param TValue $value
     *
     * @return mixed
     * @throws HttpConstraintException
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function check(ReflectionProperty $property, mixed $value): mixed;

}
