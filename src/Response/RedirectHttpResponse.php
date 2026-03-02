<?php
declare(strict_types=1);

namespace Raxos\Http\Response;

use Raxos\Http\{HttpHeader, HttpResponse, HttpResponseCode};
use Raxos\Http\Structure\HttpHeadersMap;

/**
 * Class RedirectHttpResponse
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Response
 * @since 2.1.0
 */
final class RedirectHttpResponse extends HttpResponse
{

    /**
     * RedirectHttpResponse constructor.
     *
     * @param string $destination
     * @param HttpHeadersMap $headers
     * @param HttpResponseCode $responseCode
     *
     * @author Bas Milius <bas@mili.us>
     * @since 2.1.0
     */
    public function __construct(
        public string $destination,
        HttpHeadersMap $headers = new HttpHeadersMap(),
        HttpResponseCode $responseCode = HttpResponseCode::FOUND
    )
    {
        $headers->set(HttpHeader::LOCATION, $this->destination);

        parent::__construct(
            $headers,
            $responseCode
        );
    }

}
