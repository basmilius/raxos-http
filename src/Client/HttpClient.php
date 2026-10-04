<?php
declare(strict_types=1);

namespace Raxos\Http\Client;

use GuzzleHttp\Client as GuzzleClient;
use Raxos\Contract\Http\HttpClientExceptionInterface;
use Raxos\Http\Client\Error\BadCallException;
use ReflectionMethod;
use function method_exists;
use function sprintf;

/**
 * Class HttpClient
 *
 * Creates isolated request builders around one configured HTTP transport.
 *
 * @mixin HttpClientRequest
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Client
 * @since 1.0.0
 */
readonly class HttpClient
{

    /**
     * Retains the configured transport client without rebuilding it for each request.
     *
     * @var GuzzleClient
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public GuzzleClient $client;

    /**
     * HttpClient constructor.
     *
     * @param string|null $baseUrl
     * @param float $timeout
     * @param GuzzleClient|null $client
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function __construct(
        ?string $baseUrl = null,
        float $timeout = 5.0,
        ?GuzzleClient $client = null
    )
    {
        $this->client = $client ?? new GuzzleClient([
            'base_uri' => $baseUrl,
            'http_errors' => false,
            'timeout' => $timeout
        ]);
    }

    /**
     * Creates a new request instance.
     *
     * @return HttpClientRequest
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function request(): HttpClientRequest
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
     * @throws HttpClientExceptionInterface
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function __call(
        string $name,
        array $arguments
    )
    {
        if (method_exists(HttpClientRequest::class, $name) && new ReflectionMethod(HttpClientRequest::class, $name)->isPublic()) {
            return $this
                ->request()
                ->{$name}(...$arguments);
        }

        throw new BadCallException(sprintf('Method "%s" does not exist in either "%s or "%s".', $name, static::class, HttpClientRequest::class));
    }

}
