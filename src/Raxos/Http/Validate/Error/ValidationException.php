<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Error;

use JetBrains\PhpStorm\ArrayShape;
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
    public function __construct(private array $errors)
    {
        parent::__construct('Request validation failed.', self::ERR_VALIDATION_FAILED);
    }

    /**
     * Gets the errors.
     *
     * @return FieldException[]
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function getErrors(): array
    {
        return $this->errors;
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
            'errors' => $this->errors
        ]);
    }

}
