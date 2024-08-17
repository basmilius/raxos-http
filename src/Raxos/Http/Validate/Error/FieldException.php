<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Error;

use JetBrains\PhpStorm\ArrayShape;
use Raxos\Foundation\Error\ExceptionId;
use Raxos\Http\Validate\RequestField;

/**
 * Class FieldException
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Error
 * @since 1.0.0
 */
final class FieldException extends ValidatorException
{

    public readonly array $params;

    /**
     * FieldException constructor.
     *
     * @param RequestField $field
     * @param string $message
     * @param array $params
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function __construct(
        public readonly RequestField $field,
        string $message,
        array $params = []
    )
    {
        parent::__construct(
            ExceptionId::for(__METHOD__),
            'validator_field_error',
            $message
        );

        $params['name'] = $field->field->name;

        $this->params = $params;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    #[ArrayShape([
        'message' => 'string',
        'params' => 'array'
    ])]
    public function jsonSerialize(): array
    {
        return [
            'message' => $this->message,
            'params' => $this->params
        ];
    }

}
