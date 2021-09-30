<?php
declare(strict_types=1);

namespace Raxos\Http\Body;

use Raxos\Http\HttpRequest;

/**
 * Class HttpBody
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Body
 * @since 1.0.0
 */
abstract class HttpBody
{

    /**
     * HttpBody constructor.
     *
     * @param mixed $content
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function __construct(protected mixed $content)
    {
    }

    /**
     * Gets the raw content.
     *
     * @return mixed
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function getRaw(): mixed
    {
        return $this->content;
    }

    /**
     * Parses the body.
     *
     * @param HttpRequest $request
     * @param string $content
     *
     * @return HttpBody
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public static function parse(HttpRequest $request, string $content): HttpBody
    {
        $contentType = $request->headers()->get('content-type', '');

        return match ($contentType) {
            'application/json' => new HttpBodyJson($content),
            default => new HttpBodyPlain($content)
        };
    }

}
