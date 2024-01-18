<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Error;

use Raxos\Foundation\Error\RaxosException;

/**
 * Class ValidatorException
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Error
 * @since 1.0.0
 */
class ValidatorException extends RaxosException
{

    public const int ERR_INVALID_MODEL = 1;
    public const int ERR_INVALID_PROPERTY = 2;
    public const int ERR_INVALID_TYPE = 4;
    public const int ERR_FIELD_VALIDATION_FAILED = 8;
    public const int ERR_VALIDATION_FAILED = 16;
    public const int ERR_MISSING_CONSTRAINT = 32;

}
