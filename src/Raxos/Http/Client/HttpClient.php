<?php
declare(strict_types=1);

namespace Raxos\Http\Client;

use GuzzleHttp\Client as GuzzleClient;

/**
 * Class HttpClient
 *
 * @author Bas Milius <bas@glybe.nl>
 * @package Raxos\Http\Client
 * @since 1.0.0
 */
class HttpClient
{

    protected GuzzleClient $client;

    public function __construct(string $baseUrl = null, float $timeout = 5.0)
    {
        $this->client = new GuzzleClient([
            'base_uri' => $baseUrl,
            'http_errors' => false,
            'timeout' => $timeout
        ]);
    }

    public final function getClient(): GuzzleClient
    {
        return $this->client;
    }

    public function request(): HttpClientRequest
    {
        return new HttpClientRequest($this);
    }

    public function get(string $uri, ?array $query = []): HttpClientResponse
    {
        return $this
            ->request()
            ->get($uri, $query);
    }

}
