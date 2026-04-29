<?php
declare(strict_types=1);

namespace Raxos\Http\Client\Psr;

use GuzzleHttp\Exception\{ConnectException, RequestException};
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\{RequestInterface, ResponseInterface};
use Raxos\Http\Client\HttpClient;
use Raxos\Http\Client\Psr\Error\{Psr18NetworkException, Psr18RequestException};

/**
 * Class Psr18Client
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Client\Psr
 * @since 2.2.0
 */
final readonly class Psr18Client implements ClientInterface
{

    /**
     * Psr18Client constructor.
     *
     * @param HttpClient $http
     *
     * @author Bas Milius <bas@mili.us>
     * @since 2.2.0
     */
    public function __construct(
        private HttpClient $http
    ) {}

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.2.0
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        try {
            return $this->http->client->sendRequest($request);
        } catch (ConnectException $err) {
            throw new Psr18NetworkException($request, $err->getMessage(), $err);
        } catch (RequestException $err) {
            throw new Psr18RequestException($request, $err->getMessage(), $err);
        }
    }

}
