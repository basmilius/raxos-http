<?php
declare(strict_types=1);

namespace Raxos\Http;

use Raxos\Contract\Http\HttpResponseInterface;
use Raxos\Http\Structure\HttpHeadersMap;
use function fastcgi_finish_request;
use function function_exists;
use function header;
use function http_response_code;
use function ob_end_flush;
use function ob_flush;
use function ob_start;

/**
 * Class HttpResponse
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http
 * @since 2.1.0
 */
abstract class HttpResponse implements HttpResponseInterface
{

    /**
     * HttpResponse constructor.
     *
     * @param HttpHeadersMap $headers
     * @param HttpResponseCode $responseCode
     *
     * @author Bas Milius <bas@mili.us>
     * @since 2.1.0
     */
    public function __construct(
        public HttpHeadersMap $headers = new HttpHeadersMap(),
        public HttpResponseCode $responseCode = HttpResponseCode::OK
    ) {}

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.1.0
     */
    public function header(
        string $name,
        string $value,
        bool $replace = false
    ): static
    {
        if ($replace) {
            $this->headers->set($name, $value);
        } else {
            $this->headers->add($name, $value);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.1.0
     */
    public function responseCode(HttpResponseCode $responseCode): static
    {
        $this->responseCode = $responseCode;

        return $this;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.1.0
     */
    public function send(): void
    {
        ob_start();

        $this->sendResponseCode();
        $this->sendHeaders();
        ob_flush();

        $this->sendBody();
        ob_end_flush();

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
    }

    /**
     * Sends the body.
     *
     * @return void
     * @author Bas Milius <bas@mili.us>
     * @since 02-03-2026
     */
    protected function sendBody(): void {}

    /**
     * Sends the headers.
     *
     * @return void
     * @author Bas Milius <bas@mili.us>
     * @since 02-03-2026
     */
    protected function sendHeaders(): void
    {
        if ($this->headers->has(HttpHeader::CONTENT_DISPOSITION)) {
            $this->headers->add(HttpHeader::ACCESS_CONTROL_EXPOSE_HEADERS, HttpHeader::CONTENT_DISPOSITION);
        }

        foreach ($this->headers as $name => $values) {
            foreach ($values as $index => $value) {
                header("{$name}: {$value}", $index === 0);
            }
        }
    }

    /**
     * Sends the response code.
     *
     * @return void
     * @author Bas Milius <bas@mili.us>
     * @since 02-03-2026
     */
    protected function sendResponseCode(): void
    {
        http_response_code($this->responseCode->value);
    }

}
