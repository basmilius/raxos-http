<?php
declare(strict_types=1);

namespace Raxos\Http\Response;

use Raxos\Http\{HttpResponse, HttpResponseCode};
use Raxos\Http\Structure\HttpHeadersMap;
use RuntimeException;

/**
 * Class ResultResponse
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Response
 * @since 2.1.0
 */
final class ResultHttpResponse extends HttpResponse
{

    /**
     * JsonResponse constructor.
     *
     * @param mixed $result
     * @param HttpHeadersMap $headers
     * @param HttpResponseCode $responseCode
     *
     * @author Bas Milius <bas@mili.us>
     * @since 2.1.0
     */
    public function __construct(
        public mixed $result,
        HttpHeadersMap $headers = new HttpHeadersMap(),
        HttpResponseCode $responseCode = HttpResponseCode::OK
    )
    {
        parent::__construct(
            $headers,
            $responseCode
        );
    }

    /**
     * Turns the result response into a JSON response.
     *
     * @return HtmlHttpResponse
     * @author Bas Milius <bas@mili.us>
     * @since 2.1.0
     */
    public function asHtml(): HtmlHttpResponse
    {
        return new HtmlHttpResponse((string)$this->result, $this->headers, $this->responseCode);
    }

    /**
     * Turns the result response into a JSON response.
     *
     * @return JsonHttpResponse
     * @author Bas Milius <bas@mili.us>
     * @since 2.1.0
     */
    public function asJson(): JsonHttpResponse
    {
        return new JsonHttpResponse($this->result, $this->headers, $this->responseCode);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.1.0
     */
    public function send(): void
    {
        throw new RuntimeException('Cannot send empty result response. Use wither asHtml() or asJson().');
    }

}
