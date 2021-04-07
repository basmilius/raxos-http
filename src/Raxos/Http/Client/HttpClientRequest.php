<?php
declare(strict_types=1);

namespace Raxos\Http\Client;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Raxos\Http\Client\Psr7\Psr7Request;
use Raxos\Http\HttpMethods;
use function http_build_query;

/**
 * Class HttpClientRequest
 *
 * @author Bas Milius <bas@glybe.nl>
 * @package Raxos\Http\Client
 * @since 1.0.0
 */
class HttpClientRequest
{

    private RequestInterface $request;

    public function __construct(protected HttpClient $client)
    {
        $this->request = new Psr7Request;
    }

    public final function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function bearerToken(string $token): static
    {
        return $this->header('Authorization', "Bearer {$token}");
    }

    public function header(string $name, string $value, bool $replace = true): static
    {
        if ($replace) {
            $this->request = $this->request->withHeader($name, $value);
        } else {
            $this->request = $this->request->withADdedHeader($name, $value);
        }

        return $this;
    }

    public function get(string $uri, ?array $query = []): HttpClientResponse
    {
        $uri = new Uri($uri);

        if ($query !== null) {
            $uri->withQuery(http_build_query($query));
        }

        $this->request->withMethod(HttpMethods::GET);
        $this->request->withUri(new Uri($uri));

        return new HttpClientResponse($this->client, $this, $this->client->getClient()->send($this->request));
    }

}
