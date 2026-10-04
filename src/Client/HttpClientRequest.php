<?php
declare(strict_types=1);

namespace Raxos\Http\Client;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Raxos\Contract\Http\HttpClientExceptionInterface;
use Raxos\Http\Client\Error\BadCallException;
use Raxos\Http\Client\Error\RequestFailedException;
use Raxos\Http\Client\Psr7\Psr7Request;
use Raxos\Http\HttpMethod;
use function array_replace_recursive;
use function fseek;
use function ftell;
use function is_resource;
use function min;
use function stream_get_meta_data;

/**
 * Class HttpClientRequest
 *
 * Keeps headers, payloads and optional retries local to one outgoing request.
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Client
 * @since 1.0.0
 */
final class HttpClientRequest
{
    /**
     * Applies retries only to this request builder, without changing other requests from the client.
     *
     * @var RetryPolicy|null
     * @author Bas Milius <bas@mili.us>
     * @since 3.3.0
     */
    private ?RetryPolicy $retryPolicy = null;

    /**
     * Keeps request-specific transport options isolated from other request builders.
     *
     * @var array
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    private array $options = [];

    /**
     * Retains the PSR request used for dispatch through the configured transport.
     *
     * @var RequestInterface
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    private RequestInterface $request;

    /**
     * HttpClientRequest constructor.
     *
     * @param HttpClient $client
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function __construct(
        protected readonly HttpClient $client
    )
    {
        $this->request = new Psr7Request();
        $this->header('Accept-Encoding', 'gzip');
    }

    /**
     * Sets basic authentication.
     *
     * @param string $username
     * @param string $password
     *
     * @return $this
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function basicAuth(
        string $username,
        string $password
    ): self
    {
        $this->options['auth'] = [$username, $password];

        return $this;
    }

    /**
     * Sets digest authentication.
     *
     * @param string $username
     * @param string $password
     *
     * @return $this
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function digestAuth(
        string $username,
        string $password
    ): self
    {
        $this->options['auth'] = [$username, $password, 'digest'];

        return $this;
    }

    /**
     * Sets the Authorization header to "Bearer $token".
     *
     * @param string $token
     *
     * @return $this
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function bearerToken(string $token): self
    {
        return $this->header('Authorization', "Bearer {$token}");
    }

    /**
     * Sets a request header.
     *
     * @param string $name
     * @param string $value
     * @param bool $replace
     *
     * @return $this
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function header(
        string $name,
        string $value,
        bool $replace = true
    ): self
    {
        if ($replace) {
            $this->request = $this->request->withHeader($name, $value);
        } else {
            $this->request = $this->request->withAddedHeader($name, $value);
        }

        return $this;
    }

    /**
     * Sets the request options.
     *
     * @param array $options
     *
     * @return $this
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function options(array $options): self
    {
        $this->options = array_replace_recursive($this->options, $options);

        return $this;
    }

    /**
     * Sets the request timeout.
     *
     * @param float $timeout
     *
     * @return $this
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function timeout(float $timeout): self
    {
        $this->options['timeout'] = $timeout;

        return $this;
    }

    /**
     * Sets the query string.
     *
     * @param array $query
     *
     * @return $this
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function query(array $query): self
    {
        $this->options['query'] = $query;

        return $this;
    }

    /**
     * Sets the request body to the given JSON.
     *
     * @param array $json
     *
     * @return $this
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function json(array $json): self
    {
        $this->options['json'] = $json;

        return $this;
    }

    /**
     * Sets the request body to multipart data.
     *
     * @param array $data
     *
     * @return $this
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function multipart(array $data): self
    {
        $this->options['multipart'] = $data;

        return $this;
    }

    /**
     * Attaches a bounded retry policy to this builder; unsafe methods still require policy opt-in.
     *
     * @param RetryPolicy $policy
     * @return $this
     * @author Bas Milius <bas@mili.us>
     * @since 3.3.0
     */
    public function retry(RetryPolicy $policy): self
    {
        $this->retryPolicy = $policy;

        return $this;
    }

