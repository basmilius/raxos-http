<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Error;

use Raxos\Contract\Database\DatabaseExceptionInterface;
use Raxos\Contract\Http\Validate\ConstraintWithParamsExceptionInterface;
use Raxos\Error\Exception;

/**
 * Class ModelConstraintException
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Error
 * @since 2.0.0
 */
final class ModelConstraintException extends Exception implements ConstraintWithParamsExceptionInterface
{

    public readonly array $params;

    /**
     * Class ModelConstraintException
     *
     * @param int $id
     * @param DatabaseExceptionInterface|null $err
     *
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function __construct(
        public readonly int $id,
        public readonly ?DatabaseExceptionInterface $err = null
    )
    {
        $this->params = [
            'id' => $this->id
        ];

        parent::__construct(
            'http_validation_constraint_model',
            "Cannot find an instance with id {$this->id}.",
            previous: $this->err
        );
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function jsonSerialize(): array
    {
        return [
            ...parent::jsonSerialize(),
            'params' => $this->params
        ];
    }

}
