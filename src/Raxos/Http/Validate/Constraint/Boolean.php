<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Constraint;

use Attribute;
use Raxos\Http\Validate\Error\FieldException;
use Raxos\Http\Validate\RequestField;
use function in_array;

/**
 * Class Boolean
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Constraint
 * @since 1.0.0
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Boolean extends Constraint
{

    private const FALSE = [false, 0, '0', 'false', 'no', 'off'];
    private const TRUE = [true, 1, '1', 'true', 'yes', 'on'];

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function transform(mixed $data): bool
    {
        return in_array($data, self::TRUE, true);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function validate(RequestField $field, mixed $data): void
    {
        if (!in_array($data, self::TRUE, true) && !in_array($data, self::FALSE, true)) {
            throw new FieldException($field, '{{name}} is not a boolean value.');
        }
    }

}
