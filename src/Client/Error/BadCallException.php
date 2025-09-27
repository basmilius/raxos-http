<?php
declare(strict_types=1);

namespace Raxos\Http\Client\Error;

use Raxos\Contract\Http\HttpClientExceptionInterface;
use Raxos\Error\Exception;

/**
 * Class BadCallException
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Client\Error
 * @since 2.0.0
 */
final class BadCallException extends Exception implements HttpClientExceptionInterface
{

    /**
     * BadCallException constructor.
     *
     * @param string $message
     *
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function __construct(string $message)
    {
        parent::__construct(
            'http_client_bad_call',
            $message
        );
    }

}
