<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Error;

use JetBrains\PhpStorm\ArrayShape;
use Raxos\Foundation\Error\ExceptionId;
use function array_merge;

/**
 * Class ValidationException
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Error
 * @since 1.0.0
 */
class ValidationException extends ValidatorException
{

    /**
     * ValidationException constructor.
     *
     * @param FieldException[] $errors
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function __construct(
        public readonly array $errors
    )
    {
        parent::__construct(
            ExceptionId::for(__METHOD__),
            'validator_validation_error',
            'Request validation failed.'
        );
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    #[ArrayShape([
        'code' => 'int',
        'error' => 'string',
        'error_description' => 'string',
        'errors' => 'Raxos\Http\Validate\Error\FieldException[]'
    ])]
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'errors' => $this->errors
        ]);
    }

}
