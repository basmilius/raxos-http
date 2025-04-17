<?php
declare(strict_types=1);

namespace Raxos\Http\Contract;

use JsonException;
use Raxos\Foundation\Network\IP;
use Raxos\Http\{HttpMethod, UserAgent};
use Raxos\Http\Structure\{HttpCookiesMap, HttpFilesMap, HttpHeadersMap, HttpPostMap, HttpQueryMap, HttpServerMap};

/**
 * Interface HttpRequestInterface
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Contract
 * @since 1.7.0
 */
interface HttpRequestInterface
{

    public HttpCookiesMap $cookies {
        get;
    }

    public HttpFilesMap $files {
        get;
    }

    public HttpHeadersMap $headers {
        get;
    }

    public HttpPostMap $post {
        get;
    }

    public HttpQueryMap $query {
        get;
    }

    public HttpServerMap $server {
        get;
    }

    public HttpMethod $method {
        get;
    }

    public string $pathName {
        get;
    }

    public string $uri {
        get;
    }

    /**
     * Returns the bearer token, if present.
     *
     * @return string|null
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function bearerToken(): ?string;

    /**
     * Returns the body, if present.
     *
     * @return string|null
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function body(): ?string;

    /**
     * Returns the content type, if present.
     *
     * @return string|null
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function contentType(): ?string;

    /**
     * Returns the IP address.
     *
     * @return IP|null
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function ip(): ?IP;

    /**
     * Returns TRUE if the request is secure.
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function isSecure(): bool;

    /**
     * Returns the JSON body, if present.
     *
     * @return array|null
     * @throws JsonException
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function json(): ?array;

    /**
     * Returns the primary language the browser accepts.
     *
     * @return string|null
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function language(): ?string;

    /**
     * Returns the languages the browser accepts.
     *
     * @return string[]
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function languages(): array;

    /**
     * Returns the user agent, if present.
     *
     * @return UserAgent|null
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function userAgent(): ?UserAgent;

}
