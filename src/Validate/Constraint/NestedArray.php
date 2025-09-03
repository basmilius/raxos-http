<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Constraint;

use Attribute;
use Raxos\Http\Contract\HttpRequestModelInterface;
use Raxos\Http\Validate\Contract\ConstraintAttributeInterface;
use Raxos\Http\Validate\Error\HttpConstraintException;
use Raxos\Http\Validate\Error\HttpValidatorException;
use Raxos\Http\Validate\HttpClassValidator;
use ReflectionProperty;
use function is_array;
use function is_subclass_of;

/**
 * Class NestedArray
 *
 * @template TModel of HttpRequestModelInterface
 *
 * @implements ConstraintAttributeInterface<TModel[]>
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Constraint
 * @since 2.0.0
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class NestedArray implements ConstraintAttributeInterface
{

    /**
     * NestedArray constructor.
     *
     * @param class-string<TModel> $propertyType
     *
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function __construct(
        public string $propertyType
    ) {}

    /**
     * {@inheritdoc}
     * @throws HttpValidatorException
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function check(ReflectionProperty $property, mixed $value): array
    {
        if (!is_array($value)) {
            throw HttpConstraintException::nested($property->name);
        }

        if (!is_subclass_of($this->propertyType, HttpRequestModelInterface::class)) {
            throw HttpConstraintException::nested($property->name);
        }

        $validator = new HttpClassValidator($this->propertyType);
        $results = [];

        foreach ($value as $item) {
            $validator->validate($item);
            $results[] = $validator->get();
        }

        return $results;
    }

}
