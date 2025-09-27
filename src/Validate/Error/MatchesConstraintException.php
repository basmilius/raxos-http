<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Error;

use Raxos\Contract\Http\Validate\ConstraintWithParamsExceptionInterface;
use Raxos\Error\Exception;

/**
 * Class MatchesConstraintException
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Error
 * @since 2.0.0
 */
final class MatchesConstraintException extends Exception implements ConstraintWithParamsExceptionInterface
{

    public readonly array $params;

    /**
     * Class MatchesConstraintException
     *
     * @param string $pattern
     *
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function __construct(
        public readonly string $pattern
    )
    {
        $this->params = [
            'pattern' => $this->pattern
        ];

        parent::__construct(
            'http_validation_constraint_matches',
            "Must match the pattern '{$this->pattern}'."
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
