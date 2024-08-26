<?php
declare(strict_types=1);

namespace Raxos\Http;

use JsonException;
use Raxos\Foundation\Network\{IP, IPv4, IPv6};
use Raxos\Foundation\Storage\SimpleKeyValue;
use Raxos\Http\Body\{HttpBody, HttpBodyJson};
use Raxos\Http\Store\{HttpCookieStore, HttpFileStore, HttpHeaderStore, HttpPostStore, HttpQueryStore, HttpServerStore};
use RuntimeException;
use function count;
use function explode;
use function file_get_contents;
use function strstr;
use function strtolower;

/**
 * Class HttpRequest
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http
 * @since 1.0.0
 */
readonly class HttpRequest
{

    private SimpleKeyValue $cache;

    /**
     * HttpRequest constructor.
     *
     * @param HttpCookieStore $cookies
     * @param HttpFileStore $files
     * @param HttpHeaderStore $headers
     * @param HttpPostStore $post
     * @param HttpQueryStore $query
     * @param HttpServerStore $server
     * @param HttpMethod $method
     * @param string $pathName
     * @param string $uri
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.1.0
     */
    public function __construct(
        public HttpCookieStore $cookies,
        public HttpFileStore $files,
        public HttpHeaderStore $headers,
        public HttpPostStore $post,
        public HttpQueryStore $query,
        public HttpServerStore $server,
        public HttpMethod $method,
        public string $pathName,
        public string $uri
    )
    {
        $this->cache = new SimpleKeyValue();
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
     * @return IPv4|IPv6|null
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function ip(): IPv4|IPv6|null
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
     * Ensures that the body is json.
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
     * Returns the http request from globals.
     *
     * @return self
     * @author Bas Milius <bas@mili.us>
     * @since 1.1.0
     */
    public static function fromGlobals(): self
    {
        $cookies = HttpCookieStore::fromGlobals();
        $files = HttpFileStore::fromGlobals();
        $headers = HttpHeaderStore::fromGlobals();
        $post = HttpPostStore::fromGlobals();
        $query = HttpQueryStore::fromGlobals();
        $server = HttpServerStore::fromGlobals();

        $method = HttpMethod::from(strtolower($server->get('REQUEST_METHOD') ?? 'GET'));
        $uri = $server->get('REQUEST_URI') ?? '/';
        $pathName = strstr($uri, '?', true) ?: $uri;

        return new self($cookies, $files, $headers, $post, $query, $server, $method, $pathName, $uri);
    }

}
