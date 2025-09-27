<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Error;

use Raxos\Contract\Http\Validate\ConstraintWithParamsExceptionInterface;
use Raxos\Error\Exception;

/**
 * Class MinLengthConstraintException
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Error
 * @since 2.0.0
 */
final class MinLengthConstraintException extends Exception implements ConstraintWithParamsExceptionInterface
{

    public readonly array $params;

    /**
     * Class MinLengthConstraintException
     *
     * @param int $min
     *
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function __construct(
        public readonly int $min
    )
    {
        $this->params = [
            'min' => $this->min
        ];

        parent::__construct(
            'http_validation_constraint_min',
            "Must have a minimum length of {$this->min}."
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
