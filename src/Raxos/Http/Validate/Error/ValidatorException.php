<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Error;

use Raxos\Foundation\Error\{ExceptionId, RaxosException};

/**
 * Class ValidatorException
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Error
 * @since 1.0.17
 */
class ValidatorException extends RaxosException
{

    /**
     * Returns an immutable exception.
     *
     * @param string $message
     *
     * @return self
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.17
     */
    public static function immutable(string $message): self
    {
        return new self(
            ExceptionId::for(__METHOD__),
            'validator_immutable',
            $message
        );
    }

    /**
     * Returns an invalid model exception.
     *
     * @param string $message
     *
     * @return self
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.17
     */
    public static function invalidModel(string $message): self
    {
        return new self(
            ExceptionId::for(__METHOD__),
            'validator_invalid_model',
            $message
        );
    }

    /**
     * Returns an invalid property exception.
     *
     * @param string $message
     *
     * @return self
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.17
     */
    public static function invalidProperty(string $message): self
    {
        return new self(
            ExceptionId::for(__METHOD__),
            'validator_invalid_property',
            $message
        );
    }

    /**
     * Returns an invalid type exception.
     *
     * @param string $message
     *
     * @return self
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.17
     */
    public static function invalidType(string $message): self
    {
        return new self(
            ExceptionId::for(__METHOD__),
            'validator_invalid_type',
            $message
        );
    }

    /**
     * Returns a missing constraint exception.
     *
     * @param string $message
     *
     * @return self
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.17
     */
    public static function missingConstraint(string $message): self
    {
        return new self(
            ExceptionId::for(__METHOD__),
            'validator_missing_constraint',
            $message
        );
    }

}
