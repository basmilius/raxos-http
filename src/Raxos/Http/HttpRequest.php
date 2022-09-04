<?php
declare(strict_types=1);

namespace Raxos\Http;

use JetBrains\PhpStorm\Pure;
use Raxos\Foundation\Network\IP;
use Raxos\Foundation\Network\IPv4;
use Raxos\Foundation\Network\IPv6;
use Raxos\Foundation\Storage\SimpleKeyValue;
use Raxos\Http\Body\HttpBody;
use Raxos\Http\Body\HttpBodyJson;
use RuntimeException;
use function array_is_list;
use function count;
use function explode;
use function file_get_contents;
use function parse_str;
use function strstr;
use function strtolower;

/**
 * Class HttpRequest
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http
 * @since 1.0.0
 */
class HttpRequest
{

    public readonly SimpleKeyValue $cache;
    public readonly SimpleKeyValue $cookies;
    public readonly SimpleKeyValue $files;
    public readonly SimpleKeyValue $headers;
    public readonly SimpleKeyValue $post;
    public readonly SimpleKeyValue $queryString;
    public readonly SimpleKeyValue $server;

    private HttpMethod $method;

    /**
     * HttpRequest constructor.
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->cache = new SimpleKeyValue();
        $this->cookies = static::createCookiesKeyValue();
        $this->files = static::createFilesKeyValue();
        $this->headers = static::createHeadersKeyValue();
        $this->post = static::createPostKeyValue();
        $this->queryString = static::createQueryStringKeyValue();
        $this->server = static::createServerKeyValue();

        $this->method = HttpMethod::from(strtolower($this->server->get('REQUEST_METHOD', 'GET')));
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
        $contentType = $this->headers->get('content-type', '');
        $contentType = explode(';', $contentType);

        return $contentType[0];
    }

    /**
     * Gets the request method.
     *
     * @return HttpMethod
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function method(): HttpMethod
    {
        return $this->method;
    }

    /**
     * Gets the request path name.
     *
     * @return string
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function pathName(): string
    {
        $uri = $this->uri();

        return strstr($uri, '?', true) ?: $uri;
    }

    /**
     * Gets the request uri.
     *
     * @return string
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function uri(): string
    {
        return $this->server->get('REQUEST_URI');
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

        if ($header === null)
            return null;

        $parts = explode(' ', $header, 2);

        if (count($parts) !== 2 || $parts[0] !== 'Bearer')
            return null;

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
        return $this->server->get('HTTPS', 'off') === 'on';
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

        $body = HttpBody::parse($this, $this->bodyString());

        $this->cache->set('body', $body);

        return $body;
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

        $ua = new UserAgent($this->server->get('HTTP_USER_AGENT', 'Raxos/1.0'));

        $this->cache->set('user_agent', $ua);

        return $ua;
    }

    /**
     * Creates the cookies store.
     *
     * @return SimpleKeyValue
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    #[Pure]
    protected static function createCookiesKeyValue(): SimpleKeyValue
    {
        return new SimpleKeyValue($_COOKIE);
    }

    /**
     * Creates the files store.
     *
     * @return SimpleKeyValue
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    #[Pure]
    protected static function createFilesKeyValue(): SimpleKeyValue
    {
        $files = [];

        foreach ($_FILES as $name => $value) {
            if (array_is_list($value)) {
                $files[$name] ??= [];

                foreach ($value as $file) {
                    $files[$name][] = new HttpFile($file);
                }
            } else {
                $files[$name] = new HttpFile($value);
            }
        }

        return new SimpleKeyValue($files);
    }

    /**
     * Creates the headers store.
     *
     * @return SimpleKeyValue
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    protected static function createHeadersKeyValue(): SimpleKeyValue
    {
        return new SimpleKeyValue(HttpUtil::getAllHeaders());
    }

    /**
     * Creates the post store.
     *
     * @return SimpleKeyValue
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    #[Pure]
    protected static function createPostKeyValue(): SimpleKeyValue
    {
        return new SimpleKeyValue($_POST);
    }

    /**
     * Creates the query string store.
     *
     * @return SimpleKeyValue
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    protected static function createQueryStringKeyValue(): SimpleKeyValue
    {
        parse_str($_SERVER['QUERY_STRING'] ?? '', $queryString);

        return new SimpleKeyValue($queryString);
    }

    /**
     * Creates the server store.
     *
     * @return SimpleKeyValue
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    #[Pure]
    protected static function createServerKeyValue(): SimpleKeyValue
    {
        return new SimpleKeyValue($_SERVER);
    }

}
