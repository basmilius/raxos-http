<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Error;

use BackedEnum;
use JetBrains\PhpStorm\ArrayShape;
use Raxos\Database\Error\DatabaseException;
use Raxos\Foundation\Error\{ExceptionId, RaxosException};
use Throwable;
use function sprintf;

/**
 * Class HttpConstraintException
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Error
 * @since 1.7.0
 */
final class HttpConstraintException extends RaxosException
{

    /**
     * RaxosException constructor.
     *
     * @param BackedEnum|ExceptionId $id
     * @param string $error
     * @param string $errorDescription
     * @param array<string, string|int|float|bool> $params
     * @param Throwable|null $previous
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function __construct(
        BackedEnum|ExceptionId $id,
        string $error,
        string $errorDescription,
        public readonly array $params = [],
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
        'params' => 'array'
    ])]
    public function jsonSerialize(): array
    {
        $result = parent::jsonSerialize();
        $result['params'] = $this->params;

        return $result;
    }

    /**
     * Returns the exception for when the choice constraint failed.
     *
     * @return self
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public static function choice(): self
    {
        return new self(
            ExceptionId::guess(),
            'choice',
            'Must be one of the valid options.'
        );
    }

    /**
     * Returns the exception for when the email constraint fails.
     *
     * @return self
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public static function email(): self
    {
        return new self(
            ExceptionId::guess(),
            'email',
            'Must be a valid email address.'
        );
    }

    /**
     * Returns the exception for when the matches constraint fails.
     *
     * @param string $pattern
     *
     * @return self
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public static function matches(string $pattern): self
    {
        return new self(
            ExceptionId::guess(),
            'matches',
            sprintf('Must match the pattern `%s`.', $pattern)
        );
    }

    /**
     * Returns the exception for when the maximum value is not met.
     *
     * @param int $max
     *
     * @return self
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public static function max(int $max): self
    {
        return new self(
            ExceptionId::guess(),
            'max',
            sprintf('Must have a maximum of %d.', $max),
            ['max' => $max]
        );
    }

    /**
     * Returns the exception for when the maximum length is not met.
     *
     * @param int $max
     *
     * @return self
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public static function maxLength(int $max): self
    {
        return new self(
            ExceptionId::guess(),
            'max_length',
            sprintf('Must have a maximum length of %d.', $max),
            ['max' => $max]
        );
    }

    /**
     * Returns the exception for when the minimum value is not met.
     *
     * @param int $min
     *
     * @return self
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public static function min(int $min): self
    {
        return new self(
            ExceptionId::guess(),
            'min',
            sprintf('Must have a minimum of %d.', $min),
            ['min' => $min]
        );
    }

    /**
     * Returns the exception for when the minimum length is not met.
     *
     * @param int $min
     *
     * @return self
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public static function minLength(int $min): self
    {
        return new self(
            ExceptionId::guess(),
            'min_length',
            sprintf('Must have a minimum length of %d.', $min),
            ['min' => $min]
        );
    }

    /**
     * Returns the exception for when a property is missing.
     *
     * @param string $property
     *
     * @return self
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public static function missing(string $property): self
    {
        return new self(
            ExceptionId::guess(),
            'missing',
            sprintf('Missing required property %s.', $property),
            ['property' => $property]
        );
    }

    /**
     * Returns the exception for when a database model instance was not found.
     *
     * @param string|int $id
     * @param DatabaseException|null $err
     *
     * @return self
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public static function model(string|int $id, ?DatabaseException $err = null): self
    {
        return new self(
            ExceptionId::guess(),
            'model',
            sprintf('Could not find an instance with id %s.', $id),
            ['id' => $id],
            $err
        );
    }

    /**
     * Returns the exception for when one of the requested database model instances was not found.
     *
     * @param DatabaseException|null $err
     *
     * @return self
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public static function modelArray(?DatabaseException $err = null): self
    {
        return new self(
            ExceptionId::guess(),
            'model_array',
            'Could not find all instances.',
            previous: $err
        );
    }

    /**
     * Returns a constraint exception from the given params.
     *
     * @param string $error
     * @param string $errorDescription
     * @param array<string, string|int|float|bool> $params
     * @param Throwable|null $previous
     *
     * @return self
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public static function of(string $error, string $errorDescription, array $params = [], ?Throwable $previous = null): self
    {
        return new self(
            ExceptionId::guess(),
            $error,
            $errorDescription,
            $params,
            $previous
        );
    }

    /**
     * Returns the exception for when a file upload fails.
     *
     * @return self
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public static function upload(): self
    {
        return new self(
            ExceptionId::guess(),
            'upload',
            'A file upload failed.'
        );
    }

    /**
     * Returns the exception for when the url constraint fails.
     *
     * @return self
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public static function url(): self
    {
        return new self(
            ExceptionId::guess(),
            'url',
            'Must be a valid URL.'
        );
    }

}
