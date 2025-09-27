<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Error;

use Raxos\Contract\Http\Validate\ConstraintWithParamsExceptionInterface;
use Raxos\Error\Exception;

/**
 * Class ConstraintErrorException
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Error
 * @since 2.0.0
 */
final class ConstraintErrorException extends Exception implements ConstraintWithParamsExceptionInterface
{

    /**
     * ConstraintErrorException constructor.
     *
     * @param string $error
     * @param string $errorDescription
     * @param array $params
     *
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function __construct(
        string $error,
        string $errorDescription,
        public readonly array $params = []
    )
    {
        parent::__construct($error, $errorDescription);
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
