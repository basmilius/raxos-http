<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Error;

use JetBrains\PhpStorm\ArrayShape;
use Raxos\Http\Validate\RequestField;
use function array_merge;

/**
 * Class FieldModelException
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Error
 * @since 1.0.0
 */
class FieldModelException extends FieldException
{

    /**
     * FieldModelException constructor.
     *
     * @param RequestField $field
     * @param ValidationException $err
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function __construct(RequestField $field, private readonly ValidationException $err)
    {
        parent::__construct($field, '{{name}} contains errors.');
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    #[ArrayShape([
        'error' => 'int',
        'error_description' => 'string',
        'errors' => 'Raxos\Http\Validate\Error\FieldException[]'
    ])]
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'errors' => $this->err->errors
        ]);
    }

}
