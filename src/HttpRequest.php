<?php
declare(strict_types=1);

namespace Raxos\Http;

use Raxos\Collection\{CacheMap, Map};
use Raxos\Contract\Http\HttpRequestInterface;
use Raxos\Foundation\Network\IP;
use Raxos\Http\Structure\{HttpCookiesMap, HttpFilesMap, HttpHeadersMap, HttpPostMap, HttpQueryMap, HttpServerMap};
use RuntimeException;
use function array_column;
use function count;
use function explode;
use function file_get_contents;
use function json_decode;
use function json_validate;
use function parse_str;
use function strstr;
use function strtoupper;
use function usort;
use const JSON_THROW_ON_ERROR;

/**
 * Class HttpRequest
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http
 * @since 1.0.0
 */
readonly class HttpRequest implements HttpRequestInterface
{

    private CacheMap $cache;

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
     * @param Map<string, mixed> $parameters
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
        public string $uri,
        public Map $parameters
    )
    {
        $this->cache = new CacheMap();
    }

    /**
     * Adds a query parameter as a request parameter.
     *
     * @param string $name
     * @param string $key
     * @param callable|null $sanitizer
     * @param mixed|null $defaultValue
     *
     * @return $this
     * @author Bas Milius <bas@mili.us>
     * @since 2.1.0
     */
    public function addParameterFromQuery(string $name, string $key, ?callable $sanitizer = null, mixed $defaultValue = null): self
    {
        if (!$this->query->has($key)) {
            if ($defaultValue !== null) {
                $this->parameters->set($name, $defaultValue);
            }

            return $this;
        }

        $value = $this->query->get($key);

        if ($sanitizer !== null) {
            $value = $sanitizer($value);
        }

        $this->parameters->set($name, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function bearerToken(): ?string
    {
        return $this->cache->remember(__METHOD__, function (): ?string {
            $header = $this->headers->get('authorization');

            if ($header === null) {
                return null;
            }

            $parts = explode(' ', $header, 2);

            if (count($parts) !== 2 || $parts[0] !== 'Bearer') {
                return null;
            }

            return $parts[1];
        });
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function contentType(): ?string
    {
        return $this->cache->remember(__METHOD__, function (): ?string {
            $header = $this->headers->get('content-type');

            if ($header === null) {
                return null;
            }

            $contentType = explode(';', $header, 2);

            return $contentType[0];
        });
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function ip(): ?IP
    {
        return IP::parse($this->headers->get(HttpHeader::X_FORWARDED_FOR) ?? $this->server->get('REMOTE_ADDR'));
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function isSecure(): bool
    {
        return ($this->server->get('HTTPS') ?? 'off') === 'on';
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function language(): ?string
    {
        return $this->languages()[0] ?? null;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function languages(): array
    {
        return $this->cache->remember(__METHOD__, function (): array {
            $header = $this->headers->get('accept-language');

            if ($header === null) {
                return [];
            }

            $accept = explode(',', $header);
            $languages = [];

            foreach ($accept as $language) {
                $language = explode(';', $language);

                parse_str($language[1] ?? 'q=1.0', $props);

                $props['q'] = (float)$props['q'];
                $props['code'] = $language[0];

                $languages[] = $props;
            }

            usort($languages, static fn(array $a, array $b): int => $b['q'] <=> $a['q']);

            return array_column($languages, 'code');
        });
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function body(): ?string
    {
        return $this->cache->remember(__METHOD__, function (): ?string {
            $body = file_get_contents('php://input');

            if (empty($body)) {
                return null;
            }

            return $body;
        });
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function json(): ?array
    {
        return $this->cache->remember(__METHOD__, function (): ?array {
            $body = $this->body();

            if ($body === null) {
                return null;
            }

            if (json_validate($body)) {
                return json_decode($body, true, 512, JSON_THROW_ON_ERROR);
            }

            throw new RuntimeException('Request body is not json.', 500);
        });
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function userAgent(): ?UserAgent
    {
        static $cache = [];

        $header = $this->headers->get(HttpHeader::USER_AGENT);

        if ($header === null) {
            return null;
        }

        return $cache[$header] ??= new UserAgent($header);
    }

    /**
     * Creates a request for the router.
     *
     * @param HttpCookiesMap|null $cookies
     * @param HttpFilesMap|null $files
     * @param HttpHeadersMap|null $headers
     * @param HttpPostMap|null $post
     * @param HttpQueryMap|null $query
     * @param HttpServerMap|null $server
     * @param HttpMethod|null $method
     * @param string|null $uri
     * @param Map $parameters
     *
     * @return self
     * @author Bas Milius <bas@mili.us>
     * @since 2.1.0
     */
    public static function create(
        ?HttpCookiesMap $cookies = null,
        ?HttpFilesMap $files = null,
        ?HttpHeadersMap $headers = null,
        ?HttpPostMap $post = null,
        ?HttpQueryMap $query = null,
        ?HttpServerMap $server = null,
        ?HttpMethod $method = null,
        ?string $uri = null,
        Map $parameters = new Map()
    ): self
    {
        static $request = null;

        $request ??= self::createFromGlobals();

        $cookies = $cookies ?? $request->cookies;
        $files = $files ?? $request->files;
        $headers = $headers ?? $request->headers;
        $post = $post ?? $request->post;
        $query = $query ?? $request->query;
        $server = $server ?? $request->server;
        $method = $method ?? $request->method;
        $uri = $uri ?? $request->uri;

        $pathName = strstr($uri, '?', true) ?: $uri;
        $queryString = explode('?', $uri)[1] ?? '';

        $query ??= HttpQueryMap::createFromString($queryString);

        return new self(
            cookies: $cookies,
            files: $files,
            headers: $headers,
            post: $post,
            query: $query,
            server: $server,
            method: $method,
            pathName: $pathName,
            uri: $uri,
            parameters: $parameters
        );
    }

    /**
     * Creates from globals.
     *
     * @return HttpRequestInterface
     * @author Bas Milius <bas@mili.us>
     * @since 1.2.0
     */
    public static function createFromGlobals(): HttpRequestInterface
    {
        $cookies = HttpCookiesMap::createFromGlobals();
        $files = HttpFilesMap::createFromGlobals();
        $headers = HttpHeadersMap::createFromGlobals();
        $post = HttpPostMap::createFromGlobals();
        $server = HttpServerMap::createFromGlobals();

        $method = HttpMethod::from(strtoupper($server->get('REQUEST_METHOD') ?? 'GET'));
        $uri = $server->get('REQUEST_URI') ?? '/';

        $parts = explode('?', $uri, 2);
        $pathName = $parts[0];
        $queryString = $parts[1] ?? '';
        $query = HttpQueryMap::createFromString($queryString);

        return new self(
            $cookies,
            $files,
            $headers,
            $post,
            $query,
            $server,
            $method,
            $pathName,
            $uri,
            new Map()
        );
    }

}
