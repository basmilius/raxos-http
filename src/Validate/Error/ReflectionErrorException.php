<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Error;

use Raxos\Contract\Http\Validate\ValidatorExceptionInterface;
use Raxos\Contract\Reflection\ReflectionFailedExceptionInterface;
use Raxos\Error\Exception;
use ReflectionException;

/**
 * Class ReflectionErrorException
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Error
 * @since 2.0.0
 */
final class ReflectionErrorException extends Exception implements ValidatorExceptionInterface, ReflectionFailedExceptionInterface
{

    /**
     * ReflectionErrorException constructor.
     *
     * @param ReflectionException $err
     *
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function __construct(
        public readonly ReflectionException $err
    )
    {
        parent::__construct(
            'http_validation_reflection',
            'Validation failed due to an internal reflection error.',
            previous: $this->err
        );
    }

}