    /**
     * Performs the request.
     *
     * @param HttpMethod $method
     * @param string $uri
     *
     * @return HttpClientResponse
     * @throws HttpClientExceptionInterface
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    protected function base(
        HttpMethod $method,
        string $uri
    ): HttpClientResponse
    {
        if ($method === HttpMethod::ANY) {
            throw new BadCallException('ANY is a route matcher, not an HTTP request method.');
        }

        try {
            $this->request = $this->request->withMethod($method->value);
            $this->request = $this->request->withUri(new Uri($uri));

            if ($this->retryPolicy === null) {
                $response = $this->client->client->send($this->request, $this->options);
            } else {
                $response = $this->sendWithRetries($method);
            }

            return new HttpClientResponse($this->client, $this, $response);
        } catch (GuzzleException $err) {
            throw new RequestFailedException($err);
        }
    }

    /**
     * Restores replayable bodies before each attempt and bounds transport timeouts by the remaining budget.
     *
     * @param HttpMethod $method
     * @return ResponseInterface
     * @throws GuzzleException
     * @author Bas Milius <bas@mili.us>
     * @since 3.3.0
     */
    private function sendWithRetries(HttpMethod $method): ResponseInterface
    {
        [$replayable, $positions] = $this->bodyPositions();

        return $this->retryPolicy->execute(function (float $remaining) use ($positions): ResponseInterface {
            foreach ($positions as [$body, $position]) {
                if ($position === null) {
                    continue;
                }

                if ($body instanceof StreamInterface) {
                    $body->seek($position);
                } else {
                    fseek($body, $position);
                }
            }

            return $this->client->client->send($this->request, $this->boundedOptions($remaining));
        }, $method->value, $replayable);
    }

    /**
     * Captures initial stream positions without reading bodies; one non-seekable part disables replay.
     *
     * @return array{bool, list<array{StreamInterface|resource, int|null}>}
     * @author Bas Milius <bas@mili.us>
     * @since 3.3.0
     */
    private function bodyPositions(): array
    {
        // Retain stream wrappers so Guzzle cannot close a resource between attempts.
        if (is_resource($this->options['body'] ?? null)) {
            $this->options['body'] = Utils::streamFor($this->options['body']);
        }

        $bodies = [$this->request->getBody(), $this->options['body'] ?? null];

        foreach ($this->options['multipart'] ?? [] as $index => $part) {
            $body = $part['contents'] ?? null;

            if (is_resource($body)) {
                $body = Utils::streamFor($body);
                $this->options['multipart'][$index]['contents'] = $body;
            }

            $bodies[] = $body;
        }

        $positions = [];
        $replayable = true;

        foreach ($bodies as $body) {
            if ($body instanceof StreamInterface) {
                $seekable = $body->isSeekable();
                $position = $seekable ? $body->tell() : null;
            } elseif (is_resource($body)) {
                $seekable = stream_get_meta_data($body)['seekable'];
                $position = $seekable ? ftell($body) : null;
            } else {
                continue;
            }

            $replayable = $replayable && $seekable;
            $positions[] = [$body, $position];
        }

        return [$replayable, $positions];
    }

    /**
     * Caps request and connection timeouts without changing the builder's configured options.
     *
     * @param float $remaining
     * @return array<string, mixed>
     * @author Bas Milius <bas@mili.us>
     * @since 3.3.0
     */
    private function boundedOptions(float $remaining): array
    {
        $options = $this->options;
        $timeout = $options['timeout'] ?? $this->client->client->getConfig('timeout');
        $connectTimeout = $options['connect_timeout'] ?? $this->client->client->getConfig('connect_timeout');
        $options['timeout'] = $timeout > 0 ? min((float)$timeout, $remaining) : $remaining;
        $options['connect_timeout'] = $connectTimeout > 0 ? min((float)$connectTimeout, $remaining) : $remaining;

        return $options;
    }

