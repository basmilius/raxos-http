<?php
declare(strict_types=1);

namespace Raxos\Http\Response;

use Raxos\Http\HttpResponse;
use Raxos\Http\Structure\HttpHeadersMap;

/**
 * Class BinaryHttpResponse
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Response
 * @since 2.1.0
 */
final class BinaryHttpResponse extends HttpResponse
{

    /**
     * BinaryHttpResponse constructor.
     *
     * @param string $data
     * @param HttpHeadersMap $headers
     *
     * @author Bas Milius <bas@mili.us>
     * @since 2.1.0
     */
    public function __construct(
        public string $data,
        HttpHeadersMap $headers = new HttpHeadersMap()
    )
    {
        parent::__construct($headers);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.1.0
     */
    protected function sendBody(): void
    {
        echo $this->data;
    }

}
