<?php
declare(strict_types=1);

namespace Raxos\Http;

/**
 * Enum HttpMethod
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http
 * @since 1.0.0
 */
enum HttpMethod: string
{
    case ANY = 'ANY';
    case DELETE = 'DELETE';
    case GET = 'GET';
    case HEAD = 'HEAD';
    case OPTIONS = 'OPTIONS';
    case PATCH = 'PATCH';
    case POST = 'POST';
    case PUT = 'PUT';
}
