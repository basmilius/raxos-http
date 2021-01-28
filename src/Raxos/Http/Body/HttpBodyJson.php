<?php
declare(strict_types=1);

namespace Raxos\Http\Body;

use function json_decode;

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
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function __construct(string $content)
    {
        parent::__construct(json_decode($content, true));
    }

    /**
     * Gets the json as an array.
     *
     * @return array
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function array(): array
    {
        return $this->content;
    }

}
