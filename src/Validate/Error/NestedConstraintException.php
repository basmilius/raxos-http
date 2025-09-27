<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Error;

use Raxos\Contract\Http\Validate\ConstraintWithParamsExceptionInterface;
use Raxos\Error\Exception;

/**
 * Class NestedConstraintException
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Error
 * @since 2.0.0
 */
final class NestedConstraintException extends Exception implements ConstraintWithParamsExceptionInterface
{

    public readonly array $params;

    /**
     * Class NestedConstraintException
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
        $this->params = [
            'property' => $this->property
        ];

        parent::__construct(
            'http_validation_constraint_nested',
            "Property {$this->property} should be a nested request model."
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
