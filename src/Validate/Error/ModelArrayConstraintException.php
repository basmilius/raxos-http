<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Error;

use Raxos\Contract\Database\DatabaseExceptionInterface;
use Raxos\Contract\Http\Validate\ConstraintExceptionInterface;
use Raxos\Error\Exception;

/**
 * Class ModelConstraintException
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Error
 * @since 2.0.0
 */
final class ModelArrayConstraintException extends Exception implements ConstraintExceptionInterface
{

    /**
     * Class ModelConstraintException
     *
     * @param DatabaseExceptionInterface|null $err
     *
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function __construct(
        public readonly ?DatabaseExceptionInterface $err = null
    )
    {
        parent::__construct(
            'http_validation_constraint_model_array',
            'Cannot find all instances.',
            previous: $this->err
        );
    }

}
