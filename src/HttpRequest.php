<?php
declare(strict_types=1);

namespace Raxos\Http;

use JsonException;
use Raxos\Collection\CacheMap;
use Raxos\Collection\Map;
use Raxos\Contract\Http\HttpRequestInterface;
use Raxos\Foundation\Network\IP;
use Raxos\Http\Structure\HttpCookiesMap;
use Raxos\Http\Structure\HttpFilesMap;
use Raxos\Http\Structure\HttpHeadersMap;
use Raxos\Http\Structure\HttpPostMap;
use Raxos\Http\Structure\HttpQueryMap;
use Raxos\Http\Structure\HttpServerMap;
use RuntimeException;
use function array_column;
use function explode;
use function file_get_contents;
use function is_array;
use function is_numeric;
use function is_string;
use function json_decode;
use function parse_str;
use function preg_match;
use function strstr;
use function strtoupper;
use function trim;
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
    /**
     * Shares model identities between rows loaded through this ORM connection.
     *
     * @var CacheMap
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
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
    public function addParameterFromQuery(
        string $name,
        string $key,
        ?callable $sanitizer = null,
        mixed $defaultValue = null
    ): self
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

            return preg_match('/^Bearer +([^\s]+)$/i', $header, $matches) === 1 ? $matches[1] : null;
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
        $ip = $this->headers->get('cf-connecting-ip')
            ?? $this->headers->get(HttpHeader::X_FORWARDED_FOR)
            ?? $this->server->get('REMOTE_ADDR');

        return is_string($ip) ? IP::parse(trim(explode(',', $ip, 2)[0])) : null;
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

                $quality = $props['q'] ?? '1';

                if (!is_numeric($quality) || (float)$quality <= 0 || (float)$quality > 1) {
                    continue;
                }

                $props['q'] = (float)$quality;
                $props['code'] = trim($language[0]);

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

            if ($body === false || $body === '') {
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

            try {
                $value = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $err) {
                throw new RuntimeException('Request body is not valid JSON.', 400, $err);
            }

            if ($value !== null && !is_array($value)) {
                throw new RuntimeException('Request JSON must be an object, array or null.', 400);
            }

            return $value;
        });
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function userAgent(): ?UserAgent
    {
        return $this->cache->remember(__METHOD__, function (): ?UserAgent {
            $header = $this->headers->get(HttpHeader::USER_AGENT);

            if ($header === null) {
                return null;
            }

            return new UserAgent($header);
        });
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
        $request = self::createFromGlobals();

        $cookies = $cookies ?? $request->cookies;
        $files = $files ?? $request->files;
        $headers = $headers ?? $request->headers;
        $post = $post ?? $request->post;
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
