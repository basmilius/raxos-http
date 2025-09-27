<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Error;

use Raxos\Contract\Http\Validate\ConstraintExceptionInterface;
use Raxos\Error\Exception;

/**
 * Class MissingConstraintException
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Error
 * @since 2.0.0
 */
final class MissingConstraintException extends Exception implements ConstraintExceptionInterface
{

    /**
     * Class MissingConstraintException
     *
     * @param string $property
     *
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function __construct(
        public readonly string $property
    )
    {
        parent::__construct(
            'http_validation_constraint_missing',
            "Missing required property {$this->property}."
        );
    }

}
