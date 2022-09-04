<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Error;

use JetBrains\PhpStorm\ArrayShape;
use Raxos\Http\Validate\RequestField;
use function str_replace;

/**
 * Class FieldException
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Error
 * @since 1.0.0
 */
class FieldException extends ValidatorException
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
        parent::__construct($message, self::ERR_FIELD_VALIDATION_FAILED);

        $params['name'] = $field->field->name;

        $this->params = $params;
    }

    /**
     * Renders the message.
     *
     * @return string
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function render(): string
    {
        $message = $this->message;

        foreach ($this->params as $key => $value) {
            $message = str_replace("{{{$key}}}", (string)$value, $message);
        }

        return $message;
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
