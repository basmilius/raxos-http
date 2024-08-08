<?php
declare(strict_types=1);

namespace Raxos\Http\Body;

use JsonException;
use function json_decode;
use const JSON_THROW_ON_ERROR;

/**
 * Class HttpBodyJson
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Body
 * @since 1.0.0
 */
final class HttpBodyJson extends HttpBody
{

    /**
     * HttpBodyJson constructor.
     *
     * @param string $content
     *
     * @throws JsonException
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function __construct(string $content)
    {
        parent::__construct(json_decode($content, true, 512, JSON_THROW_ON_ERROR));
    }

    /**
     * Gets the json as an array.
     *
     * @return array
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function array(): array
    {
        return $this->content ?? [];
    }

}
