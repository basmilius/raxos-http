<?php
declare(strict_types=1);

namespace Raxos\Http\Client;

use GuzzleHttp\Exception\GuzzleException;
use Raxos\Foundation\Error\{ExceptionId, RaxosException};

/**
 * Class HttpClientException
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Client
 * @since 1.0.17
 */
final class HttpClientException extends RaxosException
{

    /**
     * Returns a bad call exception.
     *
     * @param string $message
     *
     * @return self
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.17
     */
    public static function badCall(string $message): self
    {
        return new self(
            ExceptionId::for(__METHOD__),
            'http_client_bad_call',
            $message
        );
    }

    /**
     * Returns a request failed exception.
     *
     * @param GuzzleException $err
     *
     * @return self
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.17
     */
    public static function requestFailed(GuzzleException $err): self
    {
        return new self(
            ExceptionId::for(__METHOD__),
            'http_client_request_failed',
            'The HTTP request failed.',
            $err
        );
    }

}
