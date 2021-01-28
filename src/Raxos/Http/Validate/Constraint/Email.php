<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Constraint;

use Attribute;
use Raxos\Http\Validate\Error\FieldException;
use Raxos\Http\Validate\RequestField;
use function filter_var;
use const FILTER_VALIDATE_EMAIL;

/**
 * Class Email
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Constraint
 * @since 1.0.0
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Email extends Text
{

    /**
     * Email constructor.
     *
     * @param int|null $maxLength
     * @param int|null $minLength
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function __construct(
        ?int $maxLength = null,
        ?int $minLength = null
    )
    {
        parent::__construct($maxLength, $minLength);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function validate(RequestField $field, mixed $data): void
    {
        parent::validate($field, $data);

        $data = (string)$data;

        if (!filter_var($data, FILTER_VALIDATE_EMAIL)) {
            throw new FieldException($field, '{{name}} is not a valid e-mail address.');
        }
    }

}
