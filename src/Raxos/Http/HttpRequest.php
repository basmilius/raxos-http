<?php
declare(strict_types=1);

namespace Raxos\Http;

use JetBrains\PhpStorm\ExpectedValues;
use Raxos\Foundation\Network\IP;
use Raxos\Foundation\Network\IPv4;
use Raxos\Foundation\Network\IPv6;
use Raxos\Foundation\Storage\ReadonlyKeyValue;
use Raxos\Foundation\Storage\SimpleKeyValue;
use Raxos\Foundation\Util\ArrayUtil;
use Raxos\Http\Body\HttpBody;
use Raxos\Http\Body\HttpBodyJson;
use RuntimeException;
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

    private SimpleKeyValue $cache;
    private ReadonlyKeyValue $cookies;
    private ReadonlyKeyValue $files;
    private ReadonlyKeyValue $headers;
    private ReadonlyKeyValue $post;
    private ReadonlyKeyValue $queryString;
    private ReadonlyKeyValue $server;

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
     * @return string
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    #[ExpectedValues(valuesFromClass: HttpMethods::class)]
    public function method(): string
    {
        return strtolower($this->server->get('REQUEST_METHOD', 'GET'));
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
    public final function bearerToken(): ?string
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
    public final function ip(): IPv4|IPv6|null
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
    public final function isSecure(): bool
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
     * Gets the request cookies.
     *
     * @return ReadonlyKeyValue
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function cookies(): ReadonlyKeyValue
    {
        return $this->cookies;
    }

    /**
     * Gets the request files.
     *
     * @return ReadonlyKeyValue
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function files(): ReadonlyKeyValue
    {
        return $this->files;
    }

    /**
     * Gets the request headers.
     *
     * @return ReadonlyKeyValue
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function headers(): ReadonlyKeyValue
    {
        return $this->headers;
    }

    /**
     * Gets the post fields.
     *
     * @return ReadonlyKeyValue
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function post(): ReadonlyKeyValue
    {
        return $this->post;
    }

    /**
     * Gets the query string.
     *
     * @return ReadonlyKeyValue
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function queryString(): ReadonlyKeyValue
    {
        return $this->queryString;
    }

    /**
     * Gets the server properties.
     *
     * @return ReadonlyKeyValue
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function server(): ReadonlyKeyValue
    {
        return $this->server;
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
     * @return ReadonlyKeyValue
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    protected static function createCookiesKeyValue(): ReadonlyKeyValue
    {
        return new ReadonlyKeyValue($_COOKIE);
    }

    /**
     * Creates the files store.
     *
     * @return ReadonlyKeyValue
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    protected static function createFilesKeyValue(): ReadonlyKeyValue
    {
        $files = [];

        foreach ($_FILES as $name => $value) {
            if (ArrayUtil::isSequential($value)) {
                $files[$name] ??= [];

                foreach ($value as $file) {
                    $files[$name][] = new HttpFile($file);
                }
            } else {
                $files[$name] = new HttpFile($value);
            }
        }

        return new ReadonlyKeyValue($files);
    }

    /**
     * Creates the headers store.
     *
     * @return ReadonlyKeyValue
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    protected static function createHeadersKeyValue(): ReadonlyKeyValue
    {
        return new ReadonlyKeyValue(HttpUtil::getAllHeaders());
    }

    /**
     * Creates the post store.
     *
     * @return ReadonlyKeyValue
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    protected static function createPostKeyValue(): ReadonlyKeyValue
    {
        return new ReadonlyKeyValue($_POST);
    }

    /**
     * Creates the query string store.
     *
     * @return ReadonlyKeyValue
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    protected static function createQueryStringKeyValue(): ReadonlyKeyValue
    {
        parse_str($_SERVER['QUERY_STRING'] ?? '', $queryString);

        return new ReadonlyKeyValue($queryString);
    }

    /**
     * Creates the server store.
     *
     * @return ReadonlyKeyValue
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    protected static function createServerKeyValue(): ReadonlyKeyValue
    {
        return new ReadonlyKeyValue($_SERVER);
    }

}
