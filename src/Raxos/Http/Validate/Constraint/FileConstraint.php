<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Constraint;

use Raxos\Http\HttpFile;
use Raxos\Http\Validate\Error\FieldException;
use Raxos\Http\Validate\RequestField;

/**
 * Class FileConstraint
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Constraint
 * @since 1.0.0
 */
final class FileConstraint extends Constraint
{

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function transform(mixed $data): mixed
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function validate(RequestField $field, mixed $data): void
    {
        if ($field->isOptional && $data === null) {
            return;
        }

        if (!($data instanceof HttpFile)) {
            throw new FieldException($field, '{{name}} must be a file.', []);
        }

        if (!$data->isValid) {
            throw new FieldException($field, '{{name}} failed to upload.', []);
        }
    }

}
