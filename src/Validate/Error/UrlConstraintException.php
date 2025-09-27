<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Error;

use Raxos\Contract\Http\Validate\ConstraintExceptionInterface;
use Raxos\Error\Exception;

/**
 * Class UrlConstraintException
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Error
 * @since 2.0.0
 */
final class UrlConstraintException extends Exception implements ConstraintExceptionInterface
{

    /**
     * Class UrlConstraintException
     *
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function __construct()
    {
        parent::__construct(
            'http_validation_constraint_url',
            'Must be a valid URL.'
        );
    }

}
