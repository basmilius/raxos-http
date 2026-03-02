<?php
declare(strict_types=1);

namespace Raxos\Http\Response;

use Raxos\Http\{HttpResponse, HttpResponseCode};
use Raxos\Http\Structure\HttpHeadersMap;

/**
 * Class ForbiddenHttpResponse
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Response
 * @since 2.1.0
 */
final class ForbiddenHttpResponse extends HttpResponse
{

    /**
     * ForbiddenHttpResponse constructor.
     *
     * @param HttpHeadersMap $headers
     *
     * @author Bas Milius <bas@mili.us>
     * @since 2.1.0
     */
    public function __construct(
        HttpHeadersMap $headers = new HttpHeadersMap()
    )
    {
        parent::__construct(
            $headers,
            HttpResponseCode::FORBIDDEN
        );
    }

}
