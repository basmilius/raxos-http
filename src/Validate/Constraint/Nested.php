<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Constraint;

use Attribute;
use Raxos\Contract\Http\HttpRequestModelInterface;
use Raxos\Contract\Http\Validate\{ConstraintAttributeInterface, ValidatorExceptionInterface};
use Raxos\Foundation\Util\ReflectionUtil;
use Raxos\Http\Validate\Error\NestedConstraintException;
use Raxos\Http\Validate\HttpClassValidator;
use ReflectionProperty;
use function is_array;
use function is_subclass_of;

/**
 * Class Nested
 *
 * @implements ConstraintAttributeInterface<HttpRequestModelInterface>
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Constraint
 * @since 2.0.0
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class Nested implements ConstraintAttributeInterface
{

    /**
     * {@inheritdoc}
     * @throws ValidatorExceptionInterface
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function check(ReflectionProperty $property, mixed $value): object
    {
        if (!is_array($value)) {
            throw new NestedConstraintException($property->name);
        }

        $propertyTypes = ReflectionUtil::getTypes($property->getType());
        $propertyType = $propertyTypes[0] ?? null;

        if (!is_subclass_of($propertyType, HttpRequestModelInterface::class)) {
            throw new NestedConstraintException($property->name);
        }

        $validator = new HttpClassValidator($propertyType);
        $validator->validate($value);

        return $validator->get();
    }

}
