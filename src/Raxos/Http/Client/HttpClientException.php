<?php
declare(strict_types=1);

namespace Raxos\Http\Client;

use Raxos\Foundation\Error\RaxosException;

/**
 * Class HttpClientException
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Client
 * @since 1.0.0
 */
final class HttpClientException extends RaxosException
{

    public const ERR_REQUEST_FAILED = 1;
    public const ERR_BAD_METHOD_CALL = 2;

}
