<?php
declare(strict_types=1);

namespace Raxos\Http\Response;

use JsonException;
use Raxos\Http\{HttpHeader, HttpResponse, HttpResponseCode};
use Raxos\Http\Structure\HttpHeadersMap;
use RuntimeException;
use function json_encode;
use const JSON_BIGINT_AS_STRING;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

/**
 * Class JsonHttpResponse
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Response
 * @since 2.1.0
 */
final class JsonHttpResponse extends HttpResponse
{

    /**
     * JsonHttpResponse constructor.
     *
     * @param mixed $body
     * @param HttpHeadersMap $headers
     * @param HttpResponseCode $responseCode
     *
     * @author Bas Milius <bas@mili.us>
     * @since 2.1.0
     */
    public function __construct(
        public mixed $body,
        HttpHeadersMap $headers = new HttpHeadersMap(),
        HttpResponseCode $responseCode = HttpResponseCode::OK
    )
    {
        $headers->set(HttpHeader::CONTENT_TYPE, 'application/json');

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
        try {
            echo json_encode($this->body, JSON_BIGINT_AS_STRING | JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } catch (JsonException $err) {
            throw new RuntimeException("JSON encoding failed.", 500, $err);
        }
    }

}
