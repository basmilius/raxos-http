<?php
declare(strict_types=1);

namespace Raxos\Http\Client;

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\ExpectedValues;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Raxos\Foundation\PHP\MagicMethods\DebugInfoInterface;
use Raxos\Http\HttpCode;
use stdClass;
use function array_map;
use function json_decode;

/**
 * Class HttpClientResponse
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Client
 * @since 1.0.0
 */
class HttpClientResponse implements DebugInfoInterface
{

    protected string $protocolVersion;
    protected int $responseCode;
    protected string $responseText;

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
    public function __construct(protected HttpClient $client, protected HttpClientRequest $request, protected ResponseInterface $response)
    {
        $this->protocolVersion = $response->getProtocolVersion();
        $this->responseCode = $response->getStatusCode();
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
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function json(bool $associative = true): array|stdClass
    {
        return json_decode($this->body(), $associative);
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
        return $this->responseCode >= 400 && $this->responseCode < 500;
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
        return $this->responseCode >= 500 && $this->responseCode < 600;
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
        return $this->response < 200 || $this->responseCode >= 300;
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
        return $this->responseCode >= 200 && $this->responseCode < 300;
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

        return array_map(fn(array $header) => $header[0], $headers);
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
     * Gets the response code.
     *
     * @return int
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    #[ExpectedValues(valuesFromClass: HttpCode::class)]
    public function responseCode(): int
    {
        return $this->responseCode;
    }

    /**
     * Gets the response text.
     *
     * @return string
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function responseText(): string
    {
        return $this->responseText;
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
