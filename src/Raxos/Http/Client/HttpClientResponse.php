<?php
declare(strict_types=1);

namespace Raxos\Http\Client;

use JetBrains\PhpStorm\ArrayShape;
use JsonException;
use Psr\Http\Message\{ResponseInterface, StreamInterface};
use Raxos\Foundation\Contract\DebuggableInterface;
use Raxos\Http\HttpResponseCode;
use stdClass;
use function array_map;
use function json_decode;
use const JSON_THROW_ON_ERROR;

/**
 * Class HttpClientResponse
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Client
 * @since 1.0.0
 */
readonly class HttpClientResponse implements DebuggableInterface
{

    public string $protocolVersion;
    public HttpResponseCode $responseCode;
    public string $responseText;

    /**
     * HttpClientResponse constructor.
     *
     * @param HttpClient $client
     * @param HttpClientRequest $request
     * @param ResponseInterface $response
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function __construct(
        public HttpClient $client,
        public HttpClientRequest $request,
        protected ResponseInterface $response
    )
    {
        $this->protocolVersion = $response->getProtocolVersion();
        $this->responseCode = HttpResponseCode::from($response->getStatusCode());
        $this->responseText = $response->getReasonPhrase();
    }

    /**
     * Gets the response body.
     *
     * @return string
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function body(): string
    {
        return (string)$this->response->getBody();
    }

    /**
     * Gets the response body as parsed json.
     *
     * @param bool $associative
     *
     * @return array|stdClass
     * @throws JsonException
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function json(bool $associative = true): array|stdClass
    {
        return json_decode($this->body(), $associative, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Gets the body as a stream.
     *
     * @return StreamInterface
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function stream(): StreamInterface
    {
        return $this->response->getBody();
    }

    /**
     * Returns TRUE if the response code is between 400 and 500.
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function clientError(): bool
    {
        return $this->responseCode->value >= 400 && $this->responseCode->value < 500;
    }

    /**
     * Returns TRUE if the response code is between 500 and 600.
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function serverError(): bool
    {
        return $this->responseCode->value >= 500 && $this->responseCode->value < 600;
    }

    /**
     * Returns TRUE if the request failed.
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function failed(): bool
    {
        return $this->responseCode->value < 200 || $this->responseCode->value >= 300;
    }

    /**
     * Returns TRUE if the request was successful.
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function success(): bool
    {
        return $this->responseCode->value >= 200 && $this->responseCode->value < 300;
    }

    /**
     * Gets a response header.
     *
     * @param string $name
     * @param bool $single
     *
     * @return string|string[]
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function header(string $name, bool $single = true): string|array
    {
        if ($single) {
            return $this->response->getHeaderLine($name);
        }

        return $this->response->getHeader($name);
    }

    /**
     * Gets all the response headers.
     *
     * @param bool $single
     *
     * @return string[]
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function headers(bool $single = true): array
    {
        $headers = $this->response->getHeaders();

        if (!$single) {
            return $headers;
        }

        return array_map(static fn(array $header) => $header[0], $headers);
    }

    /**
     * Returns TRUE if the response headers contain the given header.
     *
     * @param string $name
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function hasHeader(string $name): bool
    {
        return $this->response->hasHeader($name);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    #[ArrayShape([
        'headers' => 'string[][]',
        'protocol_version' => 'string',
        'response_code' => 'int',
        'response_text' => 'string'
    ])]
    public final function __debugInfo(): array
    {
        return [
            'headers' => $this->response->getHeaders(),
            'protocol_version' => $this->protocolVersion,
            'response_code' => $this->responseCode,
            'response_text' => $this->responseText
        ];
    }

}
