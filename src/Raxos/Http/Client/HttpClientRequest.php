<?php
declare(strict_types=1);

namespace Raxos\Http\Client;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;
use Raxos\Http\Client\Psr7\Psr7Request;
use Raxos\Http\HttpMethod;
use function array_merge_recursive;

/**
 * Class HttpClientRequest
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Client
 * @since 1.0.0
 */
class HttpClientRequest
{

    private array $options = [];
    private RequestInterface $request;

    /**
     * HttpClientRequest constructor.
     *
     * @param HttpClient $client
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function __construct(protected HttpClient $client)
    {
        $this->request = new Psr7Request;

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
    public function basicAuth(string $username, string $password): static
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
    public function digestAuth(string $username, string $password): static
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
    public function bearerToken(string $token): static
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
    public function header(string $name, string $value, bool $replace = true): static
    {
        if ($replace) {
            $this->request = $this->request->withHeader($name, $value);
        } else {
            $this->request = $this->request->withADdedHeader($name, $value);
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
    public function options(array $options): static
    {
        $this->options = array_merge_recursive($this->options, $options);

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
    public function timeout(float $timeout): static
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
    public function query(array $query): static
    {
        $this->options['query'] = $query;

        return $this;
    }

    /**
     * Sets the request body to the given json.
     *
     * @param array $json
     *
     * @return $this
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function json(array $json): static
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
    public function multipart(array $data): static
    {
        $this->options['multipart'] = $data;

        return $this;
    }

    /**
     * Performs the request.
     *
     * @param HttpMethod $method
     * @param string $uri
     *
     * @return HttpClientResponse
     * @throws HttpClientException
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    protected function base(HttpMethod $method, string $uri): HttpClientResponse
    {
        try {
            $uri = new Uri($uri);

            $this->request->withMethod($method->value);
            $this->request->withUri($uri);

            $response = $this->client->getClient()->send($this->request, $this->options);

            return new HttpClientResponse($this->client, $this, $response);
        } catch (GuzzleException $err) {
            throw new HttpClientException('Request failed.', HttpClientException::ERR_REQUEST_FAILED, $err);
        }
    }

    /**
     * Performs a GET request to the given uri.
     *
     * @param string $uri
     * @param array|null $query
     *
     * @return HttpClientResponse
     * @throws HttpClientException
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function get(string $uri, ?array $query = null): HttpClientResponse
    {
        if ($query !== null) {
            $this->query($query);
        }

        return $this->base(HttpMethod::GET, $uri);
    }

    /**
     * Performs a GET request to the given uri.
     *
     * @param string $uri
     * @param array|null $json
     *
     * @return HttpClientResponse
     * @throws HttpClientException
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function post(string $uri, ?array $json = null): HttpClientResponse
    {
        if ($json !== null) {
            $this->json($json);
        }

        return $this->base(HttpMethod::POST, $uri);
    }

    /**
     * Performs a GET request to the given uri.
     *
     * @param string $uri
     *
     * @return HttpClientResponse
     * @throws HttpClientException
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function delete(string $uri): HttpClientResponse
    {
        return $this->base(HttpMethod::DELETE, $uri);
    }

}
