<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Constraint;

use Attribute;
use Raxos\Database\Error\DatabaseException;
use Raxos\Database\Orm\Model as DatabaseModel;
use Raxos\Http\Validate\Contract\{ConstraintAttributeInterface, TransformerInterface};
use Raxos\Http\Validate\Error\{HttpConstraintException, HttpTransformerException};
use ReflectionProperty;
use function assert;
use function count;
use function is_array;
use function is_string;

/**
 * Class ModelArray
 *
 * @implements ConstraintAttributeInterface<DatabaseModel[]>
 * @implements TransformerInterface<array<string|int>>
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Constraint
 * @since 1.7.0
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class ModelArray implements ConstraintAttributeInterface, TransformerInterface
{

    /**
     * ModelArray constructor.
     *
     * @param class-string<DatabaseModel> $modelClass
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function __construct(
        public string $modelClass
    ) {}

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function check(ReflectionProperty $property, mixed $value): array
    {
        assert(is_array($value));

        try {
            $results = $this->modelClass::find($value);

            if (count($results) !== count($value)) {
                throw HttpConstraintException::modelArray();
            }

            return $results->toArray();
        } catch (DatabaseException $err) {
            throw HttpConstraintException::modelArray($err);
        }
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function transform(mixed $value): array
    {
        if (!is_array($value)) {
            throw HttpTransformerException::invalidValue('Expected an array of primary keys.');
        }

        foreach ($value as $item) {
            if (!is_string($item) && !is_int($item)) {
                throw HttpTransformerException::invalidValue('Expected an array of primary keys.');
            }
        }

        return $value;
    }

}
