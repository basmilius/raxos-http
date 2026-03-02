<?php
declare(strict_types=1);

namespace Raxos\Http\Response;

use JetBrains\PhpStorm\Language;
use Raxos\Http\{HttpHeader, HttpResponse, HttpResponseCode};
use Raxos\Http\Structure\HttpHeadersMap;

/**
 * Class HtmlHttpResponse
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Response
 * @since 2.1.0
 */
final class HtmlHttpResponse extends HttpResponse
{

    /**
     * HtmlHttpResponse constructor.
     *
     * @param string $body
     * @param HttpHeadersMap $headers
     * @param HttpResponseCode $responseCode
     *
     * @author Bas Milius <bas@mili.us>
     * @since 2.1.0
     */
    public function __construct(
        #[Language('HTML')] public string $body,
        HttpHeadersMap $headers = new HttpHeadersMap(),
        HttpResponseCode $responseCode = HttpResponseCode::OK
    )
    {
        $headers->set(HttpHeader::CONTENT_TYPE, 'text/html');

        parent::__construct(
            $headers,
            $responseCode
        );
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.1.0
     */
    protected function sendBody(): void
    {
        echo $this->body;
    }

}
