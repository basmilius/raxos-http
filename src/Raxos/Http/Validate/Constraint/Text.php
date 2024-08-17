<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Constraint;

use Attribute;
use Raxos\Http\Validate\Error\FieldException;
use Raxos\Http\Validate\RequestField;
use function is_string;
use function mb_strlen;
use function preg_match;
use function trim;

/**
 * Class Text
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Constraint
 * @since 1.0.0
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Text extends Constraint
{

    /**
     * Text constructor.
     *
     * @param int|null $maxLength
     * @param int|null $minLength
     * @param string|null $matches
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function __construct(
        public readonly ?int $maxLength = null,
        public readonly ?int $minLength = null,
        public readonly ?string $matches = null
    ) {}

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function transform(mixed $data): string
    {
        return (string)$data;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function validate(RequestField $field, mixed $data): void
    {
        if (!is_string($data)) {
            throw new FieldException($field, '{{name}} must be a string.');
        }

        $data = trim($data);
        $length = mb_strlen($data);

        if ($length === 0 && $this->minLength !== 0) {
            if ($field->isOptional) {
                return;
            }

            throw new FieldException($field, '{{name}} is required.');
        }

        if ($this->maxLength !== null && $length > $this->maxLength) {
            throw new FieldException($field, '{{name}} must not have more than {{maxLength}} characters.', [
                'maxLength' => $this->maxLength
            ]);
        }

        if ($this->minLength !== null && $length < $this->minLength) {
            throw new FieldException($field, '{{name}} must be at least {{minLength}} characters long.', [
                'minLength' => $this->minLength
            ]);
        }

        if ($this->matches !== null && !preg_match($this->matches, $data)) {
            throw new FieldException($field, '{{name}} did not match {{pattern}}.', [
                'pattern' => $this->matches
            ]);
        }
    }

}
