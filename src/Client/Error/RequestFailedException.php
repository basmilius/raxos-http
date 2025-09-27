<?php
declare(strict_types=1);

namespace Raxos\Http\Client\Error;

use GuzzleHttp\Exception\GuzzleException;
use Raxos\Contract\Http\HttpClientExceptionInterface;
use Raxos\Error\Exception;

/**
 * Class RequestFailedException
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Client\Error
 * @since 2.0.0
 */
final class RequestFailedException extends Exception implements HttpClientExceptionInterface
{

    /**
     * RequestFailedException constructor.
     *
     * @param GuzzleException $err
     *
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function __construct(
        public readonly GuzzleException $err
    )
    {
        parent::__construct(
            'http_client_bad_call',
            'The HTTP request failed with an exception.',
            previous: $err
        );
    }

}
