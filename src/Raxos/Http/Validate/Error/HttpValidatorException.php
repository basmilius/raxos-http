<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Error;

use BackedEnum;
use JetBrains\PhpStorm\ArrayShape;
use Raxos\Foundation\Error\{ExceptionId, RaxosException};
use ReflectionException;
use Throwable;
use function sprintf;

/**
 * Class HttpValidatorException
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Error
 * @since 1.7.0
 */
class HttpValidatorException extends RaxosException
{

    /**
     * RaxosException constructor.
     *
     * @param BackedEnum|ExceptionId $id
     * @param string $error
     * @param string $errorDescription
     * @param HttpConstraintException[] $errors
     * @param Throwable|null $previous
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function __construct(
        BackedEnum|ExceptionId $id,
        string $error,
        string $errorDescription,
        public readonly array $errors = [],
        ?Throwable $previous = null
    )
    {
        parent::__construct($id, $error, $errorDescription, $previous);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    #[ArrayShape([
        'code' => 'int',
        'error' => 'string',
        'error_description' => 'string',
        'errors' => 'array'
    ])]
    public function jsonSerialize(): array
    {
        $result = parent::jsonSerialize();
        $result['errors'] = $this->errors;

        return $result;
    }

    /**
     * Returns the exception for when validation fails.
     *
     * @param array $errors
     *
     * @return HttpValidatorException
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public static function errors(array $errors): HttpValidatorException
    {
        return new self(
            ExceptionId::guess(),
            'validator_errors',
            'Validation failed. Please fix the errors to continue.',
            $errors
        );
    }

    /**
     * Returns the exception for when reflection failed.
     *
     * @param ReflectionException $err
     *
     * @return self
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public static function reflection(ReflectionException $err): self
    {
        return new self(
            ExceptionId::guess(),
            'validator_reflection',
            'Reflection failed.',
            previous: $err
        );
    }

    /**
     * Returns the exception for when a model is unvalidatable.
     *
     * @param string $class
     *
     * @return self
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public static function unvalidatable(string $class): self
    {
        return new self(
            ExceptionId::guess(),
            'validator_unvalidatable',
            sprintf('Request model %s was not validatable.', $class)
        );
    }

}
