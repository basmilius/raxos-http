<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Constraint;

use Attribute;
use Raxos\Http\Validate\Error\FieldException;
use Raxos\Http\Validate\RequestField;
use function in_array;

/**
 * Class Text
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Constraint
 * @since 1.0.0
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Choice extends Text
{

    /**
     * Choice constructor.
     *
     * @param string[] $values
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function __construct(
        protected array $values = []
    )
    {
        parent::__construct();
    }

    /**
     * Gets the possible values.
     *
     * @return string[]
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function getValues(): array
    {
        return $this->values;
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

        if (!in_array($data, $this->values)) {
            throw new FieldException($field, '{{name}} must be one of the predefined options.', []);
        }
    }

}
