<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Error;

use Raxos\Contract\Http\Validate\ValidatorExceptionInterface;
use Raxos\Error\Exception;

/**
 * Class UnvalidatableException
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Error
 * @since 2.0.0
 */
final class UnvalidatableException extends Exception implements ValidatorExceptionInterface
{

    /**
     * UnvalidatableException constructor.
     *
     * @param string $requestModel
     *
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function __construct(
        public readonly string $requestModel
    )
    {
        parent::__construct(
            'http_validation_unvalidatable',
            "Cannot validate {$this->requestModel} because it is not a valid request model."
        );
    }

}
