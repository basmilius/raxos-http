<?php
declare(strict_types=1);

namespace Raxos\Http;

use JsonException;
use Raxos\Foundation\Collection\Map;
use Raxos\Foundation\Network\IP;
use Raxos\Http\Body\{HttpBody, HttpBodyJson};
use Raxos\Http\Structure\{HttpCookiesMap, HttpFilesMap, HttpHeadersMap, HttpPostMap, HttpQueryMap, HttpServerMap};
use RuntimeException;
use function count;
use function explode;
use function file_get_contents;
use function strstr;
use function strtoupper;

/**
 * Class HttpRequest
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http
 * @since 1.0.0
 */
readonly class HttpRequest
{

    private Map $cache;

    /**
     * HttpRequest constructor.
     *
     * @param HttpCookiesMap $cookies
     * @param HttpFilesMap $files
     * @param HttpHeadersMap $headers
     * @param HttpPostMap $post
     * @param HttpQueryMap $query
     * @param HttpServerMap $server
     * @param HttpMethod $method
     * @param string $pathName
     * @param string $uri
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.1.0
     */
    public function __construct(
        public HttpCookiesMap $cookies,
        public HttpFilesMap $files,
        public HttpHeadersMap $headers,
        public HttpPostMap $post,
        public HttpQueryMap $query,
        public HttpServerMap $server,
        public HttpMethod $method,
        public string $pathName,
        public string $uri
    )
    {
        $this->cache = new Map();
    }

    /**
     * Gets the primary content type.
     *
     * @return string
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function contentType(): string
    {
        $contentType = $this->headers->get('content-type') ?? '';
        $contentType = explode(';', $contentType);

        return $contentType[0];
    }

    /**
     * Gets the bearer token.
     *
     * @return string|null
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function bearerToken(): ?string
    {
        $header = $this->headers->get('authorization');

        if ($header === null) {
            return null;
        }

        $parts = explode(' ', $header, 2);

        if (count($parts) !== 2 || $parts[0] !== 'Bearer') {
            return null;
        }

        return $parts[1];
    }

    /**
     * Gets the IP address.
     *
     * @return ?IP
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function ip(): ?IP
    {
        if ($this->cache->has('ip')) {
            return $this->cache->get('ip');
        }

        $ip = IP::parse($this->server->get('REMOTE_ADDR'));

        $this->cache->set('ip', $ip);

        return $ip;
    }

    /**
     * Returns TRUE if the request is secure.
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function isSecure(): bool
    {
        return ($this->server->get('HTTPS') ?? 'off') === 'on';
    }

    /**
     * Gets the parsed body.
     *
     * @return HttpBody
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function body(): HttpBody
    {
        if ($this->cache->has('body')) {
            return $this->cache->get('body');
        }

        try {
            $body = HttpBody::parse($this, $this->bodyString());

            $this->cache->set('body', $body);

            return $body;
        } catch (JsonException $err) {
            throw new RuntimeException($err->getMessage(), $err->getCode(), $err);
        }
    }

    /**
     * Ensures that the body is JSON.
     *
     * @return array
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function bodyJson(): array
    {
        $body = $this->body();

        if ($body instanceof HttpBodyJson) {
            return $body->array();
        }

        throw new RuntimeException('Request body is not json.', 500);
    }

    /**
     * Gets the body as string.
     *
     * @return string
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function bodyString(): string
    {
        if ($this->cache->has('body_string')) {
            return $this->cache->get('body_string');
        }

        $content = file_get_contents('php://input');

        $this->cache->set('body_string', $content);

        return $content;
    }

    /**
     * Gets the user agent.
     *
     * @return UserAgent
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function userAgent(): UserAgent
    {
        if ($this->cache->has('user_agent')) {
            return $this->cache->get('user_agent');
        }

        $ua = new UserAgent($this->server->get('HTTP_USER_AGENT') ?? 'Raxos/1.0');

        $this->cache->set('user_agent', $ua);

        return $ua;
    }

    /**
     * Creates from globals.
     *
     * @return self
     * @author Bas Milius <bas@mili.us>
     * @since 1.2.0
     */
    public static function createFromGlobals(): self
    {
        $cookies = HttpCookiesMap::createFromGlobals();
        $files = HttpFilesMap::createFromGlobals();
        $headers = HttpHeadersMap::createFromGlobals();
        $post = HttpPostMap::createFromGlobals();
        $query = HttpQueryMap::createFromGlobals();
        $server = HttpServerMap::createFromGlobals();

        $method = HttpMethod::from(strtoupper($server->get('REQUEST_METHOD') ?? 'GET'));
        $uri = $server->get('REQUEST_URI') ?? '/';
        $pathName = strstr($uri, '?', true) ?: $uri;

        return new self($cookies, $files, $headers, $post, $query, $server, $method, $pathName, $uri);
    }

}
