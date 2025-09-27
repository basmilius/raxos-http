<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Error;

use Raxos\Contract\Http\Validate\ConstraintWithParamsExceptionInterface;
use Raxos\Error\Exception;

/**
 * Class MaxLengthConstraintException
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Error
 * @since 2.0.0
 */
final class MaxLengthConstraintException extends Exception implements ConstraintWithParamsExceptionInterface
{

    public readonly array $params;

    /**
     * Class MaxLengthConstraintException
     *
     * @param int $max
     *
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function __construct(
        public readonly int $max
    )
    {
        $this->params = [
            'max' => $this->max
        ];

        parent::__construct(
            'http_validation_constraint_max',
            "Must have a maximum length of {$this->max}."
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
