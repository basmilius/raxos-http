<?php
declare(strict_types=1);

namespace Raxos\Http\Client;

use GuzzleHttp\Client as GuzzleClient;
use function method_exists;
use function sprintf;

/**
 * Class HttpClient
 *
 * @mixin HttpClientRequest
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Client
 * @since 1.0.0
 */
class HttpClient
{

    protected GuzzleClient $client;

    /**
     * HttpClient constructor.
     *
     * @param string|null $baseUrl
     * @param float $timeout
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function __construct(string $baseUrl = null, float $timeout = 5.0)
    {
        $this->client = new GuzzleClient([
            'base_uri' => $baseUrl,
            'http_errors' => false,
            'timeout' => $timeout
        ]);
    }

    /**
     * Gets the Guzzle client.
     *
     * @return GuzzleClient
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function getClient(): GuzzleClient
    {
        return $this->client;
    }

    /**
     * Creates a new request instance.
     *
     * @return HttpClientRequest
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    protected function request(): HttpClientRequest
    {
        return new HttpClientRequest($this);
    }

    /**
     * Invoked when a non-existing method is called.
     *
     * @param string $name
     * @param array $arguments
     *
     * @return mixed
     * @throws HttpClientException
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function __call(string $name, array $arguments)
    {
        if (method_exists(HttpClientRequest::class, $name)) {
            return $this
                ->request()
                ->{$name}(...$arguments);
        }

        throw new HttpClientException(sprintf('Method "%s" does not exist in either %s or %s.', $name, self::class, HttpClientRequest::class), HttpClientException::ERR_BAD_METHOD_CALL);
    }

}
