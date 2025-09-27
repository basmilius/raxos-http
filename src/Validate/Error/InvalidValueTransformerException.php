<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Error;

use Raxos\Contract\Http\Validate\TransformerExceptionInterface;
use Raxos\Error\Exception;

/**
 * Class InvalidValueTransformerException
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Error
 * @since 2.0.0
 */
final class InvalidValueTransformerException extends Exception implements TransformerExceptionInterface
{

    /**
     * InvalidValueTransformerException constructor.
     *
     * @param string $message
     *
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function __construct(string $message)
    {
        parent::__construct(
            'http_validation_transformer_invalid_value',
            $message,
        );
    }

}