    /**
     * Performs a GET request to the given uri.
     *
     * @param string $uri
     * @param array|null $query
     *
     * @return HttpClientResponse
     * @throws HttpClientExceptionInterface
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function get(
        string $uri,
        ?array $query = null
    ): HttpClientResponse
    {
        if ($query !== null) {
            $this->query($query);
        }

        return $this->base(HttpMethod::GET, $uri);
    }

    /**
     * Performs a POST request to the given uri.
     *
     * @param string $uri
     * @param array|null $json
     *
     * @return HttpClientResponse
     * @throws HttpClientExceptionInterface
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function post(
        string $uri,
        ?array $json = null
    ): HttpClientResponse
    {
        if ($json !== null) {
            $this->json($json);
        }

        return $this->base(HttpMethod::POST, $uri);
    }

    /**
     * Performs a DELETE request to the given uri.
     *
     * @param string $uri
     *
     * @return HttpClientResponse
     * @throws HttpClientExceptionInterface
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function delete(string $uri): HttpClientResponse
    {
        return $this->base(HttpMethod::DELETE, $uri);
    }

    /**
     * Dispatches a concrete HTTP method using this builder's options and retry policy.
     *
     * @param HttpMethod $method
     * @param string $uri
     * @return HttpClientResponse
     * @throws HttpClientExceptionInterface
     * @author Bas Milius <bas@mili.us>
     * @since 3.3.0
     */
    public function send(
        HttpMethod $method,
        string $uri
    ): HttpClientResponse
    {
        return $this->base($method, $uri);
    }

    /**
     * Sends this builder as PUT; seekable request bodies can be replayed under a retry policy.
     *
     * @param string $uri
     * @param array|null $json
     * @return HttpClientResponse
     * @throws HttpClientExceptionInterface
     * @author Bas Milius <bas@mili.us>
     * @since 3.3.0
     */
    public function put(
        string $uri,
        ?array $json = null
    ): HttpClientResponse
    {
        if ($json !== null) {
            $this->json($json);
        }

        return $this->base(HttpMethod::PUT, $uri);
    }

    /**
     * Sends this builder as PATCH; retries require explicit opt-in for unsafe methods.
     *
     * @param string $uri
     * @param array|null $json
     * @return HttpClientResponse
     * @throws HttpClientExceptionInterface
     * @author Bas Milius <bas@mili.us>
     * @since 3.3.0
     */
    public function patch(
        string $uri,
        ?array $json = null
    ): HttpClientResponse
    {
        if ($json !== null) {
            $this->json($json);
        }

        return $this->base(HttpMethod::PATCH, $uri);
    }

    /**
     * Sends this builder as HEAD without changing the options of other requests.
     *
     * @param string $uri
     * @return HttpClientResponse
     * @throws HttpClientExceptionInterface
     * @author Bas Milius <bas@mili.us>
     * @since 3.3.0
     */
    public function head(string $uri): HttpClientResponse
    {
        return $this->base(HttpMethod::HEAD, $uri);
    }

    /**
     * Sends an OPTIONS request; options() remains the builder configuration method.
     *
     * @param string $uri
     * @return HttpClientResponse
     * @throws HttpClientExceptionInterface
     * @author Bas Milius <bas@mili.us>
     * @since 3.3.0
     */
    public function optionsRequest(string $uri): HttpClientResponse
    {
        return $this->base(HttpMethod::OPTIONS, $uri);
    }

    /**
     * Sends this builder as TRACE through the configured HTTP transport.
     *
     * @param string $uri
     * @return HttpClientResponse
     * @throws HttpClientExceptionInterface
     * @author Bas Milius <bas@mili.us>
     * @since 3.3.0
     */
    public function trace(string $uri): HttpClientResponse
    {
        return $this->base(HttpMethod::TRACE, $uri);
    }

    /**
     * Sends this builder as CONNECT; any retries require explicit policy opt-in.
     *
     * @param string $uri
     * @return HttpClientResponse
     * @throws HttpClientExceptionInterface
     * @author Bas Milius <bas@mili.us>
     * @since 3.3.0
     */
    public function connect(string $uri): HttpClientResponse
    {
        return $this->base(HttpMethod::CONNECT, $uri);
    }
}
