<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Constraint;

use Attribute;
use Raxos\Foundation\Util\ReflectionUtil;
use Raxos\Http\Contract\HttpRequestModelInterface;
use Raxos\Http\Validate\Contract\ConstraintAttributeInterface;
use Raxos\Http\Validate\Error\HttpConstraintException;
use Raxos\Http\Validate\Error\HttpValidatorException;
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
     * @throws HttpValidatorException
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function check(ReflectionProperty $property, mixed $value): object
    {
        if (!is_array($value)) {
            throw HttpConstraintException::nested($property->name);
        }

        $propertyTypes = ReflectionUtil::getTypes($property->getType());
        $propertyType = $propertyTypes[0] ?? null;

        if (!is_subclass_of($propertyType, HttpRequestModelInterface::class)) {
            throw HttpConstraintException::nested($property->name);
        }

        $validator = new HttpClassValidator($propertyType);
        $validator->validate($value);

        return $validator->get();
    }

}
