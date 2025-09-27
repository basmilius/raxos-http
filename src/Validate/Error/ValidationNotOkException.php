<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Error;

use Raxos\Contract\Http\Validate\ValidatorExceptionInterface;
use Raxos\Error\Exception;

/**
 * Class ValidationNotOkException
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Error
 * @since 2.0.0
 */
final class ValidationNotOkException extends Exception implements ValidatorExceptionInterface
{

    /**
     * ValidationNotOkException constructor.
     *
     * @param array $errors
     *
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function __construct(
        public readonly array $errors
    )
    {
        parent::__construct(
            'http_validation_not_ok',
            'Validation failed. Please fix the errors and try again.'
        );
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function jsonSerialize(): array
    {
        return [
            ...parent::jsonSerialize(),
            'errors' => $this->errors
        ];
    }

}
