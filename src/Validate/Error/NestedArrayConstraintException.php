<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Error;

use Raxos\Contract\Http\Validate\ConstraintWithParamsExceptionInterface;
use Raxos\Error\Exception;

/**
 * Class NestedArrayConstraintException
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Error
 * @since 2.0.0
 */
final class NestedArrayConstraintException extends Exception implements ConstraintWithParamsExceptionInterface
{

    public readonly array $params;

    /**
     * Class NestedArrayConstraintException
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
            'http_validation_constraint_nested_array',
            "Property {$this->property} should be a nested request model array."
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
