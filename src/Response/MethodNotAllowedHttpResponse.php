<?php
declare(strict_types=1);

namespace Raxos\Http\Response;

use Raxos\Http\{HttpHeader, HttpResponse, HttpResponseCode};
use Raxos\Http\Structure\HttpHeadersMap;
use function implode;

/**
 * Class MethodNotAllowedHttpResponse
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Response
 * @since 2.1.0
 */
final class MethodNotAllowedHttpResponse extends HttpResponse
{

    /**
     * MethodNotAllowedHttpResponse constructor.
     *
     * @param string[] $allowedMethods
     *
     * @author Bas Milius <bas@mili.us>
     * @since 2.1.0
     */
    public function __construct(
        array $allowedMethods = []
    )
    {
        $headers = new HttpHeadersMap();

        if (!empty($allowedMethods)) {
            $headers->set(HttpHeader::ALLOW, implode(', ', $allowedMethods));
        }

        parent::__construct(
            $headers,
            HttpResponseCode::METHOD_NOT_ALLOWED
        );
    }

}
