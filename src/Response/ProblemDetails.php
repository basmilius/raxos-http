<?php
declare(strict_types=1);

namespace Raxos\Http\Response;

use JsonSerializable;
use Raxos\Contract\ExceptionInterface;
use Raxos\Error\InvalidArgumentException;
use Raxos\Http\HttpHeader;
use Raxos\Http\HttpResponseCode;
use Throwable;
use function array_fill_keys;
use function array_intersect_key;
use function str_replace;
use function strtolower;
use function ucwords;

/**
 * Class ProblemDetails
 *
 * Explicit public error representation; exception messages and causes are never copied implicitly.
 *
 * @see https://www.rfc-editor.org/rfc/rfc9457.html
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Response
 * @since 3.3.0
 */
final readonly class ProblemDetails implements JsonSerializable
{
    /**
     * Accepts a 4xx or 5xx status and keeps extension fields from replacing standard problem members.
     *
     * @param int $status
     * @param string $title
     * @param string|null $detail
     * @param string $type
     * @param string|null $instance
     * @param array<string, mixed> $extensions
     * @throws InvalidArgumentException
     * @author Bas Milius <bas@mili.us>
     * @since 3.3.0
     */
    public function __construct(
        public int $status,
        public string $title,
        public ?string $detail = null,
        public string $type = 'about:blank',
        public ?string $instance = null,
        public array $extensions = []
    )
    {
        if ($status < 400 || $status > 599 || HttpResponseCode::tryFrom($status) === null) {
            throw new InvalidArgumentException('A problem response requires a supported 4xx or 5xx status.');
        }

        if (array_intersect_key($extensions, array_fill_keys(['type', 'title', 'status', 'detail', 'instance'], true)) !== []) {
            throw new InvalidArgumentException('Problem extensions cannot override standard members.');
        }
    }

    /**
     * Only explicitly supplied detail is exposed to clients.
     *
     * @param Throwable $error
     * @param int $status
     * @param string|null $detail
     * @param string|null $instance
     * @return self
     * @throws InvalidArgumentException
     * @author Bas Milius <bas@mili.us>
     * @since 3.3.0
     */
    public static function fromException(
        Throwable $error,
        int $status = 500,
        ?string $detail = null,
        ?string $instance = null
    ): self
    {
        $code = HttpResponseCode::tryFrom($status);
        $title = $code === null ? 'Error' : ucwords(strtolower(str_replace('_', ' ', $code->name)));

        return new self($status, $title, $detail, instance: $instance, extensions: $error instanceof ExceptionInterface ? ['error' => $error->error] : []);
    }

    /**
     * Omits absent optional members while retaining standard problem fields and safe extensions.
     *
     * @return array<string, mixed>
     * @author Bas Milius <bas@mili.us>
     * @since 3.3.0
     */
    public function jsonSerialize(): array
    {
        $body = ['type' => $this->type, 'title' => $this->title, 'status' => $this->status];

        if ($this->detail !== null) {
            $body['detail'] = $this->detail;
        }

        if ($this->instance !== null) {
            $body['instance'] = $this->instance;
        }

        return [...$body, ...$this->extensions];
    }

    /**
     * Creates an HTTP response using the application/problem+json media type.
     *
     * @return JsonHttpResponse
     * @author Bas Milius <bas@mili.us>
     * @since 3.3.0
     */
    public function response(): JsonHttpResponse
    {
        $response = new JsonHttpResponse($this, responseCode: HttpResponseCode::from($this->status));
        $response->header(HttpHeader::CONTENT_TYPE, 'application/problem+json', replace: true);

        return $response;
    }
}
