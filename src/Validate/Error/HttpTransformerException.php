<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Error;

use Raxos\Foundation\Error\{ExceptionId, RaxosException};

/**
 * Class HttpTransformerException
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Error
 * @since 1.7.0
 */
final class HttpTransformerException extends RaxosException
{

    /**
     * Returns the exception for when an invalid value was given.
     *
     * @param string $message
     *
     * @return self
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public static function invalidValue(string $message): self
    {
        return new self(
            ExceptionId::guess(),
            'validate_transform_invalid_value',
            $message
        );
    }

}
