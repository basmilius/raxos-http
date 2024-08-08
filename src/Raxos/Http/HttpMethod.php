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
    case ANY = 'any';
    case DELETE = 'delete';
    case GET = 'get';
    case HEAD = 'head';
    case OPTIONS = 'options';
    case PATCH = 'patch';
    case POST = 'post';
    case PUT = 'put';
}
