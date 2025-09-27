<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Constraint;

use Attribute;
use Raxos\Contract\Database\DatabaseExceptionInterface;
use Raxos\Contract\Http\Validate\{ConstraintAttributeInterface, TransformerInterface};
use Raxos\Database\Orm\Model as DatabaseModel;
use Raxos\Foundation\Util\ReflectionUtil;
use Raxos\Http\Validate\Error\{InvalidValueTransformerException, ModelConstraintException};
use ReflectionProperty;
use function is_int;
use function is_string;
use function is_subclass_of;

/**
 * Class Model
 *
 * @implements ConstraintAttributeInterface<DatabaseModel>
 * @implements TransformerInterface<string|int>
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Constraint
 * @since 1.7.0
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class Model implements ConstraintAttributeInterface, TransformerInterface
{

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function check(ReflectionProperty $property, mixed $value): DatabaseModel
    {
        try {
            foreach (ReflectionUtil::types($property->getType()) as $type) {
                if (!is_subclass_of($type, DatabaseModel::class)) {
                    continue;
                }

                $instance = $type::single($value);

                if ($instance !== null) {
                    return $instance;
                }
            }

            throw new ModelConstraintException($value);
        } catch (DatabaseExceptionInterface $err) {
            throw new ModelConstraintException($value, $err);
        }
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function transform(mixed $value): string|int
    {
        if (!is_string($value) && !is_int($value)) {
            throw new InvalidValueTransformerException('Expected a valid primary key value.');
        }

        return $value;
    }

}
